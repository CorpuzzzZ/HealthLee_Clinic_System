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

                    while ($start->copy()->addHour()->lte($end)) {
                        if (!in_array($start->format('H:i'), $bookedTimes)) {
                            $hasAvailableSlot = true;
                            break 2;
                        }
                        $start->addHour();
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

        // ── Appointment duration is 1 hour ──
        $startTime = Carbon::parse($request->appointment_time);
        $endTime   = $startTime->copy()->addHour();

        // ── Check doctor has availability on the selected date ──
        $availabilityExists = Availability::where('doctor_id', $request->doctor_id)
            ->get()
            ->filter(fn($a) => Carbon::parse($a->available_date)->format('Y-m-d') === $request->appointment_date)
            ->isNotEmpty();

        if (!$availabilityExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_date' => 'The doctor has no availability on the selected date. Please choose a different date.',
                ]);
        }

        // ── Check the full 1-hour slot fits within the doctor's availability ──
        $slotFits = Availability::where('doctor_id', $request->doctor_id)
            ->get()
            ->filter(fn($a) => Carbon::parse($a->available_date)->format('Y-m-d') === $request->appointment_date)
            ->filter(fn($a) => $a->start_time <= $startTime->format('H:i:s')
                            && $a->end_time   >= $endTime->format('H:i:s'))
            ->isNotEmpty();

        if (!$slotFits) {
            return back()
                ->withInput()
                ->withErrors([
                    'appointment_time' => 'The selected time slot (1 hour duration) does not fit within the doctor\'s available hours.',
                ]);
        }

        // ── Check no overlapping appointment exists for this doctor ──
        $overlapping = Appointment::where('doctor_id', $request->doctor_id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('appointment_time', '<', $endTime->format('H:i:s'))
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
            'type'    => 'general',
            'status'  => 'unread',
        ]);

        // ── Notify doctor ──
        \App\Models\Notification::create([
            'user_id' => $appointment->doctor->user_id,
            'message' => "New appointment request from {$appointment->patient->first_name} {$appointment->patient->last_name} on " .
                         $appointment->appointment_date->format('d M Y') . " at " .
                         $startTime->format('h:i A') . " – " . $endTime->format('h:i A') .
                         ". Please confirm or update the status.",
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

    public function slots(Request $request)
    {
        $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'date'      => ['required', 'date'],
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);
        $date   = $request->date;

        // Fetch all and filter in PHP using Carbon to handle timezone correctly
        $availabilities = Availability::where('doctor_id', $doctor->id)
            ->get()
            ->filter(fn($a) => Carbon::parse($a->available_date)->format('Y-m-d') === $date);

        if ($availabilities->isEmpty()) {
            return response()->json(['slots' => []]);
        }

        // Get already booked times on that date (excluding cancelled)
        $bookedTimes = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->toArray();

        $slots = [];

        foreach ($availabilities as $avail) {
            $start = Carbon::createFromFormat('H:i:s', $avail->start_time);
            $end   = Carbon::createFromFormat('H:i:s', $avail->end_time);

            while ($start->copy()->addHour()->lte($end)) {
                $slotStart = $start->format('H:i');
                $slotEnd   = $start->copy()->addHour()->format('H:i');

                $slots[] = [
                    'value'  => $slotStart,
                    'label'  => Carbon::createFromFormat('H:i', $slotStart)->format('h:i A')
                              . ' – '
                              . Carbon::createFromFormat('H:i', $slotEnd)->format('h:i A'),
                    'booked' => in_array($slotStart, $bookedTimes),
                ];

                $start->addHour();
            }
        }

        return response()->json(['slots' => $slots]);
    }
}