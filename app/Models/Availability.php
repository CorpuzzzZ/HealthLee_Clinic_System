<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    protected $fillable = [
        'doctor_id',
        'available_date',
        'start_time',
        'end_time',
    ];

    protected function casts(): array
    {
        return [
            'available_date' => 'date',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}