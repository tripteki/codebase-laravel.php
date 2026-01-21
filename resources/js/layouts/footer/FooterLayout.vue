<script setup lang="ts">

import { computed, } from "vue";
import { useI18n, } from "@/composables/useI18n";

const { t, } = useI18n ();

const currentYear = computed (() => new Date ().getFullYear ());

</script>

<template>
    <footer class="border-t mt-auto">
        <div class="container mx-auto px-4 py-4">
            <p class="text-center text-sm text-muted-foreground">
                © {{ currentYear }} {{ t ("common.all_rights_reserved") }}
            </p>
        </div>
    </footer>
</template>
