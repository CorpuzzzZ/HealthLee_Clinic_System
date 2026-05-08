<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Patient Record</h5>
                <small class="text-muted">
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.patient-records.edit', $patient) }}"
                    class="btn btn-warning btn-sm rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit Record
                </a>
                <a href="{{ route('admin.patient-records.index') }}"
                    class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Patient Profile Card --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body p-4 text-center">

                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3"
                        style="width: 80px; height: 80px; font-size: 1.5rem; background: #198754;">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0,
                        1)) }}
                    </div>

                    <h6 class="fw-bold mb-1">
                        {{ $patient->first_name }}
                        {{ $patient->middle_name ? $patient->middle_name . ' ' : '' }}
                        {{ $patient->last_name }}
                    </h6>

                    <small class="text-muted d-block mb-3">
                        {{ $patient->user->email ?? '—' }}
                    </small>

                    {{-- Record completeness --}}
                    @if($patient->birthdate && $patient->blood_type && $patient->height && $patient->weight)
                    <span class="badge rounded-pill px-3 py-2" style="background: #e8f5ee; color: #198754;">
                        <i class="bi bi-check-circle me-1"></i> Record Complete
                    </span>
                    @else
                    <span class="badge rounded-pill px-3 py-2" style="background: #fff8e1; color: #e6a800;">
                        <i class="bi bi-exclamation-circle me-1"></i> Record Incomplete
                    </span>
                    @endif

                    <hr class="my-3">

                    {{-- Basic Info --}}
                    <div class="text-start">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Gender</small>
                                    <span class="fw-medium small">{{ ucfirst($patient->gender ?? '—') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Age</small>
                                    <span class="fw-medium small">{{ $patient->birthdate ? $patient->birthdate->age :
                                        '—' }}</span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                    <small class="text-muted d-block mb-1">Contact</small>
                                    <span class="fw-medium small">{{ $patient->user->contact->contact_number ?? '—'
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Medical Info --}}
        <div class="col-lg-8">

            {{-- Medical Details --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-clipboard2-pulse-fill me-2 text-danger"></i>
                        Medical Information
                    </h6>
                    <small class="text-muted">Health metrics and vital information</small>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="row g-3">

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #0d6efd;">
                                <small class="text-muted d-block mb-1">Birthdate</small>
                                <span class="fw-semibold">
                                    {{ $patient->birthdate ? $patient->birthdate->format('d M Y') : '—' }}
                                </span>
                                @if($patient->birthdate)
                                <small class="text-muted d-block mt-1">
                                    Age: {{ $patient->birthdate->age }} years old
                                </small>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #dc3545;">
                                <small class="text-muted d-block mb-1">Blood Type</small>
                                <span class="fw-semibold fs-5" style="color: #dc3545;">
                                    {{ $patient->blood_type ?? '—' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #198754;">
                                <small class="text-muted d-block mb-1">Height</small>
                                <span class="fw-semibold">
                                    {{ $patient->height ? $patient->height . ' cm' : '—' }}
                                </span>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #0dcaf0;">
                                <small class="text-muted d-block mb-1">Weight</small>
                                <span class="fw-semibold">
                                    {{ $patient->weight ? $patient->weight . ' kg' : '—' }}
                                </span>
                            </div>
                        </div>

                        {{-- BMI Calculation --}}
                        @if($patient->height && $patient->weight)
                        @php
                        $bmi = $patient->weight / (($patient->height / 100) ** 2);
                        $bmiLabel = match(true) {
                        $bmi < 18.5=> ['Underweight', '#e6a800', '#fff8e1'],
                            $bmi < 25=> ['Normal', '#198754', '#e8f5ee'],
                                $bmi < 30=> ['Overweight', '#fd7e14', '#fff3e0'],
                                    default => ['Obese', '#dc3545', '#fdecea'],
                                    };
                                    @endphp
                                    <div class="col-12">
                                        <div class="p-3 rounded-3"
                                            style="background: {{ $bmiLabel[2] }}; border-left: 3px solid {{ $bmiLabel[1] }};">
                                            <small class="text-muted d-block mb-1">BMI (Body Mass Index)</small>
                                            <span class="fw-semibold" style="color: {{ $bmiLabel[1] }};">
                                                {{ number_format($bmi, 2) }}
                                                <span class="badge rounded-pill ms-2 px-2 py-1"
                                                    style="background: {{ $bmiLabel[1] }}; color: white; font-size: 0.7rem;">
                                                    {{ $bmiLabel[0] }}
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    @endif

                    </div>
                </div>
            </div>

            {{-- Appointment History --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Appointment History</h6>
                    <small class="text-muted">All appointments for this patient</small>
                </div>
                <div class="card-body p-0 pt-2">
                    @forelse($patient->appointments->take(5) as $appt)
                    @php
                    $statusStyles = [
                    'pending' => 'background: #fff8e1; color: #e6a800;',
                    'confirmed' => 'background: #e7f1ff; color: #0d6efd;',
                    'completed' => 'background: #e8f5ee; color: #198754;',
                    'cancelled' => 'background: #fdecea; color: #dc3545;',
                    'rescheduled' => 'background: #f3e8ff; color: #7c3aed;',
                    ];
                    @endphp
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-medium small">
                                Dr. {{ $appt->doctor->first_name }} {{ $appt->doctor->last_name }}
                            </div>
                            <small class="text-muted">
                                {{ $appt->appointment_date->format('d M Y') }}
                                at {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                            </small>
                        </div>
                        <span class="badge rounded-pill px-2 py-1"
                            style="{{ $statusStyles[$appt->status] ?? '' }} font-size: 0.7rem;">
                            {{ ucfirst($appt->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <small>No appointments yet.</small>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Medical Records --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Medical Records</h6>
                    <small class="text-muted">Consultation records for this patient</small>
                </div>
                <div class="card-body p-0 pt-2">
                    @forelse($patient->medicalRecords->take(5) as $record)
                    <div class="px-4 py-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-medium small">
                                Dr. {{ $record->appointment->doctor->first_name ?? '—' }} {{
                                $record->appointment->doctor->last_name ?? '—' }}
                            </span>
                            <small class="text-muted">{{ $record->created_at->format('d M Y') }}</small>
                        </div>
                        <small class="text-muted d-block">
                            <strong>Diagnosis:</strong> {{ Str::limit($record->diagnosis, 60) }}
                        </small>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <small>No medical records yet.</small>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</x-app-layout>