<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null): mixed
    {
        $setting = Setting::query()
            ->where("key", $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return \App\Models\Setting
     */
    public static function set(string $key, $value): Setting
    {
        return Setting::query()->updateOrCreate(
            ["key" => $key],
            ["value" => $value]
        );
    }

    /**
     * Check if a setting exists.
     *
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
     * Remove a setting by key.
     *
     * @param string $key
     * @return bool
     */
    public static function remove(string $key): bool
    {
        return Setting::query()
            ->where("key", $key)
            ->delete() > 0;
    }

    /**
     * Get all settings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all()
    {
        return Setting::query()->get();
    }

    /**
     * Get all settings as a key-value array.
     *
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        return Setting::query()
            ->pluck("value", "key")
            ->toArray();
    }

    /**
     * Set multiple settings at once.
     *
     * @param array<string, mixed> $settings
     * @return void
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ["key" => $key],
                ["value" => $value]
            );
        }
    }

    /**
     * Remove multiple settings by keys.
     *
     * @param array<string> $keys
     * @return int
     */
    public static function removeMany(array $keys): int
    {
        return Setting::query()
            ->whereIn("key", $keys)
            ->delete();
    }

    /**
     * Clear all settings.
     *
     * @return int
     */
    public static function clear(): int
    {
        return Setting::query()->delete();
    }
}
