@extends('layouts.app')
@section('title')
    {{ __('labels.create_title', ['action' => 'Landing Page']) }}
@endsection
@section('content')
    <div class="d-flex gap-2 align-items-center justify-content-between mb-4 pb-2">
        <h3 class="page-title">{{ __('labels.create_title', ['action' => 'Landing Page']) }}</h3>
        <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-outline-secondary btn-sm custom-cancell">
            <i class="bi bi-arrow-left me-1"></i>{{ __('buttons.back') }}
        </a>
    </div>

    <div class="col-md-12 divide-y-1 dashboard-card-main-col">
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form id="lpForm" class="row g-0" action="{{ route('admin.landing-pages.store') }}" method="post" enctype="multipart/form-data">
            @csrf

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
                                    Select Existing Clinic <small class="text-muted fw-normal">(optional — auto-fills fields below)</small>
                                </label>
                                <select id="clinic_select" class="form-select multiSelectSearch" data-placeholder="Choose a clinic to auto-fill...">
                                    <option value=""></option>
                                    @foreach($clinics as $clinic)
                                        <option value="{{ $clinic->id }}">{{ $clinic->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Selecting a clinic will auto-fill Clinic Name, Email, Address and other fields. You can still edit them after.</small>
                            </div>

                            <div class="col-12"><hr class="my-0 border-dashed opacity-50"></div>

                            {{-- Clinic Name (text — filled by dropdown or manual) --}}
                            <x-input-field class="col-md-6" label="Clinic Name" name="clinic_name" id="clinic_name"
                                type="text" :value="old('clinic_name')" placeholder="e.g. Revival Healthcare Services"
                                errorField="clinic_name" labelClass="required" />

                            {{-- Slug --}}
                            <div class="col-md-6">
                                <label for="slug" class="form-label required">Public URL Slug</label>
                                <div class="input-group">
                                    <span class="input-group-text text-muted small" style="font-size:0.78rem;">{{ url('/lp/') }}/</span>
                                    <input type="text" name="slug" id="slug"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        value="{{ old('slug') }}"
                                        placeholder="revival-healthcare-services">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Lowercase letters, numbers, hyphens only. Auto-generated from clinic name.</small>
                            </div>

                            <x-select-field class="col-md-4" label="Status" name="status" id="status"
                                :options="[['id' => 'active', 'label' => 'Active'], ['id' => 'inactive', 'label' => 'Inactive']]"
                                :value="old('status', 'active')" errorField="status" labelClass="required" />

                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_appointment_enabled"
                                        id="is_appointment_enabled" value="1"
                                        {{ old('is_appointment_enabled', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_appointment_enabled">
                                        Enable Appointment Booking on Public Page
                                    </label>
                                </div>
                            </div>

                            <x-input-field class="col-md-4" label="Clinic Rating (0–5)" name="clinic_rating" id="clinic_rating"
                                type="number" step="0.1" min="0" max="5" :value="old('clinic_rating', '5.0')"
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
                                type="text" :value="old('whatsapp_number')" placeholder="+91 9971000000"
                                errorField="whatsapp_number" />

                            <x-input-field class="col-md-6" label="Notification Email" name="notification_email" id="notification_email"
                                type="email" :value="old('notification_email')" placeholder="clinic@example.com"
                                errorField="notification_email" />

                            <div class="col-12"><hr class="my-1"><small class="text-muted fw-semibold">SMTP Configuration <span class="text-muted fw-normal">(optional — for email notifications)</span></small></div>

                            <x-input-field class="col-md-4" label="SMTP Host" name="smtp_host" id="smtp_host"
                                type="text" :value="old('smtp_host')" placeholder="smtp.gmail.com" errorField="smtp_host" />

                            <x-input-field class="col-md-2" label="SMTP Port" name="smtp_port" id="smtp_port"
                                type="text" :value="old('smtp_port')" placeholder="587" errorField="smtp_port" />

                            <x-select-field class="col-md-2" label="Encryption" name="smtp_encryption" id="smtp_encryption"
                                :options="[['id'=>'tls','label'=>'TLS'],['id'=>'ssl','label'=>'SSL'],['id'=>'none','label'=>'None']]"
                                :value="old('smtp_encryption','tls')" errorField="smtp_encryption" />

                            <x-input-field class="col-md-4" label="SMTP Username / Email" name="smtp_username" id="smtp_username"
                                type="text" :value="old('smtp_username')" placeholder="you@gmail.com" errorField="smtp_username" />

                            <x-input-field class="col-md-4" label="SMTP Password / App Password" name="smtp_password" id="smtp_password"
                                type="password" :value="old('smtp_password')" placeholder="••••••••" errorField="smtp_password" />

                            <x-input-field class="col-md-4" label="Sender From Address" name="smtp_from_address" id="smtp_from_address"
                                type="email" :value="old('smtp_from_address')" placeholder="noreply@clinic.com" errorField="smtp_from_address" />

                            <x-input-field class="col-md-4" label="Sender From Name" name="smtp_from_name" id="smtp_from_name"
                                type="text" :value="old('smtp_from_name')" placeholder="Revival Healthcare" errorField="smtp_from_name" />
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
                                <input type="file" name="logo" id="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                                @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hero_media" class="form-label">
                                    <i class="fa-solid fa-photo-film text-primary me-1"></i>
                                    Hero Background Media <small class="text-muted">(Image or Video: MP4, WEBM, JPG, PNG)</small>
                                </label>
                                <input type="file" name="hero_media" id="hero_media" accept="image/*,video/*" class="form-control @error('hero_media') is-invalid @enderror">
                                <small class="text-muted">Dynamic background for the top hero banner. Large video/image allowed.</small>
                                @error('hero_media')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="hero_overlay_opacity" class="form-label">
                                    Hero Dark Overlay Opacity <small class="text-muted">(0.0 to 1.0)</small>
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <input type="range" class="form-range flex-grow-1" min="0" max="1" step="0.05" id="hero_overlay_range" value="{{ old('hero_overlay_opacity', '0.65') }}">
                                    <input type="number" name="hero_overlay_opacity" id="hero_overlay_opacity" class="form-control form-control-sm" style="width: 80px;" min="0" max="1" step="0.05" value="{{ old('hero_overlay_opacity', '0.65') }}">
                                </div>
                                <small class="text-muted">Ensures text is 100% crystal clear and readable over any bright video or image.</small>
                            </div>
                            <div class="col-md-6" id="heroMediaPreviewBox" style="display: none;">
                                <label class="form-label">Selected Media Preview</label>
                                <div id="heroMediaPreviewContainer" class="rounded border p-1 bg-light" style="max-height: 120px; overflow: hidden;"></div>
                            </div>


                            <div class="col-12">
                                <label for="about_clinic" class="form-label">About Clinic <small class="text-muted">(Rich Text)</small></label>
                                <textarea name="about_clinic" id="about_clinic" class="form-control ckeditor @error('about_clinic') is-invalid @enderror" rows="5">{{ old('about_clinic') }}</textarea>
                                @error('about_clinic')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <x-input-field class="col-md-6" label="Timings" name="timings" id="timings"
                                type="text" :value="old('timings')" placeholder="Mon–Sat: 9:00 AM – 7:00 PM"
                                errorField="timings" />

                            <x-text-area-field divClass="col-md-6" label="Address" name="address" id="address"
                                rows="2" :value="old('address')" />

                            <x-text-area-field divClass="col-12" label="Services (one per line)" name="services" id="services"
                                rows="4" :value="old('services')" />
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
                                <input type="time" name="booking_start_time" id="booking_start_time"
                                    class="form-control" value="{{ old('booking_start_time', '09:00') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">End Time</label>
                                <input type="time" name="booking_end_time" id="booking_end_time"
                                    class="form-control" value="{{ old('booking_end_time', '20:00') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Slot Interval (minutes)</label>
                                <input type="number" name="slot_interval_minutes" id="slot_interval_minutes"
                                    class="form-control" min="5" max="120" step="5"
                                    value="{{ old('slot_interval_minutes', 15) }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" id="generateSlotsBtn">
                                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Generate Slots
                                </button>
                            </div>
                            <div class="col-12">
                                <label for="booking_slots" class="form-label">Booking Time Slots <small class="text-muted">(one per line, editable)</small></label>
                                <textarea name="booking_slots" id="booking_slots" class="form-control" rows="6"
                                    placeholder="09:00 AM&#10;09:15 AM&#10;09:30 AM">{{ old('booking_slots') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 5: Patient Stories ── --}}
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
                            <p class="text-muted small mb-0" id="noTestimonialsMsg">No stories added yet. Click "Add Story" to add patient testimonials.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Section 6: Gallery Images ── --}}
            <div class="col-12 mb-3">
                <div class="card no-scale">
                    <div class="card-header"><h6 class="mb-0"><i class="fa-solid fa-images me-2 text-primary"></i>Clinic Gallery Images</h6></div>
                    <div class="card-body">
                        <div class="row g-2 mb-2" id="galleryPreviewContainer"></div>
                        <label for="gallery_images" class="form-label">Upload Gallery Images <small class="text-muted">(PNG/JPG, max 3MB each)</small></label>
                        <input type="file" name="gallery_images[]" id="gallery_images" multiple accept="image/*"
                            class="form-control @error('gallery_images.*') is-invalid @enderror">
                        @error('gallery_images.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                            <p class="text-muted small mb-0" id="noDoctorsMsg">No doctors added yet. Click "Add Doctor" to add doctor profiles.</p>
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
                    <x-button type="submit" class="btn btn-primary" buttons="{{ __('buttons.create') }}" />
                </div>
            </div>

        </form>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    // ── Clinic data map (from PHP) ────────────────────────────────────────────
    const CLINICS = @json($clinicsJson ? json_decode($clinicsJson, true) : []);

    // ── Clinic dropdown auto-fill ─────────────────────────────────────────────
    const clinicSelect   = document.getElementById('clinic_select');
    const clinicNameInput = document.getElementById('clinic_name');
    const slugInput      = document.getElementById('slug');
    const addressInput   = document.getElementById('address');
    const emailInput     = document.getElementById('notification_email');
    let slugManuallyEdited = false;

    // Auto-fill when clinic selected
    if (clinicSelect) {
        $(clinicSelect).on('change', function () {
            const id = this.value;
            if (!id || !CLINICS[id]) return;

            const c = CLINICS[id];

            // Fill Clinic Name
            if (clinicNameInput) clinicNameInput.value = c.name || '';

            // Auto-generate slug if not manually edited
            if (!slugManuallyEdited && c.name) {
                slugInput.value = c.name.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
            }

            // Fill address
            if (addressInput && c.address) addressInput.value = c.address;

            // Fill notification email
            if (emailInput && c.email) emailInput.value = c.email;
        });
    }

    // Slug manual edit detection
    if (slugInput) {
        slugInput.addEventListener('input', () => { slugManuallyEdited = true; });
    }

    // Slug auto-generation from typed clinic name (when no clinic selected)
    if (clinicNameInput) {
        clinicNameInput.addEventListener('input', function () {
            if (!slugManuallyEdited) {
                slugInput.value = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-');
            }
        });
    }

    // ── Slot generator ────────────────────────────────────────────────────────
    document.getElementById('generateSlotsBtn').addEventListener('click', function () {
        const start    = document.getElementById('booking_start_time').value;
        const end      = document.getElementById('booking_end_time').value;
        const interval = parseInt(document.getElementById('slot_interval_minutes').value, 10);

        if (!start || !end || !interval) {
            alert('Please fill Start Time, End Time and Interval before generating slots.');
            return;
        }

        const slots   = [];
        let [sh, sm]  = start.split(':').map(Number);
        let [eh, em]  = end.split(':').map(Number);
        let startMins = sh * 60 + sm;
        let endMins   = eh * 60 + em;

        while (startMins < endMins) {
            const h    = Math.floor(startMins / 60);
            const m    = startMins % 60;
            const ampm = h < 12 ? 'AM' : 'PM';
            const hh   = h % 12 || 12;
            const mm   = String(m).padStart(2, '0');
            slots.push(`${String(hh).padStart(2, '0')}:${mm} ${ampm}`);
            startMins += interval;
        }

        document.getElementById('booking_slots').value = slots.join('\n');
    });

    // ── Gallery preview ───────────────────────────────────────────────────────
    document.getElementById('gallery_images').addEventListener('change', function () {
        const container = document.getElementById('galleryPreviewContainer');
        container.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const reader = new FileReader();
            const col    = document.createElement('div');
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

    // ── Testimonials repeater ─────────────────────────────────────────────────
    let testimonialCount = 0;

    document.getElementById('addTestimonialBtn').addEventListener('click', function () {
        const noMsg = document.getElementById('noTestimonialsMsg');
        if (noMsg) noMsg.style.display = 'none';
        const i = testimonialCount++;
        const card = document.createElement('div');
        card.className = 'border rounded p-3 mb-3 position-relative';
        card.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-btn">
                <i class="fa-solid fa-trash"></i></button>
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label required">Patient Name</label>
                    <input type="text" name="testimonials[${i}][patient_name]" class="form-control" required placeholder="e.g. Rahul Sharma">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rating (1–5)</label>
                    <input type="number" name="testimonials[${i}][rating]" class="form-control" min="1" max="5" value="5">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Story</label>
                    <textarea name="testimonials[${i}][story]" class="form-control" rows="3" placeholder="Patient's experience..."></textarea>
                </div>
            </div>`;
        card.querySelector('.remove-btn').addEventListener('click', function () {
            card.remove();
            if (!document.querySelectorAll('#testimonialsContainer .border').length) {
                if (noMsg) noMsg.style.display = '';
            }
        });
        document.getElementById('testimonialsContainer').appendChild(card);
    });

    // ── Doctors repeater ──────────────────────────────────────────────────────
    let doctorCount = 0;

    document.getElementById('addDoctorBtn').addEventListener('click', function () {
        const noMsg = document.getElementById('noDoctorsMsg');
        if (noMsg) noMsg.style.display = 'none';
        const i = doctorCount++;
        const card = document.createElement('div');
        card.className = 'border rounded p-3 mb-3 position-relative';
        card.innerHTML = `
            <button type="button" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-btn">
                <i class="fa-solid fa-trash"></i></button>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label required">Doctor Name</label>
                    <input type="text" name="doctors[${i}][doctor_name]" class="form-control" required placeholder="Dr. Amit Kumar">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email (Login)</label>
                    <input type="email" name="doctors[${i}][email]" class="form-control" placeholder="doctor@clinic.com">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="doctors[${i}][specialization]" class="form-control" placeholder="General Physician">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Consultation Fee</label>
                    <input type="text" name="doctors[${i}][consultation_fee]" class="form-control" placeholder="₹500">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Experience</label>
                    <input type="text" name="doctors[${i}][experience]" class="form-control" placeholder="5 Years">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Doctor Photo <small class="text-muted">(PNG/JPG, max 2MB)</small></label>
                    <input type="file" name="doctors[${i}][photo]" class="form-control" accept="image/*">
                </div>
            </div>`;
        card.querySelector('.remove-btn').addEventListener('click', function () {
            card.remove();
            if (!document.querySelectorAll('#doctorsContainer .border').length) {
                if (noMsg) noMsg.style.display = '';
            }
        });
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
