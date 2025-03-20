"use strict";

import { defineConfig, } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import i18n from "vite-plugin-laravel-translations";

export default defineConfig ({

    plugins: [

        laravel ({

            ssr: "resources/js/app.ssr.tsx",

            input: [

                "resources/js/app.tsx",
                "resources/css/app.css",
                "resources/css/filament/admin/theme.css",
            ],

            refresh: true,
        }),

        react (),

        i18n ({

            namespace: "translation",
            includeJson: false,
        }),
    ],
});
