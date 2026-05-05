<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Appointment Details</h5>
                <small class="text-muted">Viewing appointment #{{ $appointment->id }}</small>
            </div>
            <a href="{{ route('patient.appointments.index') }}"
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

                    {{-- Cancel Button --}}
                    @if(in_array($appointment->status, ['pending', 'confirmed']))
                    <form method="POST" action="{{ route('patient.appointments.cancel', $appointment) }}"
                        onsubmit="return confirm('Are you sure you want to cancel this appointment?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                            <i class="bi bi-x-lg me-1"></i> Cancel Appointment
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Doctor Info --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4" style="background: #0dcaf0; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-person-badge-fill me-2"></i> Doctor
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                            style="width: 52px; height: 52px; font-size: 1rem; background: #0dcaf0;">
                            {{ strtoupper(substr($appointment->doctor->first_name, 0, 1)) }}{{
                            strtoupper(substr($appointment->doctor->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">
                                Dr. {{ $appointment->doctor->first_name }}
                                {{ $appointment->doctor->last_name }}
                            </div>
                            <span class="badge rounded-pill px-2 py-1"
                                style="background: #e0f7fc; color: #0097a7; font-size: 0.7rem;">
                                {{ $appointment->doctor->specialty ?? 'General' }}
                            </span>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Gender</small>
                                <span class="fw-medium small">
                                    {{ ucfirst($appointment->doctor->gender ?? '—') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Contact</small>
                                <span class="fw-medium small">
                                    {{-- supports both normalized (doctor->contact->contact_number) and flat --}}
                                    {{ $appointment->doctor->contact->contact_number
                                    ?? $appointment->doctor->contact_number
                                    ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service & Notes --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4" style="background: #0d6efd; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-clipboard2-pulse-fill me-2"></i> Appointment Info
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">

                    {{-- Service Type --}}
                    @if($appointment->service)
                    <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #0d6efd;">
                        <small class="text-muted d-block mb-1">Service Type</small>
                        <div class="fw-semibold small">{{ $appointment->service->name }}</div>
                        @if($appointment->service->description)
                        <div class="text-muted" style="font-size: 0.78rem; margin-top: 2px;">
                            {{ $appointment->service->description }}
                        </div>
                        @endif
                        @if($appointment->service->price)
                        <span class="badge rounded-pill mt-2 px-3 py-1"
                            style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">
                            <i class="bi bi-tag-fill me-1"></i>
                            ₱{{ number_format($appointment->service->price, 2) }}
                        </span>
                        @endif
                    </div>
                    @else
                    <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #dee2e6;">
                        <small class="text-muted d-block mb-1">Service Type</small>
                        <span class="text-muted small">No specific service selected.</span>
                    </div>
                    @endif

                    {{-- Notes --}}
                    <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #ffc107;">
                        <small class="text-muted d-block mb-1">Your Notes</small>
                        @if($appointment->notes)
                        <p class="mb-0 small">{{ $appointment->notes }}</p>
                        @else
                        <span class="text-muted small">No notes added for this appointment.</span>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- Medical Record (if completed) --}}
        @if($appointment->status === 'completed' && $appointment->medicalRecord)
        <div class="col-12">
            <div class="card border-0 shadow-sm"
                style="border-radius: 12px; border-left: 4px solid #198754 !important;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-file-medical-fill me-2 text-success"></i> Medical Record
                    </h6>
                    <small class="text-muted">
                        Consultation record from
                        Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}
                    </small>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #0d6efd;">
                                <small class="text-muted d-block mb-1 fw-semibold">DIAGNOSIS</small>
                                <p class="mb-0 small">{{ $appointment->medicalRecord->diagnosis }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #198754;">
                                <small class="text-muted d-block mb-1 fw-semibold">TREATMENT</small>
                                <p class="mb-0 small">{{ $appointment->medicalRecord->treatment }}</p>
                            </div>
                        </div>
                        @if($appointment->medicalRecord->notes)
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: #f8f9fa; border-left: 3px solid #ffc107;">
                                <small class="text-muted d-block mb-1 fw-semibold">NOTES</small>
                                <p class="mb-0 small">{{ $appointment->medicalRecord->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @elseif($appointment->status === 'completed' && !$appointment->medicalRecord)
        <div class="col-12">
            <div class="card border-0 shadow-sm"
                style="border-radius: 12px; border-left: 4px solid #dee2e6 !important;">
                <div class="card-body px-4 py-3 text-muted">
                    <i class="bi bi-file-medical me-2"></i>
                    <small>No medical record has been added for this appointment yet.</small>
                </div>
            </div>
        </div>
        @endif

    </div>

</x-app-layout>