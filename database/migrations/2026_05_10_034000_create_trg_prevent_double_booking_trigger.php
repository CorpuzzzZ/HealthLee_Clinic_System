<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the trigger if it already exists
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_double_booking');
        
        // ── TRIGGER: Prevent double booking ──
        DB::unprepared("
            CREATE TRIGGER trg_prevent_double_booking
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE booking_count INT;

                SELECT COUNT(*) INTO booking_count
                FROM appointments
                WHERE doctor_id        = NEW.doctor_id
                  AND appointment_date = NEW.appointment_date
                  AND appointment_time = NEW.appointment_time
                  AND status          != 'cancelled';

                IF booking_count > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'This time slot is already booked. Please choose a different time.';
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_double_booking');
    }
};