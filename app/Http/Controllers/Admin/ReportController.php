<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Views\AppointmentSummary;
use App\Models\Views\DoctorPerformance;
use App\Models\Views\PatientVisitHistory;
use App\Models\Appointment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all');

        // ── Appointment Summary (from vw_appointment_summary) ──
        $appointmentQuery = AppointmentSummary::query();

        if ($period === 'daily') {
            $appointmentQuery->whereDate('appointment_date', today());
        } elseif ($period === 'weekly') {
            $appointmentQuery->whereBetween('appointment_date', [
                now()->startOfWeek(), now()->endOfWeek()
            ]);
        } elseif ($period === 'monthly') {
            $appointmentQuery->whereMonth('appointment_date', now()->month)
                             ->whereYear('appointment_date',  now()->year);
        }

        $appointments = $appointmentQuery->orderBy('appointment_date', 'desc')
                                         ->paginate(10)
                                         ->withQueryString();

        // ── Appointment Status Counts ──
        $totalAppointments  = AppointmentSummary::count();
        $totalCompleted     = AppointmentSummary::where('status', 'completed')->count();
        $totalPending       = AppointmentSummary::where('status', 'pending')->count();
        $totalCancelled     = AppointmentSummary::where('status', 'cancelled')->count();
        $totalConfirmed     = AppointmentSummary::where('status', 'confirmed')->count();
        $totalRescheduled   = AppointmentSummary::where('status', 'rescheduled')->count();

        // ── Doctor Performance (from vw_doctor_performance) ──
        $doctorPerformance = DoctorPerformance::orderBy('total_appointments', 'desc')
                                              ->get();

        // ── Patient Visit History (from vw_patient_visit_history) ──
        $patientVisits = PatientVisitHistory::orderBy('total_visits', 'desc')
                                            ->take(10)
                                            ->get();

        return view('admin.reports.index', compact(
            'appointments',
            'period',
            'totalAppointments',
            'totalCompleted',
            'totalPending',
            'totalCancelled',
            'totalConfirmed',
            'totalRescheduled',
            'doctorPerformance',
            'patientVisits',
        ));
    }
}