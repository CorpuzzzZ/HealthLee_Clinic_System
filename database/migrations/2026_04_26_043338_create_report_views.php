<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── VIEW 1: Appointment Summary View ──────────────────
        // Shows all appointments with patient and doctor full details
        DB::statement("
            CREATE VIEW vw_appointment_summary AS
            SELECT
                a.id                                        AS appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                a.notes,
                a.created_at                               AS booked_at,

                -- Patient Info
                p.id                                       AS patient_id,
                CONCAT(p.first_name, ' ', p.last_name)     AS patient_name,
                p.gender                                   AS patient_gender,
                p.age                                      AS patient_age,
                p.contact_number                           AS patient_contact,

                -- Doctor Info
                d.id                                       AS doctor_id,
                CONCAT('Dr. ', d.first_name, ' ', d.last_name) AS doctor_name,
                d.specialty                                AS doctor_specialty
            FROM appointments a
            JOIN patients  p ON a.patient_id = p.id
            JOIN doctors   d ON a.doctor_id  = d.id
        ");

        // ── VIEW 2: Doctor Performance View ───────────────────
        // Shows each doctor's appointment stats
        DB::statement("
            CREATE VIEW vw_doctor_performance AS
            SELECT
                d.id                                            AS doctor_id,
                CONCAT('Dr. ', d.first_name, ' ', d.last_name) AS doctor_name,
                d.specialty,

                COUNT(a.id)                                     AS total_appointments,
                SUM(CASE WHEN a.status = 'completed'   THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN a.status = 'pending'     THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN a.status = 'confirmed'   THEN 1 ELSE 0 END) AS confirmed,
                SUM(CASE WHEN a.status = 'cancelled'   THEN 1 ELSE 0 END) AS cancelled,
                SUM(CASE WHEN a.status = 'rescheduled' THEN 1 ELSE 0 END) AS rescheduled,

                COUNT(DISTINCT a.patient_id)                    AS unique_patients
            FROM doctors d
            LEFT JOIN appointments a ON d.id = a.doctor_id
            GROUP BY d.id, d.first_name, d.last_name, d.specialty
        ");

        // ── VIEW 3: Patient Visit History View ────────────────
        // Shows each patient's visit/appointment history
        DB::statement("
            CREATE VIEW vw_patient_visit_history AS
            SELECT
                p.id                                        AS patient_id,
                CONCAT(p.first_name, ' ', p.last_name)     AS patient_name,
                p.gender,
                p.age,

                COUNT(a.id)                                 AS total_visits,
                SUM(CASE WHEN a.status = 'completed'   THEN 1 ELSE 0 END) AS completed_visits,
                SUM(CASE WHEN a.status = 'cancelled'   THEN 1 ELSE 0 END) AS cancelled_visits,
                SUM(CASE WHEN a.status = 'pending'     THEN 1 ELSE 0 END) AS pending_visits,

                MAX(a.appointment_date)                     AS last_visit_date,
                MIN(a.appointment_date)                     AS first_visit_date
            FROM patients p
            LEFT JOIN appointments a ON p.id = a.patient_id
            GROUP BY p.id, p.first_name, p.last_name, p.gender, p.age
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_appointment_summary');
        DB::statement('DROP VIEW IF EXISTS vw_doctor_performance');
        DB::statement('DROP VIEW IF EXISTS vw_patient_visit_history');
    }
};