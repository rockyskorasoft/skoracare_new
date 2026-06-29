@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<section class="login-sec">
    <div class="container">
        <div class="row justify-content-center flex-column align-items-center">
            <div class="mb-4 col-xl-4 col-lg-6 text-center">
                <h1 class="auth-logo h3 mb-0">{{ config('app.name') }}</h1>
            </div>
            <div class="col-xl-6 col-xxl-4 col-lg-8">
                <div class="card auth-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="my-2 text-center fw-bold text-dark">Reset Password</h2>

                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="token" value="{{ $token }}">
                            <x-input-field label="{{__('labels.password')}}" name="password" type="password" :isToggle="true" class="col-12" />
                            <x-input-field label="{{__('labels.confirm_password')}}" name="password_confirmation" type="password" :isToggle="true" class="col-12" placeholder="{{__('labels.confirm_password')}}" />
                            <div class="d-grid pt-2 mt-2">
                                <x-button type="submit" buttons="Reset Password" class="btn btn-primary w-100" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
