import { useEffect, useState, } from "react";

export function useTheme (): {
    theme: string;
    setTheme: (theme: string) => void;
    toggleTheme: () => void;
} {
    const [ theme, setThemeState, ] = useState<string> (() => {
        if (typeof window !== "undefined") {
            return localStorage.getItem ("theme") || (window.matchMedia ("(prefers-color-scheme: dark)").matches ? "dark" : "light");
        }
        return "light";
    });

    useEffect ((): void => {
        const root = window.document.documentElement;
        
        root.classList.remove ("light", "dark");
        
        if (theme === "dark") {
            root.classList.add ("dark");
        } else {
            root.classList.add ("light");
        }
        
        localStorage.setItem ("theme", theme);
    }, [ theme ]);

    const setTheme = (newTheme: string): void => {
        setThemeState (newTheme);
    };

    const toggleTheme = (): void => {
        setThemeState ((prev) => prev === "dark" ? "light" : "dark");
    };

    return { theme, setTheme, toggleTheme, };
};
