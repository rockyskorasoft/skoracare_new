@extends('layouts.app')
@section('title')
    {{ __('labels.show_page', ['action' => 'Clinic']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.show_page', ['action' => 'Clinic']) }}</h3>
        <a href="{{ route('admin.clinics.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
            <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
        </a>
    </div>

    <div class="show-detail-page">
        {{-- Header Card --}}
        <div class="detail-header-card">
            @if (! empty($clinic->logo))
                <div class="detail-avatar bg-transparent">
                    <img src="{{ asset('storage/clinic_logos/' . $clinic->logo) }}" alt="Logo" class="img-fluid rounded object-fit-cover" style="width: 100%; height: 100%;">
                </div>
            @else
                <div class="detail-avatar bg-primary text-white">{{ strtoupper(substr($clinic->name, 0, 1)) }}</div>
            @endif
            <div class="detail-header-info">
                <h4 class="detail-title">{{ $clinic->name }}</h4>
                <p class="detail-subtitle">Owner: {{ $clinic->doctor ? $clinic->doctor->name : 'N/A' }}</p>
            </div>
        </div>

        {{-- Clinic Details --}}
        <div class="detail-section">
            <p class="detail-section-title">Clinic Details</p>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">Clinic Name</div>
                    <div class="detail-value">{{ $clinic->name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Owner (Doctor)</div>
                    <div class="detail-value">{{ $clinic->doctor ? $clinic->doctor->name : 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone Number</div>
                    <div class="detail-value">{{ $clinic->phone_no ?? 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Consultation Fee</div>
                    <div class="detail-value">₹{{ number_format($clinic->consultation_fee, 2) }}</div>
                </div>
                <div class="detail-item full-width">
                    <div class="detail-label">Address</div>
                    <div class="detail-value">{{ $clinic->address }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Created At</div>
                    <div class="detail-value">{{ $clinic->created_at }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Updated At</div>
                    <div class="detail-value">{{ $clinic->updated_at }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
