"use strict";

/**
 * Service Worker for Progressive Web App (PWA)
 * Handles offline caching and network requests
 */

const CACHE_NAME = "pwa-cache-v1";
const OFFLINE_URL = "/offline";
const VITE_DEV_SERVER_PATHS = [
    "/@vite/",
    "/@id/",
    "/node_modules/",
    "5173", // Vite dev server port
];

/**
 * List of files to cache during service worker installation.
 * @type {string[]}
 */
const filesToCache = [
    "/",
    OFFLINE_URL,
    "/asset/favicon.png",
    "/asset/logo.png",
];

/**
 * Check if request should be skipped (e.g., Vite dev server).
 * @param {Request} request The request to check.
 * @returns {boolean} True if request should be skipped.
 */
function shouldSkipRequest(request) {
    const url = new URL(request.url);

    // Skip non-HTTP(S) requests
    if (!url.protocol.startsWith("http")) {
        return true;
    }

    // Skip Vite dev server requests
    if (VITE_DEV_SERVER_PATHS.some((path) => url.pathname.includes(path) || url.hostname.includes(path))) {
        return true;
    }

    // Skip external domains (CDN, API, etc.)
    if (url.hostname !== self.location.hostname && url.hostname !== "localhost" && url.hostname !== "127.0.0.1") {
        return true;
    }

    return false;
}

/**
 * Install event handler to preload files into cache.
 * @param {ExtendableEvent} event The install event.
 * @returns {void}
 */
self.addEventListener("install", function (event) {
    console.log("🔧 Service Worker: Installing...");

    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log("✅ Service Worker: Cache opened");
                return cache.addAll(filesToCache);
            })
            .then(() => {
                console.log("✅ Service Worker: Files cached");
                return self.skipWaiting();
            })
            .catch((error) => {
                console.error("❌ Service Worker: Installation failed", error);
            })
    );
});

/**
 * Activate event handler to clean up old caches.
 * @param {ExtendableEvent} event The activate event.
 * @returns {void}
 */
self.addEventListener("activate", function (event) {
    console.log("🔧 Service Worker: Activating...");

    event.waitUntil(
        caches.keys()
            .then((cacheNames) => {
                return Promise.all(
                    cacheNames
                        .filter((cacheName) => cacheName !== CACHE_NAME)
                        .map((cacheName) => {
                            console.log("🗑️ Service Worker: Deleting old cache", cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log("✅ Service Worker: Activated");
                return self.clients.claim();
            })
    );
});

/**
 * Fetch event handler to handle requests and serve from cache or network.
 * @param {FetchEvent} event The fetch event.
 * @returns {void}
 */
self.addEventListener("fetch", function (event) {
    const request = event.request;

    // Skip non-GET requests
    if (request.method !== "GET") {
        return;
    }

    // Skip requests that should be bypassed
    if (shouldSkipRequest(request)) {
        return;
    }

    event.respondWith(
        fetch(request)
            .then((response) => {
                // Clone the response for caching
                const responseToCache = response.clone();

                // Cache successful responses
                if (response.status === 200) {
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(request, responseToCache);
                    });
                }

                return response;
            })
            .catch(() => {
                // Network request failed, try to serve from cache
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // If it's a navigation request and not found in cache, show offline page
                    if (request.mode === "navigate") {
                        return caches.match(OFFLINE_URL);
                    }

                    // For non-navigation requests, return a basic response
                    return new Response("Offline", {
                        status: 503,
                        statusText: "Service Unavailable",
                        headers: new Headers({
                            "Content-Type": "text/plain",
                        }),
                    });
                });
            })
    );
});