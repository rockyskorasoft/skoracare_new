<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas">
    <span class="position-absolute sidebar-offcanvas-close-btn translate-middle-y p-2 text-white d-lg-none"
        data-bs-dismiss="offcanvas"><i class="bi bi-x-lg"></i></span>

    <!-- ======= Sidebar Start ======== -->
    <div class="sidebar overflow-hidden" id="sidebar">
        <div class="sidebar-inner h-100 d-flex flex-column overflow-hidden">

            <!-- Sidebar Header / Logo -->
            <div class="sidebar-header px-4 pt-3 pb-2">
                <!-- mini logo (shown when collapsed) -->
                <div class="mini-logo text-center tansition-opacity d-none">
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="d-flex align-items-center justify-content-center">
                        @if (!empty($companyLogo))
                            <img src="{{ asset('storage/company_logo_images/' . $companyLogo) }}" alt="Logo"
                                class="img-fluid sidebar-mini-img" />
                        @else
                            <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="Logo"
                                class="img-fluid sidebar-mini-img" />
                        @endif
                    </a>
                </div>
                <!-- full logo -->
                <div class="full-logo tansition-opacity">
                    <a href="{{ route('admin.dashboard.index') }}"
                        class="d-flex align-items-center text-decoration-none">
                        @if (!empty($companyLogo))
                            <img src="{{ asset('storage/company_logo_images/' . $companyLogo) }}" alt="Logo"
                                class="img-fluid sidebar-full-img" />
                        @else
                            <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="Logo"
                                class="img-fluid sidebar-full-img mx-auto" />
                        @endif
                    </a>
                </div>
            </div>

            <!-- Sidebar Divider -->
            <div class="sidebar-hr mx-3"></div>

            <!-- Sidebar menus -->
            <div class="sidebar-menus pt-2 pb-2 px-2 overflow-x-hidden overflow-y-auto flex-grow-1">
                <ul class="accordion list-unstyled" id="sidebarMenusAccordian">

                    <!-- Dashboard Menu -->
                    @php
                        $isDashboardActive = Request::routeIs('admin.dashboard.*');
                    @endphp
                    <li class="accordion-item sidebar-nav-item">
                        <a class="accordion-button cursor-pointer no-arrow sidebar-nav-link
                            @if ($isDashboardActive) active @else collapsed @endif"
                            href="{{ route('admin.dashboard.index') }}"
                            title="Dashboard">
                            <span class="sidebar-icon-wrap">
                                <i class="fa-solid fa-gauge"></i>
                            </span>
                            <span class="sidebar-menus-name ms-3 tansition-opacity">{{ __('labels.dashboard') }}</span>
                        </a>
                    </li>

                    <!-- User Management Menu -->
                    {{-- User Management --}}
                    @can('user-management')
                        @php
                            $isUserManagementActive =
                                Request::routeIs('admin.roles.*') ||
                                Request::routeIs('admin.permissions.*') ||
                                Request::routeIs('admin.users.*');
                        @endphp
                        <li class="accordion-item @if ($isUserManagementActive) active @endif">
                            <a class="accordion-button cursor-pointer @if (!$isUserManagementActive) collapsed @endif"
                                data-bs-toggle="collapse" data-bs-target="#user-management"
                                aria-expanded="{{ $isUserManagementActive ? 'true' : 'false' }}">
                                <i class="fa-solid fa-users-gear"></i>
                                <span
                                    class="sidebar-menus-name ms-2 tansition-opacity">{{ __('labels.user_management') }}</span>
                            </a>

                            <div id="user-management"
                                class="accordion-collapse collapse @if ($isUserManagementActive) show @endif"
                                data-bs-parent="#sidebarMenusAccordian">
                                <div class="accordion-body py-0 px-2">
                                    <ul class="nav flex-column">
                                        @can('role-list')
                                            <li>
                                                <a href="{{ route('admin.roles.index') }}"
                                                    class="nav-link sidebar-menu-links @if (Request::routeIs('admin.roles.*')) active @endif">
                                                    <span
                                                        class="sidebar-menus-name ms-2 tansition-opacity">{{ __('labels.roles') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('user-list')
                                            <li>
                                                <a href="{{ route('admin.users.index') }}"
                                                    class="nav-link mt-1 sidebar-menu-links @if (Request::routeIs('admin.users.*')) active @endif">
                                                    <span
                                                        class="sidebar-menus-name ms-2 tansition-opacity">{{ __('labels.users') }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                        </li>
                    @endcan

                    <!-- Doctor Panel Menu -->
                    @if (auth()->user()->can('doctor-list') || auth()->user()->can('clinic-list'))
                        @php
                            $isDoctorManagementActive =
                                Request::routeIs('admin.doctors.*') ||
                                Request::routeIs('admin.clinics.*');
                        @endphp
                        <li class="accordion-item @if ($isDoctorManagementActive) active @endif">
                            <a class="accordion-button cursor-pointer @if (!$isDoctorManagementActive) collapsed @endif"
                                data-bs-toggle="collapse" data-bs-target="#doctor-management"
                                aria-expanded="{{ $isDoctorManagementActive ? 'true' : 'false' }}">
                                <i class="fa-solid fa-user-doctor"></i>
                                <span
                                    class="sidebar-menus-name ms-2 tansition-opacity">Doctor Panel</span>
                            </a>

                            <div id="doctor-management"
                                class="accordion-collapse collapse @if ($isDoctorManagementActive) show @endif"
                                data-bs-parent="#sidebarMenusAccordian">
                                <div class="accordion-body py-0 px-2">
                                    <ul class="nav flex-column">
                                        @can('doctor-list')
                                            <li>
                                                <a href="{{ route('admin.doctors.index') }}"
                                                    class="nav-link sidebar-menu-links @if (Request::routeIs('admin.doctors.*')) active @endif">
                                                    <span
                                                        class="sidebar-menus-name ms-2 tansition-opacity">Doctors</span>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('clinic-list')
                                            <li>
                                                <a href="{{ route('admin.clinics.index') }}"
                                                    class="nav-link mt-1 sidebar-menu-links @if (Request::routeIs('admin.clinics.*')) active @endif">
                                                    <span
                                                        class="sidebar-menus-name ms-2 tansition-opacity">Clinics</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </div>
                        </li>
                    @endif

                    <!-- Activity log Menu -->
                    @can('logs-management')
                        <li class="accordion-item sidebar-nav-item">
                            <a class="accordion-button cursor-pointer no-arrow sidebar-nav-link
                            @if (Request::routeIs('admin.activity-log.*')) active @else collapsed @endif"
                                href="{{ route('admin.activity-log.index') }}" title="Dashboard">
                                <span class="sidebar-icon-wrap">
                                    <i class="fa fa-history"></i>
                                </span>
                                <span
                                    class="sidebar-menus-name ms-3 tansition-opacity">{{ __('labels.activity_logs') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
            <!-- sidebar menus end -->

            <!-- Sidebar Footer / User -->
            <div class="sidebar-user-footer px-3 py-3">
                <div class="sidebar-hr mb-3"></div>
                <div class="d-flex align-items-center dropdown-toggle cursor-pointer" id="navbarDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    @php
                        $user = auth()->user();
                    @endphp
                    @if (!empty($user->profile_pic))
                        <img src="{{ Storage::url('profile_images/' . $user->profile_pic) }}" alt="User Avatar"
                            class="rounded-circle sidebar-user-avatar object-fit-cover flex-shrink-0">
                    @else
                        <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="User Avatar"
                            class="rounded-circle sidebar-user-avatar object-fit-cover flex-shrink-0">
                    @endif
                    <div class="ms-2 tansition-opacity overflow-hidden">
                        <span
                            class="d-block sidebar-username text-nowrap text-truncate">{{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) }}</span>
                        <span
                            class="d-block sidebar-user-role text-truncate">{{ $user->getRoleNames()->first() ?? 'Super Admin' }}</span>
                    </div>
                </div>

                <!-- Profile Dropdown -->
                <ul class="user-dropdown-menu dropdown-menu dropdown-menu-end shadow inline-size-2 py-0 px-2">
                    <li>
                        <a class="dropdown-item border-bottom d-flex align-items-center gap-2 fw-medium"
                            href="{{ route('admin.edit-user-profile') }}">
                            <i class="bi bi-person-circle"></i>
                            <span>{{ __('labels.profile') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item border-bottom d-flex align-items-center gap-2 fw-medium"
                            href="{{ route('admin.change-password') }}">
                            <i class="fa-solid fa-key"></i>
                            <span>{{ __('labels.change_password') }}</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 fw-medium"
                            href="{{ route('logout') }}">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>{{ __('labels.sign_out') }}</span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>
