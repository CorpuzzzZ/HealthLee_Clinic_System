<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'specialty',
        'gender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): HasOne
    {
        return $this->hasOne(DoctorContact::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(DoctorAddress::class);
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
}