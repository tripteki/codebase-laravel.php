<script setup lang="ts">

import type { HTMLAttributes, } from "vue";
import { Loader2, } from "lucide-vue-next";
import { cn, } from "@/lib/utils";

interface Props
{
    class?: HTMLAttributes["class"];
};

const props = defineProps<Props> ();

</script>

<template>
    <Loader2
        role="status"
        :class="cn ('size-4 animate-spin', props.class)"
        v-bind="$attrs"
    />
</template>
