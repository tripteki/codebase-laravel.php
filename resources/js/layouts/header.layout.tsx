import { FC, } from "react";
import { router, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { route, } from "ziggy-js";

import { Button, } from "@/components/ui/button";
import ThemeToggle from "@/components/theme-toggle";
import I18nSwitcher from "@/components/i18n-switcher";

interface HeaderLayoutProps
{
    showLogout?: boolean;
};

const HeaderLayout: FC<HeaderLayoutProps> = ({
    showLogout = false,
}) =>
{
    const { t, }: { t: Function; } = useTranslation ();

    const handleLogout = (): void =>
    {
        router.post (route ("admin.logout"));
    };

    return (
        <header className="border-b">
            <div className="container mx-auto px-4 py-4 flex justify-between items-center">
                <h1 className="text-xl font-bold">{t ("common.welcome")}</h1>

                <div className="flex items-center gap-4">
                    <ThemeToggle />
                    <I18nSwitcher />
                    {showLogout && (
                        <Button
                            variant="outline"
                            onClick={handleLogout}
                        >
                            {t ("auth.logout")}
                        </Button>
                    )}
                </div>
            </div>
        </header>
    );
};

export default HeaderLayout;
