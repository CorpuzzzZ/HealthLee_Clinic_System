<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAddress extends Model
{
    protected $fillable = [
        'admin_id', 'street', 'barangay',
        'city', 'province', 'zip_code',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}