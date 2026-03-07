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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function all()
    {
        return Setting::query()->get();
    }

    /**
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        return Setting::query()
            ->pluck("value", "key")
            ->toArray();
    }

    /**
     * @param array<string, mixed> $settings
     * @return void
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            self::set((string) $key, $value);
        }
    }

    /**
     * @param array<string> $keys
     * @return int
     */
    public static function removeMany(array $keys): int
    {
        if ($keys === []) {
            return 0;
        }

        return Setting::query()
            ->whereIn("key", $keys)
            ->delete();
    }

    /**
     * @return int
     */
    public static function clear(): int
    {
        return Setting::query()->delete();
    }
}
