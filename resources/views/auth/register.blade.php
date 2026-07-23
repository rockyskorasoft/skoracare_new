@extends('layouts.auth')

@section('title', __('Register'))

@section('content')

{{-- ════════════════════════════════════════════════════════════
     Skoracare — Split-Panel Register Page
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

            <h1 class="sk-card-title">{{ __('Register') }}</h1>
            <p class="sk-card-subtitle">Create a new account on Skoracare.</p>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Register Form --}}
            <form method="POST" action="{{ route('register') }}" class="sk-form">
                @csrf

                <div class="sk-field">
                    <label for="name" class="sk-label required">{{ __('Name') }}</label>
                    <input id="name" type="text" class="sk-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ __('Name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="sk-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sk-field">
                    <label for="email" class="sk-label required">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="sk-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Email Address') }}" required autocomplete="email">
                    @error('email')
                        <span class="sk-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sk-field">
                    <label for="password" class="sk-label required">{{ __('Password') }}</label>
                    <div class="sk-input-group">
                        <input id="password" type="password" class="sk-input @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required autocomplete="new-password">
                        <button type="button" class="sk-eye-btn" id="skEyeBtn1" data-target="password" aria-label="Toggle password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="sk-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="sk-field mb-4">
                    <label for="password-confirm" class="sk-label required">{{ __('Confirm Password') }}</label>
                    <div class="sk-input-group">
                        <input id="password-confirm" type="password" class="sk-input" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required autocomplete="new-password">
                        <button type="button" class="sk-eye-btn" id="skEyeBtn2" data-target="password-confirm" aria-label="Toggle password">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="sk-btn-primary mb-3">
                    {{ __('Register') }}
                </button>

                <p class="sk-signup-link mt-3">
                    {{ __('labels.already_have_account') }}
                    <a href="{{ route('login') }}">{{ __('labels.login') }}</a>
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

@section('scripts')
<script>
(function () {
    'use strict';
    document.querySelectorAll('.sk-eye-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = this.getAttribute('data-target');
            var input    = document.getElementById(targetId);
            var icon     = this.querySelector('i');
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
}());
</script>
@endsection
