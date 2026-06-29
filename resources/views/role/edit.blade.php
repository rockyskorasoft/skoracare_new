@extends('layouts.app')
@section('title')
    {{ __('labels.edit_role') }}
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="flex-row-reverse gap-4 pb-2 mb-3 d-flex justify-content-end align-items-center">
                <h1 class="mb-0 page-title">{{ __('labels.edit_role') }}</h1>
                <a class="btn btn-secondary back-link-padding back-btn rounded-cirlce"
                    href="{{ route('admin.roles.index') }}">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </a>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (session('error'))
                        <div class="mx-4 mt-3 mb-0 alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('admin.roles.update', \App\Support\SecureRouteParameter::encode($role->id)) }}"
                        method="post">
                        @csrf
                        @method('PUT')

                        {{-- Role Information --}}
                        <div class="role-section-header mb-3">
                            <span class="role-section-icon"><i class="fa-solid fa-shield-halved"></i></span>
                            <span>Role Information</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="name" class="form-label required">{{ __('labels.name') }}</label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name" id="name" placeholder="{{ __('labels.name') }}"
                                    value="{{ $role->name ?? '' }}" />
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Permissions --}}
                        <div class="role-section-header mb-3">
                            <span class="role-section-icon"><i class="fa-solid fa-lock"></i></span>
                            <span>{{ __('labels.permissions') }}</span>
                        </div>

                        <div class="mb-3">
                            {{-- Select All --}}
                            <div class="role-select-all-bar mb-3">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="checkbox" id="select-all" class="form-check-input mt-0">
                                    <label for="select-all" class="form-check-label fw-semibold mb-0">
                                        {{ __('labels.select_all') }}
                                    </label>
                                </div>
                            </div>

                            {{-- Category list --}}
                            <div id="category-checkboxes" class="d-flex flex-column gap-2">
                                @foreach ($permissions['children'] as $category)
                                    @if ($category->parent_id === null)
                                        <div class="role-perm-category">
                                            {{-- Parent --}}
                                            <div class="role-perm-parent">
                                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                                    <input type="checkbox" class="form-check-input mt-0 parent-checkbox"
                                                        id="parent-{{ $category->id }}" name="parents[]"
                                                        value="{{ $category->id }}"
                                                        {{ in_array($category->id, $rolePermissionIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold mb-0"
                                                        for="parent-{{ $category->id }}">
                                                        <i class="fa-solid fa-folder me-1 role-cat-icon"></i>
                                                        {{ $category->name }}
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Children --}}
                                            <div class="child-categories role-perm-children">
                                                @foreach ($category->children as $child)
                                                    <div class="form-check d-flex align-items-center gap-2 mb-0">
                                                        <input type="checkbox"
                                                            class="form-check-input mt-0 child-checkbox"
                                                            id="child-{{ $child->id }}" name="children[]"
                                                            value="{{ $child->id }}"
                                                            data-parent-id="{{ $category->id }}"
                                                            {{ in_array($child->id, $rolePermissionIds) ? 'checked' : '' }}>
                                                        <label class="form-check-label mb-0"
                                                            for="child-{{ $child->id }}">
                                                            {{ $child->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
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
