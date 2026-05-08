<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthdate',
        'height',
        'weight',
        'blood_type',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // REMOVE THIS - patient_id doesn't exist in medical_records table anymore
    // public function medicalRecords(): HasMany
    // {
    //     return $this->hasMany(MedicalRecord::class);
    // }

    // REPLACE WITH THIS - Get medical records through appointments
    public function medicalRecords(): HasManyThrough
    {
        return $this->hasManyThrough(
            MedicalRecord::class,
            Appointment::class,
            'patient_id',      // Foreign key on appointments table
            'appointment_id',  // Foreign key on medical_records table
            'id',              // Local key on patients table
            'id'               // Local key on appointments table
        );
    }

    // Contact number accessor (kept as is)
    public function getContactNumberAttribute()
    {
        return $this->user->contact->contact_number ?? null;
    }
}