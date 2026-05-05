<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Appointment Details</h5>
                <small class="text-muted">Viewing appointment #{{ $appointment->id }}</small>
            </div>
            <a href="{{ route('doctor.appointments.index') }}"
                class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </x-slot>

    @php
    $statusStyles = [
    'pending' => 'background: #fff8e1; color: #e6a800;',
    'confirmed' => 'background: #e7f1ff; color: #0d6efd;',
    'completed' => 'background: #e8f5ee; color: #198754;',
    'cancelled' => 'background: #fdecea; color: #dc3545;',
    'rescheduled' => 'background: #f3e8ff; color: #7c3aed;',
    ];
    $statusIcons = [
    'pending' => 'bi-clock',
    'confirmed' => 'bi-check-circle',
    'completed' => 'bi-check-circle-fill',
    'cancelled' => 'bi-x-circle',
    'rescheduled' => 'bi-arrow-repeat',
    ];
    $startTime = \Carbon\Carbon::parse($appointment->appointment_time);
    $endTime = $startTime->copy()->addHour();
    @endphp

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- Status Banner --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3" style="{{ $statusStyles[$appointment->status] ?? '' }}">
                            <i class="bi {{ $statusIcons[$appointment->status] ?? 'bi-circle' }} fs-4"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Appointment Status</div>
                            <div class="fw-bold fs-5">{{ ucfirst($appointment->status) }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div class="text-center">
                            <div class="text-muted small">Date</div>
                            <div class="fw-semibold">{{ $appointment->appointment_date->format('d M Y') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-muted small">Time</div>
                            <div class="fw-semibold">
                                {{ $startTime->format('h:i A') }} – {{ $endTime->format('h:i A') }}
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-muted small">Booked On</div>
                            <div class="fw-semibold">{{ $appointment->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Patient Info --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4" style="background: #198754; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-person-heart-fill me-2"></i> Patient
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                            style="width: 52px; height: 52px; font-size: 1rem; background: #198754;">
                            {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}{{
                            strtoupper(substr($appointment->patient->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">
                                {{ $appointment->patient->first_name }}
                                {{ $appointment->patient->middle_name ? $appointment->patient->middle_name . ' ' : '' }}
                                {{ $appointment->patient->last_name }}
                            </div>
                            <small class="text-muted">Patient</small>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Gender</small>
                                <span class="fw-medium small">
                                    {{ ucfirst($appointment->patient->gender ?? '—') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Contact</small>
                                <span class="fw-medium small">
                                    {{ $appointment->patient->contact->contact_number
                                    ?? $appointment->patient->contact_number
                                    ?? '—' }}
                                </span>
                            </div>
                        </div>
                        @if($appointment->notes)
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Appointment Notes</small>
                                <span class="fw-medium small">{{ $appointment->notes }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Service + Update Status --}}
        <div class="col-md-7 d-flex flex-column gap-4">

            {{-- Service Info --}}
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4" style="background: #6f42c1; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-clipboard2-pulse-fill me-2"></i> Service & Fees
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if($appointment->service)
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold mb-1">
                                {{ $appointment->service->name }}
                            </div>
                            @if($appointment->service->description)
                            <div class="text-muted small mb-2">
                                {{ $appointment->service->description }}
                            </div>
                            @endif
                        </div>
                        @if($appointment->service->price)
                        <div class="text-end flex-shrink-0">
                            <div class="text-muted small mb-1">Fee</div>
                            <span class="badge rounded-pill px-3 py-2"
                                style="background: #e8f5ee; color: #198754; font-size: 0.85rem;">
                                <i class="bi bi-tag-fill me-1"></i>
                                ₱{{ number_format($appointment->service->price, 2) }}
                            </span>
                        </div>
                        @endif
                    </div>
                    @else
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        No specific service was selected for this appointment.
                    </div>
                    @endif
                </div>
            </div>

            {{-- Update Status --}}
            <div class="card border-0 shadow-sm flex-grow-1" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4" style="background: #0d6efd; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-pencil-square me-2"></i> Update Status
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if(in_array($appointment->status, ['completed', 'cancelled']))
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-lock fs-2 d-block mb-2 opacity-25"></i>
                        <div class="fw-medium">This appointment is {{ $appointment->status }}</div>
                        <small>Status can no longer be changed.</small>
                    </div>
                    @else
                    <p class="text-muted small mb-4">
                        Update the status of this appointment. The patient will be notified automatically.
                    </p>
                    <form method="POST" action="{{ route('doctor.appointments.status', $appointment) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="form-label fw-semibold">New Status</label>
                            <select name="status"
                                class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                <option value="" disabled selected>Select status...</option>
                                @if($appointment->status !== 'confirmed')
                                <option value="confirmed">Confirmed</option>
                                @endif
                                <option value="completed">Completed</option>
                                <option value="rescheduled">Rescheduled</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                The patient will receive a notification when the status changes.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 rounded-3">
                            <i class="bi bi-check-lg me-1"></i> Update Status
                        </button>
                    </form>
                    @endif
                </div>
            </div>

        </div>

        {{-- Medical Record --}}
        @if($appointment->status === 'completed')
        <div class="col-12">
            <div class="card border-0 shadow-sm"
                style="border-radius: 12px; border-left: 4px solid #198754 !important;">
                <div class="card-body px-4 py-3 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-1">
                            <i class="bi bi-file-medical-fill me-2 text-success"></i> Medical Record
                        </h6>
                        @if($appointment->medicalRecord)
                        <small class="text-muted">
                            Record created on {{ $appointment->medicalRecord->created_at->format('d M Y') }}
                        </small>
                        @else
                        <small class="text-muted">No medical record created yet for this appointment.</small>
                        @endif
                    </div>
                    @if($appointment->medicalRecord)
                    <a href="{{ route('doctor.medical-records.show', $appointment->medicalRecord) }}"
                        class="btn btn-sm btn-outline-success rounded-3 px-3">
                        <i class="bi bi-eye me-1"></i> View Record
                    </a>
                    @else
                    <a href="{{ route('doctor.medical-records.create') }}"
                        class="btn btn-sm btn-success rounded-3 px-3">
                        <i class="bi bi-plus-lg me-1"></i> Add Record
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

</x-app-layout>