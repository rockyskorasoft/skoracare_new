{{-- ============================================================
     doctors/partials/doctor-header.blade.php
     Top header bar for the Doctor panel matching screenshot.
     Brand: Skoracare | Full-width header with top-left logo
     ============================================================ --}}
@php
    /** @var \App\Models\User $authUser */
    $authUser = auth()->user();
    $clinics = $authUser->clinics;
    $activeClinicName = $clinics->first()->name ?? 'Default Clinic';
@endphp

<header class="doctor-header" id="drHeader">
    
    {{-- Left Section: App Logo & Brand Name --}}
    <div class="dr-header-left">
        <a href="{{ route('admin.doctor.dashboard') }}" class="dr-header-logo-wrap" title="Skoracare">
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

    {{-- Right Section: Clinic Dropdown & Action Icons --}}
    <div class="dr-header-right">

        {{-- Clinic Selector Dropdown --}}
        <div class="dr-header-dropdown">
            <button class="dr-dropdown-toggle" id="drClinicDropdownBtn">
                <span class="dr-clinic-active-name">{{ $activeClinicName }}</span>
                <i class="fa-solid fa-chevron-down dr-dropdown-arrow"></i>
            </button>
            @if($clinics->count() > 1)
                <ul class="dr-dropdown-menu" id="drClinicDropdownMenu">
                    @foreach($clinics as $clinic)
                        <li>
                            <a href="javascript:void(0);" class="dr-dropdown-item" data-clinic-id="{{ $clinic->id }}">
                                {{ $clinic->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Action Icons (Video call & Settings) --}}
        <div class="dr-header-actions">
            <a href="javascript:void(0);" class="dr-action-btn" title="Video Consultations">
                <i class="fa-regular fa-circle-play"></i>
            </a>
            <a href="javascript:void(0);" class="dr-action-btn" title="Settings">
                <i class="fa-solid fa-gear"></i>
            </a>
            <a href="{{ route('logout') }}" class="dr-action-btn logout-btn" title="{{ __('labels.sign_out') }}">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>

    </div>
</header>
