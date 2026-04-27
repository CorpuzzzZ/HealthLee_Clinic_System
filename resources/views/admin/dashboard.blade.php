<x-app-layout>

    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Admin Dashboard</h5>
                <small class="text-muted">Welcome back, {{ Auth::user()->admin->first_name ?? 'Admin' }}!</small>
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
                            <i class="bi bi-people-fill fs-4" style="color: #0d6efd;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalUsers ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Registered Users</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #198754 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e8f5ee;">
                            <i class="bi bi-person-heart-fill fs-4" style="color: #198754;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e8f5ee; color: #198754; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalPatients ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Patients</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0dcaf0 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #e0f7fc;">
                            <i class="bi bi-person-badge-fill fs-4" style="color: #0dcaf0;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #e0f7fc; color: #0dcaf0; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalDoctors ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Doctors</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important; border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="rounded-3 p-2" style="background: #fff8e1;">
                            <i class="bi bi-calendar-check-fill fs-4" style="color: #ffc107;"></i>
                        </div>
                        <span class="badge rounded-pill" style="background: #fff8e1; color: #e6a800; font-size: 0.75rem;">Total</span>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $totalAppointments ?? 0 }}</h3>
                    <p class="text-muted mb-0 small">Appointments</p>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Recent Users + Quick Actions ── --}}
    <div class="row g-3 mb-4">

        {{-- Recent Users --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="fw-bold mb-0">Recent Users</h6>
                        <small class="text-muted">Latest registered accounts</small>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        View All <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body px-4 pt-3 pb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th class="text-muted fw-normal small pb-2 border-0">User</th>
                                    <th class="text-muted fw-normal small pb-2 border-0">Email</th>
                                    <th class="text-muted fw-normal small pb-2 border-0">Role</th>
                                    <th class="text-muted fw-normal small pb-2 border-0">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUsers ?? [] as $user)
                                    <tr>
                                        <td class="border-0 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                                     style="width: 36px; height: 36px; font-size: 0.8rem;
                                                     background: {{ $user->role === 'admin' ? '#0d6efd' : ($user->role === 'doctor' ? '#0dcaf0' : '#198754') }};">
                                                    {{ strtoupper(substr($user->email, 0, 2)) }}
                                                </div>
                                                <span class="fw-medium small">{{ $user->email }}</span>
                                            </div>
                                        </td>
                                        <td class="border-0 py-3">
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </td>
                                        <td class="border-0 py-3">
                                            @if($user->role === 'admin')
                                                <span class="badge rounded-pill" style="background: #e7f1ff; color: #0d6efd;">Admin</span>
                                            @elseif($user->role === 'doctor')
                                                <span class="badge rounded-pill" style="background: #e0f7fc; color: #0dcaf0;">Doctor</span>
                                            @else
                                                <span class="badge rounded-pill" style="background: #e8f5ee; color: #198754;">Patient</span>
                                            @endif
                                        </td>
                                        <td class="border-0 py-3">
                                            <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5 border-0">
                                            <i class="bi bi-people fs-1 d-block mb-2 opacity-25"></i>
                                            No users registered yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">Quick Actions</h6>
                    <small class="text-muted">Common tasks</small>
                </div>
                <div class="card-body px-4 pt-3 pb-4 d-flex flex-column gap-2">

                    <a href="{{ route('admin.users.index') }}"
                       class="btn btn-light text-start d-flex align-items-center gap-3 p-3 border rounded-3 text-decoration-none"
                       style="border-color: #e9ecef !important;">
                        <div class="rounded-3 p-2" style="background: #e7f1ff;">
                            <i class="bi bi-people-fill" style="color: #0d6efd; font-size: 1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-medium small text-dark">Manage Users</div>
                            <div class="text-muted" style="font-size: 0.75rem;">View, edit, delete users</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                       class="btn btn-light text-start d-flex align-items-center gap-3 p-3 border rounded-3 text-decoration-none"
                       style="border-color: #e9ecef !important;">
                        <div class="rounded-3 p-2" style="background: #e8f5ee;">
                            <i class="bi bi-graph-up" style="color: #198754; font-size: 1rem;"></i>
                        </div>
                        <div>
                            <div class="fw-medium small text-dark">Reports</div>
                            <div class="text-muted" style="font-size: 0.75rem;">View analytics & reports</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                    </a>

                </div>
            </div>
        </div>

    </div>

    {{-- ── Role Breakdown ── --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0">User Role Breakdown</h6>
                    <small class="text-muted">Distribution of registered users by role</small>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="row g-3">

                        {{-- Admin --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #f8f9ff; border: 1px solid #e7f1ff;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-medium text-dark">Admins</span>
                                    <span class="fw-bold" style="color: #0d6efd;">{{ $totalAdmins ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 99px; background: #e7f1ff;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $totalUsers > 0 ? (($totalAdmins ?? 0) / $totalUsers) * 100 : 0 }}%; background: #0d6efd; border-radius: 99px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Doctors --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #f0fdfc; border: 1px solid #e0f7fc;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-medium text-dark">Doctors</span>
                                    <span class="fw-bold" style="color: #0dcaf0;">{{ $totalDoctors ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 99px; background: #e0f7fc;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $totalUsers > 0 ? (($totalDoctors ?? 0) / $totalUsers) * 100 : 0 }}%; background: #0dcaf0; border-radius: 99px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Patients --}}
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #f4fdf7; border: 1px solid #e8f5ee;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small fw-medium text-dark">Patients</span>
                                    <span class="fw-bold" style="color: #198754;">{{ $totalPatients ?? 0 }}</span>
                                </div>
                                <div class="progress" style="height: 6px; border-radius: 99px; background: #e8f5ee;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $totalUsers > 0 ? (($totalPatients ?? 0) / $totalUsers) * 100 : 0 }}%; background: #198754; border-radius: 99px;">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>