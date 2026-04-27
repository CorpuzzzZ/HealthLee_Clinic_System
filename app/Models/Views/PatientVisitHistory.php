<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class PatientVisitHistory extends Model
{
    protected $table    = 'vw_patient_visit_history';
    protected $primaryKey = 'patient_id';
    public $timestamps  = false;
    public $incrementing = false;
}