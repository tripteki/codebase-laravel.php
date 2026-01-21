<script setup lang="ts">

import { AlertCircle, } from "lucide-vue-next";
import { Alert, AlertDescription, AlertTitle, } from "@/components/ui/alert";
import { useI18n, } from "@/composables/useI18n";

interface Props
{
    errors: string[];
    title?: string;
};

const props = defineProps<Props> ();

const { t, } = useI18n ();

</script>

<template>
    <Alert variant="destructive">
        <AlertCircle />
        <AlertTitle>{{ props.title || t ("common.something_went_wrong") }}</AlertTitle>
        <AlertDescription>
            <ul class="list-inside list-disc text-sm">
                <li
                    v-for="(error, index) in Array.from (new Set (props.errors))"
                    :key="index"
                >
                    {{ error }}
                </li>
            </ul>
        </AlertDescription>
    </Alert>
</template>
