import { FC, } from "react";
import { Head, useForm, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { route, } from "ziggy-js";

import AuthLayout from "@/layouts/auth/auth-layout";
import InputError from "@/components/input-error";
import { Button, } from "@/components/ui/button";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { Spinner, } from "@/components/ui/spinner";
import { type ResetPasswordProps, } from "@/types/admin/auth";

const ResetPassword: FC<ResetPasswordProps> = ({
    token,
    email,
}) =>
{
    const { t, }: { t: Function; } = useTranslation ();
    const { data, setData, post, processing, errors, reset, } = useForm ({
        token,
        email,
        password: "",
        password_confirmation: "",
    });

    const submit = (e: React.FormEvent): void => {
        e.preventDefault ();
        post (route ("admin.password.update"), {
            onSuccess: (): void => {
                reset ("password", "password_confirmation");
            },
        });
    };

    return (
        <>
            <AuthLayout
                title={t ("auth.reset_password_title")}
                description={t ("auth.reset_password_description")}
            >
                <Head title={t ("auth.reset_password")} />

                <form onSubmit={submit}>
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="email">{t ("auth.email")}</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                autoComplete="email"
                                value={email}
                                className="mt-1 block w-full"
                                readOnly
                            />
                            <InputError
                                message={errors.email}
                                className="mt-2"
                            />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="password">{t ("auth.password")}</Label>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                value={data.password}
                                onChange={(e): void =>
                                    setData ("password", e.target.value)
                                }
                                autoComplete="new-password"
                                className="mt-1 block w-full"
                                autoFocus
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
                                name="password_confirmation"
                                value={data.password_confirmation}
                                onChange={(e): void =>
                                    setData (
                                        "password_confirmation",
                                        e.target.value
                                    )
                                }
                                autoComplete="new-password"
                                className="mt-1 block w-full"
                                placeholder={t ("auth.password_confirmation_placeholder")}
                            />
                            <InputError
                                message={errors.password_confirmation}
                                className="mt-2"
                            />
                        </div>

                        <Button
                            type="submit"
                            className="mt-4 w-full"
                            disabled={processing}
                            data-test="reset-password-button"
                        >
                            {processing && <Spinner className="mx-5" />}
                            {processing ? t ("auth.resetting") : t ("auth.reset_password")}
                        </Button>
                    </div>
                </form>
            </AuthLayout>
        </>
    );
};

export default ResetPassword;
