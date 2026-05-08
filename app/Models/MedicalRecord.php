<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $fillable = [
        'appointment_id',
        'diagnosis',
        'treatment',
        'notes',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
    
    // Access patient through appointment
    public function getPatientAttribute()
    {
        return $this->appointment->patient;
    }
    
    // Access doctor through appointment
    public function getDoctorAttribute()
    {
        return $this->appointment->doctor;
    }
}