<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>HealthLee - {{ config('app.name', 'Healthcare Appointment System') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary-blue: #0d6efd;
            --sidebar-bg: #ffffff;
            --sidebar-text: #4a5568;
            --sidebar-hover-bg: #e7f1ff;
            --sidebar-hover-text: #0d6efd;
            --sidebar-active-bg: #0d6efd;
            --sidebar-active-text: #ffffff;
        }

        /* ── Sidebar ── */
        .sidebar {
            background: var(--sidebar-bg);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.05);
            width: 260px;
            z-index: 1040;
            border-right: 1px solid #e9ecef;
        }

        .sidebar .nav-link {
            color: var(--sidebar-text);
            padding: 12px 20px;
            border-radius: 10px;
            margin: 4px 12px;
            font-weight: 500;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .nav-link:hover {
            background: var(--sidebar-hover-bg);
            color: var(--sidebar-hover-text);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            box-shadow: 0 2px 6px rgba(13, 110, 253, 0.2);
        }

        .sidebar .nav-link i {
            width: 24px;
            font-size: 1.1rem;
        }

        .brand-logo {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* ── Main content ── */
        .main-content {
            margin-left: 260px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* ── Top Navbar ── */
        .top-navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid #e9ecef;
        }

        .user-badge {
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: 20px;
            background: #e7f1ff !important;
            color: #0d6efd !important;
        }

        /* ── Logout ── */
        .logout-section {
            margin-top: auto;
            padding: 20px 16px 24px;
            border-top: 1px solid #e9ecef;
        }

        .logout-section .nav-link {
            color: #dc3545 !important;
        }

        .logout-section .nav-link:hover {
            background: #fdecea !important;
            color: #dc3545 !important;
        }

        /* ── Sidebar User Info ── */
        .sidebar-user-info {
            border-bottom: 1px solid #e9ecef;
        }

        .sidebar-user-name {
            color: #1a202c;
        }

        /* ── Responsive ── */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-260px);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="bg-light">

    <div class="d-flex">

        <!-- ==================== SIDEBAR ==================== -->
        <div class="sidebar text-dark vh-100 position-fixed overflow-auto shadow-lg d-flex flex-column rounded-4"
            id="sidebar">
            <div class="p-4 flex-grow-1">

                <!-- Branding -->
                <a href="{{ route('dashboard') }}" class="d-block text-decoration-none mb-4">
                    <h4 class="brand-logo text-primary mb-0">
                        <i class="bi bi-heart-pulse-fill text-primary me-2"></i>
                        HealthLee
                    </h4>
                    <small class="text-muted d-block mt-1">Appointment System</small>
                </a>

                {{-- User Info --}}
                <div class="mb-4 pb-3 sidebar-user-info border-top pt-3">

                    <strong class="d-block text-primary fs-6 sidebar-user-name">
                        @if(Auth::user()->role === 'admin' && Auth::user()->admin)
                        {{ Auth::user()->admin->first_name }} {{ Auth::user()->admin->last_name }}
                        @elseif(Auth::user()->role === 'doctor' && Auth::user()->doctor)
                        Dr. {{ Auth::user()->doctor->first_name }} {{ Auth::user()->doctor->last_name }}
                        @elseif(Auth::user()->role === 'patient' && Auth::user()->patient)
                        {{ Auth::user()->patient->first_name }} {{ Auth::user()->patient->last_name }}
                        @else
                        {{ Auth::user()->email }}
                        @endif
                    </strong>
                    <span class="badge user-badge mt-2">
                        {{ ucfirst(Auth::user()->role) }}
                    </span>
                </div>

                <!-- Navigation Menu (from navigation.blade.php) -->
                @include('layouts.navigation')

            </div>

            <!-- Logout -->
            <div class="logout-section">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="nav-link w-100 text-start p-3 border-0 bg-transparent rounded-3 d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-3 fs-5"></i>
                        <span class="fw-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-grow-1 main-content">

            <!-- Top Navbar -->
            {{-- <nav class="card border-0 shadow-sm mx-3 mt-3" style="border-radius: 12px; background: white;">
                <div class="card-body px-4 py-3">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <!-- Mobile sidebar toggle -->
                        <button class="btn btn-link text-dark d-lg-none me-auto p-0" id="sidebarToggle"
                            style="font-size: 1.5rem;">
                            <i class="bi bi-list"></i>
                        </button>

                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ now()->format('l, d F Y') }}
                        </span>

                        <div class="vr text-muted"></div>

                        <div class="dropdown">
                            <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                                    style="width: 32px; height: 32px; font-size: 0.8rem;">
                                    @if(Auth::user()->role === 'admin' && Auth::user()->admin)
                                    {{ strtoupper(substr(Auth::user()->admin->first_name, 0, 1)) }}
                                    @elseif(Auth::user()->role === 'doctor' && Auth::user()->doctor)
                                    {{ strtoupper(substr(Auth::user()->doctor->first_name, 0, 1)) }}
                                    @elseif(Auth::user()->role === 'patient' && Auth::user()->patient)
                                    {{ strtoupper(substr(Auth::user()->patient->first_name, 0, 1)) }}
                                    @else
                                    {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                                    @endif
                                </div>
                                <span class="text-dark fw-medium">
                                    @if(Auth::user()->role === 'admin' && Auth::user()->admin)
                                    {{ Auth::user()->admin->first_name }} {{ Auth::user()->admin->last_name }}
                                    @elseif(Auth::user()->role === 'doctor' && Auth::user()->doctor)
                                    Dr. {{ Auth::user()->doctor->first_name }} {{ Auth::user()->doctor->last_name }}
                                    @elseif(Auth::user()->role === 'patient' && Auth::user()->patient)
                                    {{ Auth::user()->patient->first_name }} {{ Auth::user()->patient->last_name }}
                                    @else
                                    {{ Auth::user()->email }}
                                    @endif
                                </span>
                                <i class="bi bi-chevron-down text-muted small"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person me-2"></i> Profile Settings
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav> --}}

            <!-- Page Heading -->
            @isset($header)
            <div class="px-4 pt-3">
                <div class="bg-white rounded-4 shadow-lg px-4 py-3 border-start">
                    {{ $header }}
                </div>
            </div>
            @endisset

            <!-- Page Content -->
            <main class="p-4">
                {{ $slot }}
            </main>

        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Modal auto-show (kept from your original)
        let myModalEl = document.querySelector('[data-modal="1"]');
        if (myModalEl) {
            const myModal = new bootstrap.Modal(myModalEl);
            myModal.show();
        }

        // Mobile sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }
    </script>

</body>

</html>