<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 
        'street', 
        'barangay', 
        'city', 
        'province', 
        'zip_code'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}