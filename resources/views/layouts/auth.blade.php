<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Auth') | {{ config('app.name', 'Laravel') }}</title>
     @viteReactRefresh
    @vite(['resources/scss/custom.scss', 'resources/js/custom.js'])
</head>
<body>
    <div id="app">
        <main class="py-0">
            @yield('content')
            @yield('scripts')
        </main>
    </div>
</body>
</html>
