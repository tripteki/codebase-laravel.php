<?php

namespace Modules\Event\App\Enums;

/**
 * @enum AddOnEnum
 */
enum AddOnEnum: string
{
    case FEATURES_AUTH_LOGIN = "features_auth_login";

    case FEATURES_AUTH_REGISTRATION = "features_auth_registration";

    case FEATURES_AUTH_PASSWORDLESS = "features_auth_passwordless";

    case FEATURES_MULTI_LANGUAGE = "features_multi_language";

    case FEATURES_IMPORT = "features_import";

    case FEATURES_EXPORT = "features_export";

    case FEATURES_MAILING = "features_mailing";

    case MODULES_USER = "modules_user";

    case MODULES_ACL = "modules_acl";

    case MODULES_LOG = "modules_log";

    case MODULES_NOTIFICATION = "modules_notification";

    /**
     * @return bool
     */
    public function isFeature(): bool
    {
        return str_starts_with($this->value, "features_");
    }

    /**
     * @return bool
     */
    public function isModule(): bool
    {
        return str_starts_with($this->value, "modules_");
    }

    /**
     * @return bool
     */
    public function isToggleable(): bool
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasFeatureConfiguration(): bool
    {
        return $this === self::FEATURES_MAILING;
    }

    /**
     * @return list<self>
     */
    public static function features(): array
    {
        return array_values(array_filter(self::cases(), static fn (self $case) => $case->isFeature()));
    }

    /**
     * @return list<self>
     */
    public static function modules(): array
    {
        return array_values(array_filter(self::cases(), static fn (self $case) => $case->isModule()));
    }

    /**
     * @return list<string>
     */
    public static function featureValues(): array
    {
        return array_map(static fn (self $case) => $case->value, self::features());
    }

    /**
     * @return list<string>
     */
    public static function moduleValues(): array
    {
        return array_map(static fn (self $case) => $case->value, self::modules());
    }

    /**
     * @return list<string>
     */
    public static function defaultFeatureValues(): array
    {
        return [
            self::FEATURES_AUTH_LOGIN->value,
            self::FEATURES_AUTH_REGISTRATION->value,
        ];
    }

    /**
     * @return list<string>
     */
    public static function defaultModuleValues(): array
    {
        return [
            self::MODULES_USER->value,
            self::MODULES_ACL->value,
            self::MODULES_LOG->value,
        ];
    }

    /**
     * @return list<string>
     */
    public static function toggleableFeatureValues(): array
    {
        return self::featureValues();
    }

    /**
     * @return list<string>
     */
    public static function toggleableModuleValues(): array
    {
        return self::moduleValues();
    }

    /**
     * @param string $value
     * @return self|null
     */
    public static function tryFromValue(string $value): ?self
    {
        return self::tryFrom(trim($value));
    }
}
