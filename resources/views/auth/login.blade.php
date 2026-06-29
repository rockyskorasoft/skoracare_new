@extends('layouts.auth')

@section('title', 'Login')

@section('content')

{{-- ════════════════════════════════════════════════════════════
     Skoracare — Split-Panel Login Page
     Left : branding illustration | Right : login form card
     ════════════════════════════════════════════════════════════ --}}
<div class="sk-login-wrapper">

    {{-- ── LEFT PANEL — Branding ───────────────────────────── --}}
    <div class="sk-login-left">
        {{-- Skoracare wordmark --}}
        <div class="sk-brand">
            <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                 alt="Skoracare Logo"
                 class="sk-brand-logo">
            <span class="sk-brand-name">Skoracare</span>
        </div>

        {{-- Medical illustration --}}
        <div class="sk-illustration-wrap">
            <img src="{{ Vite::asset('resources/images/skoracare-login.png') }}"
                 alt="Healthcare illustration"
                 class="sk-illustration">
        </div>

        {{-- Tagline --}}
        <div class="sk-tagline">
            <h2>Smart Healthcare,<br>Simplified.</h2>
            <p>Manage your clinics, patients and practice<br>all from one place.</p>
        </div>

        {{-- Powered by footer --}}
        <p class="sk-powered">Powered by <strong>Skoracare</strong></p>
    </div>

    {{-- ── RIGHT PANEL — Login Form ────────────────────────── --}}
    <div class="sk-login-right">
        <div class="sk-login-card">

            {{-- Mobile logo (shown only on small screens) --}}
            <div class="sk-card-logo d-md-none">
                <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                     alt="Skoracare"
                     class="sk-card-logo-img">
                <span class="sk-card-brand">Skoracare</span>
            </div>

            <h1 class="sk-card-title">Login</h1>
            <p class="sk-card-subtitle">Please provide the following details.</p>

            {{-- Flash messages --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('post-login') }}" class="sk-form">
                @csrf

                {{-- Email --}}
                <div class="sk-field">
                    <input type="email"
                           name="email"
                           id="sk_email"
                           class="sk-input @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="Email address"
                           autocomplete="email"
                           autofocus>
                    @error('email')
                        <span class="sk-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="sk-field">
                    <div class="sk-input-group">
                        <input type="password"
                               name="password"
                               id="sk_password"
                               class="sk-input @error('password') is-invalid @enderror"
                               placeholder="Password"
                               autocomplete="current-password">
                        <button type="button" class="sk-eye-btn" id="skEyeBtn" aria-label="Toggle password">
                            <i class="fa-regular fa-eye" id="skEyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="sk-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Forgot Password --}}
                <div class="sk-forgot">
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="sk-btn-login">Login</button>

                {{-- Sign Up Link --}}
                <p class="sk-signup-link">
                    {{ __('labels.dont_have_account') }}
                    <a href="{{ route('signup') }}">{{ __('labels.signup') }}</a>
                </p>

            </form>
        </div>
    </div>

</div>

<style>
/* ════════════════════════════════════════════════════════════
   Skoracare Login — Scoped Styles
   Pure CSS, no Tailwind.
   ════════════════════════════════════════════════════════════ */

/* ── Reset / Base ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

body { margin: 0; padding: 0; font-family: 'Inter', 'Laila', sans-serif; }

/* ── Wrapper — full-viewport split ───────────────────────── */
.sk-login-wrapper {
    display: flex;
    min-height: 100vh;
}

/* ── LEFT PANEL ───────────────────────────────────────────── */
.sk-login-left {
    flex: 1;
    background: linear-gradient(160deg, #eaf7f6 0%, #d0efed 40%, #b8e6e3 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    padding: 2.5rem 2.5rem 1.5rem;
    position: relative;
    overflow: hidden;
}

/* Decorative blob */
.sk-login-left::before {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(47,143,131,.1);
    top: -100px;
    right: -80px;
    pointer-events: none;
}

.sk-login-left::after {
    content: '';
    position: absolute;
    width: 260px;
    height: 260px;
    border-radius: 50%;
    background: rgba(47,143,131,.08);
    bottom: -60px;
    left: -60px;
    pointer-events: none;
}

/* ── Brand (top-left of left panel) ──────────────────────── */
.sk-brand {
    display: flex;
    align-items: center;
    gap: .6rem;
    align-self: flex-start;
    position: relative;
    z-index: 1;
}

.sk-brand-logo {
    height: 36px;
    width: auto;
    object-fit: contain;
}

.sk-brand-name {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f1923;
    letter-spacing: -.02em;
}

/* ── Illustration ─────────────────────────────────────────── */
.sk-illustration-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem 0;
    position: relative;
    z-index: 1;
}

.sk-illustration {
    max-width: 380px;
    width: 100%;
    height: auto;
    filter: drop-shadow(0 8px 24px rgba(15,25,35,.12));
}

/* ── Tagline ──────────────────────────────────────────────── */
.sk-tagline {
    text-align: center;
    position: relative;
    z-index: 1;
}

.sk-tagline h2 {
    font-size: 1.45rem;
    font-weight: 700;
    color: #0f2e2a;
    margin: 0 0 .5rem;
    line-height: 1.3;
}

.sk-tagline p {
    font-size: .9rem;
    color: #3a6b65;
    margin: 0;
    line-height: 1.5;
}

/* ── Powered by ───────────────────────────────────────────── */
.sk-powered {
    font-size: .72rem;
    color: #6aada8;
    margin: 0;
    position: relative;
    z-index: 1;
}

.sk-powered strong { color: #2f8f83; }

/* ── RIGHT PANEL ──────────────────────────────────────────── */
.sk-login-right {
    width: 460px;
    flex-shrink: 0;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 2rem;
    box-shadow: -4px 0 24px rgba(15,25,35,.06);
}

/* ── Card ─────────────────────────────────────────────────── */
.sk-login-card {
    width: 100%;
    max-width: 360px;
}

/* Mobile logo */
.sk-card-logo {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 1.5rem;
}

.sk-card-logo-img { height: 28px; }

.sk-card-brand {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f1923;
}

/* ── Card Title ───────────────────────────────────────────── */
.sk-card-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #0f1923;
    margin: 0 0 .35rem;
}

.sk-card-subtitle {
    font-size: .9rem;
    color: #8898a9;
    margin: 0 0 1.75rem;
}

/* ── Form ─────────────────────────────────────────────────── */
.sk-form { width: 100%; }

.sk-field { margin-bottom: 1rem; }

/* Input base */
.sk-input {
    width: 100%;
    padding: .82rem 1rem;
    border: 1.5px solid #e4e9ec;
    border-radius: .625rem;
    font-size: .9rem;
    font-family: inherit;
    color: #1a2332;
    background: #fafbfc;
    outline: none;
    transition: border-color 180ms, box-shadow 180ms;
}

.sk-input:focus {
    border-color: #2f8f83;
    box-shadow: 0 0 0 3px rgba(47,143,131,.12);
    background: #fff;
}

.sk-input.is-invalid {
    border-color: #ef4444;
}

.sk-input.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239,68,68,.12);
}

.sk-error {
    display: block;
    font-size: .78rem;
    color: #ef4444;
    margin-top: .3rem;
}

/* Input with eye toggle */
.sk-input-group {
    position: relative;
    display: flex;
}

.sk-input-group .sk-input {
    padding-right: 3rem;
}

.sk-eye-btn {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 3rem;
    border: none;
    background: transparent;
    color: #8898a9;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    transition: color 180ms;
    border-radius: 0 .625rem .625rem 0;
}

.sk-eye-btn:hover { color: #2f8f83; }

/* ── Forgot Password ──────────────────────────────────────── */
.sk-forgot {
    text-align: right;
    margin-bottom: 1.25rem;
}

.sk-forgot a {
    font-size: .83rem;
    color: #2f8f83;
    font-weight: 600;
    text-decoration: none;
}

.sk-forgot a:hover { text-decoration: underline; }

/* ── Login Button ─────────────────────────────────────────── */
.sk-btn-login {
    width: 100%;
    padding: .85rem;
    background: #2f8f83;
    color: #fff;
    border: none;
    border-radius: .625rem;
    font-size: 1rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background 180ms, box-shadow 180ms;
    letter-spacing: .01em;
}

.sk-btn-login:hover {
    background: #236d64;
    box-shadow: 0 4px 16px rgba(47,143,131,.3);
}

.sk-btn-login:active { transform: translateY(1px); }

/* ── Sign Up link ─────────────────────────────────────────── */
.sk-signup-link {
    margin-top: 1.25rem;
    text-align: center;
    font-size: .85rem;
    color: #8898a9;
}

.sk-signup-link a {
    color: #2f8f83;
    font-weight: 700;
    text-decoration: none;
}

.sk-signup-link a:hover { text-decoration: underline; }

/* ── Responsive — stack on mobile ────────────────────────── */
@media (max-width: 900px) {
    .sk-login-wrapper { flex-direction: column; }

    .sk-login-left {
        padding: 1.75rem 1.5rem 1.25rem;
        min-height: 40vh;
    }

    .sk-tagline h2 { font-size: 1.15rem; }
    .sk-tagline p  { font-size: .82rem; }

    .sk-illustration { max-width: 220px; }

    .sk-login-right {
        width: 100%;
        box-shadow: none;
        padding: 2rem 1.25rem 3rem;
    }
}

@media (max-width: 480px) {
    .sk-login-left { display: none; }

    .sk-login-right {
        min-height: 100vh;
        align-items: flex-start;
        padding-top: 3rem;
    }
}
</style>

@section('scripts')
<script>
/* ── Password eye toggle ── */
(function () {
    var btn  = document.getElementById('skEyeBtn');
    var icon = document.getElementById('skEyeIcon');
    var pwd  = document.getElementById('sk_password');
    if (!btn) return;
    btn.addEventListener('click', function () {
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            pwd.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
}());
</script>
@endsection

@endsection