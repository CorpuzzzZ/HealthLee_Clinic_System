<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Medical Records</h5>
                <small class="text-muted">Patient consultation records you have created</small>
            </div>
            <a href="{{ route('doctor.medical-records.create') }}"
               class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="bi bi-plus-lg me-1"></i> Add Record
            </a>
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
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0d6efd !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e7f1ff;">
                            <i class="bi bi-file-medical-fill fs-4" style="color: #0d6efd;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">All</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $total }}</h3>
                    <p class="text-muted mb-0 small">Total Records</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('doctor.medical-records.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted mb-1">Search by Patient Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted" style="font-size: 0.85rem;"></i>
                        </span>
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Search patient...">
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-funnel me-1"></i> Search
                    </button>
                    <a href="{{ route('doctor.medical-records.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Records Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Patient</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Appointment Date</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Diagnosis</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Treatment</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Created</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                {{-- # --}}
                                <td class="px-4 border-0 text-muted small">
                                    {{ ($records->currentPage() - 1) * $records->perPage() + $loop->iteration }}
                                </td>

                                {{-- Patient --}}
                                <td class="px-4 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                             style="width: 36px; height: 36px; font-size: 0.75rem; background: #198754;">
                                            {{ strtoupper(substr($record->patient->first_name, 0, 1)) }}{{ strtoupper(substr($record->patient->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium small">
                                                {{ $record->patient->first_name }} {{ $record->patient->last_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Appointment Date --}}
                                <td class="px-4 border-0">
                                    <small class="text-muted">
                                        {{ $record->appointment->appointment_date->format('d M Y') }}
                                    </small>
                                </td>

                                {{-- Diagnosis --}}
                                <td class="px-4 border-0">
                                    <small class="text-dark">{{ Str::limit($record->diagnosis, 40) }}</small>
                                </td>

                                {{-- Treatment --}}
                                <td class="px-4 border-0">
                                    <small class="text-muted">{{ Str::limit($record->treatment, 40) }}</small>
                                </td>

                                {{-- Created --}}
                                <td class="px-4 border-0">
                                    <small class="text-muted">{{ $record->created_at->format('d M Y') }}</small>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 border-0 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <a href="{{ route('doctor.medical-records.show', $record) }}"
                                           class="btn btn-sm btn-outline-primary rounded-3 px-3"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('doctor.medical-records.edit', $record) }}"
                                           class="btn btn-sm btn-outline-warning rounded-3 px-3"
                                           title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted border-0">
                                    <i class="bi bi-file-medical fs-1 d-block mb-2 opacity-25"></i>
                                    <div class="fw-medium">No medical records found</div>
                                    <small>Records are created from completed appointments.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($records, 'hasPages') && $records->hasPages())
                <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
                    <small class="text-muted">
                        Showing {{ $records->firstItem() }} to {{ $records->lastItem() }}
                        of {{ $records->total() }} records
                    </small>
                    {{ $records->links() }}
                </div>
            @endif

        </div>
    </div>

</x-app-layout>