@extends('layouts.auth')

@section('title', __('labels.signup'))

@section('content')

{{-- ════════════════════════════════════════════════════════════
     Skoracare — Split-Panel Signup Page
     Left : branding illustration | Right : signup form card
     ════════════════════════════════════════════════════════════ --}}
<div class="sk-login-wrapper">

    {{-- ── LEFT PANEL — Branding ───────────────────────────── --}}
    <div class="sk-login-left">
        {{-- Skoracare wordmark --}}
        <div class="sk-brand">
            <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                 alt="Skoracare Logo"
                 class="sk-brand-logo">
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

    {{-- ── RIGHT PANEL — Signup Form ────────────────────────── --}}
    <div class="sk-login-right">
        <div class="sk-login-card">

            {{-- Mobile logo (shown only on small screens) --}}
            <div class="sk-card-logo d-md-none">
                <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                     alt="Skoracare"
                     class="sk-card-logo-img">
            </div>

            <h1 class="sk-card-title">{{ __('labels.signup_title') }}</h1>
            <p class="sk-card-subtitle">{{ __('labels.signup_subtitle') }}</p>

            {{-- ── Progress Indicator ── --}}
            <div class="signup-progress mb-3" aria-label="{{ __('labels.signup') }} progress">
                <div class="signup-step active" id="progressStep1">
                    <div class="signup-step-dot">1</div>
                    <span class="signup-step-label">{{ __('labels.step_1_of_2') }}</span>
                </div>
                <div class="signup-step-line"></div>
                <div class="signup-step" id="progressStep2">
                    <div class="signup-step-dot">2</div>
                    <span class="signup-step-label">{{ __('labels.step_2_of_2') }}</span>
                </div>
            </div>

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

            {{-- Signup Form --}}
            <form method="POST" action="{{ route('signup.submit') }}" id="signupForm" class="sk-form" novalidate>
                @csrf

                {{-- ════════════════════════════════ --}}
                {{-- STEP 1: Personal Info            --}}
                {{-- ════════════════════════════════ --}}
                <div id="step1">

                    {{-- First & Last Name --}}
                    <div class="sk-row">
                        <div class="sk-field sk-col">
                            <label for="first_name" class="sk-label required">{{ __('labels.first_name') }}</label>
                            <input type="text"
                                   name="first_name"
                                   id="first_name"
                                   class="sk-input @error('first_name') is-invalid @enderror"
                                   value="{{ old('first_name') }}"
                                   placeholder="{{ __('labels.first_name') }}"
                                   autocomplete="given-name">
                            @error('first_name')
                                <span class="sk-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sk-field sk-col">
                            <label for="last_name" class="sk-label">{{ __('labels.last_name') }}</label>
                            <input type="text"
                                   name="last_name"
                                   id="last_name"
                                   class="sk-input @error('last_name') is-invalid @enderror"
                                   value="{{ old('last_name') }}"
                                   placeholder="{{ __('labels.last_name') }}"
                                   autocomplete="family-name">
                            @error('last_name')
                                <span class="sk-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="sk-field">
                        <label for="email" class="sk-label required">{{ __('labels.email') }}</label>
                        <input type="email"
                               name="email"
                               id="email"
                               class="sk-input @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="{{ __('labels.email') }}"
                               autocomplete="email">
                        @error('email')
                            <span class="sk-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="sk-field">
                        <label for="phone_no" class="sk-label required">{{ __('labels.mobile_number') }}</label>
                        <input type="tel"
                               name="phone_no"
                               id="phone_no"
                               class="sk-input @error('phone_no') is-invalid @enderror"
                               value="{{ old('phone_no') }}"
                               placeholder="{{ __('labels.mobile_number') }}"
                               autocomplete="tel">
                        @error('phone_no')
                            <span class="sk-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Next Button --}}
                    <button type="button" class="sk-btn-primary mt-2" id="nextStepBtn">
                        {{ __('labels.next_step') }} <i class="fa-solid fa-arrow-right ms-1"></i>
                    </button>

                </div>

                {{-- ════════════════════════════════ --}}
                {{-- STEP 2: Password & User Type    --}}
                {{-- ════════════════════════════════ --}}
                <div id="step2" style="display:none;">

                    {{-- Password --}}
                    <div class="sk-field">
                        <label for="password" class="sk-label required">{{ __('labels.password') }}</label>
                        <div class="sk-input-group">
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="sk-input @error('password') is-invalid @enderror"
                                   placeholder="{{ __('labels.password') }}"
                                   autocomplete="new-password">
                            <button type="button" class="sk-eye-btn signup-eye-btn" data-target="password" aria-label="Show password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <small class="sk-hint">{{ __('labels.password_hint') }}</small>
                        @error('password')
                            <span class="sk-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="sk-field">
                        <label for="password_confirmation" class="sk-label required">{{ __('labels.confirm_password') }}</label>
                        <div class="sk-input-group">
                            <input type="password"
                                   name="password_confirmation"
                                   id="password_confirmation"
                                   class="sk-input"
                                   placeholder="{{ __('labels.confirm_password') }}"
                                   autocomplete="new-password">
                            <button type="button" class="sk-eye-btn signup-eye-btn" data-target="password_confirmation" aria-label="Show confirm password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- I am a (UserType) --}}
                    <div class="sk-field mb-4">
                        <label class="sk-label required">{{ __('labels.i_am_a') }}</label>
                        <div class="signup-role-group" role="radiogroup" aria-label="{{ __('labels.i_am_a') }}">
                            @foreach ($userTypeOptions as $option)
                                <label class="signup-role-card {{ old('user_type', $userTypeOptions[0]['id'] ?? '') === $option['id'] ? 'selected' : '' }}"
                                       for="user_type_{{ $option['id'] }}">
                                    <input type="radio"
                                           name="user_type"
                                           id="user_type_{{ $option['id'] }}"
                                           value="{{ $option['id'] }}"
                                           {{ old('user_type', $userTypeOptions[0]['id'] ?? '') === $option['id'] ? 'checked' : '' }}
                                           class="signup-role-radio">
                                    @if ($option['id'] === \App\Enums\UserType::DOCTOR->value)
                                        <i class="fa-solid fa-user-doctor signup-role-icon"></i>
                                    @else
                                        <i class="fa-solid fa-user signup-role-icon"></i>
                                    @endif
                                    <span class="signup-role-label">{{ $option['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('user_type')
                            <span class="sk-error">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="button" class="sk-btn-secondary" id="backStepBtn">
                            <i class="fa-solid fa-arrow-left me-1"></i> {{ __('labels.back_step') }}
                        </button>
                        <button type="submit" class="sk-btn-primary flex-grow-1">
                            {{ __('labels.signup') }}
                        </button>
                    </div>

                </div>

            </form>

            {{-- Already have an account --}}
            <p class="sk-signup-link">
                {{ __('labels.already_have_account') }}
                <a href="{{ route('login') }}">{{ __('labels.login') }}</a>
            </p>

        </div>
    </div>

</div>

<style>
/* ════════════════════════════════════════════════════════════
   Skoracare Signup — Scoped Styles (Matches Login Page)
   ════════════════════════════════════════════════════════════ */

*, *::before, *::after { box-sizing: border-box; }
body { margin: 0; padding: 0; font-family: 'Inter', 'Laila', sans-serif; }

/* ── Wrapper — full-viewport split ───────────────────────── */
.sk-login-wrapper {
    display: flex;
    height: 100vh;
    overflow: hidden;
}

/* ── LEFT PANEL ───────────────────────────────────────────── */
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
    max-height: 120px;
}

.sk-brand-logo {
    height: 96px;
    max-height: 100%;
    width: auto;
    object-fit: contain;
}

/* ── Illustration ─────────────────────────────────────────── */
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

/* ── Tagline ──────────────────────────────────────────────── */
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

/* ── Powered by ───────────────────────────────────────────── */
.sk-powered {
    font-size: .72rem;
    color: #6aada8;
    margin: .5rem 0 0;
    position: relative;
    z-index: 1;
}

.sk-powered strong { color: #2f8f83; }

/* ── RIGHT PANEL ──────────────────────────────────────────── */
.sk-login-right {
    width: 480px;
    flex-shrink: 0;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 2.25rem;
    box-shadow: -4px 0 24px rgba(15,25,35,.06);
    height: 100%;
    overflow-y: auto;
}

/* ── Card ─────────────────────────────────────────────────── */
.sk-login-card {
    width: 100%;
    max-width: 380px;
}

/* Mobile logo */
.sk-card-logo {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 1.25rem;
}

.sk-card-logo-img { height: 98px; }

/* ── Card Title ───────────────────────────────────────────── */
.sk-card-title {
    font-size: 1.65rem;
    font-weight: 700;
    color: #0f1923;
    margin: 0 0 .25rem;
}

.sk-card-subtitle {
    font-size: .85rem;
    color: #8898a9;
    margin: 0 0 1.25rem;
}

/* ── Progress Indicator ───────────────────────────────────── */
.signup-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    margin-bottom: 1.25rem;
}

.signup-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .2rem;
}

.signup-step-dot {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e4e9ec;
    color: #8898a9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .78rem;
    font-weight: 700;
    border: 2px solid #e4e9ec;
    transition: all 200ms;
}

.signup-step.active .signup-step-dot {
    background: #2f8f83;
    color: #fff;
    border-color: #2f8f83;
}

.signup-step.done .signup-step-dot {
    background: #22c55e;
    color: #fff;
    border-color: #22c55e;
}

.signup-step-label {
    font-size: .68rem;
    color: #8898a9;
    white-space: nowrap;
    font-weight: 600;
}

.signup-step.active .signup-step-label,
.signup-step.done .signup-step-label {
    color: #1a2332;
}

.signup-step-line {
    flex: 1;
    height: 2px;
    background: #e4e9ec;
    min-width: 50px;
    margin-bottom: 0.85rem;
    transition: background 200ms;
}

/* ── Form Layout ──────────────────────────────────────────── */
.sk-form { width: 100%; }

.sk-row {
    display: flex;
    gap: .75rem;
}

.sk-col {
    flex: 1;
}

.sk-field {
    margin-bottom: 0.9rem;
}

.sk-label {
    display: block;
    font-size: .8rem;
    font-weight: 600;
    color: #1a2332;
    margin-bottom: .35rem;
}

.sk-label.required::after {
    content: " *";
    color: #ef4444;
}

/* Input base */
.sk-input {
    width: 100%;
    padding: .75rem .9rem;
    border: 1.5px solid #e4e9ec;
    border-radius: .625rem;
    font-size: .88rem;
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
    font-size: .75rem;
    color: #ef4444;
    margin-top: .25rem;
}

.sk-hint {
    display: block;
    font-size: .72rem;
    color: #8898a9;
    margin-top: .25rem;
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
    font-size: .95rem;
    transition: color 180ms;
    border-radius: 0 .625rem .625rem 0;
}

.sk-eye-btn:hover { color: #2f8f83; }

/* ── Role Card Group ──────────────────────────────────────── */
.signup-role-group {
    display: flex;
    gap: .75rem;
}

.signup-role-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .85rem .65rem;
    border: 2px solid #e4e9ec;
    border-radius: .625rem;
    cursor: pointer;
    transition: border-color 180ms, background 180ms;
    position: relative;
    text-align: center;
    background: #fafbfc;
}

.signup-role-card:hover {
    border-color: #2f8f83;
    background: #f0faf9;
}

.signup-role-card.selected {
    border-color: #2f8f83;
    background: #e8f5f4;
}

.signup-role-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.signup-role-icon {
    font-size: 1.45rem;
    color: #8898a9;
    transition: color 180ms;
}

.signup-role-card.selected .signup-role-icon,
.signup-role-card:hover .signup-role-icon {
    color: #2f8f83;
}

.signup-role-label {
    font-size: .82rem;
    font-weight: 600;
    color: #1a2332;
}

/* ── Buttons ──────────────────────────────────────────────── */
.sk-btn-primary {
    width: 100%;
    padding: .8rem;
    background: #2f8f83;
    color: #fff;
    border: none;
    border-radius: .625rem;
    font-size: .95rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background 180ms, box-shadow 180ms;
}

.sk-btn-primary:hover {
    background: #236d64;
    box-shadow: 0 4px 16px rgba(47,143,131,.3);
}

.sk-btn-secondary {
    padding: .8rem 1.1rem;
    background: transparent;
    color: #5a6b7c;
    border: 1.5px solid #d1d8dd;
    border-radius: .625rem;
    font-size: .88rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: background 180ms, border-color 180ms;
}

.sk-btn-secondary:hover {
    background: #f1f4f6;
    border-color: #b5bec6;
}

/* ── Sign Up link ─────────────────────────────────────────── */
.sk-signup-link {
    margin-top: 1.25rem;
    text-align: center;
    font-size: .83rem;
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
        padding-top: 2rem;
    }

    .sk-row {
        flex-direction: column;
        gap: 0;
    }
}
</style>

@endsection

@section('scripts')
<script>
(function () {
    'use strict';

    /* ── Elements ── */
    var step1        = document.getElementById('step1');
    var step2        = document.getElementById('step2');
    var nextBtn      = document.getElementById('nextStepBtn');
    var backBtn      = document.getElementById('backStepBtn');
    var progress1    = document.getElementById('progressStep1');
    var progress2    = document.getElementById('progressStep2');

    /* ── Step 1 → Step 2: Validate Step 1 fields client-side ── */
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            var firstName = document.getElementById('first_name').value.trim();
            var email     = document.getElementById('email').value.trim();
            var phone     = document.getElementById('phone_no').value.trim();

            if (!firstName || !email || !phone) {
                highlightEmpty(['first_name', 'email', 'phone_no']);
                return;
            }

            step1.style.display = 'none';
            step2.style.display = 'block';
            progress1.classList.remove('active');
            progress1.classList.add('done');
            progress2.classList.add('active');
        });
    }

    /* ── Step 2 → Step 1: Back button ── */
    if (backBtn) {
        backBtn.addEventListener('click', function () {
            step2.style.display = 'none';
            step1.style.display = 'block';
            progress2.classList.remove('active');
            progress1.classList.remove('done');
            progress1.classList.add('active');
        });
    }

    /* ── Role card radio toggle highlight ── */
    document.querySelectorAll('.signup-role-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.signup-role-card').forEach(function (card) {
                card.classList.remove('selected');
            });
            this.closest('.signup-role-card').classList.add('selected');
        });
    });

    /* ── Password eye toggle ── */
    document.querySelectorAll('.signup-eye-btn').forEach(function (btn) {
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

    /* ── If backend returned validation errors for step 2 → show step 2 ── */
    var hasStep2Errors = {{ $errors->has('password') || $errors->has('password_confirmation') || $errors->has('user_type') ? 'true' : 'false' }};
    if (hasStep2Errors && step1 && step2) {
        step1.style.display = 'none';
        step2.style.display = 'block';
        progress1.classList.remove('active');
        progress1.classList.add('done');
        progress2.classList.add('active');
    }

    /* ── Helper: highlight empty required fields ── */
    function highlightEmpty(ids) {
        ids.forEach(function (id) {
            var el = document.getElementById(id);
            if (el && !el.value.trim()) {
                el.classList.add('is-invalid');
                el.addEventListener('input', function () {
                    this.classList.remove('is-invalid');
                }, { once: true });
            }
        });
    }
}());
</script>
@endsection
