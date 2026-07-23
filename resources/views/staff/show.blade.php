@extends('layouts.app')
@section('title')
    Staff Details
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">Staff Details: {{ $staff->first_name }} {{ $staff->last_name }}</h3>
        <div>
            @can('staff-edit')
                <a href="{{ route('admin.staff.edit', ['staff' => \App\Support\SecureRouteParameter::encode($staff->id)]) }}" class="btn btn-primary btn-sm me-2">
                    <i class="fa-solid fa-pen-to-square me-1"></i>Edit Staff Member
                </a>
            @endcan
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
                <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card no-scale h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white"><i class="fa-solid fa-user-shield me-2"></i>{{ $staff->first_name }} {{ $staff->last_name }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Email</span>
                            <span>{{ $staff->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Phone</span>
                            <span>{{ $staff->phone_no ?: 'N/A' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Doctor / Owner</span>
                            <span class="fw-bold text-primary">{{ $staff->creator ? ($staff->creator->first_name . ' ' . $staff->creator->last_name) : 'Super Admin' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Status</span>
                            <span class="badge {{ $staff->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($staff->status) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card no-scale h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-primary"><i class="fa-solid fa-hospital me-2"></i>Assigned Clinics</h5>
                </div>
                <div class="card-body">
                    @if($staff->assignedClinics->count() > 0)
                        <div class="row g-3">
                            @foreach($staff->assignedClinics as $c)
                                <div class="col-md-6">
                                    <div class="border rounded p-3 bg-light d-flex align-items-center">
                                        <i class="fa-solid fa-clinic-medical fs-3 text-primary me-3"></i>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $c->name }}</div>
                                            <small class="text-muted">{{ $c->address }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No specific clinics assigned to this staff member.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
