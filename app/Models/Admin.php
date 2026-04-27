<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'middle_name', 'last_name',
        'gender', 'age', 'contact_number',
        'street', 'barangay', 'city', 'province', 'zip_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}