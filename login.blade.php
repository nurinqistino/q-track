@extends('layouts.app')

@section('title', 'Staff Portal')

@section('content')
<div class="auth-page-wrap">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4 auth-page-header">
                    <a href="{{ url('/') }}" class="d-inline-block mb-2">
                        @if(file_exists(public_path('images/qtrack-logo.png')))
                            <img src="{{ asset('images/qtrack-logo.png') }}" alt="Q-TRACK" class="staff-login-logo">
                        @else
                            <span class="fw-bold fs-4 text-white">Q-TRACK</span>
                        @endif
                    </a>
                    <h4 class="fw-bold mb-1 text-white">Q-TRACK</h4>
                    <p class="auth-page-tagline">Staff Portal - Track your turn, Anytime</p>
                </div>
            <div class="card border-0 shadow rounded-3 overflow-hidden">
                <div class="card-body p-0">
                    {{-- Tabs --}}
                    @php $activeLogin = !isset($activeTab) || $activeTab !== 'register'; @endphp
                    <ul class="nav nav-pills nav-fill border-bottom" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('login') }}" class="nav-link rounded-0 py-3 fw-semibold {{ $activeLogin ? 'active' : '' }}" id="login-tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="me-1 align-middle" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                                Staff Login
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="{{ route('register') }}" class="nav-link rounded-0 py-3 fw-semibold {{ isset($activeTab) && $activeTab === 'register' ? 'active' : '' }}" id="register-tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="me-1 align-middle" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.4c0-.03-.01-.06-.02-.09-.05-.25-.11-.5-.18-.74-.08-.24-.17-.48-.27-.7-.1-.23-.22-.44-.36-.65-.14-.2-.3-.38-.47-.54-.17-.16-.36-.3-.56-.41-.2-.11-.42-.2-.64-.25-.22-.06-.44-.08-.66-.08s-.44.02-.66.08c-.22.05-.43.14-.64.25-.2.11-.39.25-.56.41-.17.16-.33.34-.47.54-.14.21-.26.42-.36.65-.1.23-.19.46-.27.7-.07.24-.13.49-.18.74-.01.03-.02.06-.02.09z"/></svg>
                                Staff Registration
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content p-4">
                        {{-- Login pane --}}
                        <div class="tab-pane fade {{ $activeLogin ? 'show active' : '' }}" id="login-pane" role="tabpanel">
                            <h5 class="mb-3 fw-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-danger me-1 align-middle" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm1 2v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/></svg>
                                Staff Login
                            </h5>
                            <p class="text-muted small mb-3">Access your Q-TRACK staff dashboard</p>
                            @if ($errors->any() && !$errors->has('password_confirmation'))
                                <div class="alert alert-danger py-2">
                                    <ul class="mb-0 small">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success py-2 small">{{ session('success') }}</div>
                            @endif
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label small fw-semibold">Username or Email</label>
                                    <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="Enter username or KWSP email" value="{{ old('email') }}" required autofocus>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label small fw-semibold">Password</label>
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" required>
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="remember">Remember me</label>
                                </div>
                                <button type="submit" class="btn btn-danger btn-kwsp w-100 py-2 fw-semibold rounded-pill">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="me-1 align-middle" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/></svg>
                                    LOGIN
                                </button>
                                <div class="text-center mt-3">
                                    <a href="{{ route('password.request') }}" class="small text-danger text-decoration-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-1 align-middle" viewBox="0 0 16 16"><path d="M3.5 11.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5z"/><path d="M2.743 5.07a.5.5 0 0 1 .052-.53l3-3a.5.5 0 0 1 .706 0l3 3a.5.5 0 0 1-.052.53L8.5 3.028V11h-1V3.028z"/></svg>
                                        Forgot Password?
                                    </a>
                                </div>
                            </form>
                        </div>
                        {{-- Register pane --}}
                        <div class="tab-pane fade {{ isset($activeTab) && $activeTab === 'register' ? 'show active' : '' }}" id="register-pane" role="tabpanel">
                            <h5 class="mb-3 fw-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="text-danger me-1 align-middle" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
                                Staff Registration
                            </h5>
                            <p class="text-muted small mb-3">Register with your KWSP email to access the staff portal</p>
                            @if (isset($activeTab) && $activeTab === 'register' && $errors->any())
                                <div class="alert alert-danger py-2">
                                    <ul class="mb-0 small">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="reg_name" class="form-label small fw-semibold">Full Name</label>
                                    <input type="text" name="name" id="reg_name" class="form-control form-control-lg" placeholder="Your full name" value="{{ old('name') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="reg_staff_number" class="form-label small fw-semibold">Staff Number (No. Kakitangan)</label>
                                    <input type="text" name="staff_number" id="reg_staff_number" class="form-control form-control-lg" placeholder="e.g. STF001" value="{{ old('staff_number') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="reg_email" class="form-label small fw-semibold">Email</label>
                                    <input type="email" name="email" id="reg_email" class="form-control form-control-lg" placeholder="your.name@kwsp.gov.my" value="{{ old('email') }}" required>
                                    <div class="form-text small">Only @kwsp.gov.my email addresses are allowed.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="reg_phone" class="form-label small fw-semibold">Phone Number (Nombor Telefon)</label>
                                    <input type="text" name="phone" id="reg_phone" class="form-control form-control-lg" placeholder="e.g. 012-3456789" value="{{ old('phone') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="reg_password" class="form-label small fw-semibold">Password</label>
                                    <input type="password" name="password" id="reg_password" class="form-control form-control-lg" placeholder="Min. 8 characters" required>
                                </div>
                                <div class="mb-3">
                                    <label for="reg_password_confirmation" class="form-label small fw-semibold">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="reg_password_confirmation" class="form-control form-control-lg" placeholder="Confirm password" required>
                                </div>
                                <button type="submit" class="btn btn-danger btn-kwsp w-100 py-2 fw-semibold rounded-pill">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="me-1 align-middle" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4z"/></svg>
                                    REGISTER
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-center mt-3 small auth-page-back">
                <a href="{{ url('/') }}" class="text-decoration-none">← Back to Q-TRACK</a>
            </p>
        </div>
    </div>
</div>
</div>
<style>
/* Latar biru sama seperti visitor index */
.auth-page-wrap {
    min-height: 100vh;
    background: linear-gradient(135deg, #003366 0%, #004080 50%, rgba(196,30,58,0.3) 100%);
}
.auth-page-header .text-white { color: #fff !important; }
.auth-page-tagline { color: rgba(255,255,255,0.9); font-size: 0.875rem; margin-bottom: 0; }
.auth-page-back { color: rgba(255,255,255,0.85); }
.auth-page-back a { color: rgba(255,255,255,0.95); }
.auth-page-back a:hover { color: #fff; }
.staff-login-logo { width: 120px; height: auto; max-height: 120px; object-fit: contain; display: block; margin: 0 auto; }
#authTabs .nav-link { color: #6c757d; }
#authTabs .nav-link.active { background-color: #C41E3A; color: white; border-color: #C41E3A; }
</style>
@endsection
