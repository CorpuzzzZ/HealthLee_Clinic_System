<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class AppointmentSummary extends Model
{
    // Points to the database view
    protected $table    = 'vw_appointment_summary';
    protected $primaryKey = 'appointment_id';
    public $timestamps  = false;

    // Read-only — no inserts or updates
    public $incrementing = false;
}