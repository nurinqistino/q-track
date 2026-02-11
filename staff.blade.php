<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Staff') - Q-TRACK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #003366;
            --sidebar-width: 260px;
            --primary-color: #E93D5A;
        }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        .staff-wrapper { display: flex; min-height: 100vh; }
        .staff-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, #004080 100%);
            color: #fff;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }
        .staff-sidebar .brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .staff-sidebar .brand img { height: 44px; }
        .staff-sidebar .brand-text { font-weight: 700; font-size: 1.2rem; }
        .staff-sidebar .brand-tag { font-size: 0.7rem; opacity: 0.9; display: block; }
        .staff-sidebar .nav-section { padding: 1rem 0; }
        .staff-sidebar .nav-section-title {
            padding: 0 1.5rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255,255,255,0.5);
            margin-bottom: 0.5rem;
        }
        .staff-sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.65rem 1.5rem;
            color: rgba(255,255,255,0.85);
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .staff-sidebar .nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .staff-sidebar .nav-link.active { background: var(--primary-color); color: #fff; }
        .staff-sidebar .nav-link i { font-size: 1.1rem; width: 24px; text-align: center; }
        .staff-main { flex: 1; margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        .staff-topbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .staff-topbar .page-title { font-weight: 700; font-size: 1.25rem; color: #1e293b; margin: 0; }
        .staff-topbar .user-menu { display: flex; align-items: center; gap: 1rem; }
        .staff-topbar .user-name { font-size: 0.9rem; font-weight: 500; color: #475569; }
        .staff-topbar .btn-logout { color: var(--primary-color); font-weight: 600; }
        .staff-content { padding: 1.5rem; flex: 1; }
        .card-dashboard { border: none; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        @media (max-width: 991.98px) {
            .staff-sidebar { transform: translateX(-100%); }
            .staff-main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="staff-wrapper">
    <aside class="staff-sidebar">
        <div class="brand">
            @if(file_exists(public_path('images/qtrack-logo.png')))
                <img src="{{ asset('images/qtrack-logo.png') }}" alt="Q-TRACK">
            @endif
            <div>
                <span class="brand-text">Q-TRACK</span>
                <span class="brand-tag">Staff</span>
            </div>
        </div>
        <nav class="nav-section">
            <div class="nav-section-title">Menu</div>
            <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('staff.profile') }}" class="nav-link {{ request()->routeIs('staff.profile') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Profile
            </a>
        </nav>
        <nav class="nav-section mt-auto">
            <div class="nav-section-title">General</div>
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="bi bi-house-door"></i> Visitor Site
            </a>
            <a href="{{ route('board') }}" class="nav-link" target="_blank">
                <i class="bi bi-tv"></i> Display Board
            </a>
        </nav>
    </aside>
    <div class="staff-main">
        <header class="staff-topbar">
            <h1 class="page-title">@yield('page-title', 'Staff')</h1>
            <div class="user-menu">
                <span class="user-name"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">@csrf
                    <button type="submit" class="btn btn-sm btn-logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                </form>
            </div>
        </header>
        <main class="staff-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
