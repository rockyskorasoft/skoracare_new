@extends(auth()->user()->hasRole(config('constants.doctor_role_name')) ? 'doctors.layouts.doctor-app' : 'layouts.app')
@section('title')
    {{ __('Change Password') }}
@endsection
@section('content')
    <div class="gap-2 pb-2 mb-4 d-flex align-items-center">
        <h3 class="page-title">{{ __('Change Password') }}</h3>
    </div>
    <div class="col-md-12 divide-y-1">
        <div class="row">
            <div class="col-xxl-7 col-md-10 col-sm-12">
                <div class="card border-rounded">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.update-password') }}" class="row g-3">
                            @if (session('error'))
                                <div class="p-2 mb-3 alert alert-danger" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @csrf

                            {{-- Old Password --}}
                            <div class="col-12 col-md-12">
                                <x-input-field
                                    :label="__('labels.old_password')"
                                    name="old_password"
                                    id="old_password"
                                    type="password"
                                    :placeholder="__('labels.old_password')"
                                    error-field="old_password"
                                    class="col-12 col-md-12"
                                    :is-toggle="true" />
                            </div>

                            {{-- New Password --}}
                            <div class="col-12 col-md-12">
                                <x-input-field
                                    :label="__('labels.new_password')"
                                    name="password"
                                    id="password"
                                    type="password"
                                    :placeholder="__('labels.new_password')"
                                    error-field="password"
                                    class="col-12 col-md-12"
                                    :is-toggle="true" />
                            </div>

                            {{-- Confirm Password --}}
                            <div class="col-12 col-md-12">
                                <x-input-field
                                    :label="__('labels.confirm_password')"
                                    name="confirm_password"
                                    id="confirm_password"
                                    type="password"
                                    :placeholder="__('labels.confirm_password')"
                                    error-field="confirm_password"
                                    class="col-12 col-md-12"
                                    :is-toggle="true" />
                            </div>

                            {{-- Submit Button --}}
                            <div class="mt-4 mb-0 d-flex align-items-center justify-content-between">
                                <x-button
                                    type="submit"
                                    id="submitBtn"
                                    class="px-0 btn btn-primary d-block w-25 ms-auto"
                                    :buttons="__('buttons.submit')" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

