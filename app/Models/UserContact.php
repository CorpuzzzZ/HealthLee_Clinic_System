<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContact extends Model
{
    protected $fillable = ['user_id', 'contact_number'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}