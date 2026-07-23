@extends('layouts.app')
@section('title')
    {{ __('labels.edit_title', ['action' => 'Clinic']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.edit_title', ['action' => 'Clinic']) }}</h3>
        <a href="{{ route('admin.clinics.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
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
                <form class="row g-3" action="{{ route('admin.clinics.update', ['clinic' => \App\Support\SecureRouteParameter::encode($clinic->id)]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <x-input-field class="col-md-6" label="Clinic Name" name="name" id="name"
                        type="text" :value="old('name', $clinic->name)" placeholder="Rehab Maxx Physiotherapy Clinic"
                        errorField="name" labelClass="required" />

                    <x-input-field class="col-md-6" label="Clinic Email" name="email" id="email"
                        type="email" :value="old('email', $clinic->email)" placeholder="clinic@skoracare.com"
                        errorField="email" />

                    <div class="col-md-6">
                        <label for="doctor_id" class="form-label required">Owner (Doctor)</label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror">
                            <option value="">{{ __('labels.select') }}</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $clinic->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }} ({{ $doctor->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <x-input-field class="col-md-6" label="Phone Number" name="phone_no" id="phone_no"
                        type="number" :value="old('phone_no', $clinic->phone_no)" placeholder="9971423449"
                        errorField="phone_no" />

                    <x-input-field class="col-md-6" label="Consultation Fee (₹)" name="consultation_fee" id="consultation_fee"
                        type="number" step="0.01" :value="old('consultation_fee', $clinic->consultation_fee)" placeholder="400.00"
                        errorField="consultation_fee" labelClass="required" />

                    <x-select-field class="col-md-6" label="Clinic Status" name="status" id="status"
                        :options="[['id' => 'active', 'label' => 'Active'], ['id' => 'inactive', 'label' => 'Inactive']]"
                        :value="old('status', $clinic->status)" placeholder="{{ __('labels.select') }}"
                        errorField="status" labelClass="required" />

                    <x-text-area-field
                        divClass="col-md-12"
                        label="Full Address"
                        name="address"
                        id="address"
                        rows="3"
                        :value="old('address', $clinic->address)"
                        labelClass="required"
                    />

                    <x-input-field class="col-md-4" label="City" name="city" id="city"
                        type="text" :value="old('city', $clinic->city)" placeholder="New Delhi"
                        errorField="city" />

                    <x-input-field class="col-md-4" label="State" name="state" id="state"
                        type="text" :value="old('state', $clinic->state)" placeholder="Delhi"
                        errorField="state" />

                    <x-input-field class="col-md-4" label="Postal Code / PIN" name="postal_code" id="postal_code"
                        type="text" :value="old('postal_code', $clinic->postal_code)" placeholder="110001"
                        errorField="postal_code" />

                    <div class="col-md-12">
                        <label for="logo" class="form-label">Clinic Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror">
                        @if($clinic->logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/clinic_logos/' . $clinic->logo) }}" alt="Logo" class="img-thumbnail" style="max-height: 80px;">
                            </div>
                        @endif
                        @error('logo')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12 mt-4">
                        <a href="{{ route('admin.clinics.index') }}" class="btn btn-secondary cancel-btn mt-2 mt-sm-0">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.update') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
