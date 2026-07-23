@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')

{{-- ════════════════════════════════════════════════════════════
     Skoracare — Split-Panel Forgot Password Page
     Left : branding illustration | Right : reset link form card
     ════════════════════════════════════════════════════════════ --}}
<div class="sk-login-wrapper">

    {{-- ── LEFT PANEL — Branding ───────────────────────────── --}}
    <div class="sk-login-left">
        <div class="sk-brand">
            <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                 alt="Skoracare Logo"
                 class="sk-brand-logo">
        </div>

        <div class="sk-illustration-wrap">
            <img src="{{ Vite::asset('resources/images/skoracare-login.png') }}"
                 alt="Healthcare illustration"
                 class="sk-illustration">
        </div>

        <div class="sk-tagline">
            <h2>Smart Healthcare,<br>Simplified.</h2>
            <p>Manage your clinics, patients and practice<br>all from one place.</p>
        </div>

        <p class="sk-powered">Powered by <strong>Skoracare</strong></p>
    </div>

    {{-- ── RIGHT PANEL — Form Card ────────────────────────── --}}
    <div class="sk-login-right">
        <div class="sk-login-card">

            {{-- Mobile logo --}}
            <div class="sk-card-logo d-md-none">
                <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                     alt="Skoracare"
                     class="sk-card-logo-img">
            </div>

            <h1 class="sk-card-title">Forgot Password?</h1>
            <p class="sk-card-subtitle">No worries! Enter your email and we'll send you a reset link.</p>

            {{-- Flash Messages --}}
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fa-solid fa-circle-check me-1"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Forgot Password Form --}}
            <form action="{{ route('password.email') }}" method="POST" class="sk-form">
                @csrf

                <div class="sk-field mb-4">
                    <label for="email" class="sk-label required">Email Address</label>
                    <input type="email"
                           name="email"
                           id="email"
                           class="sk-input @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="Enter your registered email"
                           autocomplete="email"
                           autofocus>
                    @error('email')
                        <span class="sk-error">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="sk-btn-primary mb-3">
                    <i class="fa-solid fa-paper-plane me-1"></i> Send Password Reset Link
                </button>

                <p class="sk-signup-link mt-3">
                    <a href="{{ route('login') }}" class="d-inline-flex align-items-center">
                        <i class="fa-solid fa-arrow-left me-2"></i> Back to Login
                    </a>
                </p>
            </form>

        </div>
    </div>

</div>

<style>
/* ════════════════════════════════════════════════════════════
   Skoracare Auth — Scoped Styles (Matches Login Page)
   ════════════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; padding: 0; font-family: 'Inter', 'Laila', sans-serif; }

.sk-login-wrapper {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

.sk-login-left {
    flex: 1;
    background: linear-gradient(160deg, #eaf7f6 0%, #d0efed 40%, #b8e6e3 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    padding: 1.75rem 2.5rem 1.25rem;
    position: relative;
    overflow: hidden;
    height: 100%;
    min-height: 0;
}

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

.sk-brand {
    display: flex;
    align-items: center;
    gap: .6rem;
    align-self: flex-start;
    position: relative;
    z-index: 1;
    max-height: 120px;
}

.sk-brand-logo {
    height: 96px;
    max-height: 100%;
    width: auto;
    object-fit: contain;
}

.sk-illustration-wrap {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 0;
    position: relative;
    z-index: 1;
    min-height: 0;
    width: 100%;
    overflow: hidden;
}

.sk-illustration {
    max-width: 380px;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    filter: drop-shadow(0 8px 24px rgba(15,25,35,.12));
}

.sk-tagline {
    text-align: center;
    position: relative;
    z-index: 1;
}

.sk-tagline h2 {
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f2e2a;
    margin: 0 0 .35rem;
    line-height: 1.25;
}

.sk-tagline p {
    font-size: .85rem;
    color: #3a6b65;
    margin: 0;
    line-height: 1.4;
}

.sk-powered {
    font-size: .72rem;
    color: #6aada8;
    margin: .5rem 0 0;
    position: relative;
    z-index: 1;
}

.sk-powered strong { color: #2f8f83; }

.sk-login-right {
    width: 460px;
    flex-shrink: 0;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 2rem;
    box-shadow: -4px 0 24px rgba(15,25,35,.06);
    height: 100%;
    overflow-y: auto;
}

.sk-login-card {
    width: 100%;
    max-width: 360px;
}

.sk-card-logo {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 1.5rem;
}

.sk-card-logo-img { height: 98px; }

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

.sk-form { width: 100%; }

.sk-field { margin-bottom: 1rem; }

.sk-label {
    display: block;
    font-size: .82rem;
    font-weight: 600;
    color: #1a2332;
    margin-bottom: .35rem;
}

.sk-label.required::after {
    content: " *";
    color: #ef4444;
}

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

.sk-input.is-invalid { border-color: #ef4444; }

.sk-error {
    display: block;
    font-size: .78rem;
    color: #ef4444;
    margin-top: .3rem;
}

.sk-btn-primary {
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
}

.sk-btn-primary:hover {
    background: #236d64;
    box-shadow: 0 4px 16px rgba(47,143,131,.3);
}

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

@media (max-width: 900px) {
    .sk-login-wrapper {
        flex-direction: column;
        height: auto;
        min-height: 100vh;
        overflow: auto;
    }
    .sk-login-left {
        height: auto;
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

@endsection