@extends('layouts.app')
@section('title')
    {{ __('labels.users', ['action' => __('labels.user')]) }}
@endsection
@section('content')
    @can('user-list')
        <div class="gap-2 pb-2 mb-4 d-flex align-items-center">
            <h3 class="page-title">{{ __('labels.users') }} </h3>
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
