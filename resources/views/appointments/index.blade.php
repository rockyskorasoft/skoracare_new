@extends('layouts.app')

@section('title', __('labels.appointments'))

@push('styles')
    @vite(['resources/css/doctor/doctordashboard.css', 'resources/css/appointment.css'])
@endpush

@section('content')
    @php
        $authUser = auth()->user();
        $doctorName = trim(($authUser->first_name ?? '') . ' ' . ($authUser->last_name ?? ''));
    @endphp

    <div class="container-fluid p-0">
        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Welcome Header Banner (Screenshot 1) --}}
        <div class="apt-welcome-banner">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2>Welcome Dr. {{ $doctorName ?: 'Anil Chauhan' }}!</h2>
                    <p>{{ __('labels.your_appointments') }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.appointments.slot-booking') }}" class="apt-btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        {{ __('labels.add_new_appointment') }}
                    </a>
                    <button type="button" class="apt-btn-dark" data-bs-toggle="modal" data-bs-target="#walkinConsultationModal">
                        {{ __('labels.start_walk_in_consultation') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Queue Card & Tabs Section --}}
        <div class="apt-queue-card">
            {{-- Tabs Nav --}}
            <div class="apt-tabs-nav">
                <a href="{{ route('admin.appointments.index', ['tab' => 'queue', 'date' => $dateParam]) }}"
                   class="apt-tab-link {{ $currentTab === 'queue' ? 'active' : '' }}">
                    <i class="fa-regular fa-clock"></i> {{ __('labels.queue') }} ({{ $counts['queue'] ?? 0 }})
                </a>
                <a href="{{ route('admin.appointments.index', ['tab' => 'draft', 'date' => $dateParam]) }}"
                   class="apt-tab-link {{ $currentTab === 'draft' ? 'active' : '' }}">
                    <i class="fa-regular fa-file-lines"></i> {{ __('labels.draft') }} ({{ $counts['draft'] ?? 0 }})
                </a>
                <a href="{{ route('admin.appointments.index', ['tab' => 'finished', 'date' => $dateParam]) }}"
                   class="apt-tab-link {{ $currentTab === 'finished' ? 'active' : '' }}">
                    <i class="fa-regular fa-circle-check"></i> {{ __('labels.finished') }} ({{ $counts['finished'] ?? 0 }})
                </a>
                <a href="{{ route('admin.appointments.index', ['tab' => 'cancelled', 'date' => $dateParam]) }}"
                   class="apt-tab-link {{ $currentTab === 'cancelled' ? 'active' : '' }}">
                    <i class="fa-regular fa-circle-xmark"></i> {{ __('labels.cancelled') }} ({{ $counts['cancelled'] ?? 0 }})
                </a>
                <a href="{{ route('admin.appointments.index', ['tab' => 'referral', 'date' => $dateParam]) }}"
                   class="apt-tab-link {{ $currentTab === 'referral' ? 'active' : '' }}">
                    <i class="fa-regular fa-file-lines"></i> {{ __('labels.referral') }} ({{ $counts['referral'] ?? 0 }})
                </a>
            </div>

            {{-- Filter Toolbar --}}
            <form action="{{ route('admin.appointments.index') }}" method="GET" id="aptFilterForm" class="apt-filter-toolbar">
                <input type="hidden" name="tab" value="{{ $currentTab }}">

                <div class="apt-search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           class="apt-search-input"
                           placeholder="{{ __('labels.search_patient_placeholder') }}"
                           onchange="this.form.submit()">
                </div>

                <div class="d-flex align-items-center gap-2">
                    {{-- Date Nav Picker --}}
                    @php
                        $prevDate = \Carbon\Carbon::parse($dateParam)->subDay()->format('Y-m-d');
                        $nextDate = \Carbon\Carbon::parse($dateParam)->addDay()->format('Y-m-d');
                        $displayDate = \Carbon\Carbon::parse($dateParam)->format('d-m-Y');
                    @endphp
                    <div class="apt-date-nav">
                        <a href="{{ route('admin.appointments.index', ['tab' => $currentTab, 'date' => $prevDate, 'search' => $search]) }}" class="apt-date-btn">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        <span class="apt-date-text">
                            <i class="fa-regular fa-calendar me-1 text-primary"></i>
                            {{ $displayDate }}
                        </span>
                        <a href="{{ route('admin.appointments.index', ['tab' => $currentTab, 'date' => $nextDate, 'search' => $search]) }}" class="apt-date-btn">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>

                    {{-- Today Dropdown --}}
                    <select name="date" class="form-select text-semibold" style="border-radius: 10px; width: 120px;" onchange="this.form.submit()">
                        <option value="{{ date('Y-m-d') }}" {{ $dateParam === date('Y-m-d') ? 'selected' : '' }}>{{ __('labels.today') }}</option>
                        <option value="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" {{ $dateParam === \Carbon\Carbon::tomorrow()->format('Y-m-d') ? 'selected' : '' }}>Tomorrow</option>
                    </select>
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="apt-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>{{ __('labels.name') }}</th>
                            <th>{{ __('labels.mobile_number') }}</th>
                            <th>{{ __('labels.visit_type') }} <i class="fa-solid fa-filter text-muted ms-1" style="font-size: 0.75rem;"></i></th>
                            <th>{{ __('labels.slot') }} <i class="fa-solid fa-arrow-down-short-wide text-muted ms-1" style="font-size: 0.75rem;"></i></th>
                            <th class="text-end">{{ __('labels.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $index => $apt)
                            <tr>
                                <td class="fw-bold text-muted">{{ $appointments->firstItem() + $index }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $apt->patient_name }}</div>
                                    <small class="text-muted">{{ $apt->gender }}, {{ $apt->age ? $apt->age . 'y' : '30y' }}</small>
                                </td>
                                <td class="fw-semibold text-secondary">{{ $apt->patient_phone }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 rounded-3 fw-semibold">
                                        {{ $apt->visit_type }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1 rounded-3">
                                        <i class="fa-regular fa-clock me-1"></i> {{ $apt->slot_time }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.appointments.show', $apt->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-3 px-3 fw-bold">
                                            <i class="fa-regular fa-eye me-1"></i> {{ __('labels.consult') }}
                                        </a>

                                        @if ($apt->status === 'queue')
                                            <button type="button"
                                                    onclick="updateAptStatus({{ $apt->id }}, 'finished')"
                                                    class="btn btn-sm btn-success rounded-3 px-3 fw-bold">
                                                <i class="fa-solid fa-check me-1"></i> Finish
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="apt-empty-state">
                                        <svg class="apt-empty-icon" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="50" y="70" width="30" height="60" rx="4" fill="#E2E8F0"/>
                                            <rect x="90" y="70" width="30" height="60" rx="4" fill="#CBD5E1"/>
                                            <circle cx="150" cy="50" r="16" fill="#94A3B8"/>
                                            <path d="M150 70C135 70 130 85 130 110H170C170 85 165 70 150 70Z" fill="#64748B"/>
                                        </svg>
                                        <h5 class="apt-empty-title">{{ __('labels.no_patients_in_queue') }}</h5>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($appointments->hasPages())
                <div class="mt-4">
                    {{ $appointments->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Walk-In Consultation Modal (Screenshot 2) --}}
    @include('appointments.partials.walkin-modal')

@endsection

@push('scripts')
    <script>
        function updateAptStatus(aptId, newStatus) {
            if (!confirm('Are you sure you want to mark this appointment as ' + newStatus + '?')) return;

            fetch("{{ url('appointments') }}/" + aptId + "/status", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error updating status');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Server error');
            });
        }
    </script>
@endpush
