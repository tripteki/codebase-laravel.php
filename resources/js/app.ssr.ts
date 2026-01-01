import { createInertiaApp, } from "@inertiajs/vue3";
import createServer from "@inertiajs/vue3/server";
import { renderToString, } from "@vue/server-renderer";
import { resolvePageComponent, } from "laravel-vite-plugin/inertia-helpers";
import { createSSRApp, h, } from "vue";
import { createI18n, } from "vue-i18n";
import I18NLayout from "./pages/layouts/i18n.layout.vue";

createServer ((page) =>
    createInertiaApp ({
        /**
         * Configure the title for each page (SSR).
         *
         * @param {string} title
         * @return {string}
         */
        page,

        title: (
            title: string
        ): string => {
            const appName = (page.props.appName as string) || "";
            return title ? `${title} - ${appName}` : appName;
        },

        render: renderToString,

        /**
         * Resolve page components dynamically (SSR).
         *
         * @param {string} name
         * @return {Promise<any>}
         */
        resolve: (
            name: string
        ) =>
            resolvePageComponent (
                `./pages/${name}.vue`,
                import.meta.glob ("./pages/**/*.vue", { eager: true, })
            ),

        /**
         * Setup the Vue SSR application.
         *
         * @param {object} params
         * @return {any}
         */
        setup: ({
            App,
            props,
            plugin,
        }) => {
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

            const app = createSSRApp ({
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

            return app;
        },
    })
);
