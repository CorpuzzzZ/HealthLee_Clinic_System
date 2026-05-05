<ul class="nav flex-column">

    @if(Auth::user()->role === 'admin')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i> <small>Dashboard</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
            href="{{ route('admin.users.index') }}">
            <i class="bi bi-people me-2"></i> <small>Manage Users</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.patient-records.*') ? 'active' : '' }}"
            href="{{ route('admin.patient-records.index') }}">
            <i class="bi bi-clipboard2-pulse me-2"></i> <small>Patient Records</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
            href="{{ route('admin.reports.index') }}">
            <i class="bi bi-graph-up me-2"></i> <small>Reports</small>
        </a>
    </li>
    @elseif(Auth::user()->role === 'patient')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}"
            href="{{ route('patient.dashboard') }}">
            <i class="bi bi-house-door me-2"></i> <small>Dashboard</small>
        </a>
    </li>
    {{-- These will be enabled once the modules are built --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }}"
            href="{{ route('patient.appointments.index') }}">
            <i class="bi bi-calendar-check me-2"></i> <small>Appointments</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patient.doctors.*') ? 'active' : '' }}"
            href="{{ route('patient.doctors.index') }}">
            <i class="bi bi-search me-2"></i> <small>Find Doctor</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
            href="{{ route('notifications.index') }}">
            <i class="bi bi-bell me-2"></i>
            <small>Notifications</small>
            @if(isset($unreadNotifications) && $unreadNotifications > 0)
            <span class="badge rounded-pill ms-auto" style="background: #dc3545; font-size: 0.65rem; padding: 3px 7px;">
                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
            </span>
            @endif
        </a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link disabled text-secondary" style="opacity: 0.5; cursor: not-allowed;">
            <i class="bi bi-graph-up me-2"></i> <small>Reports</small>
        </a>
    </li> --}}

    @elseif(Auth::user()->role === 'doctor')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}"
            href="{{ route('doctor.dashboard') }}">
            <i class="bi bi-house-door me-2"></i> <small>Dashboard</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.availabilities.*') ? 'active' : '' }}"
            href="{{ route('doctor.availabilities.index') }}">
            <i class="bi bi-clock-history me-2"></i> <small>My Availability</small>
        </a>
    </li>
    {{-- These will be enabled once the modules are built --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }}"
            href="{{ route('doctor.appointments.index') }}">
            <i class="bi bi-calendar-event me-2"></i> <small>Appointments</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.medical-records.*') ? 'active' : '' }}"
            href="{{ route('doctor.medical-records.index') }}">
            <i class="bi bi-file-medical me-2"></i> <small>Medical Records</small>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
            href="{{ route('notifications.index') }}">
            <i class="bi bi-bell me-2"></i>
            <small>Notifications</small>
            @if(isset($unreadNotifications) && $unreadNotifications > 0)
            <span class="badge rounded-pill ms-auto" style="background: #dc3545; font-size: 0.65rem; padding: 3px 7px;">
                {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
            </span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.patient-records.*') ? 'active' : '' }}"
            href="{{ route('doctor.patient-records.index') }}">
            <i class="bi bi-clipboard2-pulse me-2"></i> <small>Patient Records</small>
        </a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link disabled text-secondary" style="opacity: 0.5; cursor: not-allowed;">
            <i class="bi bi-graph-up me-2"></i> <small>Reports</small>
        </a>
    </li> --}}

    @endif

</ul>