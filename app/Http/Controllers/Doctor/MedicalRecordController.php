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

        $query = MedicalRecord::with(['appointment.patient', 'appointment'])
            ->whereHas('appointment', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })
            ->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->whereHas('appointment.patient', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        $records = $query->paginate(10)->withQueryString();
        $total = MedicalRecord::whereHas('appointment', function ($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        })->count();

        return view('doctor.medical-records.index', compact('records', 'total', 'doctor'));
    }

    public function create(Request $request)
    {
        $doctor = $this->getDoctor();

        $appointments = Appointment::with(['patient', 'service'])
            ->where('doctor_id', $doctor->id)
            ->where('status', 'completed')
            ->whereDoesntHave('medicalRecord')
            ->orderBy('appointment_date', 'desc')
            ->get();

        $preselectedAppointment = null;
        if ($request->filled('appointment_id')) {
            $preselectedAppointment = $appointments
                ->firstWhere('id', (int) $request->appointment_id);
        }

        return view('doctor.medical-records.create', compact(
            'appointments',
            'doctor',
            'preselectedAppointment',
        ));
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
        
        abort_if($appointment->doctor_id !== $this->getDoctor()->id, 403);

        MedicalRecord::create([
            'appointment_id' => $appointment->id,
            'diagnosis'      => $request->diagnosis,
            'treatment'      => $request->treatment,
            'notes'          => $request->notes,
        ]);

        return redirect()->route('doctor.medical-records.index')
            ->with('success', 'Medical record added successfully.');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        abort_if($medicalRecord->appointment->doctor_id !== $this->getDoctor()->id, 403);
        $medicalRecord->load(['appointment.patient', 'appointment']);
        return view('doctor.medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        abort_if($medicalRecord->appointment->doctor_id !== $this->getDoctor()->id, 403);
        $medicalRecord->load(['appointment.patient', 'appointment']);
        return view('doctor.medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        abort_if($medicalRecord->appointment->doctor_id !== $this->getDoctor()->id, 403);

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