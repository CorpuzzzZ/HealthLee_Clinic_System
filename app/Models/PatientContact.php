<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientContact extends Model
{
    protected $fillable = ['patient_id', 'contact_number'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}