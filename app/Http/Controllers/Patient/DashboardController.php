<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;

        // Upcoming appointments
        $upcomingAppointments = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->whereDate('appointment_date', '>=', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Appointment history
        $appointmentHistory = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['completed', 'cancelled', 'rescheduled'])
            ->orderBy('appointment_date', 'desc')
            ->take(5)
            ->get();

        // Fetch actual notifications for the logged-in user
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Total counts
        $totalAppointments = Appointment::where('patient_id', $patient->id)->count();
        $totalCompleted    = Appointment::where('patient_id', $patient->id)
                                        ->where('status', 'completed')->count();
        $totalCancelled    = Appointment::where('patient_id', $patient->id)
                                        ->where('status', 'cancelled')->count();
        $totalUpcoming     = Appointment::where('patient_id', $patient->id)
                                        ->whereDate('appointment_date', '>=', now())
                                        ->whereIn('status', ['pending', 'confirmed'])
                                        ->count();

        return view('patient.dashboard', compact(
            'patient',
            'upcomingAppointments',
            'appointmentHistory',
            'notifications',
            'totalAppointments',
            'totalCompleted',
            'totalCancelled',
            'totalUpcoming',
        ));
    }
}