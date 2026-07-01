<?php

namespace Modules\Event\App\Support;

use App\Models\User;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;
use Modules\User\App\Support\UserDefaultsHelper;

final class AuthAddOnsHelper
{
    /**
     * @param string|null $tenantId
     * @return void
     */
    public static function initializeFromTenantId(?string $tenantId): void
    {
        if ($tenantId === null || trim($tenantId) === "") {
            return;
        }

        if (function_exists("tenancy") && tenancy()->initialized) {
            return;
        }

        $event = Event::query()->find(trim($tenantId));

        if ($event === null) {
            return;
        }

        tenancy()->initialize($event);
        sync_permissions_team_context();
    }

    /**
     * @param User|null $user
     * @return void
     */
    public static function initializeForUser(?User $user): void
    {
        if ($user === null) {
            return;
        }

        self::initializeFromTenantId(
            $user->tenant_id !== null ? (string) $user->tenant_id : null,
        );
    }

    /**
     * @return bool
     */
    public static function isTenantAuthContext(): bool
    {
        return function_exists("tenancy") && tenancy()->initialized;
    }

    /**
     * @param AddOnEnum $addOn
     * @return bool
     */
    public static function featureEnabled(AddOnEnum $addOn): bool
    {
        if (! self::isTenantAuthContext()) {
            return true;
        }

        return AddOnsHelper::has($addOn);
    }

    /**
     * @return bool
     */
    public static function isPasswordless(): bool
    {
        if (! self::isTenantAuthContext()) {
            return false;
        }

        return AddOnsHelper::has(AddOnEnum::FEATURES_AUTH_PASSWORDLESS);
    }

    /**
     * @return bool
     */
    public static function isLoginEnabled(): bool
    {
        return self::featureEnabled(AddOnEnum::FEATURES_AUTH_LOGIN);
    }

    /**
     * @return bool
     */
    public static function isRegistrationEnabled(): bool
    {
        return self::featureEnabled(AddOnEnum::FEATURES_AUTH_REGISTRATION);
    }

    /**
     * @return bool
     */
    public static function isMailingEnabled(): bool
    {
        if (! self::isTenantAuthContext()) {
            return true;
        }

        return AddOnsHelper::has(AddOnEnum::FEATURES_MAILING);
    }

    /**
     * @return bool
     */
    public static function isEmailVerificationRequired(): bool
    {
        return self::isMailingEnabled();
    }

    /**
     * @return void
     */
    public static function abortIfMailingDisabled(): void
    {
        if (! self::isMailingEnabled()) {
            abort(403, __("event.add_ons.auth.mailing_disabled"));
        }
    }

    /**
     * @return void
     */
    public static function abortIfEmailVerificationDisabled(): void
    {
        if (! self::isMailingEnabled()) {
            abort(403, __("event.add_ons.auth.email_verification_disabled"));
        }
    }

    /**
     * @param ?string $submittedPassword
     * @return string
     */
    public static function resolveLoginPassword(?string $submittedPassword): string
    {
        if (self::isPasswordless()) {
            return UserDefaultsHelper::defaultPassword();
        }

        return (string) $submittedPassword;
    }

    /**
     * @return void
     */
    public static function abortIfPasswordResetDisabled(): void
    {
        if (self::isPasswordless()) {
            abort(403, __("event.add_ons.auth.password_reset_disabled"));
        }

        if (! self::isMailingEnabled()) {
            abort(403, __("event.add_ons.auth.mailing_disabled"));
        }
    }
}
