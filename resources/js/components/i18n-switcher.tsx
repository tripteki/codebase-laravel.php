import { FC, type ChangeEvent, } from "react";

import { useLang, useChangeLang, } from "@/hooks/i18n";

const I18nSwitcher: FC = () =>
{
    const lang = useLang ();

    const handleChange = (e: ChangeEvent<HTMLSelectElement>): void =>
    {
        useChangeLang (e);
    };

    return (
        <select
            onChange={handleChange}
            value={lang.currentLang ()}
            className="px-3 py-2 rounded-md border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring"
        >
            {(lang.availableLangs () as string[]).map ((langOption: string) => (
                <option key={`lang-${langOption}`} value={langOption}>
                    {langOption.toUpperCase ()}
                </option>
            ))}
        </select>
    );
};

export default I18nSwitcher;
