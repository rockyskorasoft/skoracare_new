{{-- PWA Meta Tags & Icons --}}
<meta name="theme-color" content="#0d6efd">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Skoracare PMS">

<link rel="manifest" href="{{ asset('manifest.json') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('icon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

{{-- Service Worker Registration --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register("{{ asset('sw.js') }}")
                .then(function(registration) {
                    console.log('[Skoracare PWA] Service Worker registered with scope:', registration.scope);
                })
                .catch(function(error) {
                    console.error('[Skoracare PWA] Service Worker registration failed:', error);
                });
        });
    }
</script>
