import { FC, } from "react";
import { Head, Link, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";

import HeaderLayout from "@/layouts/header.layout";
import FooterLayout from "@/layouts/footer.layout";
import { Button, } from "@/components/ui/button";

const Index: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();

    return (
        <>
            <Head title={t ("common.welcome")} />

            <div className="min-h-screen flex flex-col bg-background">
                <HeaderLayout />

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

                <FooterLayout />
            </div>
        </>
    );
};

export default Index;
