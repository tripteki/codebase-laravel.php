"use strict";

import "./echo";
import "flowbite";
import { createRoot, hydrateRoot, } from "react-dom/client";
import { createInertiaApp, } from "@inertiajs/react";
import { resolvePageComponent, } from "laravel-vite-plugin/inertia-helpers";
import I18NLayout from "./pages/layouts/i18n.layout";

createInertiaApp (
{
    title: (
        title
    ) => {
        const appElement = document.getElementById("app");
        let appName = "";

        if (appElement?.dataset.page) {
            try {
                const pageData = JSON.parse(appElement.dataset.page);
                appName = pageData.props?.appName || "";
            } catch {
                //
            }
        }

        return title ? `${title} - ${appName}` : appName || "";
    },

    id: "app",

    resolve: (
        name
    ) => resolvePageComponent (`./pages/${name}.tsx`,
        import.meta.glob ("./pages/**/*.tsx")
    ),

    setup ({
        el,
        App,
        props,
    })
    {
        const component = (
            <I18NLayout
                lang={props.initialPage.props.lang}
                fallbackLang={props.initialPage.props.fallbackLang}
                availableLangs={props.initialPage.props.availableLangs}
            >
                <App {... props} />
            </I18NLayout>
        );

        if (el?.hasChildNodes ()) {
            hydrateRoot (el, component);
        } else {
            createRoot (el!).render (component);
        }

        // Register Service Worker for PWA
        if ("serviceWorker" in navigator && import.meta.env.PROD) {
            window.addEventListener("load", () => {
                navigator.serviceWorker.register("/sw.js")
                    .then((registration) => {
                        console.log("✅ Service Worker registered:", registration.scope);
                    })
                    .catch((error) => {
                        console.error("❌ Service Worker registration failed:", error);
                    });
            });
        }
    },
});
