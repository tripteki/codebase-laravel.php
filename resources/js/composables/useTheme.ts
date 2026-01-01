import { ref, watch, onMounted, } from "vue";

/**
 * Composable for theme management (dark/light mode).
 *
 * @return {object}
 */
export function useTheme () {
    const getInitialTheme = (): string => {
        if (typeof window !== "undefined") {
            const stored = localStorage.getItem ("theme");
            if (stored) return stored;
            
            const prefersDark = window.matchMedia ("(prefers-color-scheme: dark)").matches;
            return prefersDark ? "dark" : "light";
        }
        return "light";
    };

    const theme = ref<string> (getInitialTheme ());

    const applyTheme = () => {
        if (typeof window === "undefined") return;

        const root = window.document.documentElement;
        root.classList.remove ("light", "dark");

        if (theme.value === "dark") {
            root.classList.add ("dark");
        } else {
            root.classList.add ("light");
        }

        localStorage.setItem ("theme", theme.value);
    };

    watch (theme, () => {
        applyTheme ();
    }, { immediate: true });

    onMounted (() => {
        applyTheme ();
    });

    /**
     * Set theme explicitly.
     *
     * @param {string} newTheme
     * @return {void}
     */
    const setTheme = (newTheme: string): void => {
        theme.value = newTheme;
    };

    /**
     * Toggle between dark and light theme.
     *
     * @return {void}
     */
    const toggleTheme = (): void => {
        const newTheme = theme.value === "dark" ? "light" : "dark";
        theme.value = newTheme;
        applyTheme ();
    };

    return { theme, setTheme, toggleTheme };
}
