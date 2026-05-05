<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientRecordController extends Controller
{
    private function getDoctor()
    {
        return Auth::user()->doctor;
    }

    public function index(Request $request)
    {
        $doctor = $this->getDoctor();

        // Only show patients who have had appointments with this doctor
        $query = Patient::with('user')
            ->whereHas('appointments', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })
            ->orderBy('last_name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%');
            });
        }

        $patients = $query->paginate(10)->withQueryString();

        return view('doctor.patient-records.index', compact('patients', 'doctor'));
    }

    public function show(Patient $patient)
    {
        $doctor = $this->getDoctor();

        // Only allow viewing patients who have had appointments with this doctor
        abort_unless(
            $patient->appointments()->where('doctor_id', $doctor->id)->exists(),
            403
        );

        $patient->load(['appointments' => function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id)->orderBy('appointment_date', 'desc');
        }, 'medicalRecords' => function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id)->orderBy('created_at', 'desc');
        }]);

        return view('doctor.patient-records.show', compact('patient', 'doctor'));
    }

    public function edit(Patient $patient)
    {
        $doctor = $this->getDoctor();

        abort_unless(
            $patient->appointments()->where('doctor_id', $doctor->id)->exists(),
            403
        );

        return view('doctor.patient-records.edit', compact('patient', 'doctor'));
    }

    public function update(Request $request, Patient $patient)
    {
        $doctor = $this->getDoctor();

        abort_unless(
            $patient->appointments()->where('doctor_id', $doctor->id)->exists(),
            403
        );

        $request->validate([
            'birthdate'  => ['nullable', 'date', 'before:today'],
            'height'     => ['nullable', 'numeric', 'min:1', 'max:300'],
            'weight'     => ['nullable', 'numeric', 'min:1', 'max:700'],
            'blood_type' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
        ]);

        $patient->update([
            'birthdate'  => $request->birthdate,
            'height'     => $request->height,
            'weight'     => $request->weight,
            'blood_type' => $request->blood_type,
        ]);

        return redirect()->route('doctor.patient-records.show', $patient)
                         ->with('success', 'Patient record updated successfully.');
    }
}