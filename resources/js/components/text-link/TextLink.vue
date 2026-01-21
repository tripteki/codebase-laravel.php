<script setup lang="ts">

import type { HTMLAttributes, } from "vue";
import { Link, } from "@inertiajs/vue3";
import { cn, } from "@/lib/utils";

interface Props
{
    href: string;
    class?: HTMLAttributes["class"];
};

const props = defineProps<Props> ();

</script>

<template>
    <Link
        :href="props.href"
        :class="cn (
            'text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500',
            props.class
        )"
        v-bind="$attrs"
    >
        <slot />
    </Link>
</template>
