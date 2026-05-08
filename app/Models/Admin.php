<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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