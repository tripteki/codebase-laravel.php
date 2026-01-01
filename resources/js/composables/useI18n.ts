import { usePage, } from "@inertiajs/vue3";
import { inject, } from "vue";
import { useI18n as useVueI18n, } from "vue-i18n";
import { route, } from "ziggy-js";

/**
 * Get current language and available languages from Inertia props.
 *
 * @return {object}
 */
export function useLang () {
    const page = usePage ();

    return {
        currentLang: (): string => page.props.lang as string,
        availableLangs: (): string[] => page.props.availableLangs as string[],
    };
}

/**
 * Change language handler.
 *
 * @param {Event} event
 * @return {void}
 */
export function useChangeLang (event: Event): void {
    const target = (event.target as HTMLSelectElement);
    window.location.href = route ("i18n", target.value);
}

/**
 * Get translation function from vue-i18n.
 *
 * @return {object}
 */
export function useI18n () {
    try {
        const i18n = useVueI18n ();

        if (i18n?.t) {
            return {
                t: (key: string, params?: any) => i18n.t (key, params),
            };
        }
    } catch {
        //
    }

    const page = usePage ();
    const translations = (page.props.translations as Record<string, any>) || {};

    return {
        t: (key: string): string => {
            const keys = key.split(".");

            let value: any = translations;
            for (const k of keys) {
                value = value?.[k];
                if (value === undefined) break;
            }

            return typeof value === "string" ? value : key;
        },
    };
}
