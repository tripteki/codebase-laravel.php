<?php

namespace Modules\User\App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Auth\App\Mail\NoticeMail;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\Event\App\Support\TenantMailHelper;

class SecurityNotifier
{
    /**
     * @param \App\Models\User $user
     * @param string|null $actorId
     * @return void
     */
    public static function passwordChanged(User $user, ?string $actorId = null): void
    {
        if ($user->deleted_at !== null) {
            return;
        }

        $time = now()->timezone((string) config("app.timezone"))->format("Y-m-d H:i:s");

        if ($actorId !== null && $actorId !== (string) $user->getKey()) {
            $actor = User::query()->find($actorId);
            $adminName = $actor instanceof User ? ($actor->displayName() ?: "-") : "-";
            $line1 = __("user.security.password_line_admin", [
                "admin" => $adminName,
                "time" => $time,
            ]);
        } else {
            $line1 = __("user.security.password_line_self", [ "time" => $time, ]);
        }

        self::send(
            $user,
            $user->email,
            __("user.security.password_subject"),
            $line1,
            __("user.security.password_warning"),
            frontend_url("login"),
            __("user.security.login"),
        );
    }

    /**
     * @param \App\Models\User $user
     * @param string $oldEmail
     * @param bool $byAdmin
     * @return void
     */
    public static function emailChanged(User $user, string $oldEmail, bool $byAdmin): void
    {
        if ($user->deleted_at !== null || $oldEmail === "" || strcasecmp($oldEmail, $user->email) === 0) {
            return;
        }

        $name = $user->displayName() ?: $user->email;

        self::send(
            $user,
            $oldEmail,
            __("user.security.email_old_subject"),
            __("user.security.email_old_line", [ "email" => self::maskEmail($user->email), ]),
            __("user.security.email_old_warning"),
            null,
            null,
            $name,
        );

        if (! $byAdmin) {
            return;
        }

        self::send(
            $user,
            $user->email,
            __("user.security.email_new_subject"),
            __("user.security.email_new_line"),
            null,
            frontend_url("login"),
            __("user.security.login"),
            $name,
        );
    }

    /**
     * @param string $email
     * @return string
     */
    public static function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode("@", $email, 2), 2, "");
        $visible = strlen($local) > 2 ? substr($local, 0, 2) : substr($local, 0, 1);

        return $domain !== "" ? $visible."***@".$domain : $visible."***";
    }

    /**
     * @param \App\Models\User $user
     * @param string $to
     * @param string $subject
     * @param string $line1
     * @param string|null $line2
     * @param string|null $link
     * @param string|null $linkLabel
     * @param string|null $name
     * @return void
     */
    protected static function send(
        User $user,
        string $to,
        string $subject,
        string $line1,
        ?string $line2 = null,
        ?string $link = null,
        ?string $linkLabel = null,
        ?string $name = null,
    ): void {
        try {
            AuthAddOnsHelper::initializeForUser($user);

            if (! AuthAddOnsHelper::isMailingEnabled() || $to === "") {
                return;
            }

            $tenantId = $user->tenant_id !== null ? (string) $user->tenant_id : null;

            TenantMailHelper::send(
                $to,
                new NoticeMail(
                    subjectLine: $subject,
                    heading: $subject,
                    line: $line1,
                    userName: $name ?? ($user->displayName() ?: $user->email),
                    userEmail: $to,
                    line2: $line2,
                    actionUrl: $link,
                    actionLabel: $linkLabel,
                ),
                $tenantId,
            );
        } catch (\Throwable $exception) {
            Log::warning("security notice failed", [
                "to" => $to,
                "error" => $exception->getMessage(),
            ]);
        }
    }
}
