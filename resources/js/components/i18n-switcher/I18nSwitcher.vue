<script setup lang="ts">

import { useLang, useChangeLang, } from "@/composables/useI18n";
import { computed, } from "vue";

const lang = useLang ();

const currentLang = computed (() => lang.currentLang ());
const availableLangs = computed (() => lang.availableLangs ());

const handleChange = (
    event: Event
): void => {
    useChangeLang (event);
};

</script>

<template>
    <select
        @change="handleChange"
        :value="currentLang"
        class="px-3 py-2 rounded-md border border-input bg-background text-sm focus:outline-none focus:ring-2 focus:ring-ring"
    >
        <option
            v-for="langOption in availableLangs"
            :key="`lang-${langOption}`"
            :value="langOption"
        >
            {{ langOption.toUpperCase () }}
        </option>
    </select>
</template>
