<script setup lang="ts">

import { Head, useForm, } from "@inertiajs/vue3";
import { route, } from "ziggy-js";
import AuthLayout from "@/layouts/auth";
import InputError from "@/components/input-error";
import TextLink from "@/components/text-link";
import { Button, } from "@/components/ui/button";
import { Checkbox, } from "@/components/ui/checkbox";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { Spinner, } from "@/components/ui/spinner";
import { useI18n, } from "@/composables/useI18n";

interface Props
{
    status?: string;
    canResetPassword?: boolean;
    canRegister?: boolean;
};

const props = withDefaults (defineProps<Props> (), {
    status: undefined,
    canResetPassword: true,
    canRegister: true,
});

const { t, } = useI18n ();

const form = useForm ({
    identifier: "",
    password: "",
    remember: false,
});

const submit = (): void => {
    form.post (route ("admin.login"), {
        onSuccess: (): void => {
            form.reset ("password");
        },
    });
};

</script>

<template>
    <AuthLayout
        :title="t ('auth.login_title')"
        :description="t ('auth.login_description')"
    >
        <Head :title="t ('auth.login')" />

        <form
            @submit.prevent="submit"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">{{ t ("auth.email_address") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="identifier"
                        v-model="form.identifier"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="email"
                        :placeholder="t ('auth.email_placeholder')"
                    />
                    <InputError :message="form.errors.email || form.errors.identifier" />
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center">
                        <Label for="password">{{ t ("auth.password") }}</Label>
                        <TextLink
                            v-if="props.canResetPassword"
                            :href="route ('admin.password.request')"
                            class="ml-auto text-sm"
                            tabindex="5"
                        >
                            {{ t ("auth.forgot_password_link") }}
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        v-model="form.password"
                        required
                        tabindex="2"
                        autocomplete="current-password"
                        :placeholder="t ('auth.password_placeholder')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center space-x-3">
                    <Checkbox
                        id="remember"
                        name="remember"
                        :checked="form.remember"
                        @update:checked="(checked: boolean) => form.remember = checked"
                        tabindex="3"
                    />
                    <Label for="remember">{{ t ("auth.remember_me") }}</Label>
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    tabindex="4"
                    :disabled="form.processing"
                    data-test="login-button"
                >
                    <Spinner v-if="form.processing" class="mx-5" />
                    {{ form.processing ? t ("auth.logging_in") : t ("auth.log_in") }}
                </Button>
            </div>

            <div
                v-if="props.canRegister"
                class="text-center text-sm text-muted-foreground"
            >
                {{ t ("auth.dont_have_account") }}
                <TextLink
                    :href="route ('admin.register')"
                    tabindex="5"
                >
                    {{ t ("auth.sign_up") }}
                </TextLink>
            </div>
        </form>

        <div
            v-if="props.status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ props.status }}
        </div>
    </AuthLayout>
</template>
