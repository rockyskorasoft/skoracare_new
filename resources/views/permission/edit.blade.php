@extends('layouts.app')
@section('title')
{{ __('labels.edit_permission') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="flex-row-reverse gap-4 pb-2 mb-3 d-flex justify-content-end align-items-center">
                <h1 class="mb-0 page-title">{{ __('labels.edit_permission') }}</h1>
                <a class="btn btn-secondary back-link-padding back-btn rounded-cirlce"
                    href="{{ route('admin.permissions.index') }}">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </a>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header">{{ __('labels.edit_permission') }}</div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="mx-4 mt-3 mb-0 alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form class="row g-3" action="{{ route('admin.permissions.update', $permission->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="col-6">
                            <label for="name" class="form-label text-dark">{{ __('labels.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                id="name" placeholder="{{ __('labels.name') }}" value="{{ $permission->name ?? '' }}" />
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="col-6">
                            @if ($permission->parent_id !== null)
                                <label for="name" class="form-label text-dark">{{ __('labels.select_parent') }}</label>
                                <select class="form-select" name="parent">
                                    <option value="none" selected disabled>{{ __('labels.no_parent') }}</option>
                                    @foreach ($permissions['parents'] as $view)
                                        <option value="{{ $view->id }}"
                                            {{ $permission->parent_id == $view->id ? 'selected' : '' }}>
                                            {{ $view->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('buttons.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
