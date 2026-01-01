<template>
    <div>
        <Head :title="t ('common.welcome')" />

        <div class="min-h-screen flex flex-col bg-background">
            <!-- Header -->
            <header class="border-b">
                <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                    <h1 class="text-xl font-bold">{{ t ("common.welcome") }}</h1>

                    <div class="flex items-center gap-4">
                        <button
                            type="button"
                            @click="toggleTheme"
                            aria-label="Toggle theme"
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 w-10"
                        >
                            <Sun v-if="currentTheme === 'dark'" class="h-5 w-5" />
                            <Moon v-else class="h-5 w-5" />
                        </button>

                        <select
                            @change="(e) => useChangeLang (e as Event)"
                            :value="lang.currentLang ()"
                            class="px-3 py-2 rounded-md border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                v-for="langOption in lang.availableLangs ()"
                                :key="`lang-${langOption}`"
                                :value="langOption"
                            >
                                {{ langOption.toUpperCase () }}
                            </option>
                        </select>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
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
        </div>
    </div>
</template>

<script setup lang="ts">
import { Head, Link, } from "@inertiajs/vue3";
import { useLang, useChangeLang, useI18n, } from "@/composables/useI18n";
import { useTheme, } from "@/composables/useTheme";
import { Button, } from "@/components/ui/button";
import { Moon, Sun, } from "lucide-vue-next";
import { computed, } from "vue";

const { t, } = useI18n ();
const lang = useLang ();
const { theme, toggleTheme, } = useTheme ();

const currentTheme = computed (() => theme.value);
</script>
