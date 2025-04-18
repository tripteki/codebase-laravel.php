"use strict";

import "./echo";
import "flowbite";
import { hydrateRoot, } from "react-dom/client";
import { createInertiaApp, } from "@inertiajs/react";
import { resolvePageComponent, } from "laravel-vite-plugin/inertia-helpers";
import I18NLayout from "./pages/layouts/i18n.layout";

createInertiaApp (
{
    title: (
        title
    ) => `${title}`,

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
        hydrateRoot (el,
            <I18NLayout
                lang={props.initialPage.props.lang}
                fallbackLang={props.initialPage.props.fallbackLang}
                availableLangs={props.initialPage.props.availableLangs}
            >
                <App {... props} />
            </I18NLayout>
        );
    },
});
