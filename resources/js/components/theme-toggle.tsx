import { FC, } from "react";
import { useTranslation, } from "react-i18next";
import { Moon, Sun, } from "lucide-react";

import { Button, } from "@/components/ui/button";
import { useTheme, } from "@/hooks/theme";

const ThemeToggle: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();
    const { theme, toggleTheme, } = useTheme ();

    return (
        <Button
            variant="ghost"
            size="icon"
            onClick={toggleTheme}
            aria-label={t ("common.toggle_theme")}
        >
            {theme === "dark" ? (
                <Sun className="h-5 w-5" />
            ) : (
                <Moon className="h-5 w-5" />
            )}
        </Button>
    );
};

export default ThemeToggle;
