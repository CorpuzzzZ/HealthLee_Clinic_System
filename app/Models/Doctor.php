<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'specialty',
        'gender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    // Contact and address are now accessed through user
    public function getContactNumberAttribute()
    {
        return $this->user->contact?->contact_number;
    }

    public function getFullAddressAttribute()
    {
        return $this->user->full_address;
    }
}