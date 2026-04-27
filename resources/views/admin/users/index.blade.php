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
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control border-start-0 ps-0"
                               placeholder="Search by email...">
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Filter by Role</label>
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="admin"   {{ request('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
                        <option value="doctor"  {{ request('role') === 'doctor'  ? 'selected' : '' }}>Doctor</option>
                        <option value="patient" {{ request('role') === 'patient' ? 'selected' : '' }}>Patient</option>
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

    {{-- Users Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th class="px-4 py-3 text-muted fw-normal small border-0">#</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">User</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Email</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Role</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0">Registered</th>
                            <th class="px-4 py-3 text-muted fw-normal small border-0 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                {{-- # --}}
                                <td class="px-4 border-0 text-muted small">
    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
</td>

                                {{-- Avatar + Email --}}
                                <td class="px-4 border-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                             style="width: 40px; height: 40px; font-size: 0.8rem;
                                             background: {{ $user->role === 'admin' ? '#0d6efd' : ($user->role === 'doctor' ? '#0dcaf0' : '#198754') }};">
                                            {{ strtoupper(substr($user->email, 0, 2)) }}
                                        </div>
                                        <div>
                                            @if($user->role === 'admin' && $user->admin)
                                                <div class="fw-medium small">{{ $user->admin->first_name }} {{ $user->admin->last_name }}</div>
                                            @elseif($user->role === 'patient' && $user->patient)
                                                <div class="fw-medium small">{{ $user->patient->first_name }} {{ $user->patient->last_name }}</div>
                                            @elseif($user->role === 'doctor' && $user->doctor)
                                                <div class="fw-medium small">{{ $user->doctor->first_name }} {{ $user->doctor->last_name }}</div>
                                            @else
                                                <div class="fw-medium small text-muted fst-italic">No profile yet</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td class="px-4 border-0">
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>

                                {{-- Role Badge --}}
                                <td class="px-4 border-0">
                                    @if($user->role === 'admin')
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">
                                            <i class="bi bi-shield-fill me-1"></i> Admin
                                        </span>
                                    @elseif($user->role === 'doctor')
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background: #e0f7fc; color: #0097a7; font-size: 0.75rem;">
                                            <i class="bi bi-person-badge-fill me-1"></i> Doctor
                                        </span>
                                    @else
                                        <span class="badge rounded-pill px-3 py-2"
                                              style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">
                                            <i class="bi bi-person-heart-fill me-1"></i> Patient
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-4 border-0">
                                    <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 border-0 text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-2">

                                        {{-- View --}}
                                        <a href="{{ route('admin.users.show', $user) }}"
                                           class="btn btn-sm btn-outline-primary rounded-3 px-3"
                                           title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="btn btn-sm btn-outline-warning rounded-3 px-3"
                                           title="Edit Role">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- Delete --}}
                                        @if(Auth::id() !== $user->id)
                                            <form method="POST"
                                                  action="{{ route('admin.users.destroy', $user) }}"
                                                  onsubmit="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-3 px-3"
                                                        title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary rounded-3 px-3"
                                                    disabled title="Cannot delete yourself">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted border-0">
                                    <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                    <div class="fw-medium">No users found</div>
                                    <small>Try adjusting your search or filter.</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
@if(method_exists($users, 'hasPages') && $users->hasPages())
    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between">
        <small class="text-muted">
            Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
        </small>
        {{ $users->links() }}
    </div>
@endif

        </div>
    </div>

</x-app-layout>