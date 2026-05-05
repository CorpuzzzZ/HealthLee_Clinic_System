<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('age');
            $table->decimal('height', 5, 2)->nullable()->after('birthdate')
                  ->comment('in centimeters');
            $table->decimal('weight', 5, 2)->nullable()->after('height')
                  ->comment('in kilograms');
            $table->enum('blood_type', [
                'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
            ])->nullable()->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['birthdate', 'height', 'weight', 'blood_type']);
        });
    }
};