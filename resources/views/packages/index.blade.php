@extends('layouts.app')
@section('title')
    Subscription Packages
@endsection
@section('content')
    @can('package-list')
        <div class="gap-2 pb-2 mb-4 d-flex align-items-center justify-content-between flex-wrap">
            <h3 class="page-title">Subscription Packages</h3>
            <a href="{{ route('admin.packages.pricing') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fa-solid fa-tags me-1"></i>View Pricing Preview Cards
            </a>
        </div>
        <div class="col-md-12 divide-y-1 dashboard-card-main-col">
            <div class="row">
                <div class="col-12">
                    <div class="card no-scale">
                        @if (session('message'))
                            <div class="mx-4 mt-3 mb-0 alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mx-4 mt-3 mb-0 alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="card-body">
                            {{ $dataTable->table() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
