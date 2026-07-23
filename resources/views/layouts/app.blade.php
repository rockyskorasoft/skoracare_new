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

    @vite([
        'resources/scss/custom.scss',
        'resources/js/custom.js',
        'resources/css/doctor/doctorsidebar.css',
        'resources/css/doctor/doctordashboard.css',
    ])

    {{-- FontAwesome for icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>
<body class="doctor-page">

    {{-- Mobile sidebar overlay --}}
    <div class="dr-sidebar-overlay" id="drSidebarOverlay" onclick="drCloseSidebar()"></div>

    @auth
        {{-- Top Header --}}
        @include('layouts.partials.header')

        {{-- Sidebar Navigation --}}
        @include('layouts.partials.sidebar')

        {{-- Main Content Area --}}
        <div class="doctor-main-content" id="drMainContent">
            <div class="doctor-page-inner">
                @hasSection('sub-sidebar')
                    <div class="row">
                        <div class="col-md-3 col-lg-2">
                            @yield('sub-sidebar')
                        </div>
                        <div class="col-md-9 col-lg-10">
                            @yield('content')
                        </div>
                    </div>
                @else
                    @yield('content')
                @endif
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
            function drOpenSidebar() {
                var sidebar = document.getElementById('drSidebar');
                var overlay = document.getElementById('drSidebarOverlay');
                if (sidebar)  sidebar.classList.add('mobile-open');
                if (overlay)  overlay.classList.add('active');
            }

            window.drCloseSidebar = function () {
                var sidebar = document.getElementById('drSidebar');
                var overlay = document.getElementById('drSidebarOverlay');
                if (sidebar)  sidebar.classList.remove('mobile-open');
                if (overlay)  overlay.classList.remove('active');
            };

            function toggleClinicDropdown(e) {
                e.stopPropagation();
                var menu = document.getElementById('drClinicDropdownMenu');
                if (menu) {
                    menu.classList.toggle('show');
                }
            }

            document.addEventListener('click', function () {
                var menu = document.getElementById('drClinicDropdownMenu');
                if (menu) {
                    menu.classList.remove('show');
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                var hamburgerBtn = document.getElementById('drMobileHamburger');
                var dropdownBtn = document.getElementById('drClinicDropdownBtn');

                if (hamburgerBtn) hamburgerBtn.addEventListener('click', drOpenSidebar);
                if (dropdownBtn) dropdownBtn.addEventListener('click', toggleClinicDropdown);

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
