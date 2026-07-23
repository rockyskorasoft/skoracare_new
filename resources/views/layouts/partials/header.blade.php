{{-- ============================================================
     layouts/partials/header.blade.php
     Top header bar for the Skoracare panel with multi-clinic switching.
     Brand: Skoracare | Full-width header with top-left logo
     ============================================================ --}}
@php
    /** @var \App\Models\User $authUser */
    $authUser = auth()->user();
    $isSuperAdminOrAdmin = $authUser && $authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')]);
    $isDoctor = $authUser && $authUser->hasRole(config('constants.doctor_role_name'));
    
    // Fetch clinics: 
    // - Super Admin / Admin: all clinics
    // - Doctor: doctor's owned clinics
    // - Staff: only assigned clinics (via assignedClinics pivot table)
    if ($isSuperAdminOrAdmin) {
        $clinics = \App\Models\Clinic::latest()->get();
    } elseif ($isDoctor) {
        $clinics = $authUser->clinics()->latest()->get();
    } elseif ($authUser) {
        $assigned = $authUser->assignedClinics()->latest()->get();
        if ($assigned->isNotEmpty()) {
            $clinics = $assigned;
        } else {
            // Fallback to creator doctor's clinics if no specific assigned clinics in pivot
            $clinics = $authUser->creator ? $authUser->creator->clinics()->latest()->get() : collect();
        }
    } else {
        $clinics = collect();
    }

    $activeClinicId = session('active_clinic_id');
    
    if ($isSuperAdminOrAdmin) {
        if ($activeClinicId === 'all' || empty($activeClinicId)) {
            $activeClinic = null;
            $activeClinicName = 'All Clinics';
        } else {
            $activeClinic = $clinics->firstWhere('id', $activeClinicId);
            $activeClinicName = $activeClinic->name ?? 'All Clinics';
        }
    } else {
        $activeClinic = $clinics->firstWhere('id', $activeClinicId) ?? $clinics->first();
        $activeClinicName = $activeClinic->name ?? ($clinics->first()->name ?? 'No Clinic Assigned');
        if ($activeClinic && session('active_clinic_id') !== $activeClinic->id) {
            session(['active_clinic_id' => $activeClinic->id]);
        }
    }
@endphp

<header class="doctor-header" id="drHeader">
    
    {{-- Left Section: App Logo & Brand Name --}}
    <div class="dr-header-left">
        <a href="{{ route('admin.dashboard.index') }}" class="dr-header-logo-wrap" title="Skoracare">
            <img src="{{ Vite::asset(config('constants.company_logo')) }}"
                 alt="Skoracare {{ __('labels.logo') }}"
                 class="dr-header-logo-img">
        </a>

        {{-- Mobile Hamburger --}}
        <button class="dr-header-hamburger d-lg-none"
                id="drMobileHamburger"
                aria-label="{{ __('labels.open_menu') }}">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    {{-- Right Section: Clinic Dropdown, Action Icons & Profile Dropdown --}}
    <div class="dr-header-right">

        {{-- Clinic Selector Dropdown --}}
        <div class="dr-header-dropdown">
            <button class="dr-dropdown-toggle" id="drClinicDropdownBtn" type="button" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-hospital me-1 text-primary"></i>
                <span class="dr-clinic-active-name">{{ $activeClinicName }}</span>
                <i class="fa-solid fa-chevron-down dr-dropdown-arrow"></i>
            </button>

            <ul class="dr-dropdown-menu shadow-sm" id="drClinicDropdownMenu">
                @if($isSuperAdminOrAdmin)
                    <li class="px-3 py-1 text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                        Select View Context
                    </li>
                    <li>
                        <a href="javascript:void(0);" 
                           class="dr-dropdown-item d-flex align-items-center justify-content-between {{ ($activeClinicId === 'all' || empty($activeClinicId)) ? 'active-clinic fw-bold text-primary bg-light' : '' }}" 
                           onclick="drSwitchClinic('all', 'All Clinics')">
                            <span><i class="fa-solid fa-globe me-2 text-primary opacity-75"></i>All Clinics (Global View)</span>
                            @if($activeClinicId === 'all' || empty($activeClinicId))
                                <i class="fa-solid fa-circle-check text-primary ms-2"></i>
                            @endif
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                @endif

                @if($clinics->count() > 0)
                    <li class="px-3 py-1 text-uppercase text-muted fw-bold" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                        {{ __('labels.select_clinic') ?? 'Select Clinic' }}
                    </li>
                    @foreach($clinics as $clinic)
                        <li>
                            <a href="javascript:void(0);" 
                               class="dr-dropdown-item d-flex align-items-center justify-content-between {{ ($activeClinic && $activeClinic->id == $clinic->id) ? 'active-clinic fw-bold text-primary bg-light' : '' }}" 
                               data-clinic-id="{{ $clinic->id }}"
                               onclick="drSwitchClinic({{ $clinic->id }}, '{{ addslashes($clinic->name) }}')">
                                <span><i class="fa-solid fa-clinic-medical me-2 opacity-75"></i>{{ $clinic->name }}</span>
                                @if($activeClinic && $activeClinic->id == $clinic->id)
                                    <i class="fa-solid fa-circle-check text-primary ms-2"></i>
                                @endif
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="px-3 py-2 text-muted small">No Clinics Available</li>
                @endif

                @can('clinic-create')
                    <li><hr class="dropdown-divider my-1"></li>
                    @if(auth()->user()->canCreateClinic())
                        <li>
                            <a href="{{ route('admin.clinics.create') }}" class="dr-dropdown-item text-primary fw-semibold">
                                <i class="fa-solid fa-plus me-2"></i>{{ __('labels.add_clinic') ?? 'Add New Clinic' }}
                            </a>
                        </li>
                    @else
                        <li>
                            <span class="dr-dropdown-item text-muted small" title="Clinic limit reached for your assigned package plan" style="cursor: not-allowed; opacity: 0.6;">
                                <i class="fa-solid fa-lock me-2"></i>Add Clinic (Limit Reached)
                            </span>
                        </li>
                    @endif
                @endcan
            </ul>
        </div>

        {{-- Action Icons (Video call, Profile Settings & Logout) --}}
        <div class="dr-header-actions">
            <a href="javascript:void(0);" class="dr-action-btn" title="Video Consultations">
                <i class="fa-regular fa-circle-play"></i>
            </a>
            <a href="{{ route('admin.edit-user-profile') }}" class="dr-action-btn" title="Profile Settings">
                <i class="fa-solid fa-gear"></i>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline m-0 p-0">
                @csrf
                <button type="submit" class="dr-action-btn logout-btn border-0 bg-transparent text-danger" title="{{ __('labels.logout') }}">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>

        {{-- User Profile Dropdown Menu --}}
        <div class="dr-header-user-dropdown dropdown">
            <button class="dr-avatar-btn dropdown-toggle border-0 bg-transparent p-0 d-flex align-items-center" 
                    type="button" 
                    id="userProfileDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                @if ($authUser && $authUser->profile_pic)
                    <img src="{{ asset('storage/profile_images/' . $authUser->profile_pic) }}"
                         alt="{{ $authUser->first_name }}"
                         class="dr-avatar-img shadow-sm">
                @else
                    <span class="dr-avatar-placeholder shadow-sm">
                        {{ strtoupper(substr($authUser->first_name ?? 'U', 0, 1)) }}
                    </span>
                @endif
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" aria-labelledby="userProfileDropdown" style="min-width: 220px;">
                <li class="px-3 py-2 bg-light rounded-top">
                    <div class="fw-bold text-dark text-truncate">{{ $authUser->first_name ?? '' }} {{ $authUser->last_name ?? '' }}</div>
                    <small class="text-muted text-capitalize"><i class="fa-solid fa-user-shield me-1 text-primary"></i>{{ $authUser->getRoleNames()->first() ?? 'User' }}</small>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('admin.edit-user-profile') }}">
                        <i class="fa-solid fa-user-pen me-2 text-primary"></i>{{ __('labels.edit_profile') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2 d-flex align-items-center text-secondary" href="{{ route('admin.change-password') }}">
                        <i class="fa-solid fa-key me-2 text-primary"></i>{{ __('labels.change_password') }}
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 d-flex align-items-center text-danger fw-semibold">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>{{ __('labels.logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>

<script>
    function drSwitchClinic(clinicId, clinicName) {
        fetch('{{ route("admin.switch-clinic") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ clinic_id: clinicId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Error switching clinic:', err);
            window.location.reload();
        });
    }
</script>