@extends('layouts.app')
@section('title')
    {{ __('labels.edit_title', ['action' => 'Landing Page']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.edit_title', ['action' => 'Landing Page']) }}</h3>
        <div class="d-flex gap-2">
            <a href="{{ url('/lp/' . $landingPage->slug) }}" target="_blank" class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>View Public Page
            </a>
            <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
                <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
            </a>
        </div>
    </div>

    <div class="col-md-12 divide-y-1 dashboard-card-main-col">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="lpForm" class="row g-0"
            action="{{ route('admin.landing-pages.update', \App\Support\SecureRouteParameter::encode($landingPage->id)) }}"
            method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Track gallery items to delete --}}
            <input type="hidden" name="delete_gallery_ids" id="deleteGalleryIds" value="">

            {{-- ── Section 1: Basic Info ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Basic Information</h6></div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Clinic dropdown (auto-fill trigger) --}}
                            <div class="col-12">
                                <label for="clinic_select" class="form-label fw-semibold">
                                    <i class="fa-solid fa-hospital me-1 text-primary"></i>
                                    Switch / Re-link Clinic <small class="text-muted fw-normal">(optional — auto-fills fields below)</small>
                                </label>
                                <select id="clinic_select" class="form-select multiSelectSearch" data-placeholder="Choose a clinic to auto-fill...">
                                    <option value=""></option>
                                    @foreach($clinics as $clinic)
                                        <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecting a clinic will auto-fill fields below. Current values are preserved until you select.</small>
                            </div>

                            <div class="col-12"><hr class="my-0 border-dashed opacity-50"></div>

                            <x-input-field class="col-md-6" label="Clinic Name" name="clinic_name" id="clinic_name"
                                type="text" :value="old('clinic_name', $landingPage->clinic_name)"
                                placeholder="Revival Healthcare Services"
                                errorField="clinic_name" labelClass="required" />

                            <div class="col-md-6">
                                <label for="slug" class="form-label required">Public URL Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted small" style="font-size:0.78rem;">{{ url('/lp/') }}/</span>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug', $landingPage->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <x-select-field class="col-md-4" label="Status" name="status" id="status"
                                :options="[['id' => 'active', 'label' => 'Active'], ['id' => 'inactive', 'label' => 'Inactive']]"
                                :value="old('status', $landingPage->status)" errorField="status" labelClass="required" />

                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_appointment_enabled"
                                        id="is_appointment_enabled" value="1"
                                        {{ old('is_appointment_enabled', $landingPage->is_appointment_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_appointment_enabled">
                                        Enable Appointment Booking on Public Page
                                    </label>
                                </div>
                            </div>

                            <x-input-field class="col-md-4" label="Clinic Rating (0–5)" name="clinic_rating" id="clinic_rating"
                                type="number" step="0.1" min="0" max="5"
                                :value="old('clinic_rating', $landingPage->clinic_rating)"
                                placeholder="4.8" errorField="clinic_rating" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 2: Contact & SMTP ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0"><i class="fa-solid fa-envelope me-2 text-primary"></i>Contact & SMTP Configuration</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <x-input-field class="col-md-6" label="WhatsApp Number" name="whatsapp_number" id="whatsapp_number"
                                type="text" :value="old('whatsapp_number', $landingPage->whatsapp_number)"
                                placeholder="+91 9971000000" errorField="whatsapp_number" />

                            <x-input-field class="col-md-6" label="Notification Email" name="notification_email" id="notification_email"
                                type="email" :value="old('notification_email', $landingPage->notification_email)"
                                placeholder="clinic@example.com" errorField="notification_email" />

                            <div class="col-12"><hr class="my-1"><small class="text-muted fw-semibold">SMTP Configuration</small></div>

                            <x-input-field class="col-md-4" label="SMTP Host" name="smtp_host"
                                type="text" :value="old('smtp_host', $landingPage->smtp_host)" placeholder="smtp.gmail.com" />
                            <x-input-field class="col-md-2" label="SMTP Port" name="smtp_port"
                                type="text" :value="old('smtp_port', $landingPage->smtp_port)" placeholder="587" />
                            <x-select-field class="col-md-2" label="Encryption" name="smtp_encryption"
                                :options="[['id'=>'tls','label'=>'TLS'],['id'=>'ssl','label'=>'SSL'],['id'=>'none','label'=>'None']]"
                                :value="old('smtp_encryption', $landingPage->smtp_encryption ?? 'tls')" />
                            <x-input-field class="col-md-4" label="SMTP Username" name="smtp_username"
                                type="text" :value="old('smtp_username', $landingPage->smtp_username)" placeholder="you@gmail.com" />
                            <x-input-field class="col-md-4" label="SMTP Password" name="smtp_password"
                                type="password" :value="old('smtp_password', $landingPage->smtp_password)" placeholder="••••••••" />
                            <x-input-field class="col-md-4" label="Sender From Address" name="smtp_from_address"
                                type="email" :value="old('smtp_from_address', $landingPage->smtp_from_address)" placeholder="noreply@clinic.com" />
                            <x-input-field class="col-md-4" label="Sender From Name" name="smtp_from_name"
                                type="text" :value="old('smtp_from_name', $landingPage->smtp_from_name)" placeholder="Revival Healthcare" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 3: Content ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Clinic Content</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="logo" class="form-label">Clinic Logo <small class="text-muted">(PNG / JPG / WEBP)</small></label>
                                @if($landingPage->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('landing-page-logos/' . $landingPage->logo) }}"
                                            alt="Current Logo" style="max-height:80px;" class="rounded border">
                                        <small class="text-muted ms-2">Current logo — upload new to replace</small>
                                    </div>
                                @endif
                                <input type="file" name="logo" id="logo" accept="image/*"
                                    class="form-control @error('logo') is-invalid @enderror">
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hero_media" class="form-label">
                                    <i class="fa-solid fa-photo-film text-primary me-1"></i>
                                    Hero Background Media <small class="text-muted">(Image or Video: MP4, WEBM, JPG, PNG)</small>
                                </label>
                                @if($landingPage->hero_media)
                                    <div class="mb-2 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($landingPage->hero_media_type === 'video')
                                                <video src="{{ asset('landing-page-hero/' . $landingPage->hero_media) }}" style="height:48px; width:70px; object-fit:cover; border-radius:4px;" muted></video>
                                                <span class="badge bg-info text-dark">Current Video</span>
                                            @else
                                                <img src="{{ asset('landing-page-hero/' . $landingPage->hero_media) }}" style="height:48px; width:70px; object-fit:cover; border-radius:4px;">
                                                <span class="badge bg-primary">Current Image</span>
                                            @endif
                                        </div>
                                        <div class="form-check ms-2">
                                            <input class="form-check-input" type="checkbox" name="remove_hero_media" value="1" id="remove_hero_media">
                                            <label class="form-check-label text-danger small fw-semibold" for="remove_hero_media">Remove</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="file" name="hero_media" id="hero_media" accept="image/*,video/*" class="form-control @error('hero_media') is-invalid @enderror">
                                <small class="text-muted">Dynamic background banner for hero section.</small>
                                @error('hero_media')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hero_overlay_opacity" class="form-label">
                                    Hero Dark Overlay Opacity <small class="text-muted">(0.0 to 1.0)</small>
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="form-range flex-grow-1" min="0" max="1" step="0.05" id="hero_overlay_range" value="{{ old('hero_overlay_opacity', $landingPage->hero_overlay_opacity ?? '0.65') }}">
                                    <input type="number" name="hero_overlay_opacity" id="hero_overlay_opacity" class="form-control form-control-sm" style="width: 80px;" min="0" max="1" step="0.05" value="{{ old('hero_overlay_opacity', $landingPage->hero_overlay_opacity ?? '0.65') }}">
                                </div>
                                <small class="text-muted">Balances readability over dynamic video/image.</small>
                            </div>

                            <div class="col-md-6" id="heroMediaPreviewBox" style="display: none;">
                                <label class="form-label">New Media Preview</label>
                                <div id="heroMediaPreviewContainer" class="rounded border p-1 bg-light" style="max-height: 120px; overflow: hidden;"></div>
                            </div>


                            <div class="col-12">
                                <label for="about_clinic" class="form-label">About Clinic <small class="text-muted">(Rich Text)</small></label>
                                <textarea name="about_clinic" id="about_clinic"
                                    class="form-control ckeditor @error('about_clinic') is-invalid @enderror" rows="5">{{ old('about_clinic', $landingPage->about_clinic) }}</textarea>
                                @error('about_clinic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <x-input-field class="col-md-6" label="Timings" name="timings"
                                type="text" :value="old('timings', $landingPage->timings)" placeholder="Mon–Sat: 9:00 AM – 7:00 PM" />
                            <x-text-area-field divClass="col-md-6" label="Address" name="address"
                                rows="2" :value="old('address', $landingPage->address)" />
                            <x-text-area-field divClass="col-12" label="Services (one per line)" name="services"
                                rows="4" :value="old('services', $landingPage->services)" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 4: Booking Slots ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0"><i class="fa-regular fa-calendar-check me-2 text-primary"></i>Appointment Slot Configuration</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Start Time</label>
                                <input type="time" name="booking_start_time" class="form-control"
                                    value="{{ old('booking_start_time', $landingPage->booking_start_time ?? '09:00') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="booking_end_time" id="booking_end_time" class="form-control"
                                    value="{{ old('booking_end_time', $landingPage->booking_end_time ?? '20:00') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Slot Interval (minutes)</label>
                                <input type="number" name="slot_interval_minutes" id="slot_interval_minutes"
                                    class="form-control" min="5" max="120" step="5"
                                    value="{{ old('slot_interval_minutes', $landingPage->slot_interval_minutes ?? 15) }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" id="generateSlotsBtn">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Generate Slots
                                </button>
                            </div>
                            <div class="col-12">
                                <label for="booking_slots" class="form-label">Booking Time Slots <small class="text-muted">(one per line)</small></label>
                                <textarea name="booking_slots" id="booking_slots" class="form-control" rows="6">{{ old('booking_slots', $landingPage->booking_slots) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 5: Testimonials ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="fa-solid fa-star me-2 text-primary"></i>Patient Stories / Testimonials</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addTestimonialBtn">
                            <i class="fa-solid fa-plus me-1"></i>Add Story
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="testimonialsContainer">
                            @forelse($landingPage->testimonials as $tidx => $t)
                            <div class="border rounded p-3 mb-3 position-relative testimonial-card" data-index="{{ $tidx }}">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-testimonial-btn">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label required">Patient Name</label>
                                        <input type="text" name="testimonials[{{ $tidx }}][patient_name]" class="form-control" required value="{{ $t->patient_name }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Rating (1–5)</label>
                                        <input type="number" name="testimonials[{{ $tidx }}][rating]" class="form-control" min="1" max="5" value="{{ $t->rating }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Story</label>
                                        <textarea name="testimonials[{{ $tidx }}][story]" class="form-control" rows="3">{{ $t->story }}</textarea>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted small mb-0" id="noTestimonialsMsg">No stories added yet. Click "Add Story".</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 6: Gallery ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0"><i class="fa-solid fa-images me-2 text-primary"></i>Clinic Gallery Images</h6></div>
                    <div class="card-body">
                        @if($landingPage->gallery->count())
                        <p class="text-muted small mb-2">Existing images — click <i class="fa-solid fa-trash text-danger"></i> to remove on save:</p>
                        <div class="row g-2 mb-3" id="existingGalleryContainer">
                            @foreach($landingPage->gallery as $img)
                            <div class="col-6 col-md-3 col-lg-2 gallery-item" data-id="{{ $img->id }}">
                                <div class="border rounded overflow-hidden position-relative" style="height:90px;">
                                    <img src="{{ asset('landing-page-gallery/' . $img->image) }}"
                                        class="w-100 h-100 object-fit-cover" alt="">
                                    <button type="button"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-gallery-btn"
                                        data-id="{{ $img->id }}" title="Remove on save">
                                        <i class="fa-solid fa-trash" style="font-size:10px;"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                        <div class="mt-2">
                            <label for="gallery_images" class="form-label">Add New Gallery Images <small class="text-muted">(PNG/JPG, max 3MB each)</small></label>
                            <input type="file" name="gallery_images[]" id="gallery_images" multiple accept="image/*" class="form-control">
                        </div>
                        <div class="row g-2 mt-2" id="galleryPreviewContainer"></div>
                    </div>
                </div>
            </div>

            {{-- ── Section 7: Doctors ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="mb-0"><i class="fa-solid fa-user-doctor me-2 text-primary"></i>Manage Doctors</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addDoctorBtn">
                            <i class="fa-solid fa-plus me-1"></i>Add Doctor
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="doctorsContainer">
                            @forelse($landingPage->doctors as $didx => $d)
                            <div class="border rounded p-3 mb-3 position-relative doctor-card" data-index="{{ $didx }}">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-doctor-btn">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label required">Doctor Name</label>
                                        <input type="text" name="doctors[{{ $didx }}][doctor_name]" class="form-control" required value="{{ $d->doctor_name }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Email (Login)</label>
                                        <input type="email" name="doctors[{{ $didx }}][email]" class="form-control" value="{{ $d->email }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Specialization</label>
                                        <input type="text" name="doctors[{{ $didx }}][specialization]" class="form-control" value="{{ $d->specialization }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Consultation Fee</label>
                                        <input type="text" name="doctors[{{ $didx }}][consultation_fee]" class="form-control" value="{{ $d->consultation_fee }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Experience</label>
                                        <input type="text" name="doctors[{{ $didx }}][experience]" class="form-control" value="{{ $d->experience }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Doctor Photo</label>
                                        @if($d->photo)
                                            <div class="mb-1">
                                                <img src="{{ asset('landing-page-doctors/' . $d->photo) }}"
                                                    alt="" style="height:48px;" class="rounded border">
                                                <small class="text-muted ms-1">Upload new to replace</small>
                                            </div>
                                        @endif
                                        <input type="file" name="doctors[{{ $didx }}][photo]" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted small mb-0" id="noDoctorsMsg">No doctors added yet. Click "Add Doctor".</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="col-12 mb-4">
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-secondary cancel-btn">
                        {{ __('labels.cancel') }}
                    </a>
                    <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.update') }}" />
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<?php
use App\Support\SecureRouteParameter;
?>
<script>
(function () {
    'use strict';

    // ── Clinic data map (from PHP) ────────────────────────────────────────────
    const CLINICS = @json($clinicsJson ? json_decode($clinicsJson, true) : []);

    // ── Clinic dropdown auto-fill ─────────────────────────────────────────────
    const clinicSelect    = document.getElementById('clinic_select');
    const clinicNameInput = document.getElementById('clinic_name');
    const slugInput       = document.getElementById('slug');
    const addressEl       = document.getElementById('address');
    const emailEl         = document.getElementById('notification_email');

    if (clinicSelect) {
        $(clinicSelect).on('change', function () {
            const id = this.value;
            if (!id || !CLINICS[id]) return;
            const c = CLINICS[id];
            if (clinicNameInput && c.name) clinicNameInput.value = c.name;
            if (addressEl && c.address) addressEl.value = c.address;
            if (emailEl && c.email) emailEl.value = c.email;
        });
    }

    // ── Slot generator ────────────────────────────────────────────────────────
    const generateBtn = document.getElementById('generateSlotsBtn');
    if (generateBtn) {
        generateBtn.addEventListener('click', function () {
            const startEl = document.querySelector('[name="booking_start_time"]');
            const start    = startEl ? startEl.value : '';
            const end      = document.getElementById('booking_end_time').value;
            const interval = parseInt(document.getElementById('slot_interval_minutes').value, 10);
            if (!start || !end || !interval) { alert('Fill Start Time, End Time and Interval first.'); return; }
            const slots = [];
            let [sh, sm]  = start.split(':').map(Number);
            let [eh, em]  = end.split(':').map(Number);
            let startMins = sh * 60 + sm, endMins = eh * 60 + em;
            while (startMins < endMins) {
                const h = Math.floor(startMins / 60), m = startMins % 60;
                const ampm = h < 12 ? 'AM' : 'PM';
                const hh = h % 12 || 12, mm = String(m).padStart(2, '0');
                slots.push(`${String(hh).padStart(2, '0')}:${mm} ${ampm}`);
                startMins += interval;
            }
            document.getElementById('booking_slots').value = slots.join('\n');
        });
    }

    // ── Gallery delete tracking ───────────────────────────────────────────────
    const deleteGalleryIdsInput = document.getElementById('deleteGalleryIds');
    let deleteIds = [];
    document.querySelectorAll('.delete-gallery-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            if (!deleteIds.includes(id)) deleteIds.push(id);
            deleteGalleryIdsInput.value = deleteIds.join(',');
            this.closest('.gallery-item').style.opacity = '0.4';
            this.closest('.gallery-item').title = 'Will be removed on save';
        });
    });

    // ── Gallery new upload preview ────────────────────────────────────────────
    const galleryInput = document.getElementById('gallery_images');
    if (galleryInput) {
        galleryInput.addEventListener('change', function () {
            const container = document.getElementById('galleryPreviewContainer');
            container.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader(), col = document.createElement('div');
                col.className = 'col-6 col-md-3 col-lg-2';
                reader.onload = e => {
                    col.innerHTML = `<div class="border rounded overflow-hidden" style="height:100px;">
                        <img src="${e.target.result}" class="w-100 h-100 object-fit-cover" alt=""></div>
                        <small class="text-muted d-block text-truncate mt-1">${file.name}</small>`;
                };
                reader.readAsDataURL(file);
                container.appendChild(col);
            });
        });
    }

    // ── Testimonials repeater ─────────────────────────────────────────────────
    let testimonialCount = {{ $landingPage->testimonials->count() }};

    document.querySelectorAll('.remove-testimonial-btn').forEach(btn => {
        btn.addEventListener('click', function () { this.closest('.border.rounded').remove(); });
    });

    document.getElementById('addTestimonialBtn').addEventListener('click', function () {
        const noMsg = document.getElementById('noTestimonialsMsg');
        if (noMsg) noMsg.style.display = 'none';
        const i = testimonialCount++;
        const card = document.createElement('div');
        card.className = 'border rounded p-3 mb-3 position-relative';
        card.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-testimonial-btn">
                <i class="fa-solid fa-trash"></i></button>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label required">Patient Name</label>
                    <input type="text" name="testimonials[${i}][patient_name]" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating (1–5)</label>
                    <input type="number" name="testimonials[${i}][rating]" class="form-control" min="1" max="5" value="5">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Story</label>
                    <textarea name="testimonials[${i}][story]" class="form-control" rows="3"></textarea>
                </div>
            </div>`;
        card.querySelector('.remove-testimonial-btn').addEventListener('click', function () { card.remove(); });
        document.getElementById('testimonialsContainer').appendChild(card);
    });

    // ── Doctors repeater ──────────────────────────────────────────────────────
    let doctorCount = {{ $landingPage->doctors->count() }};

    document.querySelectorAll('.remove-doctor-btn').forEach(btn => {
        btn.addEventListener('click', function () { this.closest('.border.rounded').remove(); });
    });

    document.getElementById('addDoctorBtn').addEventListener('click', function () {
        const noMsg = document.getElementById('noDoctorsMsg');
        if (noMsg) noMsg.style.display = 'none';
        const i = doctorCount++;
        const card = document.createElement('div');
        card.className = 'border rounded p-3 mb-3 position-relative';
        card.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-doctor-btn">
                <i class="fa-solid fa-trash"></i></button>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Doctor Name</label>
                    <input type="text" name="doctors[${i}][doctor_name]" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email (Login)</label>
                    <input type="email" name="doctors[${i}][email]" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="doctors[${i}][specialization]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Consultation Fee</label>
                    <input type="text" name="doctors[${i}][consultation_fee]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Experience</label>
                    <input type="text" name="doctors[${i}][experience]" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Doctor Photo</label>
                    <input type="file" name="doctors[${i}][photo]" class="form-control" accept="image/*">
                </div>
            </div>`;
        card.querySelector('.remove-doctor-btn').addEventListener('click', function () { card.remove(); });
        document.getElementById('doctorsContainer').appendChild(card);
    });

    // ── Hero Media preview & Opacity slider sync ─────────────────────────────
    const heroMediaInput = document.getElementById('hero_media');
    const heroMediaBox = document.getElementById('heroMediaPreviewBox');
    const heroMediaContainer = document.getElementById('heroMediaPreviewContainer');
    const heroOverlayRange = document.getElementById('hero_overlay_range');
    const heroOverlayNumber = document.getElementById('hero_overlay_opacity');

    if (heroOverlayRange && heroOverlayNumber) {
        heroOverlayRange.addEventListener('input', function() { heroOverlayNumber.value = this.value; });
        heroOverlayNumber.addEventListener('input', function() { heroOverlayRange.value = this.value; });
    }

    if (heroMediaInput) {
        heroMediaInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) {
                heroMediaBox.style.display = 'none';
                heroMediaContainer.innerHTML = '';
                return;
            }
            heroMediaBox.style.display = 'block';
            heroMediaContainer.innerHTML = '';
            const url = URL.createObjectURL(file);
            if (file.type.startsWith('video/')) {
                heroMediaContainer.innerHTML = `<video src="${url}" controls muted autoplay style="max-height:110px; width:100%; object-fit:cover; border-radius:6px;"></video>
                <small class="text-muted d-block mt-1">Video selected: ${file.name} (${(file.size / (1024*1024)).toFixed(1)} MB)</small>`;
            } else {
                heroMediaContainer.innerHTML = `<img src="${url}" style="max-height:110px; width:100%; object-fit:cover; border-radius:6px;">
                <small class="text-muted d-block mt-1">Image selected: ${file.name} (${(file.size / (1024*1024)).toFixed(1)} MB)</small>`;
            }
        });
    }

})();
</script>
@endpush
