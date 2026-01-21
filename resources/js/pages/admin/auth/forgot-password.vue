<script setup lang="ts">

import { Head, useForm, } from "@inertiajs/vue3";
import { route, } from "ziggy-js";
import { LoaderCircle, } from "lucide-vue-next";
import AuthLayout from "@/layouts/auth";
import InputError from "@/components/input-error";
import TextLink from "@/components/text-link";
import { Button, } from "@/components/ui/button";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { useI18n, } from "@/composables/useI18n";

interface Props
{
    status?: string;
};

const props = defineProps<Props> ();

const { t, } = useI18n ();

const form = useForm ({
    email: "",
});

const submit = (): void => {
    form.post (route ("admin.password.email"));
};

</script>

<template>
    <AuthLayout
        :title="t ('auth.forgot_password_title')"
        :description="t ('auth.forgot_password_description')"
    >
        <Head :title="t ('auth.forgot_password')" />

        <div
            v-if="props.status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ props.status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">{{ t ("auth.email_address") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        autocomplete="off"
                        autofocus
                        :placeholder="t ('auth.email_placeholder')"
                    />

                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="form.processing"
                        data-test="email-password-reset-link-button"
                    >
                        <LoaderCircle
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin"
                        />
                        {{ form.processing ? t ("auth.sending") : t ("auth.email_password_reset_link") }}
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>{{ t ("auth.or_return_to") }}</span>
                <TextLink :href="route ('admin.login')">
                    {{ t ("auth.log_in_lower") }}
                </TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
