<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Patient Records</h5>
                <small class="text-muted">View and manage patient medical information</small>
            </div>
            <span class="badge rounded-pill bg-primary px-3 py-2">
                {{ $patients->total() }} Patients
            </span>
        </div>
    </x-slot>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Search & Filter --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('admin.patient-records.index') }}" class="row g-2 align-items-end">

                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">Search by Name</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted" style="font-size: 0.85rem;"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-start-0 ps-0" placeholder="Search patient name...">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Blood Type</label>
                    <select name="blood_type" class="form-select">
                        <option value="">All Blood Types</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bt)
                        <option value="{{ $bt }}" {{ request('blood_type')===$bt ? 'selected' : '' }}>
                            {{ $bt }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.patient-records.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Patient</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Birthdate</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Blood Type</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Height</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Weight</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Record</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                        <tr>
                            <td class="px-4 border-0 text-muted small">
                                {{ ($patients->currentPage() - 1) * $patients->perPage() + $loop->iteration }}
                            </td>

                            {{-- Patient --}}
                            <td class="px-4 border-0">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                        style="width: 38px; height: 38px; font-size: 0.75rem; background: #198754;">
                                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{
                                        strtoupper(substr($patient->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-medium small">
                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                        </div>
                                        <small class="text-muted">
                                            {{ ucfirst($patient->gender ?? '—') }}, {{ $patient->age ?? '—' }} yrs
                                        </small>
                                    </div>
                                </div>
                            </td>

                            {{-- Birthdate --}}
                            <td class="px-4 border-0">
                                <small class="text-muted">
                                    {{ $patient->birthdate ? $patient->birthdate->format('d M Y') : '—' }}
                                </small>
                            </td>

                            {{-- Blood Type --}}
                            <td class="px-4 border-0">
                                @if($patient->blood_type)
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #fdecea; color: #dc3545; font-size: 0.75rem;">
                                    <i class="bi bi-droplet-fill me-1"></i>
                                    {{ $patient->blood_type }}
                                </span>
                                @else
                                <span class="text-muted small">—</span>
                                @endif
                            </td>

                            {{-- Height --}}
                            <td class="px-4 border-0">
                                <small class="text-muted">
                                    {{ $patient->height ? $patient->height . ' cm' : '—' }}
                                </small>
                            </td>

                            {{-- Weight --}}
                            <td class="px-4 border-0">
                                <small class="text-muted">
                                    {{ $patient->weight ? $patient->weight . ' kg' : '—' }}
                                </small>
                            </td>

                            {{-- Record Status --}}
                            <td class="px-4 border-0">
                                @if($patient->birthdate && $patient->blood_type && $patient->height && $patient->weight)
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #e8f5ee; color: #198754; font-size: 0.7rem;">
                                    <i class="bi bi-check-circle me-1"></i> Complete
                                </span>
                                @else
                                <span class="badge rounded-pill px-3 py-2"
                                    style="background: #fff8e1; color: #e6a800; font-size: 0.7rem;">
                                    <i class="bi bi-exclamation-circle me-1"></i> Incomplete
                                </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 border-0 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1 border-0"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="background:#f1f3f5;" title="Actions">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3"
                                        style="min-width:160px;">
                                        <li>
                                            <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2"
                                                href="{{ route('admin.patient-records.show', $patient) }}">
                                                <i class="bi bi-eye text-primary" style="width:16px;"></i>
                                                <span class="small">View Record</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2"
                                                href="{{ route('admin.patient-records.edit', $patient) }}">
                                                <i class="bi bi-pencil text-warning" style="width:16px;"></i>
                                                <span class="small">Edit Record</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted border-0">
                                <i class="bi bi-clipboard2-x fs-1 d-block mb-2 opacity-25"></i>
                                <div class="fw-medium">No patients found</div>
                                <small>Try adjusting your search or filter.</small>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($patients, 'hasPages') && $patients->hasPages())
            <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
                <small class="text-muted">
                    Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }}
                    of {{ $patients->total() }} patients
                </small>
                {{ $patients->links() }}
            </div>
            @endif

        </div>
    </div>

</x-app-layout>