"use strict";

import "./echo";
import "flowbite";
import { hydrateRoot, } from "react-dom/client";
import { createInertiaApp, } from "@inertiajs/react";
import { resolvePageComponent, } from "laravel-vite-plugin/inertia-helpers";
import I18NLayout from "./Pages/Layouts/I18NLayout";

createInertiaApp (
{
    title: (
        title
    ) => `${title}`,

    id: "app",

    resolve: (
        name
    ) => resolvePageComponent (`./Pages/${name}.tsx`,
        import.meta.glob ("./Pages/**/*.tsx")
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
