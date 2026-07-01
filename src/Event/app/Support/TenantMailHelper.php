<?php

namespace Modules\Event\App\Support;

use Closure;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;

final class TenantMailHelper
{
    /**
     * @param string $email
     * @param Mailable|Closure(): Mailable $mailable
     * @param string|null $tenantId
     * @return void
     */
    public static function send(string $email, Mailable|Closure $mailable, ?string $tenantId = null): void
    {
        self::withTenantContext($tenantId, function () use ($email, $mailable): void {
            $instance = $mailable instanceof Closure ? $mailable() : $mailable;

            if (function_exists("tenancy") && tenancy()->initialized) {
                if (! AddOnsHelper::has(AddOnEnum::FEATURES_MAILING)) {
                    return;
                }

                $mailer = self::resolveMailerName();

                if ($mailer !== null) {
                    Mail::mailer($mailer)->to($email)->send($instance);

                    return;
                }

                Mail::to($email)->send($instance);

                return;
            }

            Mail::to($email)->send($instance);
        });
    }

    /**
     * @param string|null $tenantId
     * @param callable(): void $callback
     * @return void
     */
    public static function withTenantContext(?string $tenantId, callable $callback): void
    {
        if ($tenantId === null || $tenantId === "") {
            $callback();

            return;
        }

        if (tenancy()->initialized && (string) tenant()->getKey() === $tenantId) {
            $callback();

            return;
        }

        $event = Event::query()->find($tenantId);

        if ($event === null) {
            $callback();

            return;
        }

        tenancy()->initialize($event);

        try {
            sync_permissions_team_context();
            $callback();
        } finally {
            tenancy()->end();
            sync_permissions_team_context();
        }
    }

    /**
     * @return bool
     */
    public static function hasCustomMailingConfiguration(): bool
    {
        return self::resolveMailerName() !== null;
    }

    /**
     * @return string|null
     */
    public static function resolveMailerName(): ?string
    {
        if (! function_exists("tenancy") || ! tenancy()->initialized) {
            return null;
        }

        if (! AddOnsHelper::has(AddOnEnum::FEATURES_MAILING)) {
            return null;
        }

        $mailConfig = AddOnsHelper::config()[AddOnEnum::FEATURES_MAILING->value] ?? null;

        if (! is_array($mailConfig)) {
            return null;
        }

        $host = trim((string) ($mailConfig["host"] ?? ""));

        if ($host === "") {
            return null;
        }

        $tenantId = (string) tenant()->getKey();
        $mailerName = "tenant_".$tenantId;
        $password = $mailConfig["password"] ?? null;

        if (is_string($password) && $password !== "") {
            try {
                $password = Crypt::decryptString($password);
            } catch (\Throwable) {
                $password = null;
            }
        }

        Config::set("mail.mailers.".$mailerName, [
            "transport" => "smtp",
            "host" => $host,
            "port" => (int) ($mailConfig["port"] ?? 587),
            "encryption" => filled($mailConfig["encryption"] ?? null)
                ? (string) $mailConfig["encryption"]
                : null,
            "username" => filled($mailConfig["username"] ?? null)
                ? (string) $mailConfig["username"]
                : null,
            "password" => is_string($password) ? $password : null,
            "timeout" => null,
            "local_domain" => parse_url((string) config("app.url"), PHP_URL_HOST),
        ]);

        if (filled($mailConfig["from_address"] ?? null)) {
            Config::set("mail.from.address", (string) $mailConfig["from_address"]);
        }

        if (filled($mailConfig["from_name"] ?? null)) {
            Config::set("mail.from.name", (string) $mailConfig["from_name"]);
        }

        return $mailerName;
    }
}
