const CACHE_NAME = 'happiness-path-v1';
const urlsToCache = [
    '/',
    '/dashboard',
    '/meditate',
    '/manifest.json',
    '/css/app.css',
    '/js/app.js',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/audio/mindfulness.mp3',
    '/audio/breathing.mp3',
    '/audio/loving-kindness.mp3',
    '/audio/body-scan.mp3',
    '/audio/walking.mp3'
];

// Install event - cache resources
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
            .catch(function(error) {
                console.error('Failed to cache resources:', error);
            })
    );
});

// Fetch event - serve cached content when offline
self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Cache hit - return response
                if (response) {
                    return response;
                }

                // Clone the request
                var fetchRequest = event.request.clone();

                return fetch(fetchRequest).then(
                    function(response) {
                        // Check if valid response
                        if(!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone the response
                        var responseToCache = response.clone();

                        // Cache dynamic content for offline use
                        if (event.request.url.includes('/dashboard') || 
                            event.request.url.includes('/meditate') ||
                            event.request.url.includes('/api/')) {
                            caches.open(CACHE_NAME)
                                .then(function(cache) {
                                    cache.put(event.request, responseToCache);
                                });
                        }

                        return response;
                    }
                ).catch(function(error) {
                    // Return offline page for navigation requests
                    if (event.request.destination === 'document') {
                        return caches.match('/');
                    }
                    
                    // Return cached audio for meditation
                    if (event.request.url.includes('/audio/')) {
                        return caches.match(event.request);
                    }
                    
                    console.log('Fetch failed:', error);
                    return new Response('Offline', { 
                        status: 503, 
                        statusText: 'Service Unavailable' 
                    });
                });
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Background sync for offline actions
self.addEventListener('sync', function(event) {
    if (event.tag === 'meditation-session') {
        event.waitUntil(syncMeditationSession());
    }
});

// Push notifications
self.addEventListener('push', function(event) {
    const options = {
        body: event.data ? event.data.text() : 'Time for your daily meditation!',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'Start Meditation',
                icon: '/icons/meditation-96x96.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/icons/close-96x96.png'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification('Happiness Path', options)
    );
});

// Handle notification clicks
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/meditate')
        );
    } else if (event.action === 'close') {
        // Just close the notification
    } else {
        // Default: open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Sync meditation session when back online
function syncMeditationSession() {
    return new Promise(function(resolve, reject) {
        // Get stored session data from IndexedDB
        getStoredMeditationSessions()
            .then(function(sessions) {
                const syncPromises = sessions.map(function(session) {
                    return fetch('/api/meditation/sync', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(session)
                    });
                });
                
                return Promise.all(syncPromises);
            })
            .then(function() {
                // Clear stored sessions after successful sync
                clearStoredMeditationSessions();
                resolve();
            })
            .catch(function(error) {
                console.error('Sync failed:', error);
                reject(error);
            });
    });
}

// IndexedDB helpers for offline storage
function getStoredMeditationSessions() {
    return new Promise(function(resolve, reject) {
        const request = indexedDB.open('HappinessPathDB', 1);
        
        request.onerror = function() {
            reject('Database error');
        };
        
        request.onsuccess = function() {
            const db = request.result;
            const transaction = db.transaction(['sessions'], 'readonly');
            const store = transaction.objectStore('sessions');
            const getAllRequest = store.getAll();
            
            getAllRequest.onsuccess = function() {
                resolve(getAllRequest.result);
            };
        };
        
        request.onupgradeneeded = function() {
            const db = request.result;
            if (!db.objectStoreNames.contains('sessions')) {
                db.createObjectStore('sessions', { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

function clearStoredMeditationSessions() {
    return new Promise(function(resolve) {
        const request = indexedDB.open('HappinessPathDB', 1);
        
        request.onsuccess = function() {
            const db = request.result;
            const transaction = db.transaction(['sessions'], 'readwrite');
            const store = transaction.objectStore('sessions');
            store.clear();
            resolve();
        };
    });
}
