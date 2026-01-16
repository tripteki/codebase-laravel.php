import { FC, } from "react";
import { Head, useForm, } from "@inertiajs/react";
import { useTranslation, } from "react-i18next";
import { LoaderCircle, } from "lucide-react";
import { route, } from "ziggy-js";

import AuthLayout from "@/layouts/auth/auth-layout";
import InputError from "@/components/input-error";
import TextLink from "@/components/text-link";
import { Button, } from "@/components/ui/button";
import { Input, } from "@/components/ui/input";
import { Label, } from "@/components/ui/label";
import { type ForgotPasswordProps, } from "@/types/admin/auth";

const ForgotPassword: FC<ForgotPasswordProps> = ({
    status,
}) =>
{
    const { t, }: { t: Function; } = useTranslation ();
    const { data, setData, post, processing, errors, } = useForm ({
        email: "",
    });

    const submit = (e: React.FormEvent): void => {
        e.preventDefault ();
        post (route ("admin.password.email"));
    };

    return (
        <>
            <AuthLayout
                title={t ("auth.forgot_password_title")}
                description={t ("auth.forgot_password_description")}
            >
                <Head title={t ("auth.forgot_password")} />

                {status && (
                    <div className="mb-4 text-center text-sm font-medium text-green-600">
                        {status}
                    </div>
                )}

                <div className="space-y-6">
                    <form onSubmit={submit}>
                        <div className="grid gap-2">
                            <Label htmlFor="email">{t ("auth.email_address")}</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                onChange={(e): void => setData ("email", e.target.value)}
                                autoComplete="off"
                                autoFocus
                                placeholder={t ("auth.email_placeholder")}
                            />

                            <InputError message={errors.email} />
                        </div>

                        <div className="my-6 flex items-center justify-start">
                            <Button
                                type="submit"
                                className="w-full"
                                disabled={processing}
                                data-test="email-password-reset-link-button"
                            >
                                {processing && (
                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                )}
                                {processing ? t ("auth.sending") : t ("auth.email_password_reset_link")}
                            </Button>
                        </div>
                    </form>

                    <div className="space-x-1 text-center text-sm text-muted-foreground">
                        <span>{t ("auth.or_return_to")}</span>
                        <TextLink href={route ("admin.login")}>
                            {t ("auth.log_in_lower")}
                        </TextLink>
                    </div>
                </div>
            </AuthLayout>
        </>
    );
};

export default ForgotPassword;
