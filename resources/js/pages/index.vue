<script setup lang="ts">

import { Head, Link, } from "@inertiajs/vue3";
import HeaderLayout from "@/layouts/header";
import FooterLayout from "@/layouts/footer";
import { Button, } from "@/components/ui/button";
import { useI18n, } from "@/composables/useI18n";

const { t, } = useI18n ();

</script>

<template>
    <Head :title="t ('common.welcome')" />

    <div class="min-h-screen flex flex-col bg-background">
        <HeaderLayout />

        <main class="flex-1 flex items-center justify-center px-4 py-16">
            <div class="text-center space-y-8 max-w-2xl">
                <div class="space-y-4">
                    <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">
                        {{ t ("common.title") }}
                        <br />
                        <span class="text-primary">{{ t ("common.subtitle") }}</span>
                    </h2>

                    <p class="text-xl text-muted-foreground">
                        {{ t ("common.description") }}
                    </p>
                </div>

                <div class="flex gap-4 justify-center">
                    <Button size="lg" as-child>
                        <Link href="/admin">
                            {{ t ("common.get_started") }}
                        </Link>
                    </Button>
                    <Button variant="outline" size="lg" as-child>
                        <Link href="/api/docs">
                            {{ t ("common.view_docs") }}
                        </Link>
                    </Button>
                </div>
            </div>
        </main>

        <FooterLayout />
    </div>
</template>
