<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Medical Record</h5>
                <small class="text-muted">Record #{{ $medicalRecord->id }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('doctor.medical-records.edit', $medicalRecord) }}"
                   class="btn btn-warning btn-sm rounded-pill px-3">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
                <a href="{{ route('doctor.medical-records.index') }}"
                   class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="row g-4">

        {{-- Patient & Appointment Info --}}
        <div class="col-lg-4">

            {{-- Patient Card --}}
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #198754; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-person-heart-fill me-2"></i> Patient
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width: 50px; height: 50px; font-size: 1rem; background: #198754;">
                            {{ strtoupper(substr($medicalRecord->patient->first_name, 0, 1)) }}{{ strtoupper(substr($medicalRecord->patient->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">
                                {{ $medicalRecord->patient->first_name }}
                                {{ $medicalRecord->patient->middle_name ? $medicalRecord->patient->middle_name . ' ' : '' }}
                                {{ $medicalRecord->patient->last_name }}
                            </div>
                            <small class="text-muted">Patient</small>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Age</small>
                                <span class="fw-medium small">{{ $medicalRecord->patient->age ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Gender</small>
                                <span class="fw-medium small">{{ ucfirst($medicalRecord->patient->gender ?? '—') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Contact</small>
                                <span class="fw-medium small">{{ $medicalRecord->patient->contact_number ?? '—' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Appointment Card --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #0d6efd; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-calendar-event me-2"></i> Appointment
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Date</small>
                                <span class="fw-medium small">
                                    {{ $medicalRecord->appointment->appointment_date->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Time</small>
                                <span class="fw-medium small">
                                    {{ \Carbon\Carbon::parse($medicalRecord->appointment->appointment_time)->format('h:i A') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Status</small>
                                <span class="badge rounded-pill px-3 py-2"
                                      style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">
                                    {{ ucfirst($medicalRecord->appointment->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Record Created</small>
                                <span class="fw-medium small">
                                    {{ $medicalRecord->created_at->format('d M Y, h:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Medical Details --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Consultation Details</h6>
                    <small class="text-muted">
                        Recorded by Dr. {{ $medicalRecord->doctor->first_name }} {{ $medicalRecord->doctor->last_name }}
                    </small>
                </div>
                <div class="card-body px-4 pb-4 pt-3">

                    {{-- Diagnosis --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-3 p-1" style="background: #e7f1ff;">
                                <i class="bi bi-clipboard2-pulse-fill" style="color: #0d6efd; font-size: 0.9rem;"></i>
                            </div>
                            <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing: 0.05em;">
                                Diagnosis
                            </span>
                        </div>
                        <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #0d6efd;">
                            <p class="mb-0 small">{{ $medicalRecord->diagnosis }}</p>
                        </div>
                    </div>

                    {{-- Treatment --}}
                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-3 p-1" style="background: #e8f5ee;">
                                <i class="bi bi-capsule-pill" style="color: #198754; font-size: 0.9rem;"></i>
                            </div>
                            <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing: 0.05em;">
                                Treatment
                            </span>
                        </div>
                        <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #198754;">
                            <p class="mb-0 small">{{ $medicalRecord->treatment }}</p>
                        </div>
                    </div>

                    {{-- Notes --}}
                    @if($medicalRecord->notes)
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-3 p-1" style="background: #fff8e1;">
                                    <i class="bi bi-sticky-fill" style="color: #e6a800; font-size: 0.9rem;"></i>
                                </div>
                                <span class="fw-semibold small text-uppercase text-muted" style="letter-spacing: 0.05em;">
                                    Notes
                                </span>
                            </div>
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #ffc107;">
                                <p class="mb-0 small">{{ $medicalRecord->notes }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Last Updated --}}
                    @if($medicalRecord->updated_at != $medicalRecord->created_at)
                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bi bi-pencil me-1"></i>
                                Last updated {{ $medicalRecord->updated_at->format('d M Y, h:i A') }}
                            </small>
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>

</x-app-layout>