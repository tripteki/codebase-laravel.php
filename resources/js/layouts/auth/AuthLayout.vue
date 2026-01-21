<script setup lang="ts">

import { Link, } from "@inertiajs/vue3";
import HeaderLayout from "@/layouts/header";
import FooterLayout from "@/layouts/footer";
import AppLogoIcon from "@/components/app-logo-icon";

interface Props
{
    title?: string;
    description?: string;
};

const props = defineProps<Props> ();

</script>

<template>
    <div class="min-h-screen flex flex-col bg-background">
        <HeaderLayout />

        <main class="flex-1 flex items-center justify-center p-6 md:p-10">
            <div class="w-full max-w-sm">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col items-center gap-4">
                        <Link
                            href="/"
                            class="flex flex-col items-center gap-2 font-medium"
                        >
                            <div class="mb-1 flex h-9 w-9 items-center justify-center rounded-md">
                                <AppLogoIcon class="size-9 fill-current text-[var(--foreground)] dark:text-white" />
                            </div>
                            <span class="sr-only">{{ props.title }}</span>
                        </Link>

                        <div class="space-y-2 text-center">
                            <h1 class="text-xl font-medium">{{ props.title }}</h1>
                            <p class="text-center text-sm text-muted-foreground">
                                {{ props.description }}
                            </p>
                        </div>
                    </div>
                    <slot />
                </div>
            </div>
        </main>

        <FooterLayout />
    </div>
</template>
