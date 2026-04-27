<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admin;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers        = User::count();
        $totalPatients     = Patient::count();
        $totalDoctors      = Doctor::count();
        $totalAdmins       = Admin::count();
        $totalAppointments = Appointment::count(); // Placeholder until Appointment model is ready

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalPatients',
            'totalDoctors',
            'totalAdmins',
            'totalAppointments',
            'recentUsers',
        ));
    }
}