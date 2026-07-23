@extends('layouts.app')

@section('title', __('labels.dashboard'))

@push('styles')
    @vite(['resources/css/doctor/doctordashboard.css'])
@endpush

@section('content')

    @php
        /** @var \App\Models\User $authUser */
        $authUser   = $dashboardData['user'] ?? auth()->user();
        $doctorName = trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? ''));
        $roleName   = $authUser->getRoleNames()->first() ?? 'User';

        /* Clinic count for the logged-in user */
        $userClinics = method_exists($authUser, 'clinics') ? $authUser->clinics()->latest()->get() : collect();
        $clinicCount = $userClinics->count();

        /* Active status label */
        $isActive    = $authUser->status === \App\Enums\CommonStatus::ACTIVE->value;
        $statusLabel = $isActive ? __('labels.active') : __('labels.inactive');
        $isDoctor    = $dashboardData['isDoctor'] ?? $authUser->hasRole(config('constants.doctor_role_name'));
    @endphp

    <div class="doctor-page">
        <div class="doctor-page-inner p-0">

            {{-- Header Row: Page Title + Quick Actions --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3 pt-2">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="mb-0" style="font-weight: 700; color: var(--dr-navy); font-size: 1.5rem; letter-spacing: -0.02em;">
                        {{ __('labels.dashboard') }}
                    </h3>
                    <span class="badge bg-primary rounded-pill px-3 py-2 text-capitalize" style="font-size: 0.78rem;">
                        {{ $roleName }}
                    </span>
                </div>
                
                {{-- Quick Actions --}}
                <div class="dr-quick-actions mb-0">
                    @can('clinic-list')
                        <a href="{{ route('admin.clinics.index') }}" class="dr-quick-btn dr-quick-btn-primary">
                            <i class="fa-solid fa-hospital"></i>
                            {{ __('labels.my_clinics') }}
                        </a>
                    @endcan
                    @can('doctor-list')
                        <a href="{{ route('admin.doctors.index') }}" class="dr-quick-btn dr-quick-btn-outline">
                            <i class="fa-solid fa-user-doctor"></i>
                            {{ __('labels.doctors') }}
                        </a>
                    @endcan
                    @can('user-list')
                        <a href="{{ route('admin.users.index') }}" class="dr-quick-btn dr-quick-btn-outline">
                            <i class="fa-solid fa-users"></i>
                            {{ __('labels.users') }}
                        </a>
                    @endcan
                    @can('role-list')
                        <a href="{{ route('admin.roles.index') }}" class="dr-quick-btn dr-quick-btn-outline">
                            <i class="fa-solid fa-users-gear"></i>
                            {{ __('labels.roles') }}
                        </a>
                    @endcan
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

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show mb-4 py-2 px-3" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ── Stats Grid ────────────────────────────────────────── --}}
            <div class="dr-stat-grid mb-4">

                {{-- Admin / System Global Stats Cards (@can guarded via DashboardService) --}}
                @foreach ($dashboardData['stats'] ?? [] as $stat)
                    @if (!empty($stat['url']))
                        <a href="{{ $stat['url'] }}" class="dr-stat-card text-decoration-none">
                            <div class="dr-stat-icon">
                                <i class="{{ $stat['icon'] }}"></i>
                            </div>
                            <div class="dr-stat-body">
                                <div class="dr-stat-label">{{ $stat['label'] }}</div>
                                <div class="dr-stat-value">{{ number_format((int) $stat['value']) }}</div>
                                <div class="dr-stat-sub">Active platform data</div>
                            </div>
                        </a>
                    @else
                        <div class="dr-stat-card">
                            <div class="dr-stat-icon">
                                <i class="{{ $stat['icon'] }}"></i>
                            </div>
                            <div class="dr-stat-body">
                                <div class="dr-stat-label">{{ $stat['label'] }}</div>
                                <div class="dr-stat-value">{{ number_format((int) $stat['value']) }}</div>
                                <div class="dr-stat-sub">Active platform data</div>
                            </div>
                        </div>
                    @endif
                @endforeach

                {{-- Doctor Specific Cards --}}
                @if ($isDoctor || !empty($authUser->qualification) || !empty($authUser->registration_number))
                    {{-- Total Clinics --}}
                    @can('clinic-list')
                        <a href="{{ route('admin.clinics.index') }}" class="dr-stat-card text-decoration-none">
                            <div class="dr-stat-icon">
                                <i class="fa-solid fa-hospital"></i>
                            </div>
                            <div class="dr-stat-body">
                                <div class="dr-stat-label">{{ __('labels.my_clinics') }}</div>
                                <div class="dr-stat-value">{{ $clinicCount }}</div>
                                <div class="dr-stat-sub">{{ __('labels.registered_clinics') }}</div>
                            </div>
                        </a>
                    @endcan

                    {{-- Qualification --}}
                    <div class="dr-stat-card accent-purple">
                        <div class="dr-stat-icon">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div class="dr-stat-body">
                            <div class="dr-stat-label">{{ __('labels.qualification') }}</div>
                            <div class="dr-stat-value" style="font-size: 1.1rem; line-height: 1.3;">
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
                            <div class="dr-stat-value" style="font-size: 1.1rem; line-height: 1.3;">
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
                @endif

            </div>

            {{-- ── Info Grid: Profile + Clinics ─────────────────────── --}}
            @if ($isDoctor || !empty($authUser->qualification) || !empty($authUser->registration_number))
                <div class="dr-info-grid mb-4">

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
                                        <img src="{{ Storage::url('profile_images/' . $authUser->profile_pic) }}" alt="{{ $doctorName }}" class="dr-avatar">
                                    @else
                                        <div class="dr-avatar-placeholder">
                                            {{ strtoupper(substr($authUser->first_name ?? 'D', 0, 1)) }}{{ strtoupper(substr($authUser->last_name ?? 'R', 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="dr-profile-title">
                                    <h4>Dr. {{ $doctorName ?: 'User' }}</h4>
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
                                @if(auth()->user()->canCreateClinic())
                                    <a href="{{ route('admin.clinics.create') }}"
                                       class="dr-quick-btn dr-quick-btn-primary"
                                       style="padding:.35rem .85rem;font-size:.78rem; border-radius: 6px;">
                                        <i class="fa-solid fa-plus"></i>
                                        {{ __('labels.add_clinic') }}
                                    </a>
                                @else
                                    <button type="button" 
                                            class="dr-quick-btn dr-quick-btn-outline text-muted" 
                                            disabled 
                                            title="Clinic creation limit reached for your active package plan"
                                            style="padding:.35rem .85rem;font-size:.78rem; border-radius: 6px; cursor: not-allowed; opacity: 0.6;">
                                        <i class="fa-solid fa-lock"></i>
                                        {{ __('labels.add_clinic') }} (Limit Reached)
                                    </button>
                                @endif
                            @endcan
                        </div>
                        <div class="dr-card-body">

                            @forelse ($userClinics->take(5) as $clinic)
                                <div class="dr-clinic-item">
                                    <div class="dr-clinic-icon">
                                        <i class="fa-solid fa-clinic-medical"></i>
                                    </div>
                                    <div class="dr-clinic-info">
                                        <p class="dr-clinic-name">{{ $clinic->name }}</p>
                                        <p class="dr-clinic-fee">
                                            {{ __('labels.consultation_fee') }}:
                                            <span>₹{{ number_format($clinic->consultation_fee ?? 0, 2) }}</span>
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="dr-empty-state">
                                    <div class="dr-empty-icon">
                                        <i class="fa-solid fa-hospital-user"></i>
                                    </div>
                                    <h5>No Clinics Registered</h5>
                                    <p class="mb-0">You don't have any clinics registered under your account yet.</p>
                                    @can('clinic-create')
                                        @if(auth()->user()->canCreateClinic())
                                            <a href="{{ route('admin.clinics.create') }}" class="dr-quick-btn dr-quick-btn-primary mt-3">
                                                <i class="fa-solid fa-plus"></i>
                                                Register First Clinic
                                            </a>
                                        @endif
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
            @endif

            {{-- ── Latest Activities Table Card (guarded by @can) ──────────────── --}}
            @if ($dashboardData['showLatestActivities'] ?? false)
                <div class="dr-card mb-4">
                    <div class="dr-card-header">
                        <h3 class="dr-card-title">{{ __('labels.latest_activities') }}</h3>
                        @if (!empty($dashboardData['activityLogUrl']))
                            <a href="{{ $dashboardData['activityLogUrl'] }}" class="dr-quick-btn dr-quick-btn-outline" style="padding:.35rem .85rem;font-size:.78rem; border-radius: 6px;">
                                {{ __('labels.view_all') }}
                            </a>
                        @endif
                    </div>
                    <div class="dr-card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('labels.name') }}</th>
                                        <th>{{ __('labels.description') }}</th>
                                        <th>{{ __('labels.changes') }}</th>
                                        <th>{{ __('labels.ip_address') }}</th>
                                        <th>{{ __('labels.created_by') }}</th>
                                        <th>{{ __('labels.created_at') }}</th>
                                        <th class="text-center">{{ __('labels.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($dashboardData['latestActivities'] ?? [] as $activity)
                                        <tr>
                                            <td><strong>{{ $activity['name'] }}</strong></td>
                                            <td>{{ $activity['description'] }}</td>
                                            <td style="white-space: pre-line; font-size: 0.82rem; color: var(--dr-text-muted);">{{ $activity['changes'] }}</td>
                                            <td><code>{{ $activity['ip_address'] }}</code></td>
                                            <td>{{ $activity['created_by'] }}</td>
                                            <td><small class="text-muted">{{ $activity['created_at'] }}</small></td>
                                            <td class="text-center">
                                                @if (!empty($activity['show_url']))
                                                    @include('layouts.partials.dataTable-action-button', [
                                                        'viewModalRoute' => $activity['show_url'],
                                                        'modalData' => [
                                                            'showModalTitle' => __('labels.show_title', ['action' => __('labels.activity_log')]),
                                                        ],
                                                    ])
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-muted">
                                                {{ __('labels.no_activity_found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <x-show-base-modal>
        <div class="detail-grid">
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.log_id') }}</div>
                <div class="detail-value" data-field="log_id"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.name') }}</div>
                <div class="detail-value" data-field="name"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.event') }}</div>
                <div class="detail-value" data-field="event"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.subject') }}</div>
                <div class="detail-value" data-field="subject"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.causer') }}</div>
                <div class="detail-value" data-field="causer"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.ip_address') }}</div>
                <div class="detail-value" data-field="ip_address"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.created_by') }}</div>
                <div class="detail-value" data-field="created_by"></div>
            </div>
            <div class="detail-item">
                <div class="detail-label">{{ __('labels.created_at') }}</div>
                <div class="detail-value" data-field="created_at"></div>
            </div>
            <div class="detail-item detail-item-full">
                <div class="detail-label">{{ __('labels.description') }}</div>
                <div class="detail-value" data-field="description"></div>
            </div>
            <div class="detail-item detail-item-full">
                <div class="detail-label">{{ __('labels.properties') }}</div>
                <div class="detail-value" data-field="properties"></div>
            </div>
        </div>
    </x-show-base-modal>

@endsection
