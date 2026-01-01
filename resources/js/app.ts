import "./echo";
import { createApp, h, } from "vue";
import { createInertiaApp, } from "@inertiajs/vue3";
import { resolvePageComponent, } from "laravel-vite-plugin/inertia-helpers";
import { createI18n, } from "vue-i18n";
import I18NLayout from "./pages/layouts/i18n.layout.vue";

createInertiaApp ({
    /**
     * Configure the title for each page.
     *
     * @param {string} title
     * @return {string}
     */
    title: (
        title: string
    ): string => {
        const appElement = document.getElementById ("app");
        let appName = "";

        if (appElement?.dataset.page) {
            try {
                const pageData = JSON.parse (appElement.dataset.page);
                appName = pageData.props?.appName || "";
            } catch {
                //
            }
        }

        return title ? `${title} - ${appName}` : appName || "";
    },

    /**
     * Resolve page components dynamically.
     *
     * @param {string} name
     * @return {Promise<any>}
     */
    resolve: (
        name: string
    ) =>
        resolvePageComponent (
            `./pages/${name}.vue`,
            import.meta.glob ("./pages/**/*.vue")
        ),

    /**
     * Setup the Vue application.
     *
     * @param {object} params
     * @return {any}
     */
    setup ({
        el,
        App,
        props,
        plugin,
    }) {
        const translations = (props.initialPage.props.translations as Record<string, any>) || {};
        const currentLang = (props.initialPage.props.lang as string) || "en";
        const fallbackLang = (props.initialPage.props.fallbackLang as string) || "en";

        const i18n = createI18n ({
            legacy: false,
            locale: currentLang,
            fallbackLocale: fallbackLang,
            messages: {
                [currentLang]: translations,
            },
            silentFallbackWarn: false,
            silentTranslationWarn: false,
            missingWarn: false,
            fallbackWarn: false,
        });

        const app = createApp ({
            render: () =>
                h (I18NLayout, {
                    lang: currentLang,
                    fallbackLang: fallbackLang,
                    availableLangs: props.initialPage.props.availableLangs,
                }, {
                    default: () => h (App, props),
                }),
        });

        app.use (i18n);
        app.use (plugin);

        // Register Service Worker for PWA
        if ("serviceWorker" in navigator && import.meta.env.PROD) {
            window.addEventListener ("load", () => {
                navigator.serviceWorker
                    .register ("/sw.js")
                    .then ((registration) => {
                        console.log ("✅ Service Worker registered:", registration.scope);
                    })
                    .catch ((error) => {
                        console.error ("❌ Service Worker registration failed:", error);
                    });
            });
        }

        const root = app.mount (el);

        return root;
    },
});
