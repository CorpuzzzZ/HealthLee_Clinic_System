<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class DoctorPerformance extends Model
{
    protected $table    = 'vw_doctor_performance';
    protected $primaryKey = 'doctor_id';
    public $timestamps  = false;
    public $incrementing = false;
}