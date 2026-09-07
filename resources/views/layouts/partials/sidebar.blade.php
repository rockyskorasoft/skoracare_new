{{-- ============================================================
     layouts/partials/sidebar.blade.php
     Common sidebar navigation with role/permission (@can) control.
     Brand: Skoracare | Stacked icons layout
     ============================================================ --}}
<aside class="doctor-sidebar" id="drSidebar" aria-label="{{ __('labels.sidebar_navigation') }}">

    {{-- ── Navigation List (Vertical Stack) ── --}}
    <nav class="dr-sidebar-nav-container">
        <ul class="dr-sidebar-nav-list">

            {{-- 1. Dashboard (Always First for Everyone) --}}
            <li class="dr-nav-item">
                <a href="{{ route('admin.dashboard.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.dashboard.*') ? 'active' : '' }}"
                   title="{{ __('labels.dashboard') }}">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-gauge"></i>
                    </span>
                    <span class="dr-nav-label">{{ __('labels.dashboard') }}</span>
                </a>
            </li>

            {{-- 2. Users (@can guarded) --}}
            @can('user-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.users.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.users.*') ? 'active' : '' }}"
                   title="{{ __('labels.users') }}">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-users"></i>
                    </span>
                    <span class="dr-nav-label">{{ __('labels.users') }}</span>
                </a>
            </li>
            @endcan

            {{-- 3. Doctors (@can guarded) --}}
            @can('doctor-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.doctors.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.doctors.*') ? 'active' : '' }}"
                   title="{{ __('labels.doctors') }}">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-user-doctor"></i>
                    </span>
                    <span class="dr-nav-label">{{ __('labels.doctors') }}</span>
                </a>
            </li>
            @endcan

            {{-- Staff (@can guarded) --}}
            @can('staff-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.staff.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.staff.*') ? 'active' : '' }}"
                   title="Staff">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-user-nurse"></i>
                    </span>
                    <span class="dr-nav-label">Staff</span>
                </a>
            </li>
            @endcan

            {{-- 4. Roles (@can guarded) --}}
            @can('role-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.roles.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.roles.*') ? 'active' : '' }}"
                   title="{{ __('labels.roles') }}">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-users-gear"></i>
                    </span>
                    <span class="dr-nav-label">{{ __('labels.roles') }}</span>
                </a>
            </li>
            @endcan

            {{-- 5. Packages (@can guarded) --}}
            @can('package-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.packages.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.packages.*') ? 'active' : '' }}"
                   title="Packages">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-box-open"></i>
                    </span>
                    <span class="dr-nav-label">Packages</span>
                </a>
            </li>
            @endcan

            {{-- 6. Appointment --}}
            @can('appointment-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.appointments.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.appointments.*') ? 'active' : '' }}"
                   title="{{ __('labels.appointments') }}">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-regular fa-calendar-check"></i>
                    </span>
                    <span class="dr-nav-label">{{ __('labels.appointments') }}</span>
                </a>
            </li>
            @endcan

            {{-- 7. Ask Skoracare --}}
            @can('ask-skoracare-list')
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Ask Skoracare">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-regular fa-comments"></i>
                    </span>
                    <span class="dr-nav-label">Ask Skoracare</span>
                </a>
            </li>
            @endcan

            {{-- 8. OPD Billing / Clinics --}}
            @can('clinic-list')
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
            @endcan

            {{-- 9. All Patients --}}
            @can('patients-list')
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="All Patients">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-hospital-user"></i>
                    </span>
                    <span class="dr-nav-label">All Patients</span>
                </a>
            </li>
            @endcan

            {{-- 10. Follow Up --}}
            @can('follow-up-list')
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Follow Up">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </span>
                    <span class="dr-nav-label">Follow Up</span>
                </a>
            </li>
            @endcan

            {{-- 11. Pharmacy --}}
            @can('pharmacy-list')
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Pharmacy">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-pills"></i>
                    </span>
                    <span class="dr-nav-label">Pharmacy</span>
                </a>
            </li>
            @endcan

            {{-- 12. Analytics --}}
            @can('analytics-list')
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Analytics">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-chart-line"></i>
                    </span>
                    <span class="dr-nav-label">Analytics</span>
                </a>
            </li>
            @endcan

            {{-- 13. Messages --}}
            @can('messages-list')
            <li class="dr-nav-item">
                <a href="javascript:void(0);" class="dr-nav-link" title="Messages">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-regular fa-paper-plane"></i>
                    </span>
                    <span class="dr-nav-label">Messages</span>
                </a>
            </li>
            @endcan

            {{-- 14. Activity Log (@can guarded) --}}
            @can('activity-log-list')
            <li class="dr-nav-item">
                <a href="{{ route('admin.activity-log.index') }}"
                   class="dr-nav-link {{ Request::routeIs('admin.activity-log.*') ? 'active' : '' }}"
                   title="{{ __('labels.activity_log') }}">
                    <span class="dr-nav-icon-wrapper">
                        <i class="fa-solid fa-list-check"></i>
                    </span>
                    <span class="dr-nav-label">{{ __('labels.activity_log') }}</span>
                </a>
            </li>
            @endcan

        </ul>
    </nav>
</aside>
