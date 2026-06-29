<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('labels.dashboard')) | {{ config('app.name', 'Skoracare') }}</title>

    {{-- Google Fonts — Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Doctor-specific isolated CSS bundles --}}
    @vite([
        'resources/scss/custom.scss',
        'resources/js/custom.js',
        'resources/css/doctor/doctorsidebar.css',
        'resources/css/doctor/doctordashboard.css',
    ])

    {{-- FontAwesome for icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="doctor-page">

    {{-- Mobile sidebar overlay --}}
    <div class="dr-sidebar-overlay" id="drSidebarOverlay" onclick="drCloseSidebar()"></div>

    @auth
        {{-- Doctor Top Header (Full Width at Top) --}}
        @include('doctors.partials.doctor-header')

        {{-- Doctor Sidebar (Below Header) --}}
        @include('doctors.partials.doctor-sidebar')

        {{-- Main Content Area --}}
        <div class="doctor-main-content" id="drMainContent">
            {{-- Page Content --}}
            <div class="doctor-page-inner">
                @yield('content')
            </div>
        </div>
    @endauth

    @guest
        <div class="doctor-page-inner">
            @yield('content')
        </div>
    @endguest

    {{-- Minimal JS for sidebar toggle & dropdowns --}}
    <script>
        (function () {
            /* Open sidebar on mobile */
            function drOpenSidebar() {
                var sidebar = document.getElementById('drSidebar');
                var overlay = document.getElementById('drSidebarOverlay');
                if (sidebar)  sidebar.classList.add('mobile-open');
                if (overlay)  overlay.classList.add('active');
            }

            /* Close sidebar on mobile */
            window.drCloseSidebar = function () {
                var sidebar = document.getElementById('drSidebar');
                var overlay = document.getElementById('drSidebarOverlay');
                if (sidebar)  sidebar.classList.remove('mobile-open');
                if (overlay)  overlay.classList.remove('active');
            };

            /* Clinic Dropdown Toggle */
            function toggleClinicDropdown(e) {
                e.stopPropagation();
                var menu = document.getElementById('drClinicDropdownMenu');
                if (menu) {
                    menu.classList.toggle('show');
                }
            }

            /* Close dropdowns on clicking elsewhere */
            document.addEventListener('click', function () {
                var menu = document.getElementById('drClinicDropdownMenu');
                if (menu) {
                    menu.classList.remove('show');
                }
            });

            /* Bind elements on DOMContentLoaded */
            document.addEventListener('DOMContentLoaded', function () {
                var hamburgerBtn = document.getElementById('drMobileHamburger');
                var dropdownBtn = document.getElementById('drClinicDropdownBtn');

                if (hamburgerBtn) hamburgerBtn.addEventListener('click', drOpenSidebar);
                if (dropdownBtn) dropdownBtn.addEventListener('click', toggleClinicDropdown);

                // Clinic item switcher (optional visual update)
                document.querySelectorAll('.dr-dropdown-item').forEach(function(item) {
                    item.addEventListener('click', function() {
                        var activeLabel = document.querySelector('.dr-clinic-active-name');
                        if (activeLabel) {
                            activeLabel.textContent = this.textContent.trim();
                        }
                    });
                });
            });
        }());
    </script>

    @stack('scripts')
</body>
</html>
