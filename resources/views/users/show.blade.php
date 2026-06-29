@extends('layouts.app')
@section('title')
    {{ __('labels.show_page', ['action' => __('labels.user')]) }}
@endsection
@section('content')
    @php
        $statusLabel = \App\Enums\CommonStatus::tryFrom($user->status)?->label() ?? 'N/A';
        $isActive = strtolower($statusLabel) === 'active';
        $initials = strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1));
        $fullName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: 'N/A';
    @endphp

    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.show_page', ['action' => __('labels.user')]) }}</h3>
        <a href="{{ route('admin.users.index') }}"
            class="btn btn-outline-secondary btn-sm custom-cancell">
            <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
        </a>
    </div>

    <div class="show-detail-page">

        {{-- Header Card --}}
        <div class="detail-header-card">
            <div class="detail-avatar">{{ $initials }}</div>
            <div class="detail-header-info">
                <h4 class="detail-title">{{ $fullName }}</h4>
                <p class="detail-subtitle">{{ $user->email }}</p>
            </div>
            <div class="detail-header-badge">
                <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        {{-- Account Details --}}
        <div class="detail-section">
            <p class="detail-section-title">{{ __('labels.user') }} Details</p>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label">{{ __('labels.first_name') }}</div>
                    <div class="detail-value {{ !$user->first_name ? 'text-muted-val' : '' }}">
                        {{ $user->first_name ?? 'N/A' }}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('labels.last_name') }}</div>
                    <div class="detail-value {{ !$user->last_name ? 'text-muted-val' : '' }}">
                        {{ $user->last_name ?? 'N/A' }}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('labels.email') }}</div>
                    <div class="detail-value">{{ $user->email }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('labels.status') }}</div>
                    <div class="detail-value">
                        <span class="badge {{ $isActive ? 'bg-success' : 'bg-secondary' }}">
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>
                <div class="detail-item full-width">
                    <div class="detail-label">{{ __('labels.address') }}</div>
                    <div class="detail-value {{ !$user->address ? 'text-muted-val' : '' }}">
                        {!! $user->address ?? 'N/A' !!}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('labels.created_at') }}</div>
                    <div class="detail-value">{{ $user->created_at }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">{{ __('labels.updated_at') }}</div>
                    <div class="detail-value">{{ $user->updated_at }}</div>
                </div>
            </div>
        </div>

    </div>
@endsection
