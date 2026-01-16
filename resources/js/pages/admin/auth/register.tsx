import { FC, } from "react";
import { Head, useForm, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { route, } from "ziggy-js";

import AuthLayout from "@/layouts/auth/auth-layout";
import InputError from "@/components/input-error";
import TextLink from "@/components/text-link";
import { Button, } from "@/components/ui/button";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { Spinner, } from "@/components/ui/spinner";

const Register: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();
    const { data, setData, post, processing, errors, reset, } = useForm ({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
    });

    const submit = (e: React.FormEvent): void => {
        e.preventDefault ();
        post (route ("admin.register"), {
            onSuccess: (): void => {
                reset ("password", "password_confirmation");
            },
        });
    };

    return (
        <>
            <AuthLayout
                title={t ("auth.register_title")}
                description={t ("auth.register_description")}
            >
                <Head title={t ("auth.register")} />

                <form
                    onSubmit={submit}
                    className="flex flex-col gap-6"
                >
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="name">{t ("auth.name")}</Label>
                            <Input
                                id="name"
                                type="text"
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="name"
                                name="name"
                                value={data.name}
                                onChange={(e): void => setData ("name", e.target.value)}
                                placeholder={t ("auth.username")}
                            />
                            <InputError
                                message={errors.name}
                                className="mt-2"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="email">{t ("auth.email_address")}</Label>
                            <Input
                                id="email"
                                type="email"
                                required
                                tabIndex={2}
                                autoComplete="email"
                                name="email"
                                value={data.email}
                                onChange={(e): void => setData ("email", e.target.value)}
                                placeholder={t ("auth.email_placeholder")}
                            />
                            <InputError message={errors.email} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password">{t ("auth.password")}</Label>
                            <Input
                                id="password"
                                type="password"
                                required
                                tabIndex={3}
                                autoComplete="new-password"
                                name="password"
                                value={data.password}
                                onChange={(e): void =>
                                    setData ("password", e.target.value)
                                }
                                placeholder={t ("auth.password_placeholder")}
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password_confirmation">
                                {t ("auth.password_confirmation_label")}
                            </Label>
                            <Input
                                id="password_confirmation"
                                type="password"
                                required
                                tabIndex={4}
                                autoComplete="new-password"
                                name="password_confirmation"
                                value={data.password_confirmation}
                                onChange={(e): void =>
                                    setData ("password_confirmation", e.target.value)
                                }
                                placeholder={t ("auth.password_confirmation_placeholder")}
                            />
                            <InputError
                                message={errors.password_confirmation}
                            />
                        </div>

                        <Button
                            type="submit"
                            className="mt-2 w-full"
                            tabIndex={5}
                            disabled={processing}
                            data-test="register-user-button"
                        >
                            {processing && <Spinner className="mx-5" />}
                            {processing ? t ("auth.registering") : t ("auth.create_account")}
                        </Button>
                    </div>

                    <div className="text-center text-sm text-muted-foreground">
                        {t ("auth.already_have_account")}{" "}
                        <TextLink href={route ("admin.login")} tabIndex={6}>
                            {t ("auth.log_in")}
                        </TextLink>
                    </div>
                </form>
            </AuthLayout>
        </>
    );
};

export default Register;
