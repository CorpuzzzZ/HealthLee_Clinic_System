<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    private function getDoctor()
    {
        return Auth::user()->doctor;
    }

    public function index(Request $request)
    {
        $doctor = $this->getDoctor();

        $query = MedicalRecord::with(['patient', 'appointment'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('created_at', 'desc');

        // Search by patient name
        if ($request->filled('search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name',  'like', '%' . $request->search . '%');
            });
        }

        $records  = $query->paginate(10)->withQueryString();
        $total    = MedicalRecord::where('doctor_id', $doctor->id)->count();

        return view('doctor.medical-records.index', compact('records', 'total', 'doctor'));
    }

    public function create()
{
    $doctor = $this->getDoctor();

    $appointments = Appointment::with(['patient', 'service']) // ← add 'service'
        ->where('doctor_id', $doctor->id)
        ->where('status', 'completed')
        ->whereDoesntHave('medicalRecord')
        ->orderBy('appointment_date', 'desc')
        ->get();

    return view('doctor.medical-records.create', compact('appointments', 'doctor'));
}

    public function store(Request $request)
    {
        $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'diagnosis'      => ['required', 'string'],
            'treatment'      => ['required', 'string'],
            'notes'          => ['nullable', 'string'],
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);

        MedicalRecord::create([
            'patient_id'     => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'doctor_id'      => $this->getDoctor()->id,
            'diagnosis'      => $request->diagnosis,
            'treatment'      => $request->treatment,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('doctor.medical-records.index')
                         ->with('success', 'Medical record added successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        abort_if($medicalRecord->doctor_id !== $this->getDoctor()->id, 403);
        $medicalRecord->load(['patient', 'doctor', 'appointment']);
        return view('doctor.medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        abort_if($medicalRecord->doctor_id !== $this->getDoctor()->id, 403);
        $medicalRecord->load(['patient', 'appointment']);
        return view('doctor.medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        abort_if($medicalRecord->doctor_id !== $this->getDoctor()->id, 403);

        $request->validate([
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'notes'     => ['nullable', 'string'],
        ]);

        $medicalRecord->update([
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'notes'     => $request->notes,
        ]);

        return redirect()->route('doctor.medical-records.index')
                         ->with('success', 'Medical record updated successfully.');
    }
}