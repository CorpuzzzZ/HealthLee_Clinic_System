<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Availability;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $today = now()->format('Y-m-d');

        // Only doctors who have future availability
        $doctors = Doctor::whereHas('availabilities', function ($q) use ($today) {
                            $q->whereDate('available_date', '>=', $today);
                        })
                        ->with([
                            'availabilities' => fn($q) => $q
                                ->whereDate('available_date', '>=', $today)
                                ->orderBy('available_date')
                                ->orderBy('start_time'),
                            'services',
                        ])
                        ->orderBy('last_name')
                        ->get();

        // Pre-calculate which dates actually have available slots
        foreach ($doctors as $doctor) {
            $datesWithSlots = [];

            $dates = $doctor->availabilities
                ->pluck('available_date')
                ->map(fn($dt) => Carbon::parse($dt)->format('Y-m-d'))
                ->unique()
                ->values()
                ->filter(fn($date) => $date >= $today)
                ->toArray();

            foreach ($dates as $date) {
                $availabilitiesForDate = $doctor->availabilities
                    ->filter(fn($a) => Carbon::parse($a->available_date)->format('Y-m-d') === $date);

                $bookedTimes = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $date)
                    ->whereNotIn('status', ['cancelled'])
                    ->pluck('appointment_time')
                    ->map(fn($t) => Carbon::parse($t)->format('H:i'))
                    ->toArray();

                $hasAvailableSlot = false;

                foreach ($availabilitiesForDate as $avail) {
                    $start = Carbon::createFromFormat('H:i:s', $avail->start_time);
                    $end   = Carbon::createFromFormat('H:i:s', $avail->end_time);
                    
                    // Check if this specific time window is available (not just 1-hour slots)
                    $slotStart = $start->format('H:i');
                    if (!in_array($slotStart, $bookedTimes)) {
                        $hasAvailableSlot = true;
                        break;
                    }
                }

                if ($hasAvailableSlot) {
                    $datesWithSlots[] = $date;
                }
            }

            $doctor->availableDatesWithSlots = array_values(
                array_filter($datesWithSlots, fn($date) => $date >= $today)
            );
        }

        // ── Filter out fully booked doctors (no available dates left) ──
        $doctors = $doctors->filter(fn($doctor) => count($doctor->availableDatesWithSlots) > 0)->values();

        return view('patient.appointments.create', compact('doctors'));
    }

    public function store(Request $request)
    {
        $patient = $this->getPatient();

        $request->validate([
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'notes'            => ['nullable', 'string', 'max:500'],
            'service_id'       => ['nullable', 'exists:services,id'],
        ]);

        // Parse the selected start time
        $startTime = Carbon::parse($request->appointment_time);
        
        // Find the availability slot that matches this start time
        $availability = Availability::where('doctor_id', $request->doctor_id)
            ->whereDate('available_date', $request->appointment_date)
            ->get()
            ->first(function ($a) use ($startTime) {
                return Carbon::parse($a->start_time)->format('H:i') === $startTime->format('H:i');
            });

        if (!$availability) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'Invalid time slot selected.',
                ]);
        }

        $endTime = Carbon::parse($availability->end_time);
        $duration = $startTime->diffInMinutes($endTime);
        
        // Format duration label for notifications
        $durationLabel = $this->formatDuration($duration);

        // ── Check if this exact time window is already booked ──
        $isBooked = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->whereTime('appointment_time', $startTime->format('H:i:s'))
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($isBooked) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'This time slot is already booked. Please choose another time.',
                ]);
        }

        // ── Create appointment ──
        try {
            $appointment = Appointment::create([
                'patient_id'       => $patient->id,
                'doctor_id'        => $request->doctor_id,
                'service_id'       => $request->service_id,
                'appointment_date' => $request->appointment_date,
                'appointment_time' => $startTime->format('H:i:s'),
                'status'           => 'pending',
                'notes'            => $request->notes,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'Unable to book appointment. Please try again.',
                ]);
        }

        // ── Notify patient ──
        $appointment->load(['patient', 'doctor']);
        \App\Models\Notification::create([
            'user_id' => $patient->user_id,
            'message' => "Your appointment request with Dr. {$appointment->doctor->first_name} {$appointment->doctor->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " from " .
                         $startTime->format('h:i A') . " to " . $endTime->format('h:i A') .
                         " ({$durationLabel}) has been submitted and is awaiting confirmation.",
            'type'    => 'general',
            'status'  => 'unread',
        ]);

        // ── Notify doctor ──
        \App\Models\Notification::create([
            'user_id' => $appointment->doctor->user_id,
            'message' => "New appointment request from {$appointment->patient->first_name} {$appointment->patient->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " from " .
                         $startTime->format('h:i A') . " to " . $endTime->format('h:i A') .
                         " ({$durationLabel}). Please confirm or update the status.",
            'type'    => 'general',
            'status'  => 'unread',
        ]);

        return redirect()->route('patient.appointments.index')
                         ->with('success', 'Appointment request submitted! Awaiting doctor confirmation.');
    }

    public function show(Appointment $appointment)
    {
        abort_if($appointment->patient_id !== $this->getPatient()->id, 403);
        $appointment->load(['doctor', 'medicalRecord', 'service']);
        
        // Load the availability to get the end time
        $availability = Availability::where('doctor_id', $appointment->doctor_id)
            ->whereDate('available_date', $appointment->appointment_date)
            ->whereTime('start_time', Carbon::parse($appointment->appointment_time)->format('H:i:s'))
            ->first();
        
        $endTime = $availability ? Carbon::parse($availability->end_time) : null;
        
        return view('patient.appointments.show', compact('appointment', 'endTime'));
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

    public function slots(Request $request)
    {
        $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date'      => ['required', 'date'],
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);
        $date   = $request->date;

        // Fetch all availabilities for this date
        $availabilities = Availability::where('doctor_id', $doctor->id)
            ->get()
            ->filter(fn($a) => Carbon::parse($a->available_date)->format('Y-m-d') === $date);

        if ($availabilities->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        // Get already booked slots for this date (excluding cancelled)
        $bookedSlots = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $slots = [];

        foreach ($availabilities as $avail) {
            $start = Carbon::createFromFormat('H:i:s', $avail->start_time);
            $end   = Carbon::createFromFormat('H:i:s', $avail->end_time);
            
            $slotStart = $start->format('H:i');
            $slotEnd = $end->format('H:i');
            $duration = $start->diffInMinutes($end);
            $durationLabel = $this->formatDuration($duration);
            
            $isBooked = in_array($slotStart, $bookedSlots);

            $slots[] = [
                'value'  => $slotStart,
                'label'  => Carbon::createFromFormat('H:i', $slotStart)->format('h:i A')
                          . ' – '
                          . Carbon::createFromFormat('H:i', $slotEnd)->format('h:i A'),
                'duration_label' => $durationLabel,
                'duration_minutes' => $duration,
                'booked' => $isBooked,
            ];
        }

        return response()->json(['slots' => $slots]);
    }

    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0 && $mins > 0) {
            return $hours . 'h ' . $mins . 'm';
        } elseif ($hours > 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        } else {
            return $mins . ' minute' . ($mins > 1 ? 's' : '');
        }
    }
}