import { FC, } from "react";
import { Head, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { useLang, useChangeLang, } from "@/Hooks/i18n";
import HeaderLayout from "./Layouts/HeaderLayout";
import FooterLayout from "./Layouts/FooterLayout";

const Index: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();

    return (<>
        <Head title="Title" />

        <div>
            <HeaderLayout />
            <div className="mx-2">
                <div>{t ("common.welcome")}</div>
                <select onChange={useChangeLang} value={useLang ().currentLang ()} className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-25 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    {(useLang ().availableLangs () as string[]).map ((lang: string) => (
                        <option key={`lang-${lang}`} value={lang}>
                            {lang.toUpperCase ()}
                        </option>
                    ))}
                </select>
            </div>
            <FooterLayout />
        </div>
    </>);
}

export default Index;
