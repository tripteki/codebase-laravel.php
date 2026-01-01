<template>
    <slot />
</template>

<script setup lang="ts">
import { useI18n as useVueI18n, } from "vue-i18n";

/**
 * Props for I18N Layout component.
 */
interface Props {
    lang: string;
    fallbackLang: string;
    availableLangs: string[];
}

withDefaults (defineProps<Props> (), {
    lang: "en",
    fallbackLang: "en",
    availableLangs: () => ["en"],
});

useVueI18n ();
</script>
