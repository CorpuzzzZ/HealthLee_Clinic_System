<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with('user')->orderBy('last_name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('blood_type')) {
            $query->where('blood_type', $request->blood_type);
        }

        $patients = $query->paginate(10)->withQueryString();

        return view('admin.patient-records.index', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $patient->load(['user', 'appointments.doctor', 'medicalRecords.doctor']);
        return view('admin.patient-records.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patient-records.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
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

        return redirect()->route('admin.patient-records.show', $patient)
                         ->with('success', 'Patient record updated successfully.');
    }
}