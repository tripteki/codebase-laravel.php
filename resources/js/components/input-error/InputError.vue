<script setup lang="ts">

import type { HTMLAttributes, } from "vue";
import { cn, } from "@/lib/utils";

interface Props
{
    message?: string;
    class?: HTMLAttributes["class"];
};

const props = defineProps<Props> ();

</script>

<template>
    <p
        v-if="props.message"
        :class="cn ('text-sm text-red-600 dark:text-red-400', props.class)"
        v-bind="$attrs"
    >
        {{ props.message }}
    </p>
</template>
