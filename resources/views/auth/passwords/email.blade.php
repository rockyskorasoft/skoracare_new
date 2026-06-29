@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <section class="login-sec">
        <div class="container">
            <div class="row justify-content-center flex-column align-items-center">
                <div class="col-xl-6 col-xxl-5 col-lg-8">
                    <div class="card auth-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-3">
                                <div class="sidebar-brand d-inline-flex">
                                    <div class="sidebar-brand-icon">
                                        <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="Logo" />
                                    </div>
                                </div>
                            </div>
                            <h2 class="my-2 text-center fw-bold text-dark">Forgot Password</h2>

                            @if (session('status'))
                                <div class="alert alert-success">{{ session('status') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <form action="{{ route('password.email') }}" method="POST" class="mt-3">
                                @csrf
                                <x-input-field label="Email" name="email" type="email" :value="old('email')"
                                    placeholder="Email" class="col-12" />
                                <div class="row mt-4 g-2">
                                    <div class="col-12 col-sm-6 d-grid">
                                        <button type="submit" class="btn btn-primary">Send Link</button>
                                    </div>
                                    <div class="col-12 col-sm-6 d-grid">
                                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Back to Login</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection