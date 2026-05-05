<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Create patient_contacts table ──
        Schema::create('patient_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('contact_number', 20)->nullable();
            $table->timestamps();
        });

        // ── Create patient_addresses table ──
        Schema::create('patient_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->timestamps();
        });

        // ── Migrate existing data before dropping columns ──
        DB::table('patients')->get()->each(function ($patient) {
            DB::table('patient_contacts')->insert([
                'patient_id'     => $patient->id,
                'contact_number' => $patient->contact_number,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('patient_addresses')->insert([
                'patient_id' => $patient->id,
                'street'     => $patient->street,
                'barangay'   => $patient->barangay,
                'city'       => $patient->city,
                'province'   => $patient->province,
                'zip_code'   => $patient->zip_code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // ── Drop moved columns from patients table ──
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'contact_number',
                'street',
                'barangay',
                'city',
                'province',
                'zip_code',
            ]);
        });
    }

    public function down(): void
    {
        // ── Restore columns to patients ──
        Schema::table('patients', function (Blueprint $table) {
            $table->string('contact_number', 20)->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
        });

        // ── Restore data ──
        DB::table('patient_contacts')->get()->each(function ($contact) {
            DB::table('patients')->where('id', $contact->patient_id)->update([
                'contact_number' => $contact->contact_number,
            ]);
        });

        DB::table('patient_addresses')->get()->each(function ($address) {
            DB::table('patients')->where('id', $address->patient_id)->update([
                'street'   => $address->street,
                'barangay' => $address->barangay,
                'city'     => $address->city,
                'province' => $address->province,
                'zip_code' => $address->zip_code,
            ]);
        });

        Schema::dropIfExists('patient_contacts');
        Schema::dropIfExists('patient_addresses');
    }
};