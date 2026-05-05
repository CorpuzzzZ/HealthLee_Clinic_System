<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── Create admin_contacts table ──
        Schema::create('admin_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->string('contact_number', 20)->nullable();
            $table->timestamps();
        });

        // ── Create admin_addresses table ──
        Schema::create('admin_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->onDelete('cascade');
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->timestamps();
        });

        // ── Migrate existing data before dropping columns ──
        DB::table('admins')->get()->each(function ($admin) {
            DB::table('admin_contacts')->insert([
                'admin_id'       => $admin->id,
                'contact_number' => $admin->contact_number,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('admin_addresses')->insert([
                'admin_id'   => $admin->id,
                'street'     => $admin->street,
                'barangay'   => $admin->barangay,
                'city'       => $admin->city,
                'province'   => $admin->province,
                'zip_code'   => $admin->zip_code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // ── Drop moved columns from admins table ──
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn([
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
        Schema::table('admins', function (Blueprint $table) {
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('contact_number', 20)->nullable();
            $table->string('street')->nullable();
            $table->string('barangay')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('zip_code', 10)->nullable();
        });

        DB::table('admin_contacts')->get()->each(function ($contact) {
            DB::table('admins')->where('id', $contact->admin_id)->update([
                'contact_number' => $contact->contact_number,
            ]);
        });

        DB::table('admin_addresses')->get()->each(function ($address) {
            DB::table('admins')->where('id', $address->admin_id)->update([
                'street'   => $address->street,
                'barangay' => $address->barangay,
                'city'     => $address->city,
                'province' => $address->province,
                'zip_code' => $address->zip_code,
            ]);
        });

        Schema::dropIfExists('admin_contacts');
        Schema::dropIfExists('admin_addresses');
    }
};