@extends('layouts.app')

@section('title', 'Pricing Plans')

@section('content')
<div class="container py-4">
    
    {{-- Header Section --}}
    <div class="text-center mb-5">
        <span class="text-uppercase fw-bold text-success small tracking-widest" style="letter-spacing: 0.15em;">PRICING</span>
        <h1 class="fw-bold mt-2 text-dark" style="font-size: 2.2rem;">Simple, Transparent Pricing</h1>
        <p class="text-muted mx-auto" style="max-width: 540px;">
            No hidden fees. No per-patient charges. Just one flat price for your entire clinic.
        </p>

        {{-- Monthly / Yearly Toggle --}}
        <div class="d-inline-flex align-items-center justify-content-center bg-light border rounded-pill p-1 mt-3">
            <span class="px-3 fw-semibold text-muted small" id="monthlyLabel">Monthly</span>
            <div class="form-check form-switch m-0 p-0">
                <input class="form-check-input ms-0 me-0" type="checkbox" id="billingToggle" style="width: 2.8rem; height: 1.4rem; cursor: pointer;" checked>
            </div>
            <span class="px-3 fw-semibold text-primary small" id="yearlyLabel">
                Yearly <span class="badge bg-teal text-white ms-1 rounded-pill" style="background-color: #0d9488; font-size: 0.7rem;">Save 16.6%</span>
            </span>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="row g-4 align-items-stretch justify-content-center">
        @forelse($packages as $index => $package)
            @php
                $isPopular = $package->is_popular;
                $cardBorderClass = $isPopular ? 'border-primary shadow-lg position-relative' : 'border-light shadow-sm';
            @endphp
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="card border-2 w-100 rounded-4 {{ $cardBorderClass }}" style="{{ $isPopular ? 'border-color: #0d9488 !important;' : '' }}">
                    
                    @if($isPopular)
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge text-white px-3 py-1 rounded-pill fw-semibold shadow-sm" style="background-color: #0d9488; font-size: 0.75rem;">
                                <i class="fa-solid fa-star me-1"></i>Most Popular
                            </span>
                        </div>
                    @endif

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="text-uppercase fw-bold text-muted small mb-3" style="letter-spacing: 0.08em;">
                            {{ $package->name }}
                        </div>

                        {{-- Pricing Display --}}
                        <div class="mb-4">
                            <div class="yearly-price-display">
                                <span class="text-decoration-line-through text-muted fs-5 me-2">₹{{ number_format($package->yearly_price * 1.2, 0) }}</span>
                                <span class="fs-2 fw-bold text-dark">₹{{ number_format($package->yearly_price, 0) }}</span>
                                <span class="text-muted small">/ year</span>
                            </div>
                            <div class="monthly-price-display d-none">
                                <span class="text-decoration-line-through text-muted fs-5 me-2">₹{{ number_format($package->monthly_price * 1.2, 0) }}</span>
                                <span class="fs-2 fw-bold text-dark">₹{{ number_format($package->monthly_price, 0) }}</span>
                                <span class="text-muted small">/ month</span>
                            </div>
                        </div>

                        {{-- Features List --}}
                        <ul class="list-unstyled flex-grow-1 mb-4">
                            <li class="d-flex align-items-center py-2 border-bottom border-light text-secondary">
                                <i class="fa-solid fa-circle-check text-teal me-2" style="color: #0d9488;"></i>
                                <strong class="text-dark me-1">{{ $package->user_limit == -1 ? 'Unlimited' : $package->user_limit }}</strong> User / Staff
                            </li>
                            <li class="d-flex align-items-center py-2 border-bottom border-light text-secondary">
                                <i class="fa-solid fa-circle-check text-teal me-2" style="color: #0d9488;"></i>
                                <strong class="text-dark me-1">{{ $package->clinic_limit == -1 ? 'Unlimited' : $package->clinic_limit }}</strong> Clinic Management
                            </li>
                            
                            @php
                                $allFeatureLabels = [
                                    'appointment-list' => 'Appointment Management',
                                    'clinic-list' => 'Billing System Integrated',
                                    'patients-list' => 'Comprehensive Patient Record',
                                    'follow-up-list' => 'Digital Prescription & Follow-up',
                                    'pharmacy-list' => 'Pharmacy & Ledger Management',
                                    'analytics-list' => 'Data Analytics & Reports',
                                    'ask-skoracare-list' => 'Ask Skoracare AI Assistant',
                                    'messages-list' => 'Multi-channel Communication',
                                ];
                                $packagePermNames = $package->permissions->pluck('name')->toArray();
                            @endphp

                            @foreach($allFeatureLabels as $permCode => $featureTitle)
                                @php
                                    $hasFeature = in_array($permCode, $packagePermNames) || $package->clinic_limit == -1;
                                @endphp
                                <li class="d-flex align-items-center py-2 border-bottom border-light {{ $hasFeature ? 'text-secondary' : 'text-muted opacity-50' }}">
                                    @if($hasFeature)
                                        <i class="fa-solid fa-circle-check me-2" style="color: #0d9488;"></i>
                                        <span>{{ $featureTitle }}</span>
                                    @else
                                        <i class="fa-solid fa-circle-xmark me-2 text-muted"></i>
                                        <span class="text-decoration-line-through">{{ $featureTitle }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>

                        {{-- Action Button --}}
                        <div class="mt-auto">
                            @if($isPopular)
                                <a href="javascript:void(0);" class="btn w-100 py-2.5 rounded-pill text-white fw-bold shadow-sm" style="background-color: #0d9488;">
                                    Get Started
                                </a>
                            @else
                                <a href="javascript:void(0);" class="btn btn-outline-secondary w-100 py-2.5 rounded-pill fw-semibold">
                                    Get Started
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <i class="fa-solid fa-box-open fs-1 mb-3"></i>
                <p>No active pricing packages found.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('billingToggle');
        var yearlyDisplays = document.querySelectorAll('.yearly-price-display');
        var monthlyDisplays = document.querySelectorAll('.monthly-price-display');

        function updatePricingView() {
            if (toggle.checked) {
                // Yearly selected
                yearlyDisplays.forEach(el => el.classList.remove('d-none'));
                monthlyDisplays.forEach(el => el.classList.add('d-none'));
            } else {
                // Monthly selected
                yearlyDisplays.forEach(el => el.classList.add('d-none'));
                monthlyDisplays.forEach(el => el.classList.remove('d-none'));
            }
        }

        if (toggle) {
            toggle.addEventListener('change', updatePricingView);
        }
    });
</script>
@endsection
