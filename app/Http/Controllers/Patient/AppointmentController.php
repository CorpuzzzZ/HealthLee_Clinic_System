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

    // ── Appointment duration is 1 hour ──
    $startTime = \Carbon\Carbon::parse($request->appointment_time);
    $endTime   = $startTime->copy()->addHour();

    // ── Check doctor has availability on the selected date ──
    $availabilityExists = \App\Models\Availability::where('doctor_id', $request->doctor_id)
        ->whereDate('available_date', $request->appointment_date)
        ->exists();

    if (!$availabilityExists) {
        return back()
            ->withInput()
            ->withErrors([
                'appointment_date' => 'The doctor has no availability on the selected date. Please choose a different date.',
            ]);
    }

    // ── Check the full 1-hour slot fits within the doctor's availability ──
    $slotFits = \App\Models\Availability::where('doctor_id', $request->doctor_id)
        ->whereDate('available_date', $request->appointment_date)
        ->where('start_time', '<=', $startTime->format('H:i:s'))
        ->where('end_time',   '>=', $endTime->format('H:i:s'))
        ->exists();

    if (!$slotFits) {
        return back()
            ->withInput()
            ->withErrors([
                'appointment_time' => 'The selected time slot (1 hour duration) does not fit within the doctor\'s available hours.',
            ]);
    }

    // ── Check no overlapping appointment exists for this doctor ──
    // An overlap happens when:
    // existing start_time < new end_time AND existing end_time > new start_time
    $overlapping = \App\Models\Appointment::where('doctor_id', $request->doctor_id)
        ->whereDate('appointment_date', $request->appointment_date)
        ->whereNotIn('status', ['cancelled'])
        ->where(function ($query) use ($startTime, $endTime) {
            $query->where(function ($q) use ($startTime, $endTime) {
                // New appointment starts during an existing one
                $q->where('appointment_time', '<',  $endTime->format('H:i:s'))
                  ->whereRaw("ADDTIME(appointment_time, '01:00:00') > ?", [$startTime->format('H:i:s')]);
            });
        })
        ->exists();

    if ($overlapping) {
        return back()
            ->withInput()
            ->withErrors([
                'appointment_time' => 'This time slot overlaps with an existing appointment. Each appointment is 1 hour. Please choose a different time.',
            ]);
    }

    // ── Create appointment ──
    try {
        $appointment = \App\Models\Appointment::create([
            'patient_id'       => $patient->id,
            'doctor_id'        => $request->doctor_id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $startTime->format('H:i:s'),
            'status'           => 'pending',
            'notes'            => $request->notes,
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        return back()
            ->withInput()
            ->withErrors([
                'appointment_time' => 'This time slot is already booked. The database prevented a double booking.',
            ]);
    }

    // ── Notify patient ──
    $appointment->load(['patient', 'doctor']);
    \App\Models\Notification::create([
        'user_id' => $patient->user_id,
        'message' => "Your appointment request with Dr. {$appointment->doctor->first_name} {$appointment->doctor->last_name} on " .
                     $appointment->appointment_date->format('d M Y') . " at " .
                     $startTime->format('h:i A') . " – " . $endTime->format('h:i A') .
                     " has been submitted and is awaiting confirmation.",
        'type'   => 'general',
        'status' => 'unread',
    ]);

    // ── Notify doctor ──
    \App\Models\Notification::create([
        'user_id' => $appointment->doctor->user_id,
        'message' => "New appointment request from {$appointment->patient->first_name} {$appointment->patient->last_name} on " .
                     $appointment->appointment_date->format('d M Y') . " at " .
                     $startTime->format('h:i A') . " – " . $endTime->format('h:i A') .
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