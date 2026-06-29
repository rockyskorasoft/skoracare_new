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

                    <div class="col-md-6">
                        <label for="doctor_id" class="form-label required">Owner (Doctor)</label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror">
                            <option value="">{{ __('labels.select') }}</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $clinic->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
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

                    <x-text-area-field
                        divClass="col-md-12"
                        label="Full Address"
                        name="address"
                        id="address"
                        rows="4"
                        :value="old('address', $clinic->address)"
                        labelClass="required"
                    />

                    <div class="col-md-12">
                        <label for="logo" class="form-label">Clinic Logo</label>
                        <input type="file" name="logo" id="logo" class="form-control mb-2 @error('logo') is-invalid @enderror">
                        @error('logo')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                        @if (! empty($clinic->logo))
                            <div class="mt-2">
                                <p class="mb-1 text-muted">Current Logo:</p>
                                <img src="{{ asset('storage/clinic_logos/' . $clinic->logo) }}" alt="Logo" class="img-fluid rounded border p-1" style="max-height: 80px;">
                            </div>
                        @endif
                    </div>

                    <div class="col-12">
                        <a href="{{ route('admin.clinics.index') }}" class="btn btn-primary cancel-btn">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.update') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
