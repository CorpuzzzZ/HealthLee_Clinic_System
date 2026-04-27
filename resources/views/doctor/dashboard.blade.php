<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    Good day, Dr. <span class="text-primary">{{ $doctor->first_name }}</span>!
                </h5>
                <small class="text-muted">Here's your schedule overview for today.</small>
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
                        <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">Today</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalToday }}</h3>
                    <p class="text-muted mb-0 small">Today's Appointments</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e0f7fc;">
                            <i class="bi bi-calendar-event-fill fs-4" style="color: #0dcaf0;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e0f7fc; color: #0097a7; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalAppointments }}</h3>
                    <p class="text-muted mb-0 small">Total Appointments</p>
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
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #fff8e1;">
                            <i class="bi bi-people-fill fs-4" style="color: #ffc107;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #fff8e1; color: #e6a800; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalPatients }}</h3>
                    <p class="text-muted mb-0 small">Unique Patients</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Today's Appointments + Availability ── --}}
    <div class="row g-4 mb-4">

        {{-- Today's Appointments --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #0d6efd; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-calendar-event me-2"></i> Today's Appointments
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($todaysAppointments->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                            <div class="fw-medium">No appointments scheduled for today.</div>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($todaysAppointments as $appt)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-0 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                             style="width: 40px; height: 40px; font-size: 0.8rem; background: #198754;">
                                            {{ strtoupper(substr($appt->patient->first_name, 0, 1)) }}{{ strtoupper(substr($appt->patient->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $appt->patient->first_name }} {{ $appt->patient->last_name }}
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                                            </small>
                                        </div>
                                    </div>
                                    @php
                                        $statusStyles = [
                                            'pending'     => 'background: #fff8e1; color: #e6a800;',
                                            'confirmed'   => 'background: #e7f1ff; color: #0d6efd;',
                                            'completed'   => 'background: #e8f5ee; color: #198754;',
                                            'cancelled'   => 'background: #fdecea; color: #dc3545;',
                                            'rescheduled' => 'background: #f3e8ff; color: #7c3aed;',
                                        ];
                                    @endphp
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="{{ $statusStyles[$appt->status] ?? '' }} font-size: 0.75rem;">
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- My Availability --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header border-0 py-3 px-4"
                     style="background: #0dcaf0; border-radius: 12px 12px 0 0;">
                    <h6 class="mb-0 fw-semibold text-white">
                        <i class="bi bi-clock-history me-2"></i> My Availability (Next 7 Days)
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($availabilities->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar3 fs-1 d-block mb-2 opacity-25"></i>
                            <div class="fw-medium">No availability slots set yet.</div>
                            <a href="{{ route('doctor.availabilities.index') }}"
                               class="btn btn-sm btn-outline-primary mt-3 rounded-pill px-3">
                                Set Availability Now <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($availabilities as $slot)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-0 border-bottom">
                                    <div>
                                        <div class="fw-medium small">
                                            {{ $slot->available_date->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $slot->available_date->format('l') }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background: #e0f7fc; color: #0097a7; font-size: 0.75rem;">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                            —
                                            {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="px-4 py-3 border-top">
                            <a href="{{ route('doctor.availabilities.index') }}"
                               class="btn btn-sm btn-outline-primary rounded-pill px-3 w-100">
                                Manage Availability <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ── Recent Medical Records + Notifications ── --}}
    <div class="row g-4">

        {{-- Recent Medical Records --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Recent Medical Records</h6>
                    <small class="text-muted">Latest patient consultation records</small>
                </div>
                <div class="card-body p-0 pt-3">
                    @if($recentMedicalRecords->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-file-medical fs-1 d-block mb-2 opacity-25"></i>
                            <div class="fw-medium">No recent medical records found.</div>
                            <small>Records will appear here once the module is set up.</small>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th class="px-4 py-3 text-muted fw-normal small border-0">Date</th>
                                        <th class="px-4 py-3 text-muted fw-normal small border-0">Patient</th>
                                        <th class="px-4 py-3 text-muted fw-normal small border-0">Diagnosis</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMedicalRecords as $record)
                                        <tr>
                                            <td class="px-4 border-0 small fw-medium">
                                                {{ $record->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 border-0 small">
                                                {{ $record->patient->first_name ?? '' }}
                                                {{ $record->patient->last_name ?? '' }}
                                            </td>
                                            <td class="px-4 border-0 small text-muted">
                                                {{ Str::limit($record->diagnosis, 50) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notifications --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Recent Notifications</h6>
                    <small class="text-muted">Latest alerts and reminders</small>
                </div>
                <div class="card-body p-0 pt-3">
                    @if($notifications->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
                            <div class="fw-medium">No recent notifications.</div>
                            <small>Notifications will appear here once the module is set up.</small>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($notifications->take(6) as $notif)
                                <div class="list-group-item py-3 px-4 border-0 border-bottom">
                                    <p class="mb-1 small">{{ $notif->message }}</p>
                                    <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</x-app-layout>