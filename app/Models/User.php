<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // --- Role helpers ---
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    // --- Relationships ---
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    // --- Contact & Address Relationships ---
    public function contact(): HasOne
    {
        return $this->hasOne(UserContact::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(UserAddress::class);
    }

    // --- Helper Accessors ---
    public function getContactNumberAttribute(): ?string
    {
        return $this->contact?->contact_number;
    }

    public function getFullAddressAttribute(): ?string
    {
        $addr = $this->address;
        if (!$addr) {
            return null;
        }
        
        return implode(', ', array_filter([
            $addr->street,
            $addr->barangay,
            $addr->city,
            $addr->province,
            $addr->zip_code
        ]));
    }
}