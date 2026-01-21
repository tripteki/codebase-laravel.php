<script setup lang="ts">

import { Head, useForm, } from "@inertiajs/vue3";
import { route, } from "ziggy-js";
import AuthLayout from "@/layouts/auth";
import InputError from "@/components/input-error";
import TextLink from "@/components/text-link";
import { Button, } from "@/components/ui/button";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { Spinner, } from "@/components/ui/spinner";
import { useI18n, } from "@/composables/useI18n";

const { t, } = useI18n ();

const form = useForm ({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = (): void => {
    form.post (route ("admin.register"), {
        onSuccess: (): void => {
            form.reset ("password", "password_confirmation");
        },
    });
};

</script>

<template>
    <AuthLayout
        :title="t ('auth.register_title')"
        :description="t ('auth.register_description')"
    >
        <Head :title="t ('auth.register')" />

        <form
            @submit.prevent="submit"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">{{ t ("auth.name") }}</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        tabindex="1"
                        autocomplete="name"
                        name="name"
                        v-model="form.name"
                        :placeholder="t ('auth.username')"
                    />
                    <InputError
                        :message="form.errors.name"
                        class="mt-2"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="email">{{ t ("auth.email_address") }}</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        tabindex="2"
                        autocomplete="email"
                        name="email"
                        v-model="form.email"
                        :placeholder="t ('auth.email_placeholder')"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">{{ t ("auth.password") }}</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        v-model="form.password"
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
                        required
                        tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        v-model="form.password_confirmation"
                        :placeholder="t ('auth.password_confirmation_placeholder')"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="5"
                    :disabled="form.processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="form.processing" class="mx-5" />
                    {{ form.processing ? t ("auth.registering") : t ("auth.create_account") }}
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                {{ t ("auth.already_have_account") }}
                <TextLink :href="route ('admin.login')" tabindex="6">
                    {{ t ("auth.log_in") }}
                </TextLink>
            </div>
        </form>
    </AuthLayout>
</template>
