<?php

namespace Modules\User\App\Repositories;

use App\Models\Setting;
use App\Repositories\Repository as BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Modules\Log\App\Support\ActivityRecorder;

class SettingRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Support\Collection<int, \App\Models\Setting>
     */
    public function all(): Collection
    {
        return parent::accessGet(
            fn () => Setting::query()->orderBy("key")->get(),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return \Illuminate\Support\Collection<int, \App\Models\Setting>
     */
    public function syncRows(array $rows): Collection
    {
        return parent::mutateUpdate(
            function () use ($rows) {
                foreach ($rows as $row) {
                    $id = $row["id"] ?? null;
                    $key = trim((string) ($row["key"] ?? ""));
                    $value = $row["value"] ?? null;
                    $valueKind = ($row["value_kind"] ?? "text") === "file" ? "file" : "text";
                    $delete = (bool) ($row["delete"] ?? false);

                    if ($delete) {
                        if ($id !== null && $id !== "") {
                            $setting = Setting::query()->whereKey((string) $id)->first();

                            if ($setting !== null) {
                                $raw = (string) ($setting->value ?? "");
                                $old = [
                                    "key" => (string) $setting->key,
                                    "value" => $setting->value,
                                    "value_kind" => $setting->value_kind ?? "text",
                                ];

                                if ($raw !== "" && is_setting_file_path($raw)) {
                                    Storage::disk("public")->delete($raw);
                                }

                                $setting->delete();
                                ActivityRecorder::settingDeleted($setting, $old);
                            }
                        }

                        continue;
                    }

                    if ($key === "" && ($value === null || $value === "")) {
                        continue;
                    }

                    $setting = $id !== null && $id !== ""
                        ? Setting::query()->whereKey((string) $id)->first()
                        : null;
                    $setting ??= Setting::withTrashed()->where("key", $key)->first();

                    if ($setting !== null) {
                        if ($setting->trashed()) {
                            $setting->restore();
                        }

                        $old = [
                            "key" => (string) $setting->key,
                            "value" => $setting->value,
                            "value_kind" => $setting->value_kind ?? "text",
                        ];
                        $nextValue = $value !== "" ? $value : null;

                        $setting->update([
                            "key" => $key,
                            "value" => $nextValue,
                            "value_kind" => $valueKind,
                        ]);

                        if (
                            $old["key"] !== (string) $setting->key
                            || $old["value"] !== $setting->value
                            || $old["value_kind"] !== ($setting->value_kind ?? "text")
                        ) {
                            ActivityRecorder::settingUpdated($setting, $old);
                        }

                        continue;
                    }

                    $created = Setting::query()->create([
                        "key" => $key,
                        "value" => $value !== "" ? $value : null,
                        "value_kind" => $valueKind,
                    ]);
                    ActivityRecorder::settingCreated($created);
                }

                return Setting::query()->orderBy("key")->get();
            },
        );
    }
}
