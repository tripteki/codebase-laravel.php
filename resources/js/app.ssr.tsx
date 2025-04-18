"use strict";

import { createInertiaApp, } from "@inertiajs/react";
import { resolvePageComponent, } from "laravel-vite-plugin/inertia-helpers";
import ReactDOMServer from "react-dom/server";
import createServer from "@inertiajs/react/server";
import I18NLayout from "./pages/layouts/i18n.layout";

createServer ((page) => createInertiaApp (
{
    page,

    render: ReactDOMServer.renderToString,

    resolve: (
        name
    ) => resolvePageComponent (`./pages/${name}.tsx`,
        import.meta.glob ("./pages/**/*.tsx",
            { eager: true, }
        )
    ),

    setup: ({
        App,
        props,
    }) => (
        <I18NLayout
            lang={props.initialPage.props.lang}
            fallbackLang={props.initialPage.props.fallbackLang}
            availableLangs={props.initialPage.props.availableLangs}
        >
            <App {... props} />
        </I18NLayout>
    ),
}));
