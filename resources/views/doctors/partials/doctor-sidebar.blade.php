{{-- ============================================================
     doctors/partials/doctor-sidebar.blade.php
     Doctor Panel sidebar navigation matching the screenshot.
     Brand: Skoracare | Tatva-inspired stacked icons layout
     ============================================================ --}}
<aside class="doctor-sidebar" id="drSidebar" aria-label="{{ __('labels.sidebar_navigation') }}">

    {{-- ── Navigation List (Vertical Stack) ── --}}
    <nav class="dr-sidebar-nav-container">
        <ul class="dr-sidebar-nav-list">

            {{-- 1. Appointment (Map to Dashboard) --}}
            @if(auth()->user()->hasSidebarAccess('appointment', 'appointment-list'))
            <li class="dr-nav-item">
                <a href="{{ route('admin.doctor.dashboard') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.doctor.dashboard') ? 'active' : '' }}"
                   title="Appointment">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-regular fa-calendar-check"></i>
                    </span>
                    <span class="dr-nav-label">Appointment</span>
                </a>
            </li>
            @endif

            {{-- 2. Ask Skoracare --}}
            @if(auth()->user()->hasSidebarAccess('ask-skoracare', 'ask-skoracare-list'))
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Ask Skoracare">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-regular fa-comments"></i>
                    </span>
                    <span class="dr-nav-label">Ask Skoracare</span>
                </a>
            </li>
            @endif

            {{-- 3. OPD Billing (Map to Clinics index) --}}
            @if(auth()->user()->hasSidebarAccess('clinic', 'clinic-list'))
            <li class="dr-nav-item">
                <a href="{{ route('admin.clinics.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.clinics.*') ? 'active' : '' }}"
                   title="OPD Billing">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span class="dr-trial-badge">Trial</span>
                    </span>
                    <span class="dr-nav-label">OPD Billing</span>
                </a>
            </li>
            @endif

            {{-- 4. All Patients --}}
            @if(auth()->user()->hasSidebarAccess('patients', 'patients-list'))
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="All Patients">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-users"></i>
                    </span>
                    <span class="dr-nav-label">All Patients</span>
                </a>
            </li>
            @endif

            {{-- 5. Follow Up --}}
            @if(auth()->user()->hasSidebarAccess('follow-up', 'follow-up-list'))
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Follow Up">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-regular fa-clock"></i>
                    </span>
                    <span class="dr-nav-label">Follow Up</span>
                </a>
            </li>
            @endif

            {{-- 6. Pharmacy --}}
            @if(auth()->user()->hasSidebarAccess('pharmacy', 'pharmacy-list'))
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Pharmacy">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-store"></i>
                        <span class="dr-trial-badge">Trial</span>
                    </span>
                    <span class="dr-nav-label">Pharmacy</span>
                </a>
            </li>
            @endif

            {{-- 7. Data analytics --}}
            @if(auth()->user()->hasSidebarAccess('analytics', 'analytics-list'))
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Data analytics">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-chart-pie"></i>
                    </span>
                    <span class="dr-nav-label">Data analytics</span>
                </a>
            </li>
            @endif

            {{-- 8. Messages --}}
            @if(auth()->user()->hasSidebarAccess('messages', 'messages-list'))
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Messages">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-code-branch" style="transform: rotate(90deg);"></i>
                    </span>
                    <span class="dr-nav-label">Messages</span>
                </a>
            </li>
            @endif

        </ul>
    </nav>

    {{-- ── Sidebar Bottom Version ── --}}
    <div class="dr-sidebar-footer-version">
        <span>v2.1.17</span>
    </div>

</aside>
