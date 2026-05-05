<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminContact extends Model
{
    protected $fillable = ['admin_id', 'contact_number'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}