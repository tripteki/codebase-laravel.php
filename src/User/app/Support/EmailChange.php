<?php

namespace Modules\User\App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Auth\App\Mail\NoticeMail;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\Event\App\Support\TenantMailHelper;

class EmailChange
{
    /**
     * @param \App\Models\User $user
     * @param string $newEmail
     * @return void
     */
    public static function request(User $user, string $newEmail): void
    {
        $newEmail = trim($newEmail);
        $tenantId = $user->tenant_id !== null ? (string) $user->tenant_id : null;

        Cache::put(self::key($newEmail), [
            "user_id" => (string) $user->getKey(),
            "tenant_id" => $tenantId,
        ], now()->addHour());

        try {
            AuthAddOnsHelper::initializeForUser($user);

            if (! AuthAddOnsHelper::isMailingEnabled()) {
                return;
            }

            $name = $user->displayName() ?: $user->email;

            TenantMailHelper::send(
                $newEmail,
                new NoticeMail(
                    subjectLine: __("user.account_email_change.subject"),
                    heading: __("user.account_email_change.subject"),
                    line: __("user.account_email_change.line1"),
                    userName: $name,
                    userEmail: $newEmail,
                    line2: __("user.account_email_change.line2"),
                    actionUrl: signed_auth_frontend_url($tenantId, auth_verify_email_path($newEmail)),
                    actionLabel: __("user.account_email_change.verify"),
                ),
                $tenantId,
            );
        } catch (\Throwable $exception) {
            Log::warning("email change mail failed", [
                "email" => $newEmail,
                "error" => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param string $newEmail
     * @param string|null $tenantId
     * @return \App\Models\User|null
     */
    public static function confirm(string $newEmail, ?string $tenantId): ?User
    {
        $pending = Cache::get(self::key($newEmail));

        if (! is_array($pending) || ! filled($pending["user_id"] ?? null)) {
            return null;
        }

        $storedTenant = $pending["tenant_id"] ?? null;
        $storedTenant = is_string($storedTenant) && $storedTenant !== "" ? $storedTenant : null;

        if ($storedTenant !== $tenantId) {
            return null;
        }

        $user = User::query()->withoutTenancy()->whereNull("deleted_at")->find($pending["user_id"]);

        if (! $user instanceof User) {
            return null;
        }

        if (User::query()->withoutTenancy()->where("email", $newEmail)->whereKeyNot($user->getKey())->exists()) {
            return null;
        }

        $previousEmail = $user->email;
        $user->forceFill([ "email" => $newEmail, ])->save();
        Cache::forget(self::key($newEmail));

        SecurityNotifier::emailChanged($user->fresh() ?? $user, $previousEmail, false);

        return $user->fresh();
    }

    /**
     * @param string $email
     * @return string
     */
    protected static function key(string $email): string
    {
        return "email-change:".strtolower(trim($email));
    }
}
