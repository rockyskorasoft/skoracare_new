@extends('layouts.auth')

@section('title', __('labels.signup'))

@section('content')
<section class="login-sec signup-sec">
    <div class="container">
        <div class="row justify-content-center flex-column align-items-center">
            <div class="col-xl-5 col-xxl-4 col-lg-7 col-md-9">
                <div class="card auth-card signup-card">
                    <div class="card-body p-4 p-md-5">

                        {{-- ── Logo ── --}}
                        <div class="text-center mb-3">
                            <div class="sidebar-brand d-inline-flex">
                                <div class="sidebar-brand-icon">
                                    <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                                         alt="Skoracare {{ __('labels.logo') }}" />
                                </div>
                            </div>
                        </div>

                        {{-- ── Title ── --}}
                        <h2 class="my-2 text-center fw-bold text-dark">{{ __('labels.signup_title') }}</h2>
                        <p class="text-center text-muted mb-4" style="font-size:.88rem;">
                            {{ __('labels.signup_subtitle') }}
                        </p>

                        {{-- ── Progress Indicator ── --}}
                        <div class="signup-progress mb-4" aria-label="{{ __('labels.signup') }} progress">
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

                        {{-- ── Flash Messages ── --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- ── Signup Form ── --}}
                        <form method="POST"
                              action="{{ route('signup.submit') }}"
                              class="row g-3"
                              id="signupForm"
                              novalidate>
                            @csrf

                            {{-- ════════════════════════════════ --}}
                            {{-- STEP 1: Personal Info            --}}
                            {{-- ════════════════════════════════ --}}
                            <div id="step1">

                                {{-- First Name --}}
                                <div class="col-12 mb-3">
                                    <label for="first_name" class="form-label required">
                                        {{ __('labels.first_name') }}
                                    </label>
                                    <input type="text"
                                           name="first_name"
                                           id="first_name"
                                           class="form-control @error('first_name') is-invalid @enderror"
                                           value="{{ old('first_name') }}"
                                           placeholder="{{ __('labels.first_name') }}"
                                           autocomplete="given-name">
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Last Name --}}
                                <div class="col-12 mb-3">
                                    <label for="last_name" class="form-label">
                                        {{ __('labels.last_name') }}
                                    </label>
                                    <input type="text"
                                           name="last_name"
                                           id="last_name"
                                           class="form-control @error('last_name') is-invalid @enderror"
                                           value="{{ old('last_name') }}"
                                           placeholder="{{ __('labels.last_name') }}"
                                           autocomplete="family-name">
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label required">
                                        {{ __('labels.email') }}
                                    </label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           placeholder="{{ __('labels.email') }}"
                                           autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Phone Number --}}
                                <div class="col-12 mb-4">
                                    <label for="phone_no" class="form-label required">
                                        {{ __('labels.mobile_number') }}
                                    </label>
                                    <input type="tel"
                                           name="phone_no"
                                           id="phone_no"
                                           class="form-control @error('phone_no') is-invalid @enderror"
                                           value="{{ old('phone_no') }}"
                                           placeholder="{{ __('labels.mobile_number') }}"
                                           autocomplete="tel">
                                    @error('phone_no')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Next Step Button --}}
                                <div class="d-grid">
                                    <button type="button"
                                            class="btn btn-primary"
                                            id="nextStepBtn">
                                        {{ __('labels.next_step') }}
                                        <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </button>
                                </div>

                            </div>{{-- /step1 --}}

                            {{-- ════════════════════════════════ --}}
                            {{-- STEP 2: Password & User Type    --}}
                            {{-- ════════════════════════════════ --}}
                            <div id="step2" style="display:none;">

                                {{-- Password --}}
                                <div class="col-12 mb-3">
                                    <label for="password" class="form-label required">
                                        {{ __('labels.password') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="password"
                                               id="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               placeholder="{{ __('labels.password') }}"
                                               autocomplete="new-password">
                                        <button class="btn btn-outline-secondary signup-eye-btn"
                                                type="button"
                                                data-target="password"
                                                aria-label="Show password">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <small class="text-muted">{{ __('labels.password_hint') }}</small>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="col-12 mb-3">
                                    <label for="password_confirmation" class="form-label required">
                                        {{ __('labels.confirm_password') }}
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="password_confirmation"
                                               id="password_confirmation"
                                               class="form-control"
                                               placeholder="{{ __('labels.confirm_password') }}"
                                               autocomplete="new-password">
                                        <button class="btn btn-outline-secondary signup-eye-btn"
                                                type="button"
                                                data-target="password_confirmation"
                                                aria-label="Show confirm password">
                                            <i class="fa-regular fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                {{-- I am a (UserType) --}}
                                <div class="col-12 mb-4">
                                    <label for="user_type" class="form-label required">
                                        {{ __('labels.i_am_a') }}
                                    </label>
                                    <div class="signup-role-group" role="radiogroup" aria-label="{{ __('labels.i_am_a') }}">
                                        @foreach ($userTypeOptions as $option)
                                            <label class="signup-role-card {{ old('user_type') === $option['id'] ? 'selected' : '' }}"
                                                   for="user_type_{{ $option['id'] }}">
                                                <input type="radio"
                                                       name="user_type"
                                                       id="user_type_{{ $option['id'] }}"
                                                       value="{{ $option['id'] }}"
                                                       {{ old('user_type') === $option['id'] ? 'checked' : '' }}
                                                       class="signup-role-radio">
                                                {{-- Icon per type --}}
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
                                        <span class="text-danger" style="font-size:.85rem;">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex gap-2">
                                    <button type="button"
                                            class="btn btn-outline-secondary flex-shrink-0"
                                            id="backStepBtn">
                                        <i class="fa-solid fa-arrow-left me-1"></i>
                                        {{ __('labels.back_step') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-grow-1">
                                        {{ __('labels.signup') }}
                                    </button>
                                </div>

                            </div>{{-- /step2 --}}

                        </form>

                        {{-- Already have account --}}
                        <p class="mt-4 text-center text-muted" style="font-size:.85rem;">
                            {{ __('labels.already_have_account') }}
                            <a href="{{ route('login') }}" class="fw-semibold">{{ __('labels.login') }}</a>
                        </p>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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

    /* ── Step 1 → Step 2: Validate Step 1 fields client-side first ── */
    nextBtn.addEventListener('click', function () {
        var firstName = document.getElementById('first_name').value.trim();
        var email     = document.getElementById('email').value.trim();
        var phone     = document.getElementById('phone_no').value.trim();

        /* Basic client-side check before moving to step 2 */
        if (!firstName || !email || !phone) {
            highlightEmpty(['first_name', 'email', 'phone_no']);
            return;
        }

        /* Transition to step 2 */
        step1.style.display = 'none';
        step2.style.display = 'block';
        progress1.classList.remove('active');
        progress1.classList.add('done');
        progress2.classList.add('active');
    });

    /* ── Step 2 → Step 1: Back button ── */
    backBtn.addEventListener('click', function () {
        step2.style.display = 'none';
        step1.style.display = 'block';
        progress2.classList.remove('active');
        progress1.classList.remove('done');
        progress1.classList.add('active');
    });

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

    /* ── If backend returned validation errors → show step 2 if needed ── */
    var hasStep2Errors = {{ $errors->has('password') || $errors->has('password_confirmation') || $errors->has('user_type') ? 'true' : 'false' }};
    if (hasStep2Errors) {
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

<style>
/* ── Signup-specific styles (scoped inside this view) ── */

/* Progress indicator */
.signup-progress {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
}

.signup-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .25rem;
}

.signup-step-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e4e9ec;
    color: #8898a9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .8rem;
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
    font-size: .7rem;
    color: #8898a9;
    white-space: nowrap;
    font-weight: 500;
}

.signup-step.active .signup-step-label,
.signup-step.done .signup-step-label {
    color: #1a2332;
}

.signup-step-line {
    flex: 1;
    height: 2px;
    background: #e4e9ec;
    min-width: 60px;
    margin-bottom: 1rem;
    transition: background 200ms;
}

/* Role card radio group */
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
    gap: .4rem;
    padding: 1rem .75rem;
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
    font-size: 1.6rem;
    color: #8898a9;
    transition: color 180ms;
}

.signup-role-card.selected .signup-role-icon,
.signup-role-card:hover .signup-role-icon {
    color: #2f8f83;
}

.signup-role-label {
    font-size: .875rem;
    font-weight: 600;
    color: #1a2332;
}

/* Eye button style */
.signup-eye-btn {
    border-color: #dee2e6;
    color: #6c757d;
}

/* Password hint */
small.text-muted {
    font-size: .75rem;
}
</style>
@endsection
