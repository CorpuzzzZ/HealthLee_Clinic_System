<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorAddress extends Model
{
    protected $fillable = [
        'doctor_id', 'street', 'barangay',
        'city', 'province', 'zip_code',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}