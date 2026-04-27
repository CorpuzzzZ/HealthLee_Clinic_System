<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">My Appointments</h5>
                <small class="text-muted">View and manage your scheduled appointments</small>
            </div>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Stats ── --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #fff8e1;">
                            <i class="bi bi-sun fs-4" style="color: #ffc107;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #fff8e1; color: #e6a800; font-size: 0.75rem;">Now</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalToday }}</h3>
                    <p class="text-muted mb-0 small">Today</p>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
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
        <div class="col-sm-4">
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
    </div>

    {{-- ── Filters ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('doctor.appointments.index') }}"
                  class="row g-2 align-items-end">

                {{-- Status filter --}}
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Filter by Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed"   {{ request('status') === 'confirmed'   ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled"   {{ request('status') === 'cancelled'   ? 'selected' : '' }}>Cancelled</option>
                        <option value="rescheduled" {{ request('status') === 'rescheduled' ? 'selected' : '' }}>Rescheduled</option>
                    </select>
                </div>

                {{-- Date filter --}}
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Filter by Date</label>
                    <input type="date" name="date"
                           value="{{ request('date') }}"
                           class="form-control">
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('doctor.appointments.index') }}"
                       class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Appointments Table ── --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Patient</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Date & Time</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Status</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appointment)
                            @php
                                $statusStyles = [
                                    'pending'     => 'background: #fff8e1; color: #e6a800;',
                                    'confirmed'   => 'background: #e7f1ff; color: #0d6efd;',
                                    'completed'   => 'background: #e8f5ee; color: #198754;',
                                    'cancelled'   => 'background: #fdecea; color: #dc3545;',
                                    'rescheduled' => 'background: #f3e8ff; color: #7c3aed;',
                                ];
                                $statusIcons = [
                                    'pending'     => 'bi-clock',
                                    'confirmed'   => 'bi-check-circle',
                                    'completed'   => 'bi-check-circle-fill',
                                    'cancelled'   => 'bi-x-circle',
                                    'rescheduled' => 'bi-arrow-repeat',
                                ];
                            @endphp
                            <tr>
                                <td class="px-4 border-0 text-muted small">
                                    {{ ($appointments->currentPage() - 1) * $appointments->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-4 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                             style="width: 38px; height: 38px; font-size: 0.75rem; background: #198754;">
                                            {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}{{ strtoupper(substr($appointment->patient->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                            </div>
                                            <small class="text-muted">
                                                {{ ucfirst($appointment->patient->gender ?? '—') }},
                                                {{ $appointment->patient->age ?? '—' }} yrs
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 border-0">
                                    <div class="fw-medium small">
                                        {{ $appointment->appointment_date->format('d M Y') }}
                                    </div>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                    </small>
                                </td>
                                <td class="px-4 border-0">
                                    <span class="badge rounded-pill px-3 py-2"
                                          style="{{ $statusStyles[$appointment->status] ?? '' }} font-size: 0.75rem;">
                                        <i class="bi {{ $statusIcons[$appointment->status] ?? 'bi-circle' }} me-1"></i>
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-4 border-0 text-center">
                                    <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                       class="btn btn-sm btn-outline-primary rounded-3 px-3">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted border-0">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                                    <div class="fw-medium">No appointments found</div>
                                    <small>Try adjusting your filters.</small>
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

</x-app-layout>