@extends('layouts.app')
@section('title')
    {{ __('labels.edit_page', ['action' => __('labels.user')]) }}
@endsection
@section('content')
    <div class="profile-page">
        <div class="gap-2 pb-2 mb-4 d-flex align-items-center">
            <h3 class="page-title">{{ __('labels.user_profile') }}</h3>

            @if (session('message'))
                <div class="mx-4 mt-3 mb-0 alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="gap-3 row gap-xl-0">
            <div class="col-xxl-5">
                <div class="rounded shadow card h-100">
                    <div class="profile-bg">
                        {{-- <img src="{{ Vite::asset('resources/images/logo.png') }}" alt="bg-img"
                            class="mb-n4 w-50 border-bottom border-3 border-primary img-fluid" > --}}
                        <!-- @if ($user->profile_pic)
                            <a href="{{ asset('storage/profile_images/' . $user->profile_pic) }}" target="_blank">
                                <img src="{{ asset('storage/profile_images/' . $user->profile_pic) }}"
                                    class="m-auto bg-white border d-block mt-n5 border-3 rounded-circle border-primary profile-img"
                                    alt="profile">
                            </a>
                        @else
                            <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="user"
                                class="m-auto bg-white border d-block mt-n5 border-3 rounded-circle border-primary profile-img">
                        @endif -->


                    </div>
                    <div class="pb-3 mx-3 mt-5 mb-3 user-details border-bottom border-1">
                        <h4 class="mt-4 mb-0 text-center text-primary fw-semibold">{{ $user->first_name }}</h4>
                        <p class="m-0 text-center"><a href="mailto:{{ $user->email }}"
                                class="text-profile-text-color">{{ $user->email }}</a></p>
                    </div>
                    <div class="p-3 personal-info">
                        <h5 class="mb-4 fw-semibold">{{ __('labels.user_personal_info') }}</h5>
                        <div class="mb-3 info-div d-flex justify-content-start">
                            <label for="first_name" class="w-50 fw-semibold">{{ __('labels.first_name') }}</label>
                            <div class="info w-50">:{{ $user->first_name }}</div>
                        </div>
                        <div class="mb-3 info-div d-flex justify-content-start">
                            <label for="last_name" class="w-50 fw-semibold">{{ __('labels.last_name') }}</label>
                            <div class="info w-50">:{{ $user->last_name }}</div>
                        </div>
                        <div class="mb-3 info-div d-flex justify-content-start">
                            <label for="user-email" class="w-50 fw-semibold">{{ __('labels.email') }}</label>
                            <div class="info w-50">:{{ $user->email }}</div>
                        </div>

                        <div class="mb-3 info-div d-flex justify-content-start">
                            <label for="role" class="w-50 fw-semibold">{{ __('labels.role') }}</label>
                            <div class="info w-50">:{{ $user->roles[0]->name }}</div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-xxl-7">
                <div class="p-3 rounded card h-100">
                    <div class="gap-2 pb-2 mb-4 d-flex align-items-center">
                        <h3 class="page-title">{{ __('labels.edit_info') }}</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.update.user-profile', $user->id) }}" class="py-1 row g-3"
                        enctype="multipart/form-data">
                        @csrf
                        <!-- <div class="col-8">
                            <label for="file-upload" class="pointer">
                                @if (!empty($user->profile_pic))
                                    <img src="{{ asset('storage/profile_images/' . $user->profile_pic) }}"
                                        class="border border-primary border-3 rounded-circle upload-img" alt="profile">
                                @else
                                    <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="user" class="img-fluid">
                                @endif
                            </label>
                            <input type="file" name="profile_pic" id="file-upload" class="d-none">
                        </div> -->
                        <x-input-field
                            class="col-md-6"
                            label="{{ __('labels.first_name') }}"
                            name="first_name"
                            id="first_name"
                            type="text"
                            :value="old('first_name', $user->first_name)"
                            placeholder="{{ __('labels.first_name') }}"
                            errorField="first_name"
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
                        />
                        <x-input-field
                            class="col-md-6"
                            label="{{ __('labels.role') }}"
                            name="role"
                            id="role"
                            type="text"
                            :value="old('role', optional($user->roles->first())->name)"
                            placeholder="{{ __('labels.role') }}"
                            disabled
                        />

                        <div class="col-12">
                            <x-button
                                type="submit"
                                id="update-profile"
                                class="btn btn-primary d-block w-100 w-md-25 ms-auto"
                                buttons="{{ __('buttons.update') }}"
                            />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
