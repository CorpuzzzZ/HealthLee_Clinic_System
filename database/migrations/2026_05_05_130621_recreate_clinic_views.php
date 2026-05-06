<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_appointment_summary');
        DB::statement('DROP VIEW IF EXISTS vw_doctor_performance');
        DB::statement('DROP VIEW IF EXISTS vw_patient_visit_history');

        DB::statement("
            CREATE VIEW vw_appointment_summary AS
            SELECT
                a.id AS appointment_id,
                a.appointment_date, a.appointment_time, a.status, a.notes, a.created_at,
                CONCAT(p.first_name, ' ', p.last_name)         AS patient_name,
                p.gender AS patient_gender, p.blood_type AS patient_blood_type,
                CONCAT('Dr. ', d.first_name, ' ', d.last_name) AS doctor_name,
                d.specialty AS doctor_specialty,
                s.name AS service_name, s.price AS service_price
            FROM appointments a
            JOIN patients  p ON p.id = a.patient_id
            JOIN doctors   d ON d.id = a.doctor_id
            LEFT JOIN services s ON s.id = a.service_id
        ");

        DB::statement("
            CREATE VIEW vw_doctor_performance AS
            SELECT
                d.id AS doctor_id,
                CONCAT('Dr. ', d.first_name, ' ', d.last_name) AS doctor_name,
                d.specialty,
                COUNT(a.id)                 AS total_appointments,
                SUM(a.status = 'completed') AS completed,
                SUM(a.status = 'cancelled') AS cancelled,
                SUM(a.status = 'pending')   AS pending,
                SUM(a.status = 'confirmed') AS confirmed
            FROM doctors d
            LEFT JOIN appointments a ON a.doctor_id = d.id
            GROUP BY d.id, d.first_name, d.last_name, d.specialty
        ");

        DB::statement("
            CREATE VIEW vw_patient_visit_history AS
            SELECT
                p.id AS patient_id,
                CONCAT(p.first_name, ' ', p.last_name) AS patient_name,
                p.gender, p.blood_type,
                p.birthdate,
                TIMESTAMPDIFF(YEAR, p.birthdate, CURDATE()) AS age,
                COUNT(a.id)                 AS total_visits,
                SUM(a.status = 'completed') AS completed_visits,
                MAX(a.appointment_date)     AS last_visit_date
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