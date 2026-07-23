@extends('layouts.app')
@section('title')
    Package Details
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">Package Details: {{ $package->name }}</h3>
        <div>
            @can('package-edit')
                <a href="{{ route('admin.packages.edit', ['package' => \App\Support\SecureRouteParameter::encode($package->id)]) }}" class="btn btn-primary btn-sm me-2">
                    <i class="fa-solid fa-pen-to-square me-1"></i>Edit Package
                </a>
            @endcan
            <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
                <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="card no-scale h-100">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 text-white"><i class="fa-solid fa-box-open me-2"></i>{{ $package->name }}</h5>
                    @if($package->is_popular)
                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-star me-1"></i>Most Popular</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">{{ $package->description ?: 'No detailed description specified.' }}</p>
                    
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Monthly Price</span>
                            <span class="fs-5 fw-bold text-success">₹{{ number_format($package->monthly_price, 2) }} / mo</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Yearly Price</span>
                            <span class="fs-5 fw-bold text-primary">₹{{ number_format($package->yearly_price, 2) }} / yr</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Clinics Limit</span>
                            <span class="badge bg-info text-dark fs-6">{{ $package->clinic_limit == -1 ? 'Unlimited' : $package->clinic_limit }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">User / Staff Limit</span>
                            <span class="badge bg-info text-dark fs-6">{{ $package->user_limit == -1 ? 'Unlimited' : $package->user_limit }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="fw-semibold">Status</span>
                            <span class="badge {{ $package->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($package->status) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card no-scale h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0 text-primary"><i class="fa-solid fa-lock me-2"></i>Included Features & Permissions</h5>
                </div>
                <div class="card-body">
                    @if($package->permissions->count() > 0)
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($package->permissions as $perm)
                                <span class="badge bg-light text-dark border p-2 fw-normal">
                                    <i class="fa-solid fa-check-circle text-success me-1"></i>{{ $perm->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No specific features attached to this package.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
