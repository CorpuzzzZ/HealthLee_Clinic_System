<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Find a Doctor</h5>
                <small class="text-muted">Search and filter available doctors</small>
            </div>
            <span class="badge rounded-pill bg-primary px-3 py-2">
                {{ $doctors->total() }} Doctor(s) Found
            </span>
        </div>
    </x-slot>

    {{-- ── Search & Filter ── --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('patient.doctors.index') }}" class="row g-2 align-items-end">

                {{-- Search by name --}}
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Search by Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted" style="font-size: 0.85rem;"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Doctor name...">
                    </div>
                </div>

                {{-- Filter by specialty --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Specialty</label>
                    <select name="specialty" class="form-select">
                        <option value="">All Specialties</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty }}"
                                    {{ request('specialty') === $specialty ? 'selected' : '' }}>
                                {{ $specialty }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter by date --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Available On</label>
                    <input type="date"
                           name="date"
                           value="{{ request('date') }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="form-control">
                </div>

                {{-- Buttons --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-3 w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('patient.doctors.index') }}"
                       class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Doctor Cards ── --}}
    @if($doctors->isEmpty())
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-person-badge fs-1 d-block mb-2 opacity-25"></i>
                <div class="fw-medium">No doctors found</div>
                <small>Try adjusting your search or filters.</small>
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach($doctors as $doctor)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body p-4">

                            {{-- Doctor Avatar & Name --}}
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                     style="width: 52px; height: 52px; font-size: 1rem; background: #0dcaf0;">
                                    {{ strtoupper(substr($doctor->first_name, 0, 1)) }}{{ strtoupper(substr($doctor->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">
                                        Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                    </div>
                                    <span class="badge rounded-pill px-2 py-1"
                                          style="background: #e0f7fc; color: #0097a7; font-size: 0.7rem;">
                                        {{ $doctor->specialty ?? 'General' }}
                                    </span>
                                </div>
                            </div>

                            <hr class="my-3">

                            {{-- Info --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                        <small class="text-muted d-block mb-1">Gender</small>
                                        <span class="fw-medium small">{{ ucfirst($doctor->gender ?? '—') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3" style="background: #f8f9fa;">
                                        <small class="text-muted d-block mb-1">Available Slots</small>
                                        <span class="fw-medium small" style="color: #198754;">
                                            {{ $doctor->availabilities->where('available_date', '>=', today())->count() }} slot(s)
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Next Available --}}
                            @php
                                $nextSlot = $doctor->availabilities
                                    ->where('available_date', '>=', today()->toDateString())
                                    ->sortBy('available_date')
                                    ->first();
                            @endphp

                            @if($nextSlot)
                                <div class="p-2 rounded-3 mb-3"
                                     style="background: #e8f5ee; border-left: 3px solid #198754;">
                                    <small class="text-muted d-block mb-1">Next Available</small>
                                    <span class="fw-medium small" style="color: #198754;">
                                        {{ $nextSlot->available_date->format('d M Y') }}
                                        &bull;
                                        {{ \Carbon\Carbon::parse($nextSlot->start_time)->format('h:i A') }}
                                        —
                                        {{ \Carbon\Carbon::parse($nextSlot->end_time)->format('h:i A') }}
                                    </span>
                                </div>
                            @else
                                <div class="p-2 rounded-3 mb-3"
                                     style="background: #f8f9fa; border-left: 3px solid #dee2e6;">
                                    <small class="text-muted">No upcoming availability set.</small>
                                </div>
                            @endif

                            {{-- View Profile Button --}}
                            <a href="{{ route('patient.doctors.show', $doctor) }}"
                               class="btn btn-primary w-100 rounded-3 btn-sm py-2">
                                <i class="bi bi-eye me-1"></i> View Profile & Book
                            </a>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($doctors->hasPages())
            <div class="d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Showing {{ $doctors->firstItem() }} to {{ $doctors->lastItem() }}
                    of {{ $doctors->total() }} doctors
                </small>
                {{ $doctors->links() }}
            </div>
        @endif

    @endif

</x-app-layout>