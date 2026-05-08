<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-primary fs-3">My Availability</h5>
                <small class="text-muted">Manage your available dates and time slots</small>
            </div>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal"
                data-bs-target="#addSlotModal">
                <i class="bi bi-plus-lg me-1"></i> Add Time Slot
            </button>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Validation Errors (shown above cards so user always sees them) ── --}}
    @if(session('_add_error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        <strong>Add Slot Failed:</strong> {{ session('_add_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('_edit_error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        <strong>Update Slot Failed:</strong> {{ session('_edit_error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Stats ── --}}
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #0d6efd !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e7f1ff;">
                            <i class="bi bi-calendar3 fs-4" style="color: #0d6efd;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">All</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $availabilities->total() }}</h3>
                    <p class="text-muted mb-0 small">Total Slots</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #198754 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e8f5ee;">
                            <i class="bi bi-calendar-check fs-4" style="color: #198754;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">Future</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $upcomingCount }}</h3>
                    <p class="text-muted mb-0 small">Upcoming Slots</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #ffc107 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #fff8e1;">
                            <i class="bi bi-sun fs-4" style="color: #ffc107;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #fff8e1; color: #e6a800; font-size: 0.75rem;">Now</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $todayCount }}</h3>
                    <p class="text-muted mb-0 small">Today's Slots</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #6c757d !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #f8f9fa;">
                            <i class="bi bi-clock-history fs-4" style="color: #6c757d;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #f8f9fa; color: #6c757d; font-size: 0.75rem;">Done</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $pastCount }}</h3>
                    <p class="text-muted mb-0 small">Past Slots</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Availability Table ── --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h6 class="fw-bold mb-0">Time Slots</h6>
            <small class="text-muted">All your scheduled availability</small>
        </div>
        <div class="card-body p-0 pt-3" style="overflow: visible;">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Date</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Day</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Start Time</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">End Time</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Duration</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Status</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($availabilities as $slot)
                        @php
                        $start = \Carbon\Carbon::parse($slot->start_time);
                        $end = \Carbon\Carbon::parse($slot->end_time);
                        $duration = $start->diffInMinutes($end);
                        $hours = intdiv($duration, 60);
                        $mins = $duration % 60;
                        $durationLabel = ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                        @endphp
                        <tr>
                            <td class="px-4 border-0 text-muted small">
                                {{ ($availabilities->currentPage() - 1) * $availabilities->perPage() + $loop->iteration
                                }}
                            </td>
                            <td class="px-4 border-0">
                                <div class="fw-medium small">
                                    {{ \Carbon\Carbon::parse($slot->available_date)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-4 border-0">
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($slot->available_date)->format('l') }}
                                </small>
                            </td>
                            <td class="px-4 border-0">
                                <span class="fw-medium small">{{ $start->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 border-0">
                                <span class="fw-medium small">{{ $end->format('h:i A') }}</span>
                            </td>
                            <td class="px-4 border-0">
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">
                                    <i class="bi bi-hourglass-split me-1"></i>{{ $durationLabel }}
                                </span>
                            </td>
                            <td class="px-4 border-0">
                                @php $availDate = \Carbon\Carbon::parse($slot->available_date); @endphp
                                @if($availDate->isPast() && !$availDate->isToday())
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #f8f9fa; color: #6c757d; font-size: 0.75rem;">
                                    <i class="bi bi-clock-history me-1"></i> Past
                                </span>
                                @elseif($availDate->isToday())
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #fff8e1; color: #e6a800; font-size: 0.75rem;">
                                    <i class="bi bi-sun me-1"></i> Today
                                </span>
                                @else
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">
                                    <i class="bi bi-check-circle me-1"></i> Upcoming
                                </span>
                                @endif
                            </td>
                            <td class="px-4 border-0 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary rounded-3 px-2" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0"
                                        style="border-radius: 10px; min-width: 140px;">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2 py-2" onclick="openEditModal(
                                                            {{ $slot->id }},
                                                            '{{ \Carbon\Carbon::parse($slot->available_date)->format('Y-m-d') }}',
                                                            '{{ $start->format('H:i') }}',
                                                            '{{ $end->format('H:i') }}'
                                                        )">
                                                <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider my-1">
                                        </li>
                                        <li>
                                            <form method="POST"
                                                action="{{ route('doctor.availabilities.destroy', $slot) }}"
                                                onsubmit="return confirm('Delete this time slot?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item rounded-2 py-2 text-danger">
                                                    <i class="bi bi-trash me-2"></i> Delete
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted border-0">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-25"></i>
                                <div class="fw-medium">No time slots added yet</div>
                                <small>Click "Add Time Slot" to set your availability.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($availabilities, 'hasPages') && $availabilities->hasPages())
            <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Showing {{ $availabilities->firstItem() }} to {{ $availabilities->lastItem() }}
                    of {{ $availabilities->total() }} slots
                </small>
                {{ $availabilities->links() }}
            </div>
            @endif

        </div>
    </div>

    {{-- ── Add Slot Modal ── --}}
    <div class="modal fade" id="addSlotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-calendar-plus me-2 text-primary"></i> Add Time Slot
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-3">

                    @if(session('_add_error'))
                    <div class="alert alert-danger rounded-3 border-0 mb-3 py-2">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        {{ session('_add_error') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('doctor.availabilities.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Available Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="available_date" value="{{ old('available_date') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="form-control @error('available_date') is-invalid @enderror" required>
                            @error('available_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="start_time" value="{{ old('start_time') }}"
                                    class="form-control @error('start_time') is-invalid @enderror" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="end_time" value="{{ old('end_time') }}"
                                    class="form-control @error('end_time') is-invalid @enderror" required>
                                @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="bi bi-plus-lg me-1"></i> Add Slot
                            </button>
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Edit Slot Modal ── --}}
    <div class="modal fade" id="editSlotModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-calendar-check me-2 text-warning"></i> Edit Time Slot
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4 pt-3">

                    @if(session('_edit_error'))
                    <div class="alert alert-danger rounded-3 border-0 mb-3 py-2">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        {{ session('_edit_error') }}
                    </div>
                    @endif

                    <form method="POST" id="editSlotForm" action="">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Available Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" id="edit_available_date" name="available_date" class="form-control"
                                required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    Start Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" id="edit_start_time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">
                                    End Time <span class="text-danger">*</span>
                                </label>
                                <input type="time" id="edit_end_time" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4 rounded-3">
                                <i class="bi bi-check-lg me-1"></i> Update Slot
                            </button>
                            <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">
                                Cancel
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, date, startTime, endTime) {
            document.getElementById('editSlotForm').action = "{{ url('doctor/availabilities') }}/" + id;
            document.getElementById('edit_available_date').value = date;
            document.getElementById('edit_start_time').value     = startTime;
            document.getElementById('edit_end_time').value       = endTime;
            new bootstrap.Modal(document.getElementById('editSlotModal')).show();
        }

        // ── Re-open the correct modal on error ──
        @if(session('_edit_slot_id'))
            openEditModal(
                {{ session('_edit_slot_id') }},
                '{{ session('_edit_date') }}',
                '{{ session('_edit_start') }}',
                '{{ session('_edit_end') }}'
            );
        @elseif(session('_add_error') || old('available_date'))
            new bootstrap.Modal(document.getElementById('addSlotModal')).show();
        @endif
    </script>

</x-app-layout>