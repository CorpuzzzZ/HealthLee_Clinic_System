<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── TRIGGER 3: Auto-complete past confirmed appointments ──
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

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_auto_complete_appointment');
    }
};