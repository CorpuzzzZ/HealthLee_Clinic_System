<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-primary fs-3">Reports</h5>
                <small class="text-muted">System analytics and data summaries</small>
            </div>
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> Print Report
            </button>
        </div>
    </x-slot>

    {{-- ── Period Filter ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('admin.reports.index') }}"
                class="d-flex align-items-center gap-3 flex-wrap">
                <span class="text-muted small fw-semibold">Filter by Period:</span>
                @foreach(['all' => 'All Time', 'daily' => 'Today', 'weekly' => 'This Week', 'monthly' => 'This Month']
                as $value => $label)
                <button type="submit" name="period" value="{{ $value }}" class="btn btn-sm rounded-pill px-3
                                   {{ $period === $value ? 'btn-primary' : 'btn-outline-secondary' }}">
                    {{ $label }}
                </button>
                @endforeach
            </form>
        </div>
    </div>

    {{-- ── Stats Cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100 text-center"
                style="border-top: 4px solid #0d6efd; border-radius: 12px;">
                <div class="card-body p-3">
                    <h3 class="fw-bold mb-1" style="color: #0d6efd;">{{ $totalAppointments }}</h3>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100 text-center"
                style="border-top: 4px solid #198754; border-radius: 12px;">
                <div class="card-body p-3">
                    <h3 class="fw-bold mb-1" style="color: #198754;">{{ $totalCompleted }}</h3>
                    <small class="text-muted">Completed</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100 text-center"
                style="border-top: 4px solid #ffc107; border-radius: 12px;">
                <div class="card-body p-3">
                    <h3 class="fw-bold mb-1" style="color: #e6a800;">{{ $totalPending }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100 text-center"
                style="border-top: 4px solid #0dcaf0; border-radius: 12px;">
                <div class="card-body p-3">
                    <h3 class="fw-bold mb-1" style="color: #0097a7;">{{ $totalConfirmed }}</h3>
                    <small class="text-muted">Confirmed</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100 text-center"
                style="border-top: 4px solid #dc3545; border-radius: 12px;">
                <div class="card-body p-3">
                    <h3 class="fw-bold mb-1" style="color: #dc3545;">{{ $totalCancelled }}</h3>
                    <small class="text-muted">Cancelled</small>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm h-100 text-center"
                style="border-top: 4px solid #7c3aed; border-radius: 12px;">
                <div class="card-body p-3">
                    <h3 class="fw-bold mb-1" style="color: #7c3aed;">{{ $totalRescheduled }}</h3>
                    <small class="text-muted">Rescheduled</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Appointment Summary (vw_appointment_summary) ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-2 py-1"
                    style="background: #e7f1ff; color: #0d6efd; font-size: 0.7rem;">
                    DATABASE VIEW
                </span>
                <span class="text-muted small font-monospace">vw_appointment_summary</span>
            </div>
            <h6 class="fw-bold mb-0">Appointment Summary</h6>
            <small class="text-muted">All appointments with patient and doctor details</small>
        </div>
        <div class="card-body p-0 pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Date</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Time</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Patient</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Gender</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Blood Type</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Doctor</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Specialty</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Service</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Price</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Status</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Notes</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Booked Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appt)
                        @php
                        $statusStyles = [
                        'pending' => 'background: #fff8e1; color: #e6a800;',
                        'confirmed' => 'background: #e7f1ff; color: #0d6efd;',
                        'completed' => 'background: #e8f5ee; color: #198754;',
                        'cancelled' => 'background: #fdecea; color: #dc3545;',
                        'rescheduled' => 'background: #f3e8ff; color: #7c3aed;',
                        ];
                        @endphp
                        <tr>
                            <td class="px-4 border-0 small fw-medium">
                                {{ \Carbon\Carbon::parse($appt->appointment_date)->format('d M Y') }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                            </td>
                            <td class="px-4 border-0 small fw-medium">
                                {{ $appt->patient_name }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ ucfirst($appt->patient_gender ?? '—') }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ $appt->patient_blood_type ?? '—' }}
                            </td>
                            <td class="px-4 border-0 small fw-medium">
                                {{ $appt->doctor_name }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ $appt->doctor_specialty ?? 'General' }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ $appt->service_name ?? '—' }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ $appt->service_price ? '₱' . number_format($appt->service_price, 2) : '—' }}
                            </td>
                            <td class="px-4 border-0">
                                <span class="badge rounded-pill px-3 py-2"
                                    style="{{ $statusStyles[$appt->status] ?? '' }} font-size: 0.7rem;">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ Str::limit($appt->notes, 30) ?? '—' }}
                            </td>
                            <td class="px-4 border-0 small text-muted">
                                {{ $appt->booked_at ? \Carbon\Carbon::parse($appt->booked_at)->format('d M Y h:i A') :
                                '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted border-0">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-25"></i>
                                No appointments found for this period.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($appointments, 'hasPages') && $appointments->hasPages())
            <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Showing {{ $appointments->firstItem() }} to {{ $appointments->lastItem() }}
                    of {{ $appointments->total() }} appointments
                </small>
                {{ $appointments->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Doctor Performance (vw_doctor_performance) ── --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill px-2 py-1"
                            style="background: #e0f7fc; color: #0097a7; font-size: 0.7rem;">
                            DATABASE VIEW
                        </span>
                        <span class="text-muted small font-monospace">vw_doctor_performance</span>
                    </div>
                    <h6 class="fw-bold mb-0">Doctor Performance</h6>
                    <small class="text-muted">Appointment stats per doctor</small>
                </div>
                <div class="card-body p-0 pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Doctor</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Specialty</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Total</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Completed</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Pending</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Confirmed</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Cancelled</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Rescheduled
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Unique
                                        Patients</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($doctorPerformance as $doc)
                                <tr>
                                    <td class="px-4 border-0">
                                        <div class="fw-medium small">{{ $doc->doctor_name }}</div>
                                    </td>
                                    <td class="px-4 border-0">
                                        <small class="text-muted">{{ $doc->specialty ?? 'General' }}</small>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #0d6efd;">{{ $doc->total_appointments
                                            }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #198754;">{{ $doc->completed }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #e6a800;">{{ $doc->pending }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #0d6efd;">{{ $doc->confirmed }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #dc3545;">{{ $doc->cancelled }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #7c3aed;">{{ $doc->rescheduled ?? 0
                                            }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #0dcaf0;">{{ $doc->unique_patients ??
                                            0 }}</span>
                                    </td>
                                </tr>
                                @empty
                                <table>
                                    <td colspan="9" class="text-center py-4 text-muted border-0">
                                        No doctor data available.
                                    </td>
                                    </tr>
                                    @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Patient Visit History (vw_patient_visit_history) ── --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill px-2 py-1"
                            style="background: #e8f5ee; color: #198754; font-size: 0.7rem;">
                            DATABASE VIEW
                        </span>
                        <span class="text-muted small font-monospace">vw_patient_visit_history</span>
                    </div>
                    <h6 class="fw-bold mb-0">Patient Visit History</h6>
                    <small class="text-muted">Patient visit statistics</small>
                </div>
                <div class="card-body p-0 pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Patient</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Gender</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Blood Type</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Age</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Total Visits
                                    </th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Completed</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Cancelled</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Pending</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">Last Visit</th>
                                    <th class="px-4 py-3 text-muted fw-normal small border-0">First Visit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patientVisits as $patient)
                                <tr>
                                    <td class="px-4 border-0">
                                        <div class="fw-medium small">{{ $patient->patient_name }}</div>
                                    </td>
                                    <td class="px-4 border-0">
                                        <small class="text-muted">{{ ucfirst($patient->gender ?? '—') }}</small>
                                    </td>
                                    <td class="px-4 border-0">
                                        <small class="text-muted">{{ $patient->blood_type ?? '—' }}</small>
                                    </td>
                                    <td class="px-4 border-0">
                                        <small class="text-muted">{{ $patient->age ?? '—' }}</small>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #0d6efd;">{{ $patient->total_visits
                                            }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #198754;">{{
                                            $patient->completed_visits }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #dc3545;">{{
                                            $patient->cancelled_visits }}</span>
                                    </td>
                                    <td class="px-4 border-0 text-center">
                                        <span class="fw-bold small" style="color: #e6a800;">{{ $patient->pending_visits
                                            }}</span>
                                    </td>
                                    <td class="px-4 border-0">
                                        <small class="text-muted">
                                            {{ $patient->last_visit_date ?
                                            \Carbon\Carbon::parse($patient->last_visit_date)->format('d M Y') : '—' }}
                                        </small>
                                    </td>
                                    <td class="px-4 border-0">
                                        <small class="text-muted">
                                            {{ $patient->first_visit_date ?
                                            \Carbon\Carbon::parse($patient->first_visit_date)->format('d M Y') : '—' }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted border-0">
                                        No patient data available.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Print Style --}}
    <style>
        @media print {

            .sidebar,
            .top-navbar,
            .btn,
            form,
            nav {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 1px solid #dee2e6 !important;
            }

            table {
                font-size: 10pt !important;
            }

            th,
            td {
                padding: 4px !important;
            }
        }
    </style>

</x-app-layout>