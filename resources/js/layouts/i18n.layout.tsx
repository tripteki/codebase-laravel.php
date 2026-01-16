import { useState, useEffect, FC, ReactNode, } from "react";
import { initReactI18next, } from "react-i18next";
import i18n from "i18next";
import detector from "i18next-browser-languagedetector";

declare global
{
    interface Window
    {
        LARAVEL_TRANSLATIONS: Record<any, any>;
    };
};

const I18NLayout: FC<
{
    children: ReactNode;
    lang: string;
    fallbackLang: string;
    availableLangs: string[];
}> = ({
    children,
    lang,
    fallbackLang,
    availableLangs,
}) =>
{
    const [ isInitialed, setInitialed, ] = useState<boolean> (false);

    useEffect ((): void => {

        setInitialed (true);

        i18n.
        use (detector).
        use (initReactI18next).
        init (
        {
            interpolation: { escapeValue: false, },
            lng: lang,
            fallbackLng: fallbackLang,
            supportedLngs: availableLangs,
            resources: window.LARAVEL_TRANSLATIONS,
        });

    }, []);

    return (<>
        {isInitialed && children}
    </>);
};

export default I18NLayout;
