// InternHub Service Worker
const CACHE_NAME = 'internhub-v1';
const OFFLINE_URL = '/offline.html';

// Assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    // Add more static assets as needed
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('[SW] Service Worker installed successfully');
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error('[SW] Failed to cache assets:', error);
            })
    );
});

// Activate event - cleanup old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Service Worker...');
    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName !== CACHE_NAME)
                        .map((cacheName) => {
                            console.log('[SW] Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('[SW] Service Worker activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - Network first, fallback to cache
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Skip external requests
    if (url.origin !== location.origin) {
        return;
    }

    // Skip API requests and form submissions
    if (url.pathname.startsWith('/api/') ||
        url.pathname.startsWith('/livewire/') ||
        url.pathname.includes('/login') ||
        url.pathname.includes('/logout')) {
        return;
    }

    event.respondWith(
        // Network first strategy
        fetch(request)
            .then((networkResponse) => {
                // Clone the response before caching
                const responseClone = networkResponse.clone();

                // Cache successful responses
                if (networkResponse.ok) {
                    caches.open(CACHE_NAME)
                        .then((cache) => {
                            cache.put(request, responseClone);
                        });
                }

                return networkResponse;
            })
            .catch(async () => {
                // Network failed, try cache
                const cachedResponse = await caches.match(request);

                if (cachedResponse) {
                    return cachedResponse;
                }

                // If it's a navigation request, show offline page
                if (request.mode === 'navigate') {
                    const offlineResponse = await caches.match(OFFLINE_URL);
                    if (offlineResponse) {
                        return offlineResponse;
                    }
                }

                // Return a basic offline response for other requests
                return new Response('Offline - Content not available', {
                    status: 503,
                    statusText: 'Service Unavailable',
                    headers: new Headers({
                        'Content-Type': 'text/plain'
                    })
                });
            })
    );
});

// Background Sync for offline form submissions (future enhancement)
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync triggered:', event.tag);

    if (event.tag === 'sync-attendance') {
        event.waitUntil(syncAttendance());
    }
});

// Placeholder for syncing attendance when back online
async function syncAttendance() {
    // This can be implemented later for offline check-in/check-out
    console.log('[SW] Syncing attendance data...');
}

// Push notification handler (future enhancement)
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');

    if (event.data) {
        const data = event.data.json();

        event.waitUntil(
            self.registration.showNotification(data.title || 'InternHub', {
                body: data.body || 'Anda memiliki notifikasi baru',
                icon: '/icons/icon-192x192.png',
                badge: '/icons/icon-72x72.png',
                vibrate: [100, 50, 100],
                data: {
                    url: data.url || '/dashboard'
                },
                actions: [
                    { action: 'open', title: 'Buka' },
                    { action: 'close', title: 'Tutup' }
                ]
            })
        );
    }
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    event.notification.close();

    if (event.action === 'close') {
        return;
    }

    const urlToOpen = event.notification.data?.url || '/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                // Check if there's already a window open
                for (const client of windowClients) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        client.navigate(urlToOpen);
                        return client.focus();
                    }
                }
                // Open new window if none exists
                return clients.openWindow(urlToOpen);
            })
    );
});

console.log('[SW] Service Worker script loaded');
