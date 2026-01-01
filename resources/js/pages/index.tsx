import { FC, } from "react";
import { Head, Link, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { useLang, useChangeLang, } from "@/hooks/i18n";
import { useTheme, } from "@/hooks/theme";
import { Button, } from "@/components/ui/button";
import { Moon, Sun, } from "lucide-react";

const Index: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();
    const lang = useLang ();
    const { theme, toggleTheme, } = useTheme ();

    return (<>
        <Head title={t ("common.welcome")} />

        <div className="min-h-screen flex flex-col bg-background">
            {/* Header */}
            <header className="border-b">
                <div className="container mx-auto px-4 py-4 flex justify-between items-center">
                    <h1 className="text-xl font-bold">{t ("common.welcome")}</h1>
                    
                    <div className="flex items-center gap-4">
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={toggleTheme}
                            aria-label="Toggle theme"
                        >
                            {theme === "dark" ? (
                                <Sun className="h-5 w-5" />
                            ) : (
                                <Moon className="h-5 w-5" />
                            )}
                        </Button>
                        
                        <select
                            onChange={useChangeLang}
                            value={lang.currentLang ()}
                            className="px-3 py-2 rounded-md border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            {(lang.availableLangs () as string[]).map ((langOption: string) => (
                                <option key={`lang-${langOption}`} value={langOption}>
                                    {langOption.toUpperCase ()}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <main className="flex-1 flex items-center justify-center px-4 py-16">
                <div className="text-center space-y-8 max-w-2xl">
                    <div className="space-y-4">
                        <h2 className="text-4xl font-bold tracking-tight sm:text-5xl">
                            {t ("common.title")}
                            <br />
                            <span className="text-primary">{t ("common.subtitle")}</span>
                        </h2>
                        
                        <p className="text-xl text-muted-foreground">
                            {t ("common.description")}
                        </p>
                    </div>
                    
                    <div className="flex gap-4 justify-center">
                        <Button size="lg" asChild>
                            <Link href="/admin">
                                {t ("common.get_started")}
                            </Link>
                        </Button>
                        <Button variant="outline" size="lg" asChild>
                            <Link href="/api/docs">
                                {t ("common.view_docs")}
                            </Link>
                        </Button>
                    </div>
                </div>
            </main>
        </div>
    </>);
}

export default Index;
