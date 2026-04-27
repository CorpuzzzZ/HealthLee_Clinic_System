<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        // Today's appointments
        $todaysAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        // Upcoming appointments (excluding today)
        $upcomingAppointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', '>', today())
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        // Availability for next 7 days
        $availabilities = Availability::where('doctor_id', $doctor->id)
            ->whereDate('available_date', '>=', today())
            ->whereDate('available_date', '<=', now()->addDays(7))
            ->orderBy('available_date')
            ->orderBy('start_time')
            ->get();

        // Recent medical records
        $recentMedicalRecords = MedicalRecord::with('patient')
            ->where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Notifications (placeholder until Notification model is built)
        $notifications = collect();

        // Total counts
        $totalAppointments = Appointment::where('doctor_id', $doctor->id)->count();
        $totalToday        = $todaysAppointments->count();
        $totalCompleted    = Appointment::where('doctor_id', $doctor->id)
                                        ->where('status', 'completed')->count();
        $totalPatients     = Appointment::where('doctor_id', $doctor->id)
                                        ->distinct('patient_id')
                                        ->count('patient_id');

        return view('doctor.dashboard', compact(
            'doctor',
            'todaysAppointments',
            'upcomingAppointments',
            'availabilities',
            'recentMedicalRecords',
            'notifications',
            'totalAppointments',
            'totalToday',
            'totalCompleted',
            'totalPatients',
        ));
    }
}