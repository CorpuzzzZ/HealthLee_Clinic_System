<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    protected $fillable = ['doctor_id', 'name', 'description', 'price'];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}