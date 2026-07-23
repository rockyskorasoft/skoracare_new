@extends('layouts.app')
@section('title')
    Create Package
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">Create Subscription Package</h3>
        <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
            <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
        </a>
    </div>

    <div class="col-md-12 divide-y-1 dashboard-card-main-col">
        <div class="card no-scale">
            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <form class="row g-3" action="{{ route('admin.packages.store') }}" method="post">
                    @csrf

                    {{-- Package Basic Information --}}
                    <div class="role-section-header mb-3">
                        <span class="role-section-icon"><i class="fa-solid fa-box-open"></i></span>
                        <span>Package Information</span>
                    </div>
                    
                    <x-input-field class="col-md-6" label="Package Name" name="name" id="name"
                        type="text" :value="old('name')" placeholder="e.g. Package 1, Gold, Platinum"
                        errorField="name" labelClass="required" />

                    <x-select-field class="col-md-6" label="Status" name="status" id="status"
                        :options="[['id' => 'active', 'label' => 'Active'], ['id' => 'inactive', 'label' => 'Inactive']]" 
                        :value="old('status', 'active')" placeholder="{{ __('labels.select') }}"
                        errorField="status" labelClass="required" />

                    <x-input-field class="col-md-6" label="Monthly Price (₹)" name="monthly_price" id="monthly_price"
                        type="number" step="0.01" :value="old('monthly_price', '0')" placeholder="790.00"
                        errorField="monthly_price" labelClass="required" />

                    <x-input-field class="col-md-6" label="Yearly Price (₹)" name="yearly_price" id="yearly_price"
                        type="number" step="0.01" :value="old('yearly_price', '0')" placeholder="7890.00"
                        errorField="yearly_price" labelClass="required" />

                    <x-input-field class="col-md-6" label="Clinic Limit" name="clinic_limit" id="clinic_limit"
                        type="number" :value="old('clinic_limit', '1')" placeholder="-1 for Unlimited"
                        errorField="clinic_limit" labelClass="required" />

                    <x-input-field class="col-md-6" label="User / Staff Limit" name="user_limit" id="user_limit"
                        type="number" :value="old('user_limit', '1')" placeholder="-1 for Unlimited"
                        errorField="user_limit" labelClass="required" />

                    <div class="col-md-12">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" name="is_popular" value="1" id="is_popular" {{ old('is_popular') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-success" for="is_popular">
                                <i class="fa-solid fa-star me-1"></i>Mark as "Most Popular" Plan
                            </label>
                        </div>
                    </div>

                    <x-text-area-field
                        divClass="col-md-12"
                        label="Description / Tagline"
                        name="description"
                        id="description"
                        rows="3"
                        :value="old('description')"
                    />

                    {{-- Package Permissions (Same exact tree layout as Roles) --}}
                    <div class="role-section-header my-3">
                        <span class="role-section-icon"><i class="fa-solid fa-lock"></i></span>
                        <span>Package Permissions & Included Features</span>
                    </div>

                    <div class="col-md-12 mb-3">
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
                                                    value="{{ $category->id }}">
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
                                                        data-parent-id="{{ $category->id }}">
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

                    <div class="col-12 mt-4">
                        <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary cancel-btn mt-2 mt-sm-0">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.create') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
