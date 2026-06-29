@extends('layouts.app')
@section('title')
    {{ __('labels.activity_logs') }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center mb-4 pb-2">
    <h3 class="page-title">{{ __('labels.activity_logs') }}</h3>
    </div>
    <div class="col-md-12 divide-y-1 dashboard-card-main-col">
        <div class="row">
            <div class="col-12">
                <div class="card no-scale">
                    @if (session('message'))
                        <div class="alert alert-success alert-dismissible fade show mx-4 mb-0 mt-3" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mt-4">
                        <div class="employee-cards-filter d-flex justify-content-sm-between float-end   align-items-center">
                            <div class="employees-number"><span class="font-h3 fw-bold">
                                </span></div>
                            <div class="filter-btn me-3"><button
                                    class="gap-2  btn d-flex align-items-center collapsed btn btn-primary"
                                    data-bs-toggle="collapse" data-bs-target="#accordian-filter" aria-expanded="false"
                                    aria-controls="accordian-filter"><img src="">{{ __('buttons.filter') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- filter Start -->
                    <div class="filter-card-row mt-3">
                        <div class="col-12">
                            <div id="accordian-filter" class="accordion-collapse collapse"
                                data-bs-parent="#accordionFlushExample">
                                <div class="mb-3 rounded-2 px-3 py-5" style="background: linear-gradient(135deg, #7e8c9a 0%, #96a3b0 100%);">
                                    <form action="#" id="filter-form">
                                        <div class="row gy-4 align-items-end">
                                            @if (!empty($userData))
                                                <x-select-field name="created_by" id="created_by"
                                                    label="{{ __('labels.user_name') }}" :options="$userData" keyName="name"
                                                    placeholder="{{ __('labels.select') }}" :class="'leaveSelectSearch'"
                                                    divClass="col-md-4 col-lg-3" labelClass="text-white" />
                                            @endif

                                            @if (!empty($providerData))
                                                <x-select-field name="tenant_id" id="tenant_id"
                                                    label="{{ __('labels.provider') }}" :options="$providerData"
                                                    placeholder="{{ __('labels.select') }}" :class="'leaveSelectSearch'"
                                                    divClass="col-md-4 col-lg-3" labelClass="text-white" />
                                            @endif

                                            @if (!empty($commonStatuses))
                                                <x-select-field name="user_status" id="active_inactive"
                                                    label="{{ __('labels.status') }}" :options="$commonStatuses" keyName="name"
                                                    placeholder="{{ __('labels.select') }}" divClass="col-md-4 col-lg-3"
                                                    labelClass="text-white" />
                                            @endif

                                            @if (!empty($statuses))
                                                <x-select-field name="status" label="{{ __('labels.attendance_status') }}"
                                                    :options="$statuses" placeholder="{{ __('labels.select') }}"
                                                    :value="old('status')" divClass="col-md-4 col-lg-3"
                                                    labelClass="text-white" />
                                            @endif

                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                <label for="daterange"
                                                    class="text-white form-label">{{ __('labels.created_at') }}</label>
                                                <input type="text" class="form-control form-select" name="created_at"
                                                    id="daterange" placeholder="Select Date range" autocomplete="off">

                                            </div>

                                            <!-- filter Button -->
                                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                <button class="text-black btn bg-white fw-semibold" style="color: #6b7a88 !important;" id="apply-filter"
                                                    type="button">{{ __('buttons.apply') }}</button>
                                                <button class="text-black btn bg-white fw-semibold" style="color: #6b7a88 !important;" id="reset-filter"
                                                    type="button">{{ __('buttons.reset') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- filter end  -->

                    <div class="card-body pt-0">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
