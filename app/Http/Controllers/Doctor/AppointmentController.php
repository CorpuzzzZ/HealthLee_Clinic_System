<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    private function getDoctor()
    {
        return Auth::user()->doctor;
    }

    public function index(Request $request)
    {
        $doctor = $this->getDoctor();

        $query = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->orderBy('appointment_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        $appointments   = $query->paginate(10)->withQueryString();
        $totalToday     = Appointment::where('doctor_id', $doctor->id)
                                     ->whereDate('appointment_date', today())->count();
        $totalUpcoming  = Appointment::where('doctor_id', $doctor->id)
                                     ->whereDate('appointment_date', '>', today())
                                     ->whereIn('status', ['pending', 'confirmed'])->count();
        $totalCompleted = Appointment::where('doctor_id', $doctor->id)
                                     ->where('status', 'completed')->count();

        return view('doctor.appointments.index', compact(
            'appointments',
            'totalToday',
            'totalUpcoming',
            'totalCompleted',
        ));
    }

    public function show(Appointment $appointment)
    {
        abort_if($appointment->doctor_id !== $this->getDoctor()->id, 403);
        $appointment->load(['patient', 'medicalRecord']);
        return view('doctor.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        abort_if($appointment->doctor_id !== $this->getDoctor()->id, 403);

        $request->validate([
            'status' => ['required', 'in:confirmed,completed,cancelled,rescheduled'],
        ]);

        $oldStatus = $appointment->status;
        $appointment->load(['patient', 'doctor']);
        $appointment->update(['status' => $request->status]);

        $service = new NotificationService();

        if ($request->status !== $oldStatus) {
    if ($request->status === 'confirmed') {
        $service->appointmentConfirmation($appointment); // ← only fires when doctor confirms
    } elseif ($request->status === 'cancelled') {
        $service->appointmentCancelled($appointment);
    } elseif ($request->status === 'rescheduled') {
        $service->appointmentRescheduled($appointment);
    }
}

        return redirect()->route('doctor.appointments.show', $appointment)
                         ->with('success', 'Appointment status updated.');
    }
}