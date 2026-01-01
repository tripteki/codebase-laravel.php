"use strict";

import { defineConfig, } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import i18n from "vite-plugin-laravel-translations";

export default defineConfig ({

    plugins: [

        laravel ({

            ssr: "resources/js/app.ssr.ts",

            input: [

                "resources/js/app.ts",
                "resources/css/app.css",
                "resources/css/filament/admin/theme.css",
            ],

            refresh: true,
        }),

        vue ({

            template: {

                transformAssetUrls: {

                    base: null,
                    includeAbsolute: false,
                },
            },
        }),

        i18n ({

            namespace: "translation",
            includeJson: false,
        }),
    ],

    resolve: {

        alias: {

            "@": "/resources/js",
        },
    },
});
