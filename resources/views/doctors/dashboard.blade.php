{{-- ============================================================
     doctors/dashboard.blade.php
     Doctor Dashboard — Skoracare
     Extends: doctors/layouts/doctor-app.blade.php
     Uses config('constants.*') and __('labels.*')
     ============================================================ --}}
@extends('doctors.layouts.doctor-app')

@section('title', __('labels.doctor_dashboard'))

{{-- Page title shown in the top header bar --}}
@section('header_title', __('labels.dashboard'))

@section('content')

    @php
        /** @var \App\Models\User $authUser */
        $authUser   = auth()->user();
        $doctorName = trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? ''));

        /* Clinic count for the logged-in doctor */
        $clinicCount = $authUser->clinics()->count();

        /* Active status label */
        $isActive    = $authUser->status === \App\Enums\CommonStatus::ACTIVE->value;
        $statusLabel = $isActive ? __('labels.active') : __('labels.inactive');
    @endphp

    {{-- Header Row: Page Title + Quick Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h3 class="mb-0" style="font-weight: 700; color: var(--dr-navy); font-size: 1.5rem; letter-spacing: -0.02em;">
            {{ __('labels.dashboard') }}
        </h3>
        
        {{-- Quick Actions --}}
        <div class="dr-quick-actions mb-0">
            <a href="{{ route('admin.clinics.index') }}" class="dr-quick-btn dr-quick-btn-primary">
                <i class="fa-solid fa-hospital"></i>
                {{ __('labels.my_clinics') }}
            </a>
            <a href="{{ route('admin.edit-user-profile') }}" class="dr-quick-btn dr-quick-btn-outline">
                <i class="fa-solid fa-user-pen"></i>
                {{ __('labels.edit_profile') }}
            </a>
            <a href="{{ route('admin.change-password') }}" class="dr-quick-btn dr-quick-btn-outline">
                <i class="fa-solid fa-key"></i>
                {{ __('labels.change_password') }}
            </a>
        </div>
    </div>

    {{-- ── Stats Grid ────────────────────────────────────────── --}}
    <div class="dr-stat-grid">

        {{-- Total Clinics --}}
        <a href="{{ route('admin.clinics.index') }}" class="dr-stat-card">
            <div class="dr-stat-icon">
                <i class="fa-solid fa-hospital"></i>
            </div>
            <div class="dr-stat-body">
                <div class="dr-stat-label">{{ __('labels.my_clinics') }}</div>
                <div class="dr-stat-value">{{ $clinicCount }}</div>
                <div class="dr-stat-sub">{{ __('labels.registered_clinics') }}</div>
            </div>
        </a>

        {{-- Qualification --}}
        <div class="dr-stat-card accent-purple">
            <div class="dr-stat-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="dr-stat-body">
                <div class="dr-stat-label">{{ __('labels.qualification') }}</div>
                <div class="dr-stat-value">
                    {{ $authUser->qualification ?? '—' }}
                </div>
                <div class="dr-stat-sub">{{ __('labels.medical_qualification') }}</div>
            </div>
        </div>

        {{-- Registration Number --}}
        <div class="dr-stat-card accent-orange">
            <div class="dr-stat-icon">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <div class="dr-stat-body">
                <div class="dr-stat-label">{{ __('labels.reg_number') }}</div>
                <div class="dr-stat-value">
                    {{ $authUser->registration_number ?? '—' }}
                </div>
                <div class="dr-stat-sub">{{ __('labels.medical_council_reg') }}</div>
            </div>
        </div>

        {{-- Account Status --}}
        <div class="dr-stat-card {{ $isActive ? '' : 'accent-rose' }}">
            <div class="dr-stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="dr-stat-body">
                <div class="dr-stat-label">{{ __('labels.account_status') }}</div>
                <div class="dr-stat-value">
                    <span class="dr-badge {{ $isActive ? 'dr-badge-active' : 'dr-badge-inactive' }}">
                        <i class="fa-solid fa-circle" style="font-size:.4rem;"></i>
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="dr-stat-sub">Skoracare {{ __('labels.account') }}</div>
            </div>
        </div>

    </div>

    {{-- ── Info Grid: Profile + Clinics ─────────────────────── --}}
    <div class="dr-info-grid">

        {{-- Doctor Profile Card --}}
        <div class="dr-card">
            <div class="dr-card-header">
                <h3 class="dr-card-title">
                    {{ __('labels.doctor_profile') }}
                </h3>
                <a href="{{ route('admin.edit-user-profile') }}"
                   class="dr-quick-btn dr-quick-btn-outline"
                   style="padding:.35rem .85rem;font-size:.78rem; border-radius: 6px;">
                    <i class="fa-solid fa-pen"></i>
                    {{ __('labels.edit') }}
                </a>
            </div>
            <div class="dr-card-body">
                {{-- Profile Summary Header --}}
                <div class="dr-profile-summary">
                    <div class="dr-avatar-wrapper">
                        @if(!empty($authUser->profile_pic))
                            <img src="{{ Storage::url('profile_images/' . $authUser->profile_pic) }}" alt="Dr. {{ $authUser->first_name }}" class="dr-avatar">
                        @else
                            <div class="dr-avatar-placeholder">
                                {{ strtoupper(substr($authUser->first_name ?? 'D', 0, 1)) }}{{ strtoupper(substr($authUser->last_name ?? 'R', 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="dr-profile-title">
                        <h4>Dr. {{ $doctorName ?: 'Rocky Kumar' }}</h4>
                        <span class="dr-badge {{ $isActive ? 'dr-badge-active' : 'dr-badge-inactive' }}">
                            <i class="fa-solid fa-circle" style="font-size:.4rem;"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                {{-- Profile Details Grid --}}
                <div class="dr-profile-grid">
                    <div class="dr-profile-grid-item">
                        <div class="dr-item-icon"><i class="fa-solid fa-envelope"></i></div>
                        <div class="dr-item-text">
                            <span class="dr-item-label">{{ __('labels.email') }}</span>
                            <span class="dr-item-value">{{ $authUser->email }}</span>
                        </div>
                    </div>
                    
                    <div class="dr-profile-grid-item">
                        <div class="dr-item-icon"><i class="fa-solid fa-phone"></i></div>
                        <div class="dr-item-text">
                            <span class="dr-item-label">{{ __('labels.mobile_number') }}</span>
                            <span class="dr-item-value">{{ $authUser->phone_no ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="dr-profile-grid-item">
                        <div class="dr-item-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                        <div class="dr-item-text">
                            <span class="dr-item-label">{{ __('labels.qualification') }}</span>
                            <span class="dr-item-value">{{ $authUser->qualification ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="dr-profile-grid-item">
                        <div class="dr-item-icon"><i class="fa-solid fa-id-card"></i></div>
                        <div class="dr-item-text">
                            <span class="dr-item-label">{{ __('labels.reg_number') }}</span>
                            <span class="dr-item-value">{{ $authUser->registration_number ?? '—' }}</span>
                        </div>
                    </div>

                    <div class="dr-profile-grid-item dr-grid-item-full">
                        <div class="dr-item-icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div class="dr-item-text">
                            <span class="dr-item-label">{{ __('labels.address') }}</span>
                            <span class="dr-item-value">{{ $authUser->address ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- My Clinics Card --}}
        <div class="dr-card">
            <div class="dr-card-header">
                <h3 class="dr-card-title">
                    {{ __('labels.my_clinics') }}
                </h3>
                @can('clinic-create')
                    <a href="{{ route('admin.clinics.create') }}"
                       class="dr-quick-btn dr-quick-btn-primary"
                       style="padding:.35rem .85rem;font-size:.78rem; border-radius: 6px;">
                        <i class="fa-solid fa-plus"></i>
                        {{ __('labels.add_clinic') }}
                    </a>
                @endcan
            </div>
            <div class="dr-card-body">

                @forelse ($authUser->clinics()->latest()->take(5)->get() as $clinic)
                    <div class="dr-clinic-item">
                        <div class="dr-clinic-icon">
                            <i class="fa-solid fa-clinic-medical"></i>
                        </div>
                        <div class="dr-clinic-info">
                            <p class="dr-clinic-name">{{ $clinic->name }}</p>
                            <p class="dr-clinic-fee">
                                {{ __('labels.consultation_fee') }}:
                                <span>₹{{ number_format($clinic->consultation_fee, 2) }}</span>
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="dr-empty-state">
                        <div class="dr-empty-icon">
                            <i class="fa-solid fa-hospital-user"></i>
                        </div>
                        <h5>No Clinics Registered</h5>
                        <p class="mb-0">You don't have any clinics registered under your doctor account yet.</p>
                        @can('clinic-create')
                            <a href="{{ route('admin.clinics.create') }}" class="dr-quick-btn dr-quick-btn-primary mt-3">
                                <i class="fa-solid fa-plus"></i>
                                Register First Clinic
                            </a>
                        @endcan
                    </div>
                @endforelse

                @if ($clinicCount > 5)
                    <div class="mt-3 text-center">
                        <a href="{{ route('admin.clinics.index') }}"
                           style="font-size:.82rem;color:var(--dr-teal);text-decoration:none;font-weight:600; display: inline-flex; align-items: center; gap: .3rem;">
                            {{ __('labels.view_all') }} ({{ $clinicCount }})
                            <i class="fa-solid fa-arrow-right fa-xs"></i>
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>

@endsection
