import { FC, } from "react";
import { Head, useForm, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { route, } from "ziggy-js";

import AuthLayout from "@/layouts/auth/auth-layout";
import InputError from "@/components/input-error";
import TextLink from "@/components/text-link";
import { Button, } from "@/components/ui/button";
import { Checkbox, } from "@/components/ui/checkbox";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { Spinner, } from "@/components/ui/spinner";
import { type LoginProps, } from "@/types/admin/auth";

const Login: FC<LoginProps> = ({
    status,
    canResetPassword = true,
    canRegister = true,
}) =>
{
    const { t, }: { t: Function; } = useTranslation ();
    const { data, setData, post, processing, errors, reset, } = useForm ({
        identifier: "",
        password: "",
        remember: false,
    });

    const submit = (e: React.FormEvent): void => {
        e.preventDefault ();
        post (route ("admin.login"), {
            onSuccess: (): void => {
                reset ("password");
            },
        });
    };

    return (
        <>
            <AuthLayout
                title={t ("auth.login_title")}
                description={t ("auth.login_description")}
            >
                <Head title={t ("auth.login")} />

                <form
                    onSubmit={submit}
                    className="flex flex-col gap-6"
                >
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="email">{t ("auth.email_address")}</Label>
                            <Input
                                id="email"
                                type="email"
                                name="identifier"
                                value={data.identifier}
                                onChange={(e): void =>
                                    setData ("identifier", e.target.value)
                                }
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="email"
                                placeholder={t ("auth.email_placeholder")}
                            />
                            <InputError message={errors.email || errors.identifier} />
                        </div>

                        <div className="grid gap-2">
                            <div className="flex items-center">
                                <Label htmlFor="password">{t ("auth.password")}</Label>
                                {canResetPassword && (
                                    <TextLink
                                        href={route ("admin.password.request")}
                                        className="ml-auto text-sm"
                                        tabIndex={5}
                                    >
                                        {t ("auth.forgot_password_link")}
                                    </TextLink>
                                )}
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                value={data.password}
                                onChange={(e): void =>
                                    setData ("password", e.target.value)
                                }
                                required
                                tabIndex={2}
                                autoComplete="current-password"
                                placeholder={t ("auth.password_placeholder")}
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="flex items-center space-x-3">
                            <Checkbox
                                id="remember"
                                name="remember"
                                checked={data.remember}
                                onCheckedChange={(checked): void =>
                                    setData ("remember", checked === true)
                                }
                                tabIndex={3}
                            />
                            <Label htmlFor="remember">{t ("auth.remember_me")}</Label>
                        </div>

                        <Button
                            type="submit"
                            className="mt-4 w-full"
                            tabIndex={4}
                            disabled={processing}
                            data-test="login-button"
                        >
                            {processing && <Spinner className="mx-5" />}
                            {processing ? t ("auth.logging_in") : t ("auth.log_in")}
                        </Button>
                    </div>

                    {canRegister && (
                        <div className="text-center text-sm text-muted-foreground">
                            {t ("auth.dont_have_account")}{" "}
                            <TextLink
                                href={route ("admin.register")}
                                tabIndex={5}
                            >
                                {t ("auth.sign_up")}
                            </TextLink>
                        </div>
                    )}
                </form>

                {status && (
                    <div className="mb-4 text-center text-sm font-medium text-green-600">
                        {status}
                    </div>
                )}
            </AuthLayout>
        </>
    );
};

export default Login;
