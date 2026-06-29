<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/scss/custom.scss', 'resources/js/custom.js'])
</head>
<body>
     <div id="app">
        <main>
            @auth
            @include('layouts.partials.sidebar')
            <div id="layoutSidenav_content" class="main-content">
                @include('layouts.partials.header')
                <div class="main-inner-content">
                    <div class="container-fluid">
                        <div class="row">
                            {{-- Sub-sidebar (only if section is defined) --}}
                            @hasSection('sub-sidebar')
                            <div class="col-md-3 col-lg-2">
                                @yield('sub-sidebar')
                            </div>
                            <div class="col-md-9 col-lg-10">
                                @yield('content')
                            </div>
                            @else
                            <div class="col-12">
                                @yield('content')
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            @endauth
            @guest
            <div class="main-inner-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
            @endguest
        </main>
    </div>
    @stack('scripts')
</body>
</html>
