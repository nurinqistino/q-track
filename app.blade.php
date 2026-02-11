<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Q-TRACK') - KWSP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --kwsp-blue: #003366;
            --kwsp-blue-light: #004080;
            --kwsp-red: #C41E3A;
            --kwsp-red-hover: #a01830;
            --kwsp-gold: #C5A028;
            --primary-color: #C41E3A;
            --primary-dark: #a01830;
            --primary-light: #e94d5a;
            --secondary-color: #003366;
        }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, sans-serif;
            color: #212529;
            background: #f8f9fa;
        }
        .navbar-custom {
            background: #fff;
            padding: 0.75rem 0;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            border-bottom: 4px solid var(--kwsp-red);
        }
        .navbar-custom .navbar-brand { color: #212529; text-decoration: none; }
        .navbar-custom .logo-img { height: 160px; }
        .navbar-custom .kwsp-logo { height: 160px; }
        .navbar-custom .brand-text { font-weight: 700; font-size: 1.75rem; color: var(--kwsp-blue); }
        .navbar-custom .brand-tagline { font-size: 0.95rem; color: #495057; display: block; font-weight: 500; }
        .navbar-custom .brand-separator { height: 120px; width: 3px; background: var(--kwsp-red); margin: 0 0.25rem; border-radius: 1px; }
        .navbar-custom .logos-wrap { display: flex; align-items: center; gap: 0; }
        .navbar-custom .kwsp-badge { background: var(--kwsp-red); color: white; font-size: 0.7rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 4px; margin-left: 0.5rem; letter-spacing: 0.02em; }
        .navbar-custom .btn-staff-login { background: #212529; color: #fff; border: none; font-weight: 600; }
        .navbar-custom .btn-staff-login:hover { background: #000; color: #fff; }
        .btn-kwsp {
            background-color: var(--kwsp-red);
            color: white;
            border: none;
            font-weight: 600;
        }
        .btn-kwsp:hover { background-color: var(--kwsp-red-hover); color: white; }
        .btn-kwsp-outline {
            border: 2px solid var(--kwsp-red);
            color: var(--kwsp-red);
            font-weight: 600;
        }
        .btn-kwsp-outline:hover { background-color: var(--kwsp-red); color: white; }
        .card-kwsp { border-left: 4px solid var(--kwsp-red); border-radius: 8px; }
        .footer-kwsp {
            background: #1a1a1a;
            color: #e0e0e0;
            padding: 2rem 0 1rem;
            margin-top: auto;
        }
        .footer-kwsp .slogan { color: var(--primary-color); font-weight: 600; font-size: 1.05rem; }
        .footer-kwsp .logo-qtrack, .footer-kwsp .logo-kwsp { height: 100px; opacity: 0.95; }
        .footer-kwsp .footer-separator { height: 64px; width: 2px; background: var(--primary-color); opacity: 0.8; }
        .footer-kwsp .mb-2.fw-bold { font-size: 1.25rem; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <div class="d-flex align-items-center">
                    <div class="logos-wrap">
                        @if(file_exists(public_path('images/qtrack-logo.png')))
                            <img src="{{ asset('images/qtrack-logo.png') }}" alt="Q-TRACK Logo" class="logo-img">
                        @else
                            <span class="brand-text me-2">Q-TRACK</span>
                        @endif
                        <div class="brand-separator"></div>
                        @if(file_exists(public_path('images/kwsp-logo.png')))
                            <img src="{{ asset('images/kwsp-logo.png') }}" alt="KWSP EPF Logo" class="kwsp-logo">
                        @endif
                    </div>
                    <div class="d-inline-flex flex-column ms-3">
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <span class="brand-text">Q-TRACK</span>
                            <span class="kwsp-badge">KWSP EPF</span>
                        </div>
                        <small class="brand-tagline">Track your turn, Anytime</small>
                    </div>
                </div>
            </a>
            <div class="d-flex">
                @auth
                    <a href="{{ route('logout') }}" class="btn btn-outline-dark btn-sm px-4" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-staff-login btn-sm px-4"><i class="bi bi-person-badge me-2"></i>Staff Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @if(session('success'))
            <div class="container pt-3"><div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
        @endif
        @if(session('error'))
            <div class="container pt-3"><div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
        @endif
        @if(session('info'))
            <div class="container pt-3"><div class="alert alert-info alert-dismissible fade show" role="alert">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
        @endif
        @yield('content')
    </main>

    @hasSection('no-footer')
    @else
    <footer class="footer-kwsp text-center">
        <div class="container">
            <div class="mb-3 d-flex align-items-center justify-content-center gap-1">
                @if(file_exists(public_path('images/qtrack-logo.png')))
                    <img src="{{ asset('images/qtrack-logo.png') }}" alt="Q-TRACK" class="logo-qtrack">
                @endif
                <div class="footer-separator"></div>
                @if(file_exists(public_path('images/kwsp-logo.png')))
                    <img src="{{ asset('images/kwsp-logo.png') }}" alt="KWSP EPF" class="logo-kwsp">
                @endif
            </div>
            <h5 class="mb-2 fw-bold text-white">Q-TRACK</h5>
            <p class="mb-3 slogan">Track your turn, Anytime</p>
            <p class="mb-1 small">© {{ date('Y') }} Q-TRACK - KWSP Kwasa Damansara Branch</p>
            <small class="text-white-50">Powered by Smart Queue Technology | KWSP EPF</small>
        </div>
    </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
