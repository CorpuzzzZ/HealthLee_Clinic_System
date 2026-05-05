<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns for reschedule
        Schema::table('appointments', function (Blueprint $table) {
            $table->text('reschedule_notes')->nullable()->after('notes');
            $table->date('proposed_date')->nullable()->after('reschedule_notes');
            $table->time('proposed_time')->nullable()->after('proposed_date');
        });
        
        // Update status enum to include reschedule_requested
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'rescheduled', 'reschedule_requested') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['reschedule_notes', 'proposed_date', 'proposed_time']);
        });
        
        DB::statement("ALTER TABLE appointments MODIFY COLUMN status ENUM('pending', 'confirmed', 'cancelled', 'completed', 'rescheduled') NOT NULL DEFAULT 'pending'");
    }
};