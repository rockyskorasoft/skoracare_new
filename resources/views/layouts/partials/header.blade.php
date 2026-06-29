<nav class="navbar navbar-expand-lg crm-header px-sm-4 p-2" id="header">
    <div class="container-fluid">

        <!-- Left: Mobile Hamburger -->
        <button class="btn header-hamburger p-0 d-lg-none mobile-offcanvas" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#sidebarOffcanvas">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Left: Desktop Hamburger -->
        <button class="btn header-hamburger p-0 d-lg-block d-none" type="button">
            <i class="fas fa-bars hamburg-icon"></i>
        </button>

        <!-- Spacer -->
        <div class="flex-grow-1"></div>

        <!-- Right: Profile -->
        <!-- <div class="d-flex align-items-center gap-3 header-right-content">
            <div class="dropdown">
                <div class="header-profile-image d-flex align-items-center gap-2 cursor-pointer"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    @php
                        $user = auth()->user();
                    @endphp
                    @if (!empty($user->profile_pic))
                        <img src="{{ Storage::url('profile_images/' . $user->profile_pic) }}" alt="Profile"
                            class="rounded-circle object-fit-cover header-avatar">
                    @else
                        <img src="{{ Vite::asset(config('constants.company_logo')) }}" alt="Profile"
                            class="rounded-circle object-fit-cover header-avatar">
                    @endif
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow inline-size-2 py-0 px-2 pullDown">
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
                        <a class="dropdown-item d-flex align-items-center gap-2 fw-medium" href="{{ route('logout') }}">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>{{ __('labels.sign_out') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div> -->

    </div>
</nav>

@javascript([
'create' => __('buttons.create'),
'update' => __('buttons.update'),
'delete_modal_title' => __('labels.delete_modal_title'),
'delete_modal_text' => __('labels.delete_modal_text'),
'confirm_button_modal' => __('labels.confirm_button_modal'),
'cancel' => __('buttons.cancel'),
'error_message' => __('labels.error_message'),
'showTitle' => __('labels.Are_you'),
])