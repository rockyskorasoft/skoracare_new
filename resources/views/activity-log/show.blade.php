@extends('layouts.app')
@section('title')
    {{ __('labels.show_page', ['action' => __('labels.activity_log')]) }}
@endsection

@php
    $userName = function ($user) {
        if (! $user) {
            return 'N/A';
        }

        return trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email ?? 'N/A';
    };

    $subjectName = function ($subject) {
        if (! $subject) {
            return 'N/A';
        }

        return trim(($subject->first_name ?? '') . ' ' . ($subject->last_name ?? ''))
            ?: ($subject->name ?? null)
            ?: ($subject->email ?? null)
            ?: class_basename($subject);
    };

    $subjectCreatedBy = null;
    if ($logData->subject && method_exists($logData->subject, 'createdBy')) {
        $subjectCreatedBy = $logData->subject->createdBy;
    }

    $createdBy = $subjectCreatedBy ?: $logData->createdBy ?: $logData->causer;
    $changes = $logData->changeLines();

    $eventColors = [
        'created' => 'bg-success',
        'updated' => 'bg-primary',
        'deleted' => 'bg-danger',
    ];
    $eventBadge = $eventColors[strtolower($logData->event ?? '')] ?? 'bg-secondary';
    $initials = strtoupper(substr($logData->log_name ?? 'AL', 0, 2));
@endphp

@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.show_page', ['action' => __('labels.activity_log')]) }}</h3>
        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
            <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
        </a>
    </div>

    <div class="show-detail-page">

        {{-- Header Card --}}
        <div class="detail-header-card">
            <div class="detail-avatar">{{ $initials }}</div>
            <div class="detail-header-info">
                <h4 class="detail-title">{{ $logData->log_name ?? 'N/A' }}</h4>
                <p class="detail-subtitle">{{ $logData->description ?? 'No description' }}</p>
            </div>
            <div class="detail-header-badge">
                <span class="badge {{ $eventBadge }}">
                    {{ ucfirst($logData->event ?? 'N/A') }}
                </span>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">

                {{-- Log Details --}}
                <div class="detail-section">
                    <p class="detail-section-title">Log Details</p>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.log_id') }}</div>
                            <div class="detail-value">{{ $logData->id }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.event') }}</div>
                            <div class="detail-value">
                                <span class="badge {{ $eventBadge }}">
                                    {{ ucfirst($logData->event ?? 'N/A') }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.name') }}</div>
                            <div class="detail-value {{ !$logData->log_name ? 'text-muted-val' : '' }}">
                                {{ $logData->log_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.subject') }}</div>
                            <div class="detail-value">{{ $subjectName($logData->subject) }}</div>
                        </div>
                        <div class="detail-item full-width">
                            <div class="detail-label">{{ __('labels.description') }}</div>
                            <div class="detail-value {{ !$logData->description ? 'text-muted-val' : '' }}">
                                {{ $logData->description ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6">

                {{-- Actor Details --}}
                <div class="detail-section">
                    <p class="detail-section-title">Actor & System</p>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.causer') }}</div>
                            <div class="detail-value">{{ $userName($logData->causer) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.created_by') }}</div>
                            <div class="detail-value">{{ $userName($createdBy) }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.ip_address') }}</div>
                            <div class="detail-value {{ !$logData->ip_address ? 'text-muted-val' : '' }}">
                                {{ $logData->ip_address ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ __('labels.created_at') }}</div>
                            <div class="detail-value">{{ $logData->created_at ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Changes --}}
        <div class="detail-section">
            <p class="detail-section-title">{{ __('labels.changes') }}</p>
            <div class="p-3">
                @forelse ($changes as $change)
                    <div class="d-flex align-items-center gap-2 py-2 border-bottom border-light">
                        <i class="bi bi-arrow-right-circle text-primary mt-1 flex-shrink-0"></i>
                        <span class="small">{{ $change }}</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0 py-2">No changes recorded</p>
                @endforelse
            </div>
        </div>

    </div>
@endsection
