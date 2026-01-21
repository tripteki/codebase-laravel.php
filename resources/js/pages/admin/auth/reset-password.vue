<script setup lang="ts">

import { Head, useForm, } from "@inertiajs/vue3";
import { route, } from "ziggy-js";
import AuthLayout from "@/layouts/auth";
import InputError from "@/components/input-error";
import { Button, } from "@/components/ui/button";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { Spinner, } from "@/components/ui/spinner";
import { useI18n, } from "@/composables/useI18n";

interface Props
{
    token: string;
    email: string;
};

const props = defineProps<Props> ();

const { t, } = useI18n ();

const form = useForm ({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

const submit = (): void => {
    form.post (route ("admin.password.update"), {
        onSuccess: (): void => {
            form.reset ("password", "password_confirmation");
        },
    });
};

</script>

<template>
    <AuthLayout
        :title="t ('auth.reset_password_title')"
        :description="t ('auth.reset_password_description')"
    >
        <Head :title="t ('auth.reset_password')" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="email">{{ t ("auth.email") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        v-model="form.email"
                        class="mt-1 block w-full"
                        readonly
                    />
                    <InputError
                        :message="form.errors.email"
                        class="mt-2"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ t ("auth.password") }}</Label>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        v-model="form.password"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        autofocus
                        :placeholder="t ('auth.password_placeholder')"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        {{ t ("auth.password_confirmation_label") }}
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        v-model="form.password_confirmation"
                        autocomplete="new-password"
                        class="mt-1 block w-full"
                        :placeholder="t ('auth.password_confirmation_placeholder')"
                    />
                    <InputError
                        :message="form.errors.password_confirmation"
                        class="mt-2"
                    />
                </div>

                <Button
                    type="submit"
                    class="mt-4 w-full"
                    :disabled="form.processing"
                    data-test="reset-password-button"
                >
                    <Spinner v-if="form.processing" class="mx-5" />
                    {{ form.processing ? t ("auth.resetting") : t ("auth.reset_password") }}
                </Button>
            </div>
        </form>
    </AuthLayout>
</template>
