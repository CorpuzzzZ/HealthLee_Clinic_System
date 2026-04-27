<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    Welcome back, <span class="text-primary">{{ $patient->first_name }}</span>!
                </h5>
                <small class="text-muted">Here's an overview of your health appointments today.</small>
            </div>
            <span class="text-muted small">{{ now()->format('l, d F Y') }}</span>
        </div>
    </x-slot>

    {{-- ── Stats Cards ── --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e7f1ff;">
                            <i class="bi bi-calendar-check-fill fs-4" style="color: #0d6efd;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalAppointments }}</h3>
                    <p class="text-muted mb-0 small">Total Appointments</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e0f7fc;">
                            <i class="bi bi-clock-fill fs-4" style="color: #0dcaf0;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e0f7fc; color: #0097a7; font-size: 0.75rem;">Soon</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalUpcoming }}</h3>
                    <p class="text-muted mb-0 small">Upcoming</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e8f5ee;">
                            <i class="bi bi-check-circle-fill fs-4" style="color: #198754;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">Done</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalCompleted }}</h3>
                    <p class="text-muted mb-0 small">Completed</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #dc3545 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #fdecea;">
                            <i class="bi bi-x-circle-fill fs-4" style="color: #dc3545;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #fdecea; color: #dc3545; font-size: 0.75rem;">Cancelled</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalCancelled }}</h3>
                    <p class="text-muted mb-0 small">Cancelled</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Profile Summary + Quick Actions + Notifications ── --}}
    <div class="row g-4 mb-4">

        {{-- Profile Summary --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #0d6efd; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-person-circle me-2"></i> Your Profile
                    </h6>
                </div>
                <div class="card-body p-4">

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                             style="width: 56px; height: 56px; font-size: 1.1rem; background: #0d6efd;">
                            {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="fw-bold">
                                {{ $patient->first_name }}
                                {{ $patient->middle_name ? $patient->middle_name . ' ' : '' }}
                                {{ $patient->last_name }}
                            </div>
                            <small class="text-muted">Patient</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Age</small>
                                <span class="fw-semibold">{{ $patient->age ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Gender</small>
                                <span class="fw-semibold">{{ ucfirst($patient->gender ?? 'N/A') }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 rounded-3" style="background: #f8f9fa;">
                                <small class="text-muted d-block mb-1">Contact Number</small>
                                <span class="fw-semibold">{{ $patient->contact_number ?? 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary w-100 rounded-3">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </a>

                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #198754; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column gap-3">

                    {{-- Find Doctor --}}
<a href="{{ route('patient.doctors.index') }}"
                       class="btn btn-light text-start d-flex align-items-center gap-3 p-3 border rounded-3 text-decoration-none"
                       style="border-color: #e9ecef !important;">
                        <div class="rounded-3 p-2" style="background: #e7f1ff;">
                            <i class="bi bi-search" style="color: #0d6efd; font-size: 1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-medium small text-dark">Find a Doctor</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Browse available doctors</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>

                    {{-- Manage Appointments - enabled once patient.appointments.index is built --}}
                    <a href="{{route('patient.appointments.index')}}"
                       class="btn btn-light text-start d-flex align-items-center gap-3 p-3 border rounded-3 text-decoration-none"
                       style="border-color: #e9ecef !important;">
                        <div class="rounded-3 p-2" style="background: #e8f5ee;">
                            <i class="bi bi-calendar-check" style="color: #198754; font-size: 1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-medium small text-dark">My Appointments</div>
                            <div class="text-muted" style="font-size: 0.75rem;">View and manage bookings</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="btn btn-light text-start d-flex align-items-center gap-3 p-3 border rounded-3 text-decoration-none"
                       style="border-color: #e9ecef !important;">
                        <div class="rounded-3 p-2" style="background: #fff8e1;">
                            <i class="bi bi-person-gear" style="color: #e6a800; font-size: 1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-medium small text-dark">Edit Profile</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Update your information</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>

                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #ffc107; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-bell-fill me-2"></i> Recent Notifications
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($notifications->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
                            <div class="fw-medium">No new notifications.</div>
                            <small>You're all caught up!</small>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications->take(4) as $notif)
                                <div class="list-group-item border-0 border-bottom py-3 px-4">
                                    <small class="text-muted d-block mb-1">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </small>
                                    <p class="mb-0 small">{{ $notif->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── Upcoming Appointments ── --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
            <div>
                <h6 class="fw-bold mb-0">Upcoming Appointments</h6>
                <small class="text-muted">Your next scheduled consultations</small>
            </div>
            {{-- Enable once patient.appointments.index is built --}}
            <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                View All <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body pt-4 pb-4 px-4">
            @if($upcomingAppointments->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                    <div class="fw-medium">No upcoming appointments.</div>
                    <small>Use "Find a Doctor" to book your first appointment.</small>
                </div>
            @else
                <div class="row g-3">
                    @foreach($upcomingAppointments as $appt)
                        @php
                            $statusStyles = [
                                'pending'     => 'background: #fff8e1; color: #e6a800;',
                                'confirmed'   => 'background: #e7f1ff; color: #0d6efd;',
                                'completed'   => 'background: #e8f5ee; color: #198754;',
                                'cancelled'   => 'background: #fdecea; color: #dc3545;',
                                'rescheduled' => 'background: #f3e8ff; color: #7c3aed;',
                            ];
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 h-100" style="border-radius: 12px; background: #f8f9fa;">
                                <div class="card-body p-4">

                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <div class="fw-bold">
                                                {{ $appt->appointment_date->format('M d, Y') }}
                                            </div>
                                            <div class="text-primary fw-medium small">
                                                {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                                            </div>
                                        </div>
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="{{ $statusStyles[$appt->status] ?? '' }} font-size: 0.75rem;">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </div>

                                    <hr class="my-3">

                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                             style="width: 36px; height: 36px; font-size: 0.75rem; background: #0dcaf0;">
                                            {{ strtoupper(substr($appt->doctor->first_name, 0, 1)) }}{{ strtoupper(substr($appt->doctor->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                Dr. {{ $appt->doctor->first_name }} {{ $appt->doctor->last_name }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $appt->doctor->specialty ?? 'General' }}
                                            </small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</x-app-layout>