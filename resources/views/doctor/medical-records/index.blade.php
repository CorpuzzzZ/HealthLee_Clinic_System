<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-primary fs-3">Medical Records</h5>
                <small class="text-muted">Patient consultation records you have created</small>
            </div>
            <a href="{{ route('doctor.medical-records.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #0d6efd !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e7f1ff;">
                            <i class="bi bi-file-medical-fill fs-4" style="color: #0d6efd;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-3 d-flex justify-content-end text-primary">{{ $total }}</h3>
                    <p class="text-muted mb-0 small">Total Records</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #198754 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e8f5ee;">
                            <i class="bi bi-people-fill fs-4" style="color: #198754;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">Patients</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-3 d-flex justify-content-end text-success">{{
                        $records->groupBy('appointment.patient_id')->count() }}</h3>
                    <p class="text-muted mb-0 small">Unique Patients</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100"
                style="border-left: 4px solid #ffc107 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #fff8e1;">
                            <i class="bi bi-calendar-check fs-4" style="color: #ffc107;"></i>
                        </div>
                        <span class="badge rounded-pill"
                            style="background: #fff8e1; color: #e6a800; font-size: 0.75rem;">Average</span>
                    </div>
                    <h3 class="fw-bold mb-1 fs-3 d-flex justify-content-end text-warning">{{ round($total /
                        max($records->groupBy('appointment.patient_id')->count(), 1)) }}</h3>
                    <p class="text-muted mb-0 small">Records per Patient</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('doctor.medical-records.index') }}" class="row g-2 align-items-end">
                <div class="row">
                    <div class="col">
                        <label class="form-label small text-muted mb-1">Search by Patient Name</label>
                        <div class="input-group" style="width: 100%">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-search text-muted" style="font-size: 0.85rem;"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control border-start-0 ps-0" placeholder="Search patient...">
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-funnel me-1"></i> Search
                            </button>
                            <a href="{{ route('doctor.medical-records.index') }}"
                                class="btn btn-outline-secondary px-3">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Grouped Records by Patient --}}
    @php
    $groupedRecords = $records->groupBy('appointment.patient_id');
    @endphp

    @forelse($groupedRecords as $patientId => $patientRecords)
    @php
    $firstRecord = $patientRecords->first();
    $patient = $firstRecord->appointment->patient;
    $recordCount = $patientRecords->count();
    $latestRecord = $patientRecords->sortByDesc('created_at')->first();
    @endphp

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        {{-- Patient Header --}}
        <div class="card-header border-0 px-4 py-3"
            style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px 12px 0 0; cursor: pointer;"
            onclick="togglePatientRecords({{ $patientId }})">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                        style="width: 48px; height: 48px; font-size: 1rem; background: #198754;">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0,
                        1)) }}
                    </div>
                    <div>
                        <div class="fw-bold fs-6">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </div>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <small class="text-muted">
                                <i class="bi bi-gender-ambiguous me-1"></i>
                                {{ ucfirst($patient->gender ?? 'Not specified') }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                Age: {{ $patient->birthdate?->age ?? 'N/A' }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-telephone me-1"></i>
                                {{ $patient->user->contact->contact_number ?? 'No contact' }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-bold text-primary">{{ $recordCount }}</div>
                        <small class="text-muted">Record(s)</small>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">Last Record</div>
                        <small class="text-muted">{{ $latestRecord->created_at->format('d M Y') }}</small>
                    </div>
                    <i class="bi bi-chevron-down text-muted" id="icon-{{ $patientId }}"
                        style="transition: transform 0.2s;"></i>
                </div>
            </div>
        </div>

        {{-- Records Table (collapsible) --}}
        <div id="patient-records-{{ $patientId }}" class="collapse show">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr style="background: #f8f9fa;">
                                <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                                <th class="px-4 py-3 text-muted fw-normal small border-0">Appointment Date</th>
                                <th class="px-4 py-3 text-muted fw-normal small border-0">Diagnosis</th>
                                <th class="px-4 py-3 text-muted fw-normal small border-0">Treatment</th>
                                <th class="px-4 py-3 text-muted fw-normal small border-0">Record Created</th>
                                <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patientRecords as $index => $record)
                            <tr>
                                <td class="px-4 border-0 text-muted small">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-4 border-0">
                                    <small class="fw-medium">
                                        {{ $record->appointment->appointment_date->format('d M Y') }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($record->appointment->appointment_time)->format('h:i
                                        A') }}
                                    </small>
                                </td>
                                <td class="px-4 border-0">
                                    <small class="text-dark">{{ Str::limit($record->diagnosis, 50) }}</small>
                                </td>
                                <td class="px-4 border-0">
                                    <small class="text-muted">{{ Str::limit($record->treatment, 40) }}</small>
                                </td>
                                <td class="px-4 border-0">
                                    <small class="text-muted">{{ $record->created_at->format('d M Y') }}</small>
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
                                                <a class="dropdown-item rounded-2 py-2"
                                                    href="{{ route('doctor.medical-records.show', $record) }}">
                                                    <i class="bi bi-eye me-2 text-primary"></i> View
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider my-1">
                                            </li>
                                            <li>
                                                <a class="dropdown-item rounded-2 py-2"
                                                    href="{{ route('doctor.medical-records.edit', $record) }}">
                                                    <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-file-medical fs-1 d-block mb-2 opacity-25"></i>
            <div class="fw-medium">No medical records found</div>
            <small>Records are created from completed appointments.</small>
        </div>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if(method_exists($records, 'hasPages') && $records->hasPages())
    <div class="d-flex align-items-center justify-content-between mt-4">
        <small class="text-muted">
            Showing {{ $records->firstItem() }} to {{ $records->lastItem() }}
            of {{ $records->total() }} records
        </small>
        {{ $records->links() }}
    </div>
    @endif

    <script>
        function togglePatientRecords(patientId) {
            const element = document.getElementById('patient-records-' + patientId);
            const icon = document.getElementById('icon-' + patientId);
            
            if (element.classList.contains('show')) {
                element.classList.remove('show');
                icon.style.transform = 'rotate(-90deg)';
            } else {
                element.classList.add('show');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>

    <style>
        .card-header {
            transition: background 0.2s ease;
        }

        .card-header:hover {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%) !important;
        }

        .collapse {
            transition: all 0.2s ease;
        }
    </style>

</x-app-layout>