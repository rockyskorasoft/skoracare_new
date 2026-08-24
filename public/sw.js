// ── Skoracare Service Worker v1 ──────────────────────────────────────
const CACHE_NAME = 'skoracare-pwa-v1';

const STATIC_ASSETS = [
    '/manifest.json',
    '/icon-192x192.png',
    '/icon-512x512.png',
    '/apple-touch-icon.png',
    '/favicon.png'
];

// ── Install: cache static core assets safely ──────────────────────────
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            var adds = STATIC_ASSETS.map(function(url) {
                return cache.add(url).catch(function(err) {
                    console.warn('[Skoracare SW] Failed to cache:', url, err);
                });
            });
            return Promise.all(adds);
        }).then(function() {
            console.log('[Skoracare SW] Installed, skipping waiting');
            return self.skipWaiting();
        })
    );
});

// ── Activate: clean up old caches & claim clients ────────────────────
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames
                    .filter(function(name) { return name !== CACHE_NAME; })
                    .map(function(name) {
                        console.log('[Skoracare SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(function() {
            console.log('[Skoracare SW] Activated & clients claimed');
            return self.clients.claim();
        })
    );
});

// ── Fetch strategy ───────────────────────────────────────────────────
self.addEventListener('fetch', function(event) {
    var request = event.request;

    // Only handle GET requests on same origin
    if (request.method !== 'GET') return;
    if (!request.url.startsWith(self.location.origin)) return;
    if (request.url.includes('chrome-extension')) return;
    if (request.url.includes('socket.io')) return;
    if (request.url.includes('hot-update')) return; // Vite HMR

    // Navigation: Network-first, fallback to cache
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(function(response) {
                    if (response && response.status === 200) {
                        var clone = response.clone();
                        caches.open(CACHE_NAME).then(function(cache) {
                            cache.put(request, clone);
                        });
                    }
                    return response;
                })
                .catch(function() {
                    return caches.match(request).then(function(cached) {
                        return cached || caches.match('/');
                    });
                })
        );
        return;
    }

    // Static assets (CSS, JS, images, fonts): Cache-first
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then(function(cached) {
                if (cached) return cached;
                return fetch(request).then(function(response) {
                    if (response && response.status === 200) {
                        var clone = response.clone();
                        caches.open(CACHE_NAME).then(function(cache) {
                            cache.put(request, clone);
                        });
                    }
                    return response;
                }).catch(function() {
                    return new Response('', { status: 503 });
                });
            })
        );
        return;
    }

    // Default: Network-first
    event.respondWith(
        fetch(request).catch(function() {
            return caches.match(request);
        })
    );
});
