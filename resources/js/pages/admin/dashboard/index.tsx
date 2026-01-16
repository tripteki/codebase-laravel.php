import { FC, } from "react";
import { Head, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";

import HeaderLayout from "@/layouts/header.layout";
import FooterLayout from "@/layouts/footer.layout";
import { Card, CardContent, CardDescription, CardHeader, CardTitle, } from "@/components/ui/card";

const Dashboard: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();

    return (
        <>
            <Head title={t ("common.dashboard")} />

            <div className="min-h-screen flex flex-col bg-background">
                <HeaderLayout showLogout={true} />

                <main className="flex-1 container mx-auto px-4 py-8">
                    <div className="space-y-8">
                        <div className="space-y-2">
                            <h1 className="text-3xl font-bold tracking-tight">
                                {t ("common.dashboard_title")}
                            </h1>
                            <p className="text-muted-foreground">
                                {t ("common.dashboard_description")}
                            </p>
                        </div>

                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <Card>
                                <CardHeader>
                                    <CardTitle>{t ("common.overview")}</CardTitle>
                                    <CardDescription>
                                        {t ("common.overview_description")}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground">
                                        {t ("common.overview_content")}
                                    </p>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>{t ("common.statistics")}</CardTitle>
                                    <CardDescription>
                                        {t ("common.statistics_description")}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground">
                                        {t ("common.statistics_content")}
                                    </p>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>{t ("common.activity")}</CardTitle>
                                    <CardDescription>
                                        {t ("common.activity_description")}
                                    </CardDescription>
                                </CardHeader>
                                <CardContent>
                                    <p className="text-sm text-muted-foreground">
                                        {t ("common.activity_content")}
                                    </p>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </main>

                <FooterLayout />
            </div>
        </>
    );
};

export default Dashboard;
