@extends('layouts.app')
@section('title')
    Create Staff Member
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">Create Staff Member</h3>
        <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
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
                <form class="row g-3" action="{{ route('admin.staff.store') }}" method="post">
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

                    <x-input-field class="col-md-6" label="Password" name="password" id="password"
                        type="password" placeholder="Minimum 6 characters"
                        errorField="password" labelClass="required" />

                    <x-select-field class="col-md-6" label="Account Status" name="status" id="status"
                        :options="[['id' => 'active', 'label' => 'Active'], ['id' => 'inactive', 'label' => 'Inactive']]"
                        :value="old('status', 'active')" placeholder="{{ __('labels.select') }}"
                        errorField="status" labelClass="required" />

                    <div class="col-md-6">
                        <label for="role" class="form-label fw-bold required">Assigned Staff Role</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ old('role', 'Staff') == $r->name ? 'selected' : '' }}>
                                    {{ $r->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    @if($authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')]))
                        <div class="col-md-6">
                            <label for="doctor_id" class="form-label fw-bold required">Assign Owner Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                                <option value="">-- Select Doctor --</option>
                                @foreach($doctors as $doc)
                                    <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                        Dr. {{ $doc->first_name }} {{ $doc->last_name }} ({{ $doc->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('doctor_id')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    {{-- ── Assigned Clinics Selection ── --}}
                    <div class="col-md-12 mt-4">
                        <h6 class="fw-bold text-dark"><i class="fa-solid fa-hospital me-2 text-primary"></i>Assign Staff to Clinics</h6>
                        <small class="text-muted d-block mb-3">Check the clinics this staff member is authorized to access and operate in.</small>
                        
                        <div class="border rounded p-3 bg-light">
                            <div id="no_clinics_msg" class="alert alert-info py-2 mb-2 d-none">
                                <i class="fa-solid fa-info-circle me-1"></i>Please select an Owner Doctor above to load their clinics.
                            </div>
                            <div class="row g-3">
                                @forelse($clinics as $clinic)
                                    <div class="col-md-4 clinic-item-col" data-doctor-id="{{ $clinic->doctor_id }}">
                                        <div class="form-check bg-white p-2 border rounded">
                                            <input type="checkbox" name="clinic_ids[]" value="{{ $clinic->id }}" id="clinic_chk_{{ $clinic->id }}" class="form-check-input ms-1 me-2 clinic-checkbox"
                                                {{ (is_array(old('clinic_ids')) && in_array($clinic->id, old('clinic_ids'))) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-semibold" for="clinic_chk_{{ $clinic->id }}">
                                                <i class="fa-solid fa-clinic-medical me-1 text-primary"></i>{{ $clinic->name }}
                                                @if($authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')]))
                                                    <small class="text-muted d-block ms-4" style="font-size: 0.75rem;">(Dr. {{ $clinic->doctor?->first_name ?? '' }} {{ $clinic->doctor?->last_name ?? '' }})</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted">
                                        No clinics found for assignment.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- ── Scoped Staff Permissions Tree ── --}}
                    <div class="col-md-12 mt-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-lock me-2 text-primary"></i>Custom Staff Permissions (Scoped to Doctor)</h6>
                            <button type="button" class="btn btn-sm btn-link text-decoration-none" data-bs-toggle="collapse" data-bs-target="#staffPermsCollapse">
                                Toggle Permissions Tree <i class="fa-solid fa-chevron-down ms-1"></i>
                            </button>
                        </div>
                        <small class="text-muted d-block mb-3">Optional: Grant or restrict specific permissions for this staff member based on your available doctor privileges.</small>

                        <div class="collapse border rounded p-3 bg-white" id="staffPermsCollapse">
                            @foreach($groupedPermissions as $categoryName => $perms)
                                <div class="mb-3 p-2 border-bottom">
                                    <div class="fw-bold text-primary mb-2"><i class="fa-solid fa-folder me-1"></i>{{ $categoryName }}</div>
                                    <div class="row g-2">
                                        @foreach($perms as $p)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input type="checkbox" name="permissions[]" value="{{ $p->name }}" id="perm_{{ $p->id }}" class="form-check-input"
                                                        {{ (is_array(old('permissions')) && in_array($p->name, old('permissions'))) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="perm_{{ $p->id }}">{{ $p->name }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <a href="{{ route('admin.staff.index') }}" class="btn btn-secondary cancel-btn mt-2 mt-sm-0">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.create') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const doctorSelect = document.getElementById('doctor_id');
    const isSuperAdminOrAdmin = {{ $authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')]) ? 'true' : 'false' }};

    if (isSuperAdminOrAdmin && doctorSelect) {
        function filterClinicsByDoctor() {
            const selectedDocId = doctorSelect.value;
            const clinicCols = document.querySelectorAll('.clinic-item-col');
            const noClinicsMsg = document.getElementById('no_clinics_msg');
            let visibleCount = 0;

            clinicCols.forEach(function (col) {
                const docId = col.getAttribute('data-doctor-id');
                if (!selectedDocId) {
                    col.style.display = 'none';
                } else if (docId === selectedDocId) {
                    col.style.display = 'block';
                    visibleCount++;
                } else {
                    col.style.display = 'none';
                    const chk = col.querySelector('.clinic-checkbox');
                    if (chk) chk.checked = false;
                }
            });

            if (noClinicsMsg) {
                if (!selectedDocId) {
                    noClinicsMsg.innerHTML = '<i class="fa-solid fa-info-circle me-1"></i>Please select an Owner Doctor above to load their clinics.';
                    noClinicsMsg.classList.remove('d-none');
                } else if (visibleCount === 0) {
                    noClinicsMsg.innerHTML = '<i class="fa-solid fa-exclamation-triangle me-1"></i>No clinics found for the selected doctor.';
                    noClinicsMsg.classList.remove('d-none');
                } else {
                    noClinicsMsg.classList.add('d-none');
                }
            }
        }

        doctorSelect.addEventListener('change', filterClinicsByDoctor);
        filterClinicsByDoctor();
    }
});
</script>
@endpush
