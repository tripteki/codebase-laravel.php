<?php

namespace App\Enum\Event;

/**
 * @enum AddOnEnum
 */
enum AddOnEnum: string
{
    /**
     * @var string
     */
    case FEATURES_MULTI_LANGUAGE = 'features_multi_language';

    /**
     * @var string
     */
    case FEATURES_IMPORT = 'features_import';

    /**
     * @var string
     */
    case FEATURES_EXPORT = 'features_export';

    /**
     * @var string
     */
    case FEATURES_MAILING = 'features_mailing';

    /**
     * @var string
     */
    case FEATURES_COPYRIGHT = 'features_copyright';

    /**
     * @var string
     */
    case MODULES_ATTENDANCE_QRCODE = 'modules_attendance_qrcode';

    /**
     * @var string
     */
    // case MODULES_ATTENDANCE_FACE_RECOGNITION = 'modules_attendance_face_recognition'; //

    /**
     * @var string
     */
    case MODULES_STAGE_MEETING = 'modules_stage_meeting';

    /**
     * @var string
     */
    case MODULES_STAGE_SESSION = 'modules_stage_session';

    /**
     * @return bool
     */
    public function isFeature(): bool
    {
        return str_starts_with($this->value, 'features_');
    }

    /**
     * @return bool
     */
    public function isModule(): bool
    {
        return str_starts_with($this->value, 'modules_');
    }

    /**
     * @return string|null
     */
    public function category(): ?string
    {
        if (! $this->isModule()) {
            return null;
        }
        $parts = explode('_', $this->value);

        return $parts[1] ?? null;
    }

    /**
     * @return string|null
     */
    public function categoryLabelKey(): ?string
    {
        $cat = $this->category();
        if ($cat === null) {
            return null;
        }

        return match ($cat) {
            'attendance' => 'module_event.add_ons_module_attendance',
            'stage' => 'module_event.add_ons_module_stage',
            default => null,
        };
    }

    /**
     * @return string
     */
    public function labelKey(): string
    {
        return match ($this) {
            self::FEATURES_IMPORT => 'module_event.add_on_checklist_import',
            self::FEATURES_EXPORT => 'module_event.add_on_checklist_export',
            self::FEATURES_MULTI_LANGUAGE => 'module_event.add_on_checklist_multi_language',
            self::FEATURES_MAILING => 'module_event.add_on_checklist_mailing',
            self::FEATURES_COPYRIGHT => 'module_event.add_on_checklist_copyright',
            self::MODULES_ATTENDANCE_QRCODE => 'module_event.add_on_qrcode',
            // self::MODULES_ATTENDANCE_FACE_RECOGNITION => 'module_event.add_on_face_recognition', //
            self::MODULES_STAGE_MEETING => 'module_event.add_on_stage_meeting',
            self::MODULES_STAGE_SESSION => 'module_event.add_on_stage_session',
        };
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this === self::FEATURES_EXPORT || $this === self::MODULES_ATTENDANCE_QRCODE;
    }

    /**
     * @return bool
     */
    public function hasFeatureConfiguration(): bool
    {
        if (! $this->isFeature()) {
            return false;
        }

        return match ($this) {
            self::FEATURES_MAILING => true,
            self::FEATURES_COPYRIGHT => true,
            default => false,
        };
    }

    /**
     * @return string|null
     */
    public function featureConfigHintLabelKey(): ?string
    {
        return match ($this) {
            self::FEATURES_MAILING => 'module_event.mailing_config_hint',
            self::FEATURES_COPYRIGHT => 'module_event.copyright_config_hint',
            default => null,
        };
    }

    /**
     * @param array<string, mixed> $rawConfig
     * @return array<string, string>
     */
    public function buildFeatureDisplayConfig(array $rawConfig): array
    {
        if (! $this->hasFeatureConfiguration()) {
            return [];
        }

        return match ($this) {
            self::FEATURES_MAILING => self::buildMailingFeatureDisplayConfig($rawConfig),
            self::FEATURES_COPYRIGHT => self::buildCopyrightFeatureDisplayConfig($rawConfig),
            default => self::buildGenericFeatureDisplayConfig($rawConfig),
        };
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, string>
     */
    private static function buildCopyrightFeatureDisplayConfig(array $raw): array
    {
        $out = [];
        if (isset($raw['owner'])) {
            $out['OWNER'] = (string) $raw['owner'];
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, string>
     */
    private static function buildMailingFeatureDisplayConfig(array $raw): array
    {
        $out = [];

        $stringFields = [
            'host' => 'MAIL_HOST',
            'port' => 'MAIL_PORT',
            'username' => 'MAIL_USERNAME',
            'from_address' => 'MAIL_FROM_ADDRESS',
            'from_name' => 'MAIL_FROM_NAME',
            'encryption' => 'MAIL_ENCRYPTION',
        ];

        foreach ($stringFields as $rawKey => $displayKey) {
            if (isset($raw[$rawKey])) {
                $out[$displayKey] = (string) $raw[$rawKey];
            }
        }

        if (array_key_exists('password', $raw)) {
            $out['MAIL_PASSWORD'] = '********';
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $raw
     * @return array<string, string>
     */
    private static function buildGenericFeatureDisplayConfig(array $raw): array
    {
        $out = [];
        foreach ($raw as $k => $v) {
            if ($v === null || is_scalar($v)) {
                $out[(string) $k] = $v === null || $v === '' ? '' : (string) $v;
            } else {
                $out[(string) $k] = json_encode($v) ?: '';
            }
        }

        return $out;
    }

    /**
     * @return string
     */
    public function inputIdPart(): string
    {
        $parts = explode('_', $this->value);
        if ($this->isFeature()) {
            return $parts[1] ?? $this->value;
        }
        $last = end($parts);

        return str_replace('_', '-', $last);
    }

    /**
     * @return array<self>
     */
    public static function features(): array
    {
        return array_values(array_filter(self::cases(), fn (self $c) => $c->isFeature()));
    }

    /**
     * @return array<string, array<self>>
     */
    public static function modulesGroupedByCategory(): array
    {
        $grouped = [];
        foreach (self::cases() as $c) {
            if (! $c->isModule()) {
                continue;
            }
            $key = $c->category();
            if ($key !== null) {
                $grouped[$key] ??= [];
                $grouped[$key][] = $c;
            }
        }

        return $grouped;
    }

    /**
     * @param string $category
     * @return string|null
     */
    public static function categoryLabelKeyFor(string $category): ?string
    {
        return match ($category) {
            'attendance' => 'module_event.add_ons_module_attendance',
            'stage' => 'module_event.add_ons_module_stage',
            default => null,
        };
    }
}
