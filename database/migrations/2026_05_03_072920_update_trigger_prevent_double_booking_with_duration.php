<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old trigger
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_double_booking');

        // New overlap-aware trigger (1 hour duration)
        DB::unprepared("
            CREATE TRIGGER trg_prevent_double_booking
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                DECLARE overlap_count INT;

                SELECT COUNT(*) INTO overlap_count
                FROM appointments
                WHERE doctor_id        = NEW.doctor_id
                  AND appointment_date = NEW.appointment_date
                  AND status          != 'cancelled'
                  AND appointment_time < ADDTIME(NEW.appointment_time, '01:00:00')
                  AND ADDTIME(appointment_time, '01:00:00') > NEW.appointment_time;

                IF overlap_count > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'This time slot overlaps with an existing appointment. Each appointment is 1 hour.';
                END IF;
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_prevent_double_booking');

        // Restore old trigger
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
                    SET MESSAGE_TEXT = 'This time slot is already booked for the selected doctor.';
                END IF;
            END
        ");
    }
};