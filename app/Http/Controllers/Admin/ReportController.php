<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all');

        // ── Appointment Summary Query ──
        $appointmentSql = "SELECT * FROM vw_appointment_summary ORDER BY appointment_date DESC";
        
        if ($period === 'daily') {
            $appointmentSql = "SELECT * FROM vw_appointment_summary 
                               WHERE DATE(appointment_date) = CURDATE() 
                               ORDER BY appointment_date DESC";
        } elseif ($period === 'weekly') {
            $appointmentSql = "SELECT * FROM vw_appointment_summary 
                               WHERE YEARWEEK(appointment_date, 1) = YEARWEEK(CURDATE(), 1) 
                               ORDER BY appointment_date DESC";
        } elseif ($period === 'monthly') {
            $appointmentSql = "SELECT * FROM vw_appointment_summary 
                               WHERE MONTH(appointment_date) = MONTH(CURDATE()) 
                               AND YEAR(appointment_date) = YEAR(CURDATE())
                               ORDER BY appointment_date DESC";
        }
        
        $appointments = DB::select($appointmentSql);
        
        // Convert to pagination manually if needed (or use simplePaginate)
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $appointmentsPaginated = array_slice($appointments, $offset, $perPage);
        
        // Create a custom paginator
        $appointments = new \Illuminate\Pagination\LengthAwarePaginator(
            $appointmentsPaginated,
            count($appointments),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // ── Appointment Status Counts (Raw SQL) ──
        $totalAppointments = DB::select("SELECT COUNT(*) as total FROM vw_appointment_summary")[0]->total;
        $totalCompleted = DB::select("SELECT COUNT(*) as total FROM vw_appointment_summary WHERE status = 'completed'")[0]->total;
        $totalPending = DB::select("SELECT COUNT(*) as total FROM vw_appointment_summary WHERE status = 'pending'")[0]->total;
        $totalCancelled = DB::select("SELECT COUNT(*) as total FROM vw_appointment_summary WHERE status = 'cancelled'")[0]->total;
        $totalConfirmed = DB::select("SELECT COUNT(*) as total FROM vw_appointment_summary WHERE status = 'confirmed'")[0]->total;
        $totalRescheduled = DB::select("SELECT COUNT(*) as total FROM vw_appointment_summary WHERE status = 'rescheduled'")[0]->total;

        // ── Doctor Performance (Raw SQL) ──
        $doctorPerformance = DB::select("SELECT * FROM vw_doctor_performance ORDER BY total_appointments DESC");

        // ── Patient Visit History (Raw SQL) ──
        $patientVisits = DB::select("SELECT * FROM vw_patient_visit_history ORDER BY total_visits DESC");

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