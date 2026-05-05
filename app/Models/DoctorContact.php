<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorContact extends Model
{
    protected $fillable = ['doctor_id', 'contact_number'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}