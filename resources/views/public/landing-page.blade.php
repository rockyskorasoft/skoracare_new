<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $landingPage->clinic_name }}</title>

    {{-- Bootstrap 5 (grid + utilities only) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Google Fonts: Fraunces (display serif, carries the clinic's warmth) + Inter (body) --}}
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500;1,9..144,600&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* ── Core palette — deep clinical teal + warm gold, not the default SaaS blue ── */
            --sp-ink:          #172723;
            --sp-teal-900:     #0b332c;
            --sp-teal-800:     #0f4a40;
            --sp-teal-700:     #14604f;
            --sp-teal-500:     #1f8a72;
            --sp-teal-100:     #e6f2ee;
            --sp-gold-600:     #b3873f;
            --sp-gold-500:     #c79a52;
            --sp-gold-100:     #f6ecd8;
            --sp-coral-600:    #d15b3f;
            --sp-coral-100:    #fbe7e0;
            --sp-cream:        #faf7f1;
            --sp-white:        #ffffff;
            --sp-muted:        #5f7069;
            --sp-border:       #e6ddcd;

            --sp-font-display: 'Fraunces', Georgia, serif;
            --sp-font-body:    'Inter', sans-serif;
        }

        * { box-sizing: border-box; }

        body {
            font-family: var(--sp-font-body);
            color: var(--sp-ink);
            background: var(--sp-cream);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, .sp-serif { font-family: var(--sp-font-display); }

        a { color: var(--sp-teal-700); }

        :focus-visible {
            outline: 2.5px solid var(--sp-teal-500);
            outline-offset: 3px;
            border-radius: 4px;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }

        /* ── Navbar ─────────────────────────────────────────── */
        .lp-navbar {
            background: var(--sp-cream);
            border-bottom: 1px solid var(--sp-border);
            padding: 14px 0;
            position: sticky; top: 0; z-index: 999;
        }
        .lp-navbar .logo-img { max-height: 50px; }
        .lp-navbar .clinic-title {
            font-family: var(--sp-font-display);
            font-style: italic;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--sp-teal-900);
        }
        .lp-navbar .nav-cta {
            background: var(--sp-teal-800);
            color: var(--sp-white);
            border-radius: 999px;
            padding: 10px 24px;
            font-weight: 600;
            font-size: .92rem;
            text-decoration: none;
            transition: background .2s ease, transform .2s ease;
        }
        .lp-navbar .nav-cta:hover { background: var(--sp-teal-900); transform: translateY(-1px); }

        /* ── Hero ───────────────────────────────────────────── */
        .lp-hero {
            background:
                radial-gradient(60% 90% at 88% 10%, rgba(199,154,82,.20), transparent 60%),
                linear-gradient(160deg, var(--sp-teal-900) 0%, var(--sp-teal-800) 55%, var(--sp-teal-700) 100%);
            color: var(--sp-white);
            padding: 96px 0 110px;
            position: relative; overflow: hidden;
            z-index: 1;
        }
        .lp-hero-media-wrapper {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -2;
            pointer-events: none;
        }
        .lp-hero-media-wrapper img,
        .lp-hero-media-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .lp-hero-overlay {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(13, 43, 36, 0.92) 0%, rgba(17, 63, 52, 0.85) 60%, rgba(13, 43, 36, 0.90) 100%);
            z-index: -1;
            pointer-events: none;
        }
        .lp-hero::after {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.035'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
            z-index: -1;
        }
        .lp-hero .hero-inner {
            opacity: 0; transform: translateY(14px);
            animation: sp-hero-in .7s ease-out .1s forwards;
            position: relative;
            z-index: 2;
        }
        @keyframes sp-hero-in { to { opacity: 1; transform: translateY(0); } }

        .lp-hero .hero-badge {
            background: rgba(199,154,82,.18);
            border: 1px solid rgba(199,154,82,.45);
            color: var(--sp-gold-100);
            border-radius: 999px;
            padding: 7px 18px; font-size: .85rem; font-weight: 500;
            display: inline-flex; align-items: center; gap: 8px;
            margin-bottom: 22px;
        }
        .lp-hero h1 {
            font-size: 3.1rem; font-weight: 600; line-height: 1.15;
            letter-spacing: -.01em;
            max-width: 14ch;
        }
        .lp-hero .hero-lede {
            font-size: 1.15rem; line-height: 1.7; opacity: .88;
            max-width: 52ch; font-weight: 400;
        }
        .lp-hero .hero-meta {
            display: flex; flex-wrap: wrap; gap: 10px; margin-top: 28px;
        }
        .lp-hero .hero-meta span {
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 999px;
            padding: 8px 16px; font-size: .85rem;
            display: inline-flex; align-items: center; gap: 7px;
        }
        .lp-hero .hero-cta {
            background: var(--sp-gold-500);
            color: var(--sp-teal-900);
            border: none;
            border-radius: 999px;
            padding: 15px 34px;
            font-weight: 700; font-size: 1rem;
            display: inline-flex; align-items: center; gap: 10px;
            text-decoration: none;
            transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
            box-shadow: 0 14px 30px rgba(0,0,0,.2);
        }
        .lp-hero .hero-cta:hover {
            background: var(--sp-gold-600); transform: translateY(-2px);
            box-shadow: 0 18px 38px rgba(0,0,0,.28);
        }

        /* ── Section headings ───────────────────────────────── */
        .section-eyebrow {
            font-family: var(--sp-font-display);
            font-style: italic;
            color: var(--sp-teal-600, var(--sp-teal-700));
            font-size: 1.05rem;
            margin-bottom: 6px;
        }
        .section-heading {
            font-size: 2.1rem; font-weight: 600; color: var(--sp-ink);
            line-height: 1.2;
        }
        .section-heading span { color: var(--sp-teal-700); font-style: italic; }
        .section-divider {
            width: 46px; height: 3px; background: var(--sp-gold-500);
            border-radius: 2px; margin: 16px 0 32px;
        }

        /* ── About ──────────────────────────────────────────── */
        .lp-about { padding: 84px 0; background: var(--sp-white); }
        .about-content { line-height: 1.9; color: #384843; font-size: 1.04rem; }
        .about-content::first-letter {
            font-family: var(--sp-font-display);
            font-size: 3.4rem; font-weight: 600; color: var(--sp-teal-700);
            float: left; line-height: .85; padding-right: 10px; padding-top: 4px;
        }
        .about-address {
            margin-top: 28px; padding: 16px 18px; border-radius: 10px;
            background: var(--sp-teal-100); border-left: 3px solid var(--sp-teal-600, var(--sp-teal-700));
            color: var(--sp-teal-900); font-size: .95rem;
        }
        .about-sidecard {
            background: var(--sp-white);
            border: 1px solid var(--sp-border);
            border-radius: 16px;
            padding: 30px 26px;
            box-shadow: 0 18px 40px rgba(15,74,64,.07);
        }
        .about-sidecard .side-row {
            display: flex; gap: 14px; align-items: flex-start;
            padding: 14px 0; border-bottom: 1px solid var(--sp-border);
        }
        .about-sidecard .side-row:last-child { border-bottom: none; padding-bottom: 0; }
        .about-sidecard .side-row:first-child { padding-top: 0; }
        .about-sidecard .side-icon {
            width: 38px; height: 38px; min-width: 38px; border-radius: 50%;
            background: var(--sp-gold-100); color: var(--sp-gold-600);
            display: flex; align-items: center; justify-content: center; font-size: .95rem;
        }
        .about-sidecard .side-icon.wa { background: #dcf5e6; color: #1f9e50; }
        .about-sidecard .side-label { font-size: .82rem; color: var(--sp-muted); font-weight: 500; }
        .about-sidecard .side-value { font-size: .96rem; font-weight: 600; color: var(--sp-ink); }

        /* ── Services ───────────────────────────────────────── */
        .lp-services { padding: 84px 0; background: var(--sp-teal-100); }
        .service-card {
            background: var(--sp-white);
            border: 1px solid var(--sp-border);
            border-top: 3px solid var(--sp-gold-500);
            border-radius: 12px;
            padding: 22px 24px;
            display: flex; align-items: flex-start; gap: 16px;
            height: 100%;
            box-shadow: 0 4px 16px rgba(15,74,64,.04);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }
        .service-card:hover {
            box-shadow: 0 16px 36px rgba(15,74,64,.12);
            transform: translateY(-4px);
            border-color: var(--sp-teal-600, var(--sp-teal-700));
        }
        .service-card-icon {
            width: 42px; height: 42px; min-width: 42px; border-radius: 10px;
            background: var(--sp-teal-100); color: var(--sp-teal-700);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .service-card-title {
            font-weight: 600; font-size: 1.05rem; color: var(--sp-ink);
            line-height: 1.4; margin-bottom: 0;
        }

        /* ── Doctors ────────────────────────────────────────── */
        .lp-doctors { padding: 84px 0; background: var(--sp-white); }
        .doctor-card {
            background: var(--sp-white); border-radius: 16px; overflow: hidden;
            border: 1px solid var(--sp-border);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 44px rgba(15,74,64,.14);
            border-color: transparent;
        }
        .doctor-card .doc-photo { height: 230px; object-fit: cover; width: 100%; }
        .doctor-card .doc-placeholder {
            height: 230px; background: var(--sp-teal-100);
            display: flex; align-items: center; justify-content: center;
        }
        .doctor-card .doc-info { padding: 22px; }
        .doctor-card .doc-name {
            font-family: var(--sp-font-display);
            font-size: 1.15rem; font-weight: 600; margin-bottom: 6px;
        }
        .doctor-card .doc-spec {
            display: inline-block;
            background: var(--sp-gold-100); color: var(--sp-gold-600);
            border-radius: 999px; padding: 3px 12px;
            font-size: .78rem; font-weight: 600; margin-bottom: 14px;
        }
        .doctor-card .doc-meta span {
            font-size: .83rem; color: var(--sp-muted);
            display: inline-flex; align-items: center; gap: 5px; margin-right: 12px;
        }

        /* ── Testimonials ───────────────────────────────────── */
        .lp-testimonials { padding: 84px 0; background: var(--sp-teal-100); }
        .testimonial-card {
            background: var(--sp-white); border-radius: 12px;
            border-left: 3px solid var(--sp-gold-500);
            padding: 28px; box-shadow: 0 8px 24px rgba(15,74,64,.06); height: 100%;
        }
        .testimonial-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--sp-teal-800); color: var(--sp-white);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .95rem;
        }
        .testimonial-name {
            font-family: var(--sp-font-display); font-style: italic; font-weight: 600;
        }
        .star-rating { color: var(--sp-gold-600); font-size: .95rem; letter-spacing: 1px; }
        .testimonial-story { color: #45524d; line-height: 1.75; font-size: .95rem; }

        /* ── Gallery ────────────────────────────────────────── */
        .lp-gallery { padding: 84px 0; background: var(--sp-white); }
        .gallery-thumb {
            border-radius: 12px; overflow: hidden; height: 190px; cursor: pointer;
            position: relative;
        }
        .gallery-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; }
        .gallery-thumb::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(180deg, transparent 60%, rgba(11,51,44,.35) 100%);
            opacity: 0; transition: opacity .3s ease;
        }
        .gallery-thumb:hover img { transform: scale(1.07); }
        .gallery-thumb:hover::after { opacity: 1; }

        /* ── Booking Form ───────────────────────────────────── */
        .lp-booking {
            padding: 88px 0;
            background: linear-gradient(160deg, var(--sp-teal-900), var(--sp-teal-700));
        }
        .booking-card {
            background: var(--sp-white); border-radius: 20px; padding: 44px;
            box-shadow: 0 30px 70px rgba(0,0,0,.25);
        }
        .booking-card .form-label { color: var(--sp-ink); }
        .booking-card .form-control, .booking-card .form-select {
            border-radius: 8px; border: 1.5px solid var(--sp-border); padding: 11px 14px;
        }
        .booking-card .form-control:focus, .booking-card .form-select:focus {
            border-color: var(--sp-teal-600, var(--sp-teal-700));
            box-shadow: 0 0 0 3px rgba(31,138,114,.15);
        }
        .booking-card .btn-book {
            background: var(--sp-gold-500); color: var(--sp-teal-900);
            border-radius: 10px; padding: 13px 32px; font-weight: 700; font-size: 1rem;
            border: none; width: 100%; transition: background .2s ease, transform .2s ease;
        }
        .booking-card .btn-book:hover { background: var(--sp-gold-600); transform: translateY(-1px); }
        .lp-booking .booking-heading {
            color: var(--sp-white); font-weight: 600; font-size: 2rem;
        }
        .lp-booking .booking-sub { color: rgba(255,255,255,.75); }

        /* ── Footer ─────────────────────────────────────────── */
        .lp-footer {
            background: var(--sp-teal-900); color: rgba(255,255,255,.65);
            padding: 36px 0; text-align: center;
            border-top: 1px solid rgba(199,154,82,.35);
        }
        .lp-footer a { color: var(--sp-gold-500); text-decoration: none; }

        /* ── WhatsApp Float ───────────────────────────────────── */
        .whatsapp-float { position: fixed; bottom: 24px; right: 24px; z-index: 9999; }
        .whatsapp-float a {
            background: #25d366; color: #fff; border-radius: 50%;
            width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; box-shadow: 0 6px 20px rgba(37,211,102,.45); transition: transform .2s ease;
        }
        .whatsapp-float a:hover { transform: scale(1.08); }

        @media (max-width: 767px) {
            .lp-hero h1 { font-size: 2.2rem; max-width: none; }
            .lp-hero { padding: 64px 0 76px; }
            .about-content::first-letter { font-size: 2.6rem; }
        }
    </style>
</head>
<body>

{{-- ── Navbar ─────────────────────────────────────────────────────────────────────── --}}
<nav class="lp-navbar">
    <div class="container d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            @if($landingPage->logo)
                <img src="{{ asset('landing-page-logos/' . $landingPage->logo) }}" alt="Logo" class="logo-img">
            @else
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                    style="width:46px;height:46px;font-size:1.1rem;background:var(--sp-teal-800);">
                    {{ strtoupper(substr($landingPage->clinic_name, 0, 1)) }}
                </div>
            @endif
            <span class="clinic-title">{{ $landingPage->clinic_name }}</span>
        </div>
        @if($landingPage->is_appointment_enabled)
        <a href="#booking-section" class="nav-cta d-none d-md-inline-block">Book Appointment</a>
        @endif
    </div>
</nav>

{{-- ── Hero Section ──────────────────────────────────────────────────────────────── --}}
<section class="lp-hero">
    @if(!empty($landingPage->hero_media))
        <div class="lp-hero-media-wrapper">
            @if($landingPage->hero_media_type === 'video')
                <video src="{{ asset('landing-page-hero/' . $landingPage->hero_media) }}"
                    autoplay loop muted playsinline preload="auto"></video>
            @else
                <img src="{{ asset('landing-page-hero/' . $landingPage->hero_media) }}"
                    alt="{{ $landingPage->clinic_name }}">
            @endif
        </div>
        {{-- Configurable Dark Overlay so text is 100% crystal clear --}}
        @php
            $overlayOpacity = floatval($landingPage->hero_overlay_opacity ?? 0.65);
            if ($overlayOpacity <= 0) $overlayOpacity = 0.65;
        @endphp
        <div class="lp-hero-overlay" style="background: rgba(11, 38, 32, {{ $overlayOpacity }});"></div>
    @endif

    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8 hero-inner">
                <div class="hero-badge"><i class="fa-solid fa-shield-halved"></i>Trusted Healthcare Provider</div>
                <h1>{{ $landingPage->clinic_name }}</h1>
                @if($landingPage->about_clinic)
                    <p class="hero-lede mt-3 mb-4">
                        {{ strip_tags(\Illuminate\Support\Str::limit($landingPage->about_clinic, 200)) }}
                    </p>
                @endif
                <div class="hero-meta">
                    @if($landingPage->timings)
                    <span><i class="fa-regular fa-clock"></i> {{ $landingPage->timings }}</span>
                    @endif
                    @if($landingPage->clinic_rating)
                    <span><i class="fa-solid fa-star" style="color:var(--sp-gold-500);"></i> {{ number_format($landingPage->clinic_rating, 1) }} Rating</span>
                    @endif
                    @if($landingPage->whatsapp_number)
                    <span><i class="fa-brands fa-whatsapp"></i> {{ $landingPage->whatsapp_number }}</span>
                    @endif
                </div>
                @if($landingPage->is_appointment_enabled)
                <div class="mt-5">
                    <a href="#booking-section" class="hero-cta">
                        <i class="fa-regular fa-calendar-check"></i>Book Appointment
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ── About ──────────────────────────────────────────────────────────────────────── --}}
@if($landingPage->about_clinic)
<section class="lp-about" id="about-section">
    <div class="container">
        <div class="section-eyebrow">Who we are</div>
        <div class="section-heading">About <span>Us</span></div>
        <div class="section-divider"></div>
        <div class="row align-items-start g-5">
            <div class="col-lg-8">
                <div class="about-content">
                    {!! $landingPage->about_clinic !!}
                </div>
                @if($landingPage->address)
                <div class="about-address">
                    <i class="fa-solid fa-location-dot me-2"></i>
                    <strong>Address:</strong> {{ strip_tags(html_entity_decode($landingPage->address, ENT_QUOTES | ENT_HTML5, 'UTF-8')) }}
                </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="about-sidecard">
                    @if($landingPage->timings)
                    <div class="side-row">
                        <div class="side-icon"><i class="fa-regular fa-clock"></i></div>
                        <div>
                            <div class="side-label">Working Hours</div>
                            <div class="side-value">{{ strip_tags(html_entity_decode($landingPage->timings, ENT_QUOTES | ENT_HTML5, 'UTF-8')) }}</div>
                        </div>
                    </div>
                    @endif
                    @if($landingPage->whatsapp_number)
                    <div class="side-row">
                        <div class="side-icon wa"><i class="fa-brands fa-whatsapp"></i></div>
                        <div>
                            <div class="side-label">WhatsApp</div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}"
                                target="_blank" class="side-value" style="color:var(--sp-teal-700);">{{ $landingPage->whatsapp_number }}</a>
                        </div>
                    </div>
                    @endif
                    @if($landingPage->clinic_rating)
                    <div class="side-row">
                        <div class="side-icon"><i class="fa-solid fa-star"></i></div>
                        <div>
                            <div class="side-label">Rating</div>
                            <div class="side-value">{{ number_format($landingPage->clinic_rating, 1) }} / 5.0</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── Services ────────────────────────────────────────────────────────────────────── --}}
@if(count($services))
<section class="lp-services" id="services-section">
    <div class="container">
        <div class="section-eyebrow">What we offer</div>
        <div class="section-heading">Our <span>Services</span></div>
        <div class="section-divider"></div>
        <div class="row g-4 justify-content-center">
            @foreach($services as $svc)
            <div class="{{ count($services) === 1 ? 'col-md-8 col-lg-6' : (count($services) === 2 ? 'col-md-6 col-lg-5' : 'col-md-6 col-lg-4') }}">
                <div class="service-card">
                    <div class="service-card-icon">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <div>
                        <h5 class="service-card-title">{{ strip_tags(html_entity_decode($svc, ENT_QUOTES | ENT_HTML5, 'UTF-8')) }}</h5>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Doctors ──────────────────────────────────────────────────────────────────────── --}}
@if($landingPage->doctors->count())
@php
    $docCount = $landingPage->doctors->count();
    // Dynamically adjust column size based on doctor count for balanced presentation
    if ($docCount === 1) {
        $colClass = 'col-md-8 col-lg-5';
    } elseif ($docCount === 2) {
        $colClass = 'col-md-6 col-lg-5';
    } elseif ($docCount === 3) {
        $colClass = 'col-md-6 col-lg-4';
    } else {
        $colClass = 'col-md-6 col-lg-3';
    }
@endphp
<section class="lp-doctors" id="doctors-section">
    <div class="container">
        <div class="section-eyebrow">Our specialists</div>
        <div class="section-heading">Meet Our <span>Doctors</span></div>
        <div class="section-divider"></div>
        <div class="row g-4 justify-content-center">
            @foreach($landingPage->doctors as $doc)
            <div class="{{ $colClass }}">
                <div class="doctor-card h-100">
                    @if($doc->photo)
                    <img src="{{ asset('landing-page-doctors/' . $doc->photo) }}" class="doc-photo" alt="{{ $doc->doctor_name }}">
                    @else
                    <div class="doc-placeholder">
                        <i class="fa-solid fa-user-doctor fa-4x" style="color:var(--sp-teal-600, var(--sp-teal-700));opacity:.45;"></i>
                    </div>
                    @endif
                    <div class="doc-info">
                        <div class="doc-name">{{ strip_tags($doc->doctor_name) }}</div>
                        @if($doc->specialization)
                        <div class="doc-spec">{{ strip_tags($doc->specialization) }}</div>
                        @endif
                        <div class="doc-meta">
                            @if($doc->consultation_fee)
                            <span><i class="fa-solid fa-indian-rupee-sign"></i> {{ strip_tags($doc->consultation_fee) }}</span>
                            @endif
                            @if($doc->experience)
                            <span><i class="fa-solid fa-briefcase-medical"></i> {{ strip_tags($doc->experience) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Gallery ───────────────────────────────────────────────────────────────────────── --}}
@if($landingPage->gallery->count())
<section class="lp-gallery" id="gallery-section">
    <div class="container">
        <div class="section-eyebrow">A look inside</div>
        <div class="section-heading">Clinic <span>Gallery</span></div>
        <div class="section-divider"></div>
        <div class="row g-3">
            @foreach($landingPage->gallery as $img)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="gallery-thumb">
                    <img src="{{ asset('landing-page-gallery/' . $img->image) }}" alt="Gallery Image" loading="lazy">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Patient Stories ───────────────────────────────────────────────────────────────── --}}
@if($landingPage->testimonials->count())
@php
    $testCount = $landingPage->testimonials->count();
    $tColClass = $testCount === 1 ? 'col-md-8 col-lg-6' : ($testCount === 2 ? 'col-md-6 col-lg-5' : 'col-md-6 col-lg-4');
@endphp
<section class="lp-testimonials" id="testimonials-section">
    <div class="container">
        <div class="section-eyebrow">In their words</div>
        <div class="section-heading">Patient <span>Stories</span></div>
        <div class="section-divider"></div>
        <div class="row g-4 justify-content-center">
            @foreach($landingPage->testimonials as $t)
            <div class="{{ $tColClass }}">
                <div class="testimonial-card">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="testimonial-avatar">{{ strtoupper(substr(strip_tags($t->patient_name), 0, 1)) }}</div>
                        <div>
                            <span class="d-block testimonial-name">{{ strip_tags($t->patient_name) }}</span>
                            <div class="star-rating">
                                @for($s = 1; $s <= 5; $s++)
                                    {{ $s <= $t->rating ? '★' : '☆' }}
                                @endfor
                            </div>
                        </div>
                    </div>
                    <p class="mb-0 testimonial-story">
                        {!! nl2br(e(strip_tags($t->story))) !!}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ── Booking Form (only if appointment booking is enabled) ───────────────────────── --}}
@if($landingPage->is_appointment_enabled)
<section class="lp-booking" id="booking-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="booking-heading">Book an Appointment</h2>
                    <p class="booking-sub">Fill in your details and we'll confirm your slot shortly.</p>
                </div>

                @if(session('booking_success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>{{ session('booking_success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="booking-card" id="bookingForm">
                    <div id="bookingSuccessMsg" class="d-none">
                        <div class="text-center py-4">
                            <i class="fa-solid fa-circle-check fa-4x mb-3" style="color:var(--sp-teal-600, var(--sp-teal-700));"></i>
                            <h4 class="fw-bold" style="color:var(--sp-teal-700);">Appointment Booked!</h4>
                            <p class="text-muted">Thank you! We will contact you to confirm your appointment.</p>
                            <button class="btn-book" style="width:auto;padding:11px 26px;" onclick="resetBookingForm()">Book Another Appointment</button>
                        </div>
                    </div>
                    <div id="bookingFormInner">
                        <h5 class="fw-bold mb-4" style="color:var(--sp-teal-900);"><i class="fa-regular fa-calendar-check me-2" style="color:var(--sp-gold-600);"></i>Appointment Details</h5>
                        <form id="lpBookingForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Patient Name <span class="text-danger">*</span></label>
                                    <input type="text" name="patient_name" class="form-control" placeholder="Your Full Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="patient_phone" class="form-control" placeholder="+91 9876543210" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <input type="email" name="patient_email" class="form-control" placeholder="your@email.com">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Gender</label>
                                    <select name="gender" class="form-select">
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Age</label>
                                    <input type="number" name="age" class="form-control" min="0" max="150" placeholder="25">
                                </div>
                                @if($landingPage->doctors->count())
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Doctor</label>
                                    <select name="doctor_name" class="form-select">
                                        <option value="">Any Available Doctor</option>
                                        @foreach($landingPage->doctors as $doc)
                                        <option value="{{ $doc->doctor_name }}">{{ $doc->doctor_name }}{{ $doc->specialization ? ' (' . $doc->specialization . ')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Appointment Date <span class="text-danger">*</span></label>
                                    <input type="date" name="appointment_date" class="form-control"
                                        min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                                </div>
                                @if(count($slots))
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Time Slot <span class="text-danger">*</span></label>
                                    <select name="slot_time" class="form-select" required>
                                        <option value="">Select a Time Slot</option>
                                        @foreach($slots as $slot)
                                        <option value="{{ $slot }}">{{ $slot }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Preferred Time <span class="text-danger">*</span></label>
                                    <input type="text" name="slot_time" class="form-control" placeholder="e.g. 10:00 AM" required>
                                </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Message / Symptoms</label>
                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Brief description of your concern..."></textarea>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="submit" class="btn-book" id="bookBtn">
                                        <i class="fa-regular fa-calendar-check me-2"></i>Confirm Appointment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ── Footer ──────────────────────────────────────────────────────────────────────── --}}
<footer class="lp-footer">
    <div class="container">
        @if($landingPage->address)
        <p class="mb-1"><i class="fa-solid fa-location-dot me-2"></i>{{ $landingPage->address }}</p>
        @endif
        @if($landingPage->timings)
        <p class="mb-1"><i class="fa-regular fa-clock me-2"></i>{{ $landingPage->timings }}</p>
        @endif
        <p class="mb-0 mt-3" style="font-size:.82rem;">
            &copy; {{ date('Y') }} {{ $landingPage->clinic_name }} — Powered by SkoraCare
        </p>
    </div>
</footer>

{{-- ── WhatsApp Float Button ───────────────────────────────────────────────────────── --}}
@if($landingPage->whatsapp_number)
<div class="whatsapp-float">
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $landingPage->whatsapp_number) }}" target="_blank"
        title="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
</div>
@endif

{{-- ── Scripts ─────────────────────────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@if($landingPage->is_appointment_enabled)
<script>
(function () {
    'use strict';

    const form    = document.getElementById('lpBookingForm');
    const bookBtn = document.getElementById('bookBtn');
    const formInner   = document.getElementById('bookingFormInner');
    const successMsg  = document.getElementById('bookingSuccessMsg');
    const csrfToken   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        bookBtn.disabled = true;
        bookBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Booking...';

        const formData = new FormData(form);

        try {
            const resp = await fetch('{{ route("landing-page.book", $landingPage->slug) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData,
            });
            const data = await resp.json();

            if (data.success) {
                formInner.style.display = 'none';
                successMsg.classList.remove('d-none');
            } else {
                alert(data.message || 'Something went wrong. Please try again.');
                bookBtn.disabled = false;
                bookBtn.innerHTML = '<i class="fa-regular fa-calendar-check me-2"></i>Confirm Appointment';
            }
        } catch (err) {
            alert('Network error. Please try again.');
            bookBtn.disabled = false;
            bookBtn.innerHTML = '<i class="fa-regular fa-calendar-check me-2"></i>Confirm Appointment';
        }
    });

    window.resetBookingForm = function () {
        form.reset();
        formInner.style.display = '';
        successMsg.classList.add('d-none');
        bookBtn.disabled = false;
        bookBtn.innerHTML = '<i class="fa-regular fa-calendar-check me-2"></i>Confirm Appointment';
    };
})();
</script>
@endif
</body>
</html>