"use strict";

(function () {

    if (! ("serviceWorker" in navigator)) {
        console.warn("Service Worker not supported in this browser.");
        return;
    }

    /**
     * Convert a base64 key to a Uint8Array (VAPID helper).
     *
     * @param {string} base64String
     * @returns {Uint8Array}
     */
    function urlBase64ToUint8Array(base64String) {

        var padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        var base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");

        var rawData = window.atob(base64);
        var outputArray = new Uint8Array(rawData.length);

        for (var i = 0; i < rawData.length; ++i) {

            outputArray[i] = rawData.charCodeAt(i);
        }

        return outputArray;
    }

    /**
     * Get CSRF token from meta tag.
     *
     * @returns {string|null}
     */
    function getCsrfToken () {

        var meta = document.querySelector("meta[name='csrf-token']");

        return meta ? meta.getAttribute("content") : null;
    }

    /**
     * Subscribe the current user to web push notifications.
     *
     * @param {ServiceWorkerRegistration} registration
     */
    function ensurePushSubscription (registration) {

        if (! ("Notification" in window) || ! ("PushManager" in window)) {
            console.warn("Web Push not supported in this browser.");
            return;
        }

        var vapidMeta = document.querySelector("meta[name='vapid-key']");
        var vapidKey = vapidMeta ? vapidMeta.getAttribute("content") : null;

        if (! vapidKey) {
            return;
        }

        if (Notification.permission !== "granted") {
            console.warn("Service Worker: Notification permission not granted. Permission:", Notification.permission);
            return;
        }

        registration.pushManager.getSubscription()
            .then(function (subscription) {

                if (subscription) {
                    return subscription;
                }

                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(vapidKey),
                });
            })
            .then(function (subscription) {

                if (! subscription) {
                    return;
                }

                var csrf = getCsrfToken ();
                var subscribeUrl = (document.querySelector("meta[name='webpush-subscribe-url']") || {}).content || "/webpush/subscribe";

                return fetch(subscribeUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        ... (csrf ? { "X-CSRF-TOKEN": csrf } : {}),
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    credentials: "same-origin",
                    body: JSON.stringify(subscription),
                });
            })
            .catch(function (error) {
                console.error("❌ Service Worker: Error creating subscription", error);
            });
    }

    /**
     * Request notification permission and subscribe to push (must be called from user click).
     *
     * @param {ServiceWorkerRegistration} registration
     */
    function requestPushPermission(registration) {

        if (! ("Notification" in window) || !("PushManager" in window)) {
            return Promise.reject(new Error("Web Push not supported."));
        }

        if (Notification.permission === "denied") {
            return Promise.reject(new Error("Permission denied."));
        }

        if (Notification.permission === "granted") {
            return Promise.resolve(registration).then(ensurePushSubscription);
        }

        return Notification.requestPermission().then(function (permission) {

            if (permission !== "granted") {
                return Promise.reject(new Error("Permission not granted."));
            }

            return ensurePushSubscription(registration);
        });
    }

    var serviceWorkerRegistration = null;

    window.addEventListener("load", function () {

        navigator.serviceWorker
            .register("/sw.js")
            .then(function (registration) {

                serviceWorkerRegistration = registration;

                if (Notification.permission === "granted") {
                    ensurePushSubscription(registration);
                } else {
                    console.warn("ℹ️ [PWA] Notification permission not granted. Call requestPushPermission() from a button click to enable push notifications.");
                }
            })
            .catch(function (error) {
                console.error("❌ [PWA] Service Worker registration failed:", error);
            });
    });

    window.requestPushPermission = function () {

        if (! serviceWorkerRegistration) {
            return Promise.reject(new Error("Service Worker not ready."));
        }

        return requestPushPermission(serviceWorkerRegistration);
    };
}) ();
