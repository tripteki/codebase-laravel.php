<script setup lang="ts">

import { router, } from "@inertiajs/vue3";
import { route, } from "ziggy-js";
import { Button, } from "@/components/ui/button";
import ThemeToggle from "@/components/theme-toggle";
import I18nSwitcher from "@/components/i18n-switcher";
import { useI18n, } from "@/composables/useI18n";

interface Props
{
    showLogout?: boolean;
};

const props = withDefaults (defineProps<Props> (), {
    showLogout: false,
});

const { t, } = useI18n ();

const handleLogout = (
): void => {
    router.post (route ("admin.logout"));
};

</script>

<template>
    <header class="border-b">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">{{ t ("common.welcome") }}</h1>

            <div class="flex items-center gap-4">
                <ThemeToggle />
                <I18nSwitcher />
                <Button
                    v-if="props.showLogout"
                    variant="outline"
                    @click="handleLogout"
                >
                    {{ t ("auth.logout") }}
                </Button>
            </div>
        </div>
    </header>
</template>
