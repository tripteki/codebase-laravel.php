<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null): mixed
    {
        return Setting::query()
            ->where("key", $key)
            ->value("value") ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public static function set(string $key, $value): Setting
    {
        return Setting::query()->updateOrCreate(
            [ "key" => $key, ],
            [ "value" => $value, ],
        );
    }

    /**
     * @param string $key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return Setting::query()
            ->where("key", $key)
            ->exists();
    }

    /**
     * @param array<string> $keys
     * @return array<string, mixed>
     */
    public static function many(array $keys): array
    {
        $settings = [];

        foreach ($keys as $key) {
            $settings[$key] = self::get($key);
        }

        return $settings;
    }
}
