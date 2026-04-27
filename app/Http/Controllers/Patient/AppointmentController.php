<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Availability;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    private function getPatient()
    {
        return Auth::user()->patient;
    }

    public function index(Request $request)
    {
        $patient = $this->getPatient();

        $query = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->orderBy('appointment_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments   = $query->paginate(10)->withQueryString();
        $totalUpcoming  = Appointment::where('patient_id', $patient->id)
                                     ->whereDate('appointment_date', '>=', today())
                                     ->whereIn('status', ['pending', 'confirmed'])
                                     ->count();
        $totalCompleted = Appointment::where('patient_id', $patient->id)
                                     ->where('status', 'completed')->count();
        $totalCancelled = Appointment::where('patient_id', $patient->id)
                                     ->where('status', 'cancelled')->count();

        return view('patient.appointments.index', compact(
            'appointments',
            'totalUpcoming',
            'totalCompleted',
            'totalCancelled',
        ));
    }

    public function create(Request $request)
    {
        $doctor = null;

        if ($request->filled('doctor_id')) {
            $doctor = Doctor::with(['availabilities' => function ($q) {
                $q->whereDate('available_date', '>=', today())
                  ->orderBy('available_date')
                  ->orderBy('start_time');
            }])->findOrFail($request->doctor_id);
        }

        $doctors = Doctor::orderBy('last_name')->get();

        return view('patient.appointments.create', compact('doctors', 'doctor'));
    }

    public function store(Request $request)
    {
        $patient = $this->getPatient();

        $request->validate([
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        // ── Check doctor has availability on the selected date ──
        $availabilityExists = Availability::where('doctor_id', $request->doctor_id)
            ->whereDate('available_date', $request->appointment_date)
            ->exists();

        if (!$availabilityExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_date' => 'The doctor has no availability on the selected date. Please choose a different date.',
                ]);
        }

        // ── Check time is within the doctor's available slot ──
        $timeIsValid = Availability::where('doctor_id', $request->doctor_id)
            ->whereDate('available_date', $request->appointment_date)
            ->where('start_time', '<=', $request->appointment_time)
            ->where('end_time',   '>=', $request->appointment_time)
            ->exists();

        if (!$timeIsValid) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'The selected time is outside the doctor\'s available hours. Please choose a time within the available slot.',
                ]);
        }

        // ── Check no existing appointment at same date/time for this doctor ──
        $alreadyBooked = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($alreadyBooked) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'This time slot is already booked. Please choose a different time.',
                ]);
        }

        // ── Create appointment as pending ──
        $appointment = Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status'           => 'pending',
            'notes'            => $request->notes,
        ]);

        // ── Notify patient that booking is received (NOT confirmed yet) ──
        $appointment->load(['patient', 'doctor']);
        \App\Models\Notification::create([
            'user_id' => $patient->user_id,
            'message' => "Your appointment request with Dr. {$appointment->doctor->first_name} {$appointment->doctor->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') .
                         " has been submitted and is awaiting confirmation.",
            'type'   => 'general',
            'status' => 'unread',
        ]);

        // ── Notify doctor that a new booking request was made ──
        \App\Models\Notification::create([
            'user_id' => $appointment->doctor->user_id,
            'message' => "New appointment request from {$appointment->patient->first_name} {$appointment->patient->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') .
                         ". Please confirm or update the status.",
            'type'   => 'general',
            'status' => 'unread',
        ]);

        return redirect()->route('patient.appointments.index')
                         ->with('success', 'Appointment request submitted! Awaiting doctor confirmation.');
    }

    public function show(Appointment $appointment)
    {
        abort_if($appointment->patient_id !== $this->getPatient()->id, 403);
        $appointment->load(['doctor', 'medicalRecord']);
        return view('patient.appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment)
    {
        abort_if($appointment->patient_id !== $this->getPatient()->id, 403);
        abort_if(in_array($appointment->status, ['completed', 'cancelled']), 403);

        $appointment->load(['patient', 'doctor']);
        $appointment->update(['status' => 'cancelled']);

        (new NotificationService())->appointmentCancelled($appointment);

        return redirect()->route('patient.appointments.index')
                         ->with('success', 'Appointment cancelled successfully.');
    }
}