@extends('layouts.app')
@section('title')
    {{ __('labels.create_title', ['action' => 'Doctor']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.create_title', ['action' => 'Doctor']) }}</h3>
        <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
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
                <form class="row g-3" action="{{ route('admin.doctors.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <x-input-field class="col-md-6" label="First Name" name="first_name" id="first_name"
                        type="text" :value="old('first_name')" placeholder="First Name"
                        errorField="first_name" labelClass="required" />
                    
                    <x-input-field class="col-md-6" label="Last Name" name="last_name" id="last_name"
                        type="text" :value="old('last_name')" placeholder="Last Name"
                        errorField="last_name" />
                    
                    <x-input-field class="col-md-6" label="Email Address" name="email" id="email"
                        type="email" :value="old('email')" placeholder="Email Address"
                        errorField="email" labelClass="required" />
                    
                    <x-input-field class="col-md-6" label="Phone Number" name="phone_no" id="phone_no"
                        type="number" :value="old('phone_no')" placeholder="Phone Number"
                        errorField="phone_no" />

                    <x-input-field class="col-md-6" label="Qualification" name="qualification" id="qualification"
                        type="text" :value="old('qualification')" placeholder="MBBS, MD"
                        errorField="qualification" />

                    <x-input-field class="col-md-6" label="Registration Number" name="registration_number" id="registration_number"
                        type="text" :value="old('registration_number')" placeholder="REG123456"
                        errorField="registration_number" />

                    <x-text-area-field
                        divClass="col-md-12"
                        label="Address"
                        name="address"
                        id="address"
                        rows="4"
                        :value="old('address')"
                    />

                    {{-- ── Subscription & Access Control ── --}}
                    <div class="col-md-12 mt-4">
                        <h5 class="fw-semibold mb-2">Subscription & Access Control</h5>
                        <hr class="mt-0">
                    </div>
                    <div class="col-md-6">
                        <label for="package_id" class="form-label">Subscription Package</label>
                        <select name="package_id" id="package_id" class="form-select @error('package_id') is-invalid @enderror">
                            <option value="">No Package Plan</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} (Clinics: {{ $package->clinic_limit == -1 ? 'Unlimited' : $package->clinic_limit }}, Staff: {{ $package->user_limit == -1 ? 'Unlimited' : $package->user_limit }})
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-12 mt-3">
                        <label class="form-label fw-bold mb-2">Direct User Permissions (Doctor Sidebar & Operations)</label>
                        <div class="row g-3">
                            @foreach($permissionGroups as $groupName => $groupPerms)
                                <div class="col-md-4 col-sm-6">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <h6 class="fw-bold text-primary mb-2">{{ $groupName }}</h6>
                                        <div class="d-flex flex-column gap-1">
                                            @foreach($groupPerms as $action => $permName)
                                                <div class="form-check">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permName }}" id="perm_{{ $permName }}" 
                                                           class="form-check-input"
                                                           {{ (is_array(old('permissions')) && in_array($permName, old('permissions'))) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $permName }}">
                                                        {{ ucfirst($action) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <a href="{{ route('admin.doctors.index') }}" class="btn btn-primary cancel-btn mt-2 mt-sm-0">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.create') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
