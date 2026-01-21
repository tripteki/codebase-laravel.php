<script setup lang="ts">

import { Head, } from "@inertiajs/vue3";
import HeaderLayout from "@/layouts/header";
import FooterLayout from "@/layouts/footer";
import { Card, CardContent, CardDescription, CardHeader, CardTitle, } from "@/components/ui/card";
import { useI18n, } from "@/composables/useI18n";

const { t, } = useI18n ();

</script>

<template>
    <Head :title="t ('common.dashboard')" />

    <div class="min-h-screen flex flex-col bg-background">
        <HeaderLayout :show-logout="true" />

        <main class="flex-1 container mx-auto px-4 py-8">
            <div class="space-y-8">
                <div class="space-y-2">
                    <h1 class="text-3xl font-bold tracking-tight">
                        {{ t ("common.dashboard_title") }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{ t ("common.dashboard_description") }}
                    </p>
                </div>

                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t ("common.overview") }}</CardTitle>
                            <CardDescription>
                                {{ t ("common.overview_description") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                {{ t ("common.overview_content") }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t ("common.statistics") }}</CardTitle>
                            <CardDescription>
                                {{ t ("common.statistics_description") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                {{ t ("common.statistics_content") }}
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>{{ t ("common.activity") }}</CardTitle>
                            <CardDescription>
                                {{ t ("common.activity_description") }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground">
                                {{ t ("common.activity_content") }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </main>

        <FooterLayout />
    </div>
</template>
