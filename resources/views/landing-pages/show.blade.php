@extends('layouts.app')
@section('title')
    {{ __('labels.show_title', ['action' => 'Landing Page']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">Landing Page: {{ $landingPage->clinic_name }}</h3>
        <div class="d-flex gap-2">
            <a href="{{ url('/lp/' . $landingPage->slug) }}" target="_blank" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View Public Page
            </a>
            @can('landing-page-edit')
            <a href="{{ route('admin.landing-pages.edit', \App\Support\SecureRouteParameter::encode($landingPage->id)) }}"
                class="btn btn-sm btn-primary">
                <i class="fa-solid fa-pen me-1"></i>Edit
            </a>
            @endcan
            <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
            </a>
        </div>
    </div>

    <div class="col-md-12 divide-y-1 dashboard-card-main-col">
        <div class="row g-3">

            {{-- Basic Info --}}
            <div class="col-md-8">
                <div class="card no-scale h-100">
                    <div class="card-header"><h6 class="mb-0">Basic Information</h6></div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr><th width="180">Clinic Name</th><td>{{ $landingPage->clinic_name }}</td></tr>
                            <tr><th>Public URL</th>
                                <td><a href="{{ url('/lp/'.$landingPage->slug) }}" target="_blank" class="text-primary">
                                    {{ url('/lp/'.$landingPage->slug) }}</a></td></tr>
                            <tr><th>Status</th>
                                <td><span class="badge {{ $landingPage->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($landingPage->status) }}</span></td></tr>
                            <tr><th>Booking Enabled</th>
                                <td><span class="badge {{ $landingPage->is_appointment_enabled ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $landingPage->is_appointment_enabled ? 'Yes' : 'No' }}</span></td></tr>
                            <tr><th>Clinic Rating</th><td>{{ $landingPage->clinic_rating }} / 5.0 ⭐</td></tr>
                            <tr><th>Timings</th><td>{{ $landingPage->timings ?? '—' }}</td></tr>
                            <tr><th>WhatsApp</th><td>{{ $landingPage->whatsapp_number ?? '—' }}</td></tr>
                            <tr><th>Notification Email</th><td>{{ $landingPage->notification_email ?? '—' }}</td></tr>
                            <tr><th>Address</th><td>{{ $landingPage->address ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Logo & Hero Media --}}
            <div class="col-md-4">
                <div class="card no-scale h-100">
                    <div class="card-header"><h6 class="mb-0">Media Assets</h6></div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <small class="text-muted fw-bold d-block mb-1">Clinic Logo</small>
                            @if($landingPage->logo)
                                <img src="{{ asset('landing-page-logos/' . $landingPage->logo) }}"
                                    alt="Logo" style="max-height:100px;max-width:100%;" class="rounded border p-1">
                            @else
                                <span class="text-muted small">No logo uploaded</span>
                            @endif
                        </div>
                        <div class="text-center border-top pt-2">
                            <small class="text-muted fw-bold d-block mb-1">Hero Background Banner</small>
                            @if($landingPage->hero_media)
                                @if($landingPage->hero_media_type === 'video')
                                    <video src="{{ asset('landing-page-hero/' . $landingPage->hero_media) }}"
                                        style="max-height:100px;max-width:100%;border-radius:6px;" controls muted></video>
                                    <span class="badge bg-info text-dark d-block mt-1">Background Video</span>
                                @else
                                    <img src="{{ asset('landing-page-hero/' . $landingPage->hero_media) }}"
                                        style="max-height:100px;max-width:100%;border-radius:6px;object-fit:cover;">
                                    <span class="badge bg-primary d-block mt-1">Background Image</span>
                                @endif
                                <small class="text-muted d-block mt-1">Overlay Opacity: {{ $landingPage->hero_overlay_opacity ?? '0.65' }}</small>
                            @else
                                <span class="text-muted small">Default Dark Teal Theme</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- About --}}
            @if($landingPage->about_clinic)
            <div class="col-12">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0">About Clinic</h6></div>
                    <div class="card-body">{!! $landingPage->about_clinic !!}</div>
                </div>
            </div>
            @endif

            {{-- Services --}}
            @if($landingPage->services)
            <div class="col-md-6">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0">Services</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @foreach(array_filter(explode("\n", $landingPage->services)) as $svc)
                            <li><i class="fa-solid fa-check text-success me-2"></i>{{ trim($svc) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- Booking Slots --}}
            @if($landingPage->booking_slots)
            <div class="col-md-6">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0">Booking Slots</h6></div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(array_filter(explode("\n", $landingPage->booking_slots)) as $slot)
                            <span class="badge bg-light text-dark border">{{ trim($slot) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Doctors --}}
            @if($landingPage->doctors->count())
            <div class="col-12">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0">Doctors ({{ $landingPage->doctors->count() }})</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($landingPage->doctors as $doc)
                            <div class="col-md-4 col-lg-3">
                                <div class="card border h-100">
                                    @if($doc->photo)
                                    <img src="{{ asset('landing-page-doctors/' . $doc->photo) }}"
                                        class="card-img-top object-fit-cover" style="height:140px;" alt="">
                                    @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:140px;">
                                        <i class="fa-solid fa-user-doctor fa-3x text-secondary"></i>
                                    </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <h6 class="mb-0 fw-bold">{{ $doc->doctor_name }}</h6>
                                        @if($doc->specialization)<small class="text-primary">{{ $doc->specialization }}</small><br>@endif
                                        @if($doc->consultation_fee)<small class="text-muted">Fee: {{ $doc->consultation_fee }}</small><br>@endif
                                        @if($doc->experience)<small class="text-muted">Exp: {{ $doc->experience }}</small><br>@endif
                                        @if($doc->email)<small class="text-muted">{{ $doc->email }}</small>@endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Testimonials --}}
            @if($landingPage->testimonials->count())
            <div class="col-12">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0">Patient Stories ({{ $landingPage->testimonials->count() }})</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($landingPage->testimonials as $t)
                            <div class="col-md-4">
                                <div class="card border h-100 p-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                            style="width:36px;height:36px;font-size:14px;">
                                            {{ strtoupper(substr($t->patient_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong class="d-block">{{ $t->patient_name }}</strong>
                                            <span class="text-warning">
                                                @for($s=1;$s<=5;$s++)
                                                    {{ $s <= $t->rating ? '★' : '☆' }}
                                                @endfor
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mb-0 text-muted small">{!! nl2br(e($t->story)) !!}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Gallery --}}
            @if($landingPage->gallery->count())
            <div class="col-12">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0">Gallery ({{ $landingPage->gallery->count() }} images)</h6></div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($landingPage->gallery as $img)
                            <div class="col-6 col-md-3 col-lg-2">
                                <div class="border rounded overflow-hidden" style="height:100px;">
                                    <img src="{{ asset('landing-page-gallery/' . $img->image) }}"
                                        class="w-100 h-100 object-fit-cover" alt="">
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection
