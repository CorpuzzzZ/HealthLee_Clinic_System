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
        }

        /* ── Sidebar ── */
        .sidebar {
            background: linear-gradient(180deg, #1a2535 0%, #0f1826 100%);
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
            width: 260px;
            z-index: 1040;
        }

        .sidebar .nav-link {
            color: #a8b5c8;
            padding: 14px 24px;
            border-radius: 8px;
            margin: 6px 12px;
            font-weight: 500;
            transition: all 0.25s ease;
            display: flex;           /* add this */
    align-items: center;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--primary-blue);
            color: white;
            transform: translateX(6px);
        }

        .sidebar .nav-link i {
            width: 26px;
            font-size: 1.15rem;
        }

        .brand-logo {
            font-size: 1.8rem;
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
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            border-bottom: 1px solid #e9ecef;
        }

        .user-badge {
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 50px;
        }

        /* ── Logout ── */
        .logout-section {
            margin-top: auto;
            padding: 20px 16px 24px;
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
        <div class="sidebar text-white vh-100 position-fixed overflow-auto shadow d-flex flex-column" id="sidebar">
            <div class="p-4 flex-grow-1">

                <!-- Branding -->
                <a href="{{ route('dashboard') }}" class="d-block text-decoration-none mb-5">
                    <h4 class="brand-logo text-white mb-0">
                        <i class="bi bi-heart-pulse-fill text-primary me-2"></i>
                        HealthLee
                    </h4>
                    <small class="text-muted d-block mt-1">Appointment System</small>
                </a>

                {{-- User Info --}}
<div class="mb-5 pb-4 border-bottom border-secondary">
    <small class="opacity-75 d-block mb-1">Logged in as</small>
    <strong class="d-block text-white fs-6">
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
    <span class="badge user-badge bg-primary mt-2">
        {{ ucfirst(Auth::user()->role) }}
    </span>
</div>

                <!-- Navigation Menu (from navigation.blade.php) -->
                @include('layouts.navigation')

            </div>

            <!-- Logout -->
            <div class="logout-section border-top border-secondary">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="nav-link text-white w-100 text-start p-3 border-0 bg-transparent rounded-3 d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-3 fs-5"></i>
                        <span class="fw-medium"><small>Logout</small></span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ==================== MAIN CONTENT ==================== -->
        <div class="flex-grow-1 main-content">

            <!-- Top Navbar -->
            <nav class="top-navbar navbar py-3">
                <div class="container-fluid px-4">

                    <!-- Mobile sidebar toggle -->
                    <button class="btn btn-link text-dark d-lg-none me-3 p-0" id="sidebarToggle">
                        <i class="bi bi-list fs-3"></i>
                    </button>

                    <div class="ms-auto d-flex align-items-center gap-3">
                        <span class="text-muted small d-none d-md-inline">
                            {{ now()->format('l, d F Y') }}
                        </span>
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
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            @isset($header)
                <div class="px-4 pt-3">
                    <div class="bg-white rounded-3 shadow-sm px-4 py-3 border-start border-primary border-4">
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

{{-- <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="min-vh-100 bg-light pb-2">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow-sm">
                <div class="container py-4">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="container">
            {{ $slot }}
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let myModalEl = document.querySelector('[data-modal="1"]');
        if (myModalEl) {
            const myModal = new bootstrap.Modal(myModalEl);
            myModal.show();
        }
    </script>
</body>

</html> --}}
