<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing views if they exist
        DB::statement('DROP VIEW IF EXISTS vw_appointment_summary');
        DB::statement('DROP VIEW IF EXISTS vw_doctor_performance');
        DB::statement('DROP VIEW IF EXISTS vw_patient_visit_history');

        // ── VIEW 1: Appointment Summary (excluding IDs) ──
        DB::statement("
            CREATE VIEW vw_appointment_summary AS
            SELECT
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.notes,
                a.created_at AS booked_at,
                CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                p.gender AS patient_gender,
                p.blood_type AS patient_blood_type,
                CONCAT('Dr. ', d.first_name, ' ', d.last_name) AS doctor_name,
                d.specialty AS doctor_specialty,
                s.name AS service_name,
                s.price AS service_price
            FROM appointments a
            JOIN patients p ON p.id = a.patient_id
            JOIN doctors d ON d.id = a.doctor_id
            LEFT JOIN services s ON s.id = a.service_id
        ");

        // ── VIEW 2: Doctor Performance (excluding IDs) ──
        DB::statement("
            CREATE VIEW vw_doctor_performance AS
            SELECT
                CONCAT('Dr. ', d.first_name, ' ', d.last_name) AS doctor_name,
                d.specialty,
                COUNT(a.id) AS total_appointments,
                SUM(a.status = 'completed') AS completed,
                SUM(a.status = 'cancelled') AS cancelled,
                SUM(a.status = 'pending') AS pending,
                SUM(a.status = 'confirmed') AS confirmed,
                SUM(a.status = 'rescheduled') AS rescheduled,
                COUNT(DISTINCT a.patient_id) AS unique_patients
            FROM doctors d
            LEFT JOIN appointments a ON a.doctor_id = d.id
            GROUP BY d.id, d.first_name, d.last_name, d.specialty
        ");

        // ── VIEW 3: Patient Visit History (excluding IDs) ──
        DB::statement("
            CREATE VIEW vw_patient_visit_history AS
            SELECT
                CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                p.gender,
                p.blood_type,
                TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE()) AS age,
                COUNT(a.id) AS total_visits,
                SUM(a.status = 'completed') AS completed_visits,
                SUM(a.status = 'cancelled') AS cancelled_visits,
                SUM(a.status = 'pending') AS pending_visits,
                MAX(a.appointment_date) AS last_visit_date,
                MIN(a.appointment_date) AS first_visit_date
            FROM patients p
            LEFT JOIN appointments a ON a.patient_id = p.id
            GROUP BY p.id, p.first_name, p.last_name, p.gender, p.blood_type, p.birthdate
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_appointment_summary');
        DB::statement('DROP VIEW IF EXISTS vw_doctor_performance');
        DB::statement('DROP VIEW IF EXISTS vw_patient_visit_history');
    }
};