@extends('layouts.app')
@section('title')
    {{ __('labels.dashboard') }}
@endsection
@section('content')
    <div class="pb-2 mb-4 d-flex flex-column align-items-start justify-content-between">
        <div class="w-100 d-flex justify-content-between align-items-center mb-3">
            <h3 class="page-title mb-0">{{ __('labels.dashboard') }}</h3>
            @if (session('message'))
                <div class="mx-4 mb-0 alert alert-success alert-dismissible fade show py-2 px-3" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        
        {{-- Welcome Greeting Banner --}}
        @php
            $authUser = auth()->user();
            $adminName = trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? ''));
        @endphp
        <div class="dashboard-welcome-banner w-100 mb-2" role="banner">
            <div class="dr-welcome-text">
                <h2 class="mb-1">Welcome back, {{ $adminName ?: 'Administrator' }}!</h2>
                <p class="mb-0 opacity-75">Here's what's happening on your medical solutions platform today.</p>
            </div>
            <div class="dr-welcome-brand">
                <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="Skoracare Logo">
                <span>Skoracare</span>
            </div>
        </div>
    </div>

    <div class="col-md-12 divide-y-1 -main-col dashboard-page">
        <div class="px-4 dashboard-stat-grid">
            @foreach ($dashboardData['stats'] ?? [] as $stat)
                @if (!empty($stat['url']))
                    <a href="{{ $stat['url'] }}" class="dashboard-stat-card text-decoration-none mb-0 card-{{ Str::slug($stat['label']) }}">
                        <div class="dashboard-stat-icon-wrapper">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="dashboard-stat-details">
                            <span class="dashboard-stat-label">{{ $stat['label'] }}</span>
                            <strong>{{ number_format((int) $stat['value']) }}</strong>
                        </div>
                    </a>
                @else
                    <div class="dashboard-stat-card mb-0 card-{{ Str::slug($stat['label']) }}">
                        <div class="dashboard-stat-icon-wrapper">
                            <i class="{{ $stat['icon'] }}"></i>
                        </div>
                        <div class="dashboard-stat-details">
                            <span class="dashboard-stat-label">{{ $stat['label'] }}</span>
                            <strong>{{ number_format((int) $stat['value']) }}</strong>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        @if ($dashboardData['showLatestActivities'] ?? false)
            <div class="px-4">
                <div class="dashboard-table-card">
                    <div class="dashboard-table-header">
                        <h2>{{ __('labels.latest_activities') }}</h2>
                        @if (!empty($dashboardData['activityLogUrl']))
                            <a href="{{ $dashboardData['activityLogUrl'] }}" class="btn btn-primary">
                                {{ __('labels.view_all') }}
                            </a>
                        @endif
                    </div>
                    <div class="table-responsive">
                        <table class="mb-0 table">
                            <thead>
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
                                        <td>{{ $activity['name'] }}</td>
                                        <td>{{ $activity['description'] }}</td>
                                        <td class="dashboard-activity-changes">{{ $activity['changes'] }}</td>
                                        <td>{{ $activity['ip_address'] }}</td>
                                        <td>{{ $activity['created_by'] }}</td>
                                        <td>{{ $activity['created_at'] }}</td>
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
            <div class="detail-item full-width">
                <div class="detail-label">{{ __('labels.description') }}</div>
                <div class="detail-value" data-field="description"></div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">{{ __('labels.changes') }}</div>
                <div class="detail-value dashboard-activity-changes" data-field="changes"></div>
            </div>
        </div>
    </x-show-base-modal>
@endsection
