<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Create doctor_contacts table ──
        Schema::create('doctor_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->string('contact_number', 20)->nullable();
            $table->timestamps();
        });

        // ── Create doctor_addresses table ──
        Schema::create('doctor_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->onDelete('cascade');
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->timestamps();
        });

        // ── Migrate existing data before dropping columns ──
        DB::table('doctors')->get()->each(function ($doctor) {
            DB::table('doctor_contacts')->insert([
                'doctor_id'      => $doctor->id,
                'contact_number' => $doctor->contact_number,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('doctor_addresses')->insert([
                'doctor_id'  => $doctor->id,
                'street'     => $doctor->street,
                'barangay'   => $doctor->barangay,
                'city'       => $doctor->city,
                'province'   => $doctor->province,
                'zip_code'   => $doctor->zip_code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // ── Drop moved columns from doctors table ──
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'age',
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
        // ── Restore columns to doctors ──
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->after('first_name');
            $table->unsignedTinyInteger('age')->nullable()->after('gender');
            $table->string('contact_number', 20)->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
        });

        // ── Restore data ──
        DB::table('doctor_contacts')->get()->each(function ($contact) {
            DB::table('doctors')->where('id', $contact->doctor_id)->update([
                'contact_number' => $contact->contact_number,
            ]);
        });

        DB::table('doctor_addresses')->get()->each(function ($address) {
            DB::table('doctors')->where('id', $address->doctor_id)->update([
                'street'   => $address->street,
                'barangay' => $address->barangay,
                'city'     => $address->city,
                'province' => $address->province,
                'zip_code' => $address->zip_code,
            ]);
        });

        Schema::dropIfExists('doctor_contacts');
        Schema::dropIfExists('doctor_addresses');
    }
};