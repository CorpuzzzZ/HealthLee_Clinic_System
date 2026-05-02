<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── TRIGGER 1: Auto-set status to 'pending' on every new appointment ──
        DB::unprepared("
            CREATE TRIGGER trg_set_status_pending
            BEFORE INSERT ON appointments
            FOR EACH ROW
            BEGIN
                SET NEW.status = 'pending';
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_set_status_pending');
    }
};