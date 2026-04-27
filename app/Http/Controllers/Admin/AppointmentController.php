<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor'])
                            ->orderBy('appointment_date', 'desc')
                            ->orderBy('appointment_time', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments = $query->paginate(10)->withQueryString();
        $doctors      = Doctor::orderBy('last_name')->get();

        return view('admin.appointments.index', compact('appointments', 'doctors'));
    }

    public function create()
    {
        $patients = Patient::orderBy('last_name')->get();
        $doctors  = Doctor::orderBy('last_name')->get();
        return view('admin.appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id'       => ['required', 'exists:patients,id'],
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'status'           => ['required', 'in:pending,confirmed,cancelled,completed,rescheduled'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $appointment = Appointment::create($request->only([
            'patient_id',
            'doctor_id',
            'appointment_date',
            'appointment_time',
            'status',
            'notes',
        ]));

        // Send notification based on status
        $appointment->load(['patient', 'doctor']);
        $service = new NotificationService();

        if (in_array($appointment->status, ['confirmed', 'pending'])) {
            $service->appointmentConfirmation($appointment);
        } elseif ($appointment->status === 'cancelled') {
            $service->appointmentCancelled($appointment);
        } elseif ($appointment->status === 'rescheduled') {
            $service->appointmentRescheduled($appointment);
        }

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);
        $patients = Patient::orderBy('last_name')->get();
        $doctors  = Doctor::orderBy('last_name')->get();
        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'patient_id'       => ['required', 'exists:patients,id'],
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['required'],
            'status'           => ['required', 'in:pending,confirmed,cancelled,completed,rescheduled'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $appointment->status;

        $appointment->update($request->only([
            'patient_id',
            'doctor_id',
            'appointment_date',
            'appointment_time',
            'status',
            'notes',
        ]));

        // Send notification only if status changed
        $appointment->load(['patient', 'doctor']);
        $service = new NotificationService();

        if ($appointment->status !== $oldStatus) {
            if ($appointment->status === 'confirmed') {
                $service->appointmentConfirmation($appointment);
            } elseif ($appointment->status === 'cancelled') {
                $service->appointmentCancelled($appointment);
            } elseif ($appointment->status === 'rescheduled') {
                $service->appointmentRescheduled($appointment);
            }
        }

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        // Notify before deleting so relationships still exist
        $appointment->load(['patient', 'doctor']);
        (new NotificationService())->appointmentCancelled($appointment);

        $appointment->delete();

        return redirect()->route('admin.appointments.index')
                         ->with('success', 'Appointment cancelled successfully.');
    }
}