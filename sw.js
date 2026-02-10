const CACHE_NAME = 'gr-randomizer-v1';
const STATIC_ASSETS = [
    '/',
    '/index.php',
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/js/api.js',
    '/assets/js/auth.js',
    '/assets/js/components.js',
    '/assets/js/pwa.js',
    '/assets/icons/icon.svg',
    '/manifest.json',
];

const EXTERNAL_CACHE = [
    'https://cdn.tailwindcss.com',
    'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Literata:ital,wght@0,400;0,500;0,700;1,400&display=swap',
];

// Install: cache static assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll([...STATIC_ASSETS, ...EXTERNAL_CACHE]).catch(() => {
                // External assets might fail, that's ok
                return cache.addAll(STATIC_ASSETS);
            });
        })
    );
    self.skipWaiting();
});

// Activate: clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch: cache-first for static, network-only for API
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);

    // Network-only for API calls
    if (url.pathname.includes('api.php')) {
        event.respondWith(fetch(event.request));
        return;
    }

    // Cache-first for everything else
    event.respondWith(
        caches.match(event.request).then((cached) => {
            if (cached) return cached;

            return fetch(event.request).then((response) => {
                // Cache successful GET responses
                if (response.ok && event.request.method === 'GET') {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, clone);
                    });
                }
                return response;
            }).catch(() => {
                // Offline fallback
                if (event.request.mode === 'navigate') {
                    return caches.match('/');
                }
            });
        })
    );
});
