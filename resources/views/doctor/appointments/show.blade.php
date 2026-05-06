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
    'reschedule_requested' => 'background: #fff3e0; color: #fd7e14;',
    ];
    $statusIcons = [
    'pending' => 'bi-clock',
    'confirmed' => 'bi-check-circle',
    'completed' => 'bi-check-circle-fill',
    'cancelled' => 'bi-x-circle',
    'rescheduled' => 'bi-arrow-repeat',
    'reschedule_requested' => 'bi-calendar2-week',
    ];
    $startTime = \Carbon\Carbon::parse($appointment->appointment_time);
    $endTime = $startTime->copy()->addHour();

    // Check if appointment date has passed
    $isPastDate = \Carbon\Carbon::parse($appointment->appointment_date)->isPast();
    $isToday = \Carbon\Carbon::parse($appointment->appointment_date)->isToday();

    // Check if this is a reschedule request
    $isRescheduleRequest = $appointment->status === 'reschedule_requested';

    // Determine available status options based on current status
    $availableStatuses = [];
    switch($appointment->status) {
    case 'pending':
    $availableStatuses = ['confirmed' => 'Confirmed'];
    break;
    case 'reschedule_requested':
    $availableStatuses = [
    'confirmed' => 'Approve Reschedule',
    'cancelled' => 'Reject & Cancel',
    ];
    break;
    case 'confirmed':
    $options = [];
    if ($isPastDate || $isToday) {
    $options['completed'] = 'Completed';
    } else {
    $completedDisabled = true;
    }
    $options['cancelled'] = 'Cancelled';
    $options['rescheduled'] = 'Reschedule (by doctor)';
    $availableStatuses = $options;
    break;
    default:
    $availableStatuses = [];
    }
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
                            <div class="fw-bold fs-5">
                                @if($appointment->status === 'reschedule_requested')
                                Reschedule Requested
                                @else
                                {{ ucfirst($appointment->status) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div class="text-center">
                            <div class="text-muted small">Date</div>
                            <div class="fw-semibold">{{ $appointment->appointment_date->format('d M Y') }}</div>
                            @if(!$isPastDate && !$isToday && $appointment->status === 'confirmed')
                            <small class="text-warning d-block mt-1">
                                <i class="bi bi-calendar-exclamation"></i> Future date
                            </small>
                            @endif
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
                        @if($appointment->notes || $appointment->reschedule_notes)
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                @if($appointment->reschedule_notes)
                                <small class="text-muted d-block mb-1">Reschedule Request Notes</small>
                                <span class="fw-medium small text-warning">{{ $appointment->reschedule_notes }}</span>
                                @elseif($appointment->notes)
                                <small class="text-muted d-block mb-1">Appointment Notes</small>
                                <span class="fw-medium small">{{ $appointment->notes }}</span>
                                @endif
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
                    @elseif(empty($availableStatuses))
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-question-circle fs-2 d-block mb-2 opacity-25"></i>
                        <div class="fw-medium">No status updates available</div>
                        <small>Please contact support if you need to change this appointment.</small>
                    </div>
                    @else

                    @if($isRescheduleRequest)
                    <div class="mb-4">
                        <div class="alert alert-warning rounded-3 border-0 mb-3" style="background: #fff3e0;">
                            <i class="bi bi-calendar2-week me-2 text-warning"></i>
                            <strong>Patient requested to reschedule this appointment.</strong><br>
                            <small>Please review the request and choose to approve or reject it.</small>
                        </div>
                    </div>
                    @else
                    <div class="mb-4">
                        <div class="alert alert-info rounded-3 border-0 mb-3" style="background: #e7f1ff;">
                            <i class="bi bi-info-circle-fill me-2 text-primary"></i>
                            @if($appointment->status === 'pending')
                            <strong>Step 1 of 2:</strong> Confirm this appointment first. After confirmation, you can
                            mark it as
                            <strong>Completed</strong>, <strong>Cancelled</strong>, or <strong>Rescheduled</strong>.
                            @elseif($appointment->status === 'confirmed')
                            @if(!$isPastDate && !$isToday)
                            <strong>⚠️ Cannot mark as completed yet:</strong> This appointment is scheduled for a future
                            date ({{ $appointment->appointment_date->format('d M Y') }}).
                            You can only mark it as <strong>Completed</strong> on or after the appointment date.
                            @else
                            <strong>Step 2 of 2:</strong> Mark this appointment as
                            <strong>Completed</strong> (if service was provided),
                            <strong>Cancelled</strong>, or <strong>Rescheduled</strong>.
                            @endif
                            @endif
                        </div>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('doctor.appointments.status', $appointment) }}">
                        @csrf
                        @method('PATCH')
                        <div class="mb-4">
                            <label class="form-label fw-semibold">New Status</label>
                            <select name="status"
                                class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                <option value="" disabled selected>Select status...</option>
                                @foreach($availableStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
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
                    {{-- ✅ Pass appointment_id so the create form pre-selects this patient --}}
                    <a href="{{ route('doctor.medical-records.create', ['appointment_id' => $appointment->id]) }}"
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