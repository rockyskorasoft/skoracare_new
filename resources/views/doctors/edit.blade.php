@extends('layouts.app')
@section('title')
    {{ __('labels.edit_title', ['action' => 'Doctor']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.edit_title', ['action' => 'Doctor']) }}</h3>
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
                <form class="row g-3" action="{{ route('admin.doctors.update', ['doctor' => \App\Support\SecureRouteParameter::encode($user->id)]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <x-input-field class="col-md-6" label="First Name" name="first_name" id="first_name"
                        type="text" :value="old('first_name', $user->first_name)" placeholder="First Name"
                        errorField="first_name" labelClass="required" />
                    
                    <x-input-field class="col-md-6" label="Last Name" name="last_name" id="last_name"
                        type="text" :value="old('last_name', $user->last_name)" placeholder="Last Name"
                        errorField="last_name" />
                    
                    <x-input-field class="col-md-6" label="Email Address" name="email" id="email"
                        type="email" :value="old('email', $user->email)" placeholder="Email Address"
                        errorField="email" labelClass="required" />
                    
                    <x-input-field class="col-md-6" label="Phone Number" name="phone_no" id="phone_no"
                        type="number" :value="old('phone_no', $user->phone_no)" placeholder="Phone Number"
                        errorField="phone_no" />

                    <div class="col-md-6">
                        <label for="password" class="form-label">{{ __('labels.password') }}</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current">
                        <small class="text-muted">Minimum 6 characters</small>
                        @error('password')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <x-select-field class="col-md-6" label="Account Status" name="status" id="status"
                        :options="$statusData" :value="old('status', $user->status)" placeholder="{{ __('labels.select') }}"
                        errorField="status" labelClass="required" />

                    <x-input-field class="col-md-6" label="Qualification" name="qualification" id="qualification"
                        type="text" :value="old('qualification', $user->qualification)" placeholder="MBBS, MD"
                        errorField="qualification" />

                    <x-input-field class="col-md-6" label="Registration Number" name="registration_number" id="registration_number"
                        type="text" :value="old('registration_number', $user->registration_number)" placeholder="REG123456"
                        errorField="registration_number" />

                    <x-text-area-field
                        divClass="col-md-12"
                        label="Address"
                        name="address"
                        id="address"
                        rows="3"
                        :value="old('address', $user->address)"
                    />

                    {{-- ── Subscription & Package Limits ── --}}
                    <div class="role-section-header mb-3 mt-4">
                        <span class="role-section-icon"><i class="fa-solid fa-box-open"></i></span>
                        <span>Subscription Package & Custom Limits</span>
                    </div>

                    <div class="col-md-6">
                        <label for="package_id" class="form-label fw-bold">Subscription Package</label>
                        <select name="package_id" id="package_id" class="form-select @error('package_id') is-invalid @enderror">
                            <option value="">No Package Plan (Manual Permissions)</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" {{ old('package_id', $user->package_id) == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} (Clinics: {{ $package->clinic_limit == -1 ? 'Unlimited' : $package->clinic_limit }}, Users: {{ $package->user_limit == -1 ? 'Unlimited' : $package->user_limit }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Auto-populates default clinic/user limits and permissions</small>
                        @error('package_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="max_clinics" class="form-label fw-bold">Max Clinics Limit</label>
                        <input type="number" name="max_clinics" id="max_clinics" class="form-control @error('max_clinics') is-invalid @enderror" value="{{ old('max_clinics', $user->max_clinics ?? $user->package?->clinic_limit) }}" placeholder="-1 for Unlimited">
                        <small class="text-muted">Editable limit</small>
                        @error('max_clinics')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label for="max_users" class="form-label fw-bold">Max Users/Staff Limit</label>
                        <input type="number" name="max_users" id="max_users" class="form-control @error('max_users') is-invalid @enderror" value="{{ old('max_users', $user->max_users ?? $user->package?->user_limit) }}" placeholder="-1 for Unlimited">
                        <small class="text-muted">Editable limit</small>
                        @error('max_users')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- ── Toggleable Permissions Section ── --}}
                    <div class="col-md-12 mt-4">
                        <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded border">
                            <div>
                                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-shield-cat me-2 text-primary"></i>Custom Operational Permissions</h6>
                                <small class="text-muted">View or fine-tune specific permissions for this doctor user</small>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-pill" id="btnTogglePermissions">
                                <i class="fa-solid fa-eye me-1"></i>Show Custom Permissions <i class="fa-solid fa-chevron-down ms-1" id="permIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 d-none" id="permissionsWrapper">
                        <div class="border rounded p-3 bg-white mt-2">
                            {{-- Select All --}}
                            <div class="role-select-all-bar mb-3">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input type="checkbox" id="select-all" class="form-check-input mt-0">
                                    <label for="select-all" class="form-check-label fw-semibold mb-0">
                                        {{ __('labels.select_all') }}
                                    </label>
                                </div>
                            </div>

                            {{-- Category Tree list --}}
                            <div id="category-checkboxes" class="d-flex flex-column gap-2">
                                @foreach ($permissions['children'] as $category)
                                    @if ($category->parent_id === null)
                                        <div class="role-perm-category">
                                            {{-- Parent Category --}}
                                            <div class="role-perm-parent">
                                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                                    <input type="checkbox" class="form-check-input mt-0 parent-checkbox"
                                                        id="parent-{{ $category->id }}" name="parents[]"
                                                        value="{{ $category->id }}"
                                                        {{ in_array($category->id, $userPermissionIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold mb-0"
                                                        for="parent-{{ $category->id }}">
                                                        <i class="fa-solid fa-folder me-1 role-cat-icon"></i>
                                                        {{ $category->name }}
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Children Permissions --}}
                                            <div class="child-categories role-perm-children">
                                                @foreach ($category->children as $child)
                                                    <div class="form-check d-flex align-items-center gap-2 mb-0">
                                                        <input type="checkbox"
                                                            class="form-check-input mt-0 child-checkbox"
                                                            id="child-{{ $child->id }}" name="children[]"
                                                            value="{{ $child->id }}"
                                                            data-parent-id="{{ $category->id }}"
                                                            {{ in_array($child->id, $userPermissionIds) ? 'checked' : '' }}>
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
                    </div>

                    <div class="col-12 mt-4">
                        <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary cancel-btn mt-2 mt-sm-0">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.update') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Package selection auto-check & toggle JS script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var packageMap = @json($packagePermissionsMap);
            var packageSelect = document.getElementById('package_id');
            var maxClinicsInput = document.getElementById('max_clinics');
            var maxUsersInput = document.getElementById('max_users');

            var btnToggle = document.getElementById('btnTogglePermissions');
            var permWrapper = document.getElementById('permissionsWrapper');

            // Toggle permissions section visibility
            if (btnToggle && permWrapper) {
                btnToggle.addEventListener('click', function () {
                    var isHidden = permWrapper.classList.contains('d-none');
                    if (isHidden) {
                        permWrapper.classList.remove('d-none');
                        btnToggle.innerHTML = '<i class="fa-solid fa-eye-slash me-1"></i>Hide Custom Permissions <i class="fa-solid fa-chevron-up ms-1"></i>';
                    } else {
                        permWrapper.classList.add('d-none');
                        btnToggle.innerHTML = '<i class="fa-solid fa-eye me-1"></i>Show Custom Permissions <i class="fa-solid fa-chevron-down ms-1"></i>';
                    }
                });
            }

            function syncPackageValuesOnLoad() {
                var selectedPkgId = packageSelect ? packageSelect.value : null;
                if (selectedPkgId && packageMap[selectedPkgId]) {
                    var pkg = packageMap[selectedPkgId];
                    if (maxClinicsInput && (maxClinicsInput.value === '' || maxClinicsInput.value === null)) {
                        maxClinicsInput.value = pkg.clinic_limit;
                    }
                    if (maxUsersInput && (maxUsersInput.value === '' || maxUsersInput.value === null)) {
                        maxUsersInput.value = pkg.user_limit;
                    }
                }
            }

            if (packageSelect) {
                syncPackageValuesOnLoad();

                packageSelect.addEventListener('change', function () {
                    var selectedPkgId = this.value;
                    if (selectedPkgId && packageMap[selectedPkgId]) {
                        var pkg = packageMap[selectedPkgId];
                        
                        // Populate limits
                        if (maxClinicsInput) maxClinicsInput.value = pkg.clinic_limit;
                        if (maxUsersInput) maxUsersInput.value = pkg.user_limit;

                        // Check permissions
                        var permIds = pkg.permission_ids || [];
                        document.querySelectorAll('.child-checkbox').forEach(function (cb) {
                            var val = parseInt(cb.value);
                            cb.checked = permIds.includes(val);
                            cb.dispatchEvent(new Event('change'));
                        });
                    } else if (!selectedPkgId) {
                        if (maxClinicsInput) maxClinicsInput.value = '';
                        if (maxUsersInput) maxUsersInput.value = '';
                    }
                });
            }
        });
    </script>
@endsection
