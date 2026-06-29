@extends('layouts.app')
@section('title')
    {{ __('labels.edit_page', ['action' => __('labels.user')]) }}
@endsection
@section('content')
    <div class="container-fluid">
        <div class="gap-2 pb-2 mb-4 d-flex align-items-center">
            <h3 class="page-title">{{ __('labels.edit_page', ['action' => __('labels.user')]) }}</h3>
        </div>
        <div class="card">
            <div class="card-body">
                @if (session('error'))
                    <div class="mx-4 mt-3 mb-0 alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                <form class="row g-3" action="{{ route('admin.users.update', ['user' => \App\Support\SecureRouteParameter::encode($user->id)]) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <x-input-field
                        class="col-md-6"
                        label="{{ __('labels.first_name') }}"
                        name="first_name"
                        id="first_name"
                        type="text"
                        :value="old('first_name', $user->first_name)"
                        placeholder="{{ __('labels.first_name') }}"
                        errorField="first_name"
                        labelClass="required"
                    />
                    <x-input-field
                        class="col-md-6"
                        label="{{ __('labels.last_name') }}"
                        name="last_name"
                        id="last_name"
                        type="text"
                        :value="old('last_name', $user->last_name)"
                        placeholder="{{ __('labels.last_name') }}"
                        errorField="last_name"
                    />
                    <x-input-field
                        class="col-md-6"
                        label="{{ __('labels.email') }}"
                        name="email"
                        id="email"
                        type="email"
                        :value="old('email', $user->email)"
                        placeholder="{{ __('labels.email') }}"
                        errorField="email"
                        labelClass="required"
                    />

                    <x-input-field
                        class="col-md-6"
                        label="{{ __('labels.mobile_number') }}"
                        name="phone_no"
                        id="phone_no"
                        type="number"
                        :value="old('phone_no', $user->phone_no)"
                        placeholder="{{ __('labels.mobile_number') }}"
                        errorField="phone_no"
                        labelClass="required"
                    />

                    <div class="col-md-6">
                        <label for="role" class="form-label required">{{ __('labels.role') }}</label>
                        <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                            <option value="">{{ __('labels.select') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role', $user->roles->pluck('id')->first()) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <x-select-field
                        class="col-md-6"
                        label="{{ __('labels.status') }}"
                        name="status"
                        id="status"
                        :options="$statusData"
                        :value="old('status', $user->status)"
                        placeholder="{{ __('labels.select') }}"
                        errorField="status"
                        labelClass="required"
                    />

                    <x-text-area-field
                        divClass="col-md-12"
                        label="{{ __('labels.address') }}"
                        name="address"
                        id="address"
                        rows="4"
                        :value="old('address', $user->address)"
                    />

                    <div class="col-12">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary cancel-btn">
                            {{ __('labels.cancel') }}
                        </a>
                        <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.update') }}" />
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
