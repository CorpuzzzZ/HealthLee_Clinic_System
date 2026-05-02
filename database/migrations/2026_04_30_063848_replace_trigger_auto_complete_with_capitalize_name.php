<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Drop old trigger ──
        DB::unprepared('DROP TRIGGER IF EXISTS trg_auto_complete_appointment');

        // ── TRIGGER 3: Auto-capitalize patient first and last name on insert ──
        DB::unprepared("
            CREATE TRIGGER trg_capitalize_patient_name
            BEFORE INSERT ON patients
            FOR EACH ROW
            BEGIN
                SET NEW.first_name = CONCAT(
                    UPPER(SUBSTRING(NEW.first_name, 1, 1)),
                    LOWER(SUBSTRING(NEW.first_name, 2))
                );
                SET NEW.last_name = CONCAT(
                    UPPER(SUBSTRING(NEW.last_name, 1, 1)),
                    LOWER(SUBSTRING(NEW.last_name, 2))
                );
                SET NEW.middle_name = IF(
                    NEW.middle_name IS NOT NULL,
                    CONCAT(
                        UPPER(SUBSTRING(NEW.middle_name, 1, 1)),
                        LOWER(SUBSTRING(NEW.middle_name, 2))
                    ),
                    NULL
                );
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_capitalize_patient_name');

        // ── Restore old trigger if rolled back ──
        DB::unprepared("
            CREATE TRIGGER trg_auto_complete_appointment
            BEFORE UPDATE ON appointments
            FOR EACH ROW
            BEGIN
                IF NEW.status = 'confirmed'
                   AND NEW.appointment_date < CURDATE() THEN
                    SET NEW.status = 'completed';
                END IF;
            END
        ");
    }
};