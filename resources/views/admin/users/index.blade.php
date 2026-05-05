<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Manage Users</h5>
                <small class="text-muted">All registered accounts in the system</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill bg-primary px-3 py-2">
                    {{ $users->total() }} Total Users
                </span>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-person-plus me-1"></i> Add User
                </a>
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
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filter & Search --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body px-4 py-3">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted" style="font-size: 0.85rem;"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-start-0 ps-0" placeholder="Search by email...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Filter by Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role')==='admin' ? 'selected' : '' }}>Admin</option>
                        <option value="doctor" {{ request('role')==='doctor' ? 'selected' : '' }}>Doctor</option>
                        <option value="patient" {{ request('role')==='patient' ? 'selected' : '' }}>Patient</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
    $admins = $users->getCollection()->where('role', 'admin');
    $doctors = $users->getCollection()->where('role', 'doctor');
    $patients = $users->getCollection()->where('role', 'patient');
    @endphp

    {{-- ── Admins Table ── --}}
    @if($admins->isNotEmpty() || !request('role') || request('role') === 'admin')
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header border-0 px-4 pt-4 pb-3" style="background: #f0f5ff; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:32px;height:32px;background:#0d6efd;">
                    <i class="bi bi-shield-fill text-white" style="font-size:0.8rem;"></i>
                </span>
                <div>
                    <span class="fw-bold text-dark">Admins</span>
                    <span class="badge rounded-pill bg-primary ms-2" style="font-size:0.7rem;">
                        {{ $admins->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($admins->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-shield fs-1 d-block mb-2 opacity-25"></i>
                <small>No admins found.</small>
            </div>
            @else
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Name</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Email</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Registered</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins->values() as $i => $user)
                        <tr>
                            <td class="px-4 border-0 text-muted small">{{ $i + 1 }}</td>
                            <td class="px-4 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                        style="width:38px;height:38px;font-size:0.8rem;background:#0d6efd;">
                                        {{ strtoupper(substr($user->email, 0, 2)) }}
                                    </div>
                                    <span class="fw-medium small">
                                        {{ $user->admin?->first_name }} {{ $user->admin?->last_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 border-0"><small class="text-muted">{{ $user->email }}</small></td>
                            <td class="px-4 border-0"><small class="text-muted">{{ $user->created_at->format('d M Y')
                                    }}</small></td>
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
                                                href="{{ route('admin.users.show', $user) }}">
                                                <i class="bi bi-eye text-primary" style="width:16px;"></i>
                                                <span class="small">View Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2"
                                                href="{{ route('admin.users.edit', $user) }}">
                                                <i class="bi bi-pencil text-warning" style="width:16px;"></i>
                                                <span class="small">Edit</span>
                                            </a>
                                        </li>
                                        @if(Auth::id() !== $user->id)
                                        <li>
                                            <hr class="dropdown-divider my-1">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 text-danger">
                                                    <i class="bi bi-trash" style="width:16px;"></i>
                                                    <span class="small">Delete</span>
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Doctors Table ── --}}
    @if($doctors->isNotEmpty() || !request('role') || request('role') === 'doctor')
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header border-0 px-4 pt-4 pb-3" style="background: #e0f7fc; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:32px;height:32px;background:#0097a7;">
                    <i class="bi bi-person-badge-fill text-white" style="font-size:0.8rem;"></i>
                </span>
                <div>
                    <span class="fw-bold text-dark">Doctors</span>
                    <span class="badge rounded-pill ms-2" style="font-size:0.7rem;background:#0097a7;color:#fff;">
                        {{ $doctors->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($doctors->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-person-badge fs-1 d-block mb-2 opacity-25"></i>
                <small>No doctors found.</small>
            </div>
            @else
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Name</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Email</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Specialty</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Services</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Registered</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors->values() as $i => $user)
                        <tr>
                            <td class="px-4 border-0 text-muted small">{{ $i + 1 }}</td>
                            <td class="px-4 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                        style="width:38px;height:38px;font-size:0.8rem;background:#0097a7;">
                                        {{ strtoupper(substr($user->email, 0, 2)) }}
                                    </div>
                                    <span class="fw-medium small">
                                        Dr. {{ $user->doctor?->first_name }} {{ $user->doctor?->last_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 border-0"><small class="text-muted">{{ $user->email }}</small></td>
                            <td class="px-4 border-0">
                                <small class="text-muted">
                                    {{ $user->doctor?->specialty ?? '—' }}
                                </small>
                            </td>
                            <td class="px-4 border-0">
                                @php $svcCount = $user->doctor?->services?->count() ?? 0; @endphp
                                @if($svcCount > 0)
                                <span class="badge rounded-pill px-2 py-1"
                                    style="background:#e0f7fc;color:#0097a7;font-size:0.72rem;">
                                    {{ $svcCount }} service{{ $svcCount > 1 ? 's' : '' }}
                                </span>
                                @else
                                <small class="text-muted">—</small>
                                @endif
                            </td>
                            <td class="px-4 border-0"><small class="text-muted">{{ $user->created_at->format('d M Y')
                                    }}</small></td>
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
                                                href="{{ route('admin.users.show', $user) }}">
                                                <i class="bi bi-eye text-primary" style="width:16px;"></i>
                                                <span class="small">View Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2"
                                                href="{{ route('admin.users.edit', $user) }}">
                                                <i class="bi bi-pencil text-warning" style="width:16px;"></i>
                                                <span class="small">Edit</span>
                                            </a>
                                        </li>
                                        @if(Auth::id() !== $user->id)
                                        <li>
                                            <hr class="dropdown-divider my-1">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 text-danger">
                                                    <i class="bi bi-trash" style="width:16px;"></i>
                                                    <span class="small">Delete</span>
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Patients Table ── --}}
    @if($patients->isNotEmpty() || !request('role') || request('role') === 'patient')
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header border-0 px-4 pt-4 pb-3" style="background: #e8f5ee; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span class="rounded-circle d-flex align-items-center justify-content-center"
                    style="width:32px;height:32px;background:#198754;">
                    <i class="bi bi-person-badge-fill text-white" style="font-size:0.8rem;"></i>
                </span>
                <div>
                    <span class="fw-bold text-dark">Patients</span>
                    <span class="badge rounded-pill ms-2" style="font-size:0.7rem;background:#198754;color:#fff;">
                        {{ $patients->count() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($patients->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-person-heart fs-1 d-block mb-2 opacity-25"></i>
                <small>No patients found.</small>
            </div>
            @else
            <div class="table-responsive" style="overflow:visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background:#f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Name</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Email</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Blood Type</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Registered</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients->values() as $i => $user)
                        <tr>
                            <td class="px-4 border-0 text-muted small">{{ $i + 1 }}</td>
                            <td class="px-4 border-0">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                        style="width:38px;height:38px;font-size:0.8rem;background:#198754;">
                                        {{ strtoupper(substr($user->email, 0, 2)) }}
                                    </div>
                                    <span class="fw-medium small">
                                        {{ $user->patient?->first_name }} {{ $user->patient?->last_name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 border-0"><small class="text-muted">{{ $user->email }}</small></td>
                            <td class="px-4 border-0">
                                @if($user->patient?->blood_type)
                                <span class="badge rounded-pill px-2 py-1"
                                    style="background:#e8f5ee;color:#198754;font-size:0.72rem;">
                                    {{ $user->patient->blood_type }}
                                </span>
                                @else
                                <small class="text-muted">—</small>
                                @endif
                            </td>
                            <td class="px-4 border-0"><small class="text-muted">{{ $user->created_at->format('d M Y')
                                    }}</small></td>
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
                                                href="{{ route('admin.users.show', $user) }}">
                                                <i class="bi bi-eye text-primary" style="width:16px;"></i>
                                                <span class="small">View Profile</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2"
                                                href="{{ route('admin.users.edit', $user) }}">
                                                <i class="bi bi-pencil text-warning" style="width:16px;"></i>
                                                <span class="small">Edit</span>
                                            </a>
                                        </li>
                                        @if(Auth::id() !== $user->id)
                                        <li>
                                            <hr class="dropdown-divider my-1">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item rounded-2 d-flex align-items-center gap-2 py-2 text-danger">
                                                    <i class="bi bi-trash" style="width:16px;"></i>
                                                    <span class="small">Delete</span>
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Pagination --}}
    @if(method_exists($users, 'hasPages') && $users->hasPages())
    <div class="d-flex align-items-center justify-content-between mt-2 px-1">
        <small class="text-muted">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
        </small>
        {{ $users->links() }}
    </div>
    @endif

</x-app-layout>