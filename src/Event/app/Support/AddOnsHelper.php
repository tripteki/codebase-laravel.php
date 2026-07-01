<?php

namespace Modules\Event\App\Support;

use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;

final class AddOnsHelper
{
    /**
     * @param AddOnEnum $addOn
     * @return bool
     */
    public static function has(AddOnEnum $addOn): bool
    {
        $event = self::currentEvent();

        if ($event === null) {
            return false;
        }

        $list = $addOn->isFeature()
            ? self::featureValues($event)
            : self::moduleValues($event);

        return in_array($addOn->value, $list, true);
    }

    /**
     * @param Event $event
     * @return list<AddOnEnum>
     */
    public static function enabledFeatureCases(Event $event): array
    {
        $allowed = array_flip(self::featureValues($event));

        return array_values(array_filter(
            AddOnEnum::features(),
            static fn (AddOnEnum $case) => isset($allowed[$case->value]),
        ));
    }

    /**
     * @param Event $event
     * @return list<AddOnEnum>
     */
    public static function enabledModuleCases(Event $event): array
    {
        $allowed = array_flip(self::moduleValues($event));

        return array_values(array_filter(
            AddOnEnum::modules(),
            static fn (AddOnEnum $case) => isset($allowed[$case->value]),
        ));
    }

    /**
     * @param Event|null $event
     * @return array<string, array<string, string|null>>
     */
    public static function config(?Event $event = null): array
    {
        $event ??= self::currentEvent();

        if ($event === null) {
            return [];
        }

        $raw = $event->getAttribute("add_ons_config");

        return is_array($raw) ? $raw : [];
    }

    /**
     * @param Event $event
     * @return list<string>
     */
    public static function featureValues(Event $event): array
    {
        $raw = $event->getAttribute("add_ons_features");

        if ($raw === null) {
            return AddOnEnum::defaultFeatureValues();
        }

        $parsed = self::parseList($raw);

        if ($parsed === []) {
            return AddOnEnum::defaultFeatureValues();
        }

        $allowed = array_flip(AddOnEnum::featureValues());

        return array_values(array_unique(array_values(array_filter(
            $parsed,
            static fn (string $value) => isset($allowed[$value]),
        ))));
    }

    /**
     * @param Event $event
     * @return list<string>
     */
    public static function moduleValues(Event $event): array
    {
        $raw = $event->getAttribute("add_ons_modules");

        if ($raw === null) {
            return AddOnEnum::defaultModuleValues();
        }

        $parsed = self::parseList($raw);

        $allowed = array_flip(AddOnEnum::moduleValues());

        return array_values(array_unique(array_values(array_filter(
            $parsed,
            static fn (string $value) => isset($allowed[$value]),
        ))));
    }

    /**
     * @param mixed $raw
     * @return list<string>
     */
    public static function parseList(mixed $raw): array
    {
        if (is_array($raw)) {
            return array_values(array_filter(array_map(
                static fn ($value) => trim((string) $value),
                $raw,
            )));
        }

        if (! is_string($raw) || trim($raw) === "") {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($value) => trim($value),
            explode(",", $raw),
        )));
    }

    /**
     * @param list<string> $values
     * @return list<string>
     */
    public static function normalizeFeatureValues(array $values): array
    {
        $allowed = array_flip(AddOnEnum::featureValues());
        $normalized = [];

        foreach ($values as $value) {
            if (! isset($allowed[$value])) {
                continue;
            }

            $normalized[] = $value;
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @param list<string> $values
     * @return list<string>
     */
    public static function normalizeModuleValues(array $values): array
    {
        $allowed = array_flip(AddOnEnum::moduleValues());
        $normalized = [];

        foreach ($values as $value) {
            if (! isset($allowed[$value])) {
                continue;
            }

            $normalized[] = $value;
        }

        return array_values(array_unique($normalized));
    }

    /**
     * @param array<string, array<string, string|null>> $config
     * @param list<string> $enabledFeatures
     * @return array<string, array<string, string|null>>
     */
    public static function pruneConfig(array $config, array $enabledFeatures): array
    {
        $pruned = [];

        foreach ($config as $feature => $rows) {
            if (! is_array($rows)) {
                continue;
            }

            if (! in_array($feature, $enabledFeatures, true)) {
                continue;
            }

            $case = AddOnEnum::tryFromValue($feature);

            if ($case === null || ! $case->hasFeatureConfiguration()) {
                continue;
            }

            $pruned[$feature] = $rows;
        }

        return $pruned;
    }

    /**
     * @return Event|null
     */
    private static function currentEvent(): ?Event
    {
        if (! function_exists("tenant")) {
            return null;
        }

        $tenant = tenant();

        return $tenant instanceof Event ? $tenant : null;
    }
}
