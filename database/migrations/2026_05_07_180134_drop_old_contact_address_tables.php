<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('doctor_contacts');
        Schema::dropIfExists('doctor_addresses');
        Schema::dropIfExists('patient_contacts');
        Schema::dropIfExists('patient_addresses');
        
        // Also drop admin_contacts and admin_addresses if they exist
        Schema::dropIfExists('admin_contacts');
        Schema::dropIfExists('admin_addresses');
    }

    public function down(): void
    {
        // Recreate old tables if needed (optional)
        Schema::create('doctor_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->string('contact_number', 20)->nullable();
            $table->timestamps();
        });
        
        // ... add other tables similarly
    }
};