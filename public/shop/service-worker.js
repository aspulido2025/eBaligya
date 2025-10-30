// Auto-versioned PWA Service Worker
const VERSION = 'v' + new Date().getTime();  // generates a new version on each deployment
const CACHE_NAME = `thanksdad-shop-${VERSION}`;
const OFFLINE_URL = "../auth/offline.html";

const urlsToCache = [
  "./",
  "./index.php",
  "./assets/css/main.css",
  "./assets/js/main.js",
  "./images/logo.png",
  OFFLINE_URL
];

// Install event – cache essential files
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
  self.skipWaiting(); // activate immediately
});

// Activate event – remove old caches
self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

// Fetch event – network first, fallback to cache or offline page
self.addEventListener("fetch", event => {
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Clone and store successful responses
        const resClone = response.clone();
        caches.open(CACHE_NAME).then(cache => cache.put(event.request, resClone));
        return response;
      })
      .catch(() =>
        caches.match(event.request).then(
          cached => cached || caches.match(OFFLINE_URL)
        )
      )
  );
});
