<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing trigger if it exists
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_double_booking');
        
        // ── Simple trigger: prevents exact duplicate time slots only ──
        // This acts as a safety net for race conditions
        // The application-level validation handles overlap detection
        DB::unprepared("
            CREATE TRIGGER trg_prevent_double_booking
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE duplicate_count INT;
                
                SELECT COUNT(*) INTO duplicate_count
                FROM appointments
                WHERE doctor_id = NEW.doctor_id
                  AND appointment_date = NEW.appointment_date
                  AND appointment_time = NEW.appointment_time
                  AND status != 'cancelled';
                
                IF duplicate_count > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'This exact time slot is already booked. Please choose a different time.';
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_double_booking');
    }
};