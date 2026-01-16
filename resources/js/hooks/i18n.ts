import { ChangeEvent, } from "react";
import { route, } from "ziggy-js";
import { usePage, } from "@inertiajs/react";

export function useLang (): {
    currentLang: () => string;
    availableLangs: () => string[];
} {
    const { props, } = usePage ();

    return {

        currentLang: (): string => props.lang as string,
        availableLangs: (): string[] => props.availableLangs as string[],
    };
};

export function useChangeLang (
    eventListener: ChangeEvent<HTMLSelectElement>
): void {
    window.location.href = route ("i18n", eventListener.target.value);
};
