<script setup lang="ts">

import { Button, } from "@/components/ui/button";
import { useTheme, } from "@/composables/useTheme";
import { useI18n, } from "@/composables/useI18n";
import { Moon, Sun, } from "lucide-vue-next";
import { computed, } from "vue";

const { t, } = useI18n ();
const { theme, toggleTheme, } = useTheme ();

const currentTheme = computed (() => theme.value);

</script>

<template>
    <Button
        variant="ghost"
        size="icon"
        @click="toggleTheme"
        :aria-label="t ('common.toggle_theme')"
    >
        <Sun v-if="currentTheme === 'dark'" class="h-5 w-5" />
        <Moon v-else class="h-5 w-5" />
    </Button>
</template>
