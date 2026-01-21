"use strict";

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const pusherKey = import.meta.env.VITE_PUSHER_APP_KEY;

if (pusherKey) {

    const isDevelopment = import.meta.env.MODE === "development" || import.meta.env.DEV;
    const defaultPort = isDevelopment ? 6001 : 443;
    const defaultHost = import.meta.env.VITE_PUSHER_HOST || (isDevelopment ? "127.0.0.1" : undefined);
    const defaultScheme = import.meta.env.VITE_PUSHER_SCHEME || (isDevelopment ? "http" : "https");
    const wsPort = import.meta.env.VITE_PUSHER_PORT ? parseInt(import.meta.env.VITE_PUSHER_PORT, 10) : defaultPort;

    window.Echo = new Echo (
    {
        broadcaster: "pusher",
        key: pusherKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
        wsHost: defaultHost,
        wsPort: wsPort,
        wssPort: wsPort,
        forceTLS: defaultScheme === "https",
        enabledTransports: [ "ws", "wss", ],
    });

} else {

    window.Echo = {
        channel: () => ({
            listen: () => ({ stop: () => {}, }),
            stopListening: () => {},
        }),
        private: () => ({
            listen: () => ({ stop: () => {}, }),
            stopListening: () => {},
        }),
        join: () => ({
            listen: () => ({ stop: () => {}, }),
            leave: () => {},
            here: () => {},
            joining: () => {},
            leaving: () => {},
        }),
        leave: () => {},
        disconnect: () => {},
    };
};
