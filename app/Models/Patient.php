<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'age',
        'contact_number',
        'street',
        'barangay',
        'city',
        'province',
        'zip_code',
    ];

    public function appointments(): HasMany
{
    return $this->hasMany(Appointment::class);
}

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecords(): HasMany
{
    return $this->hasMany(MedicalRecord::class);
}
}