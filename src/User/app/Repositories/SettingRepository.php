<?php

namespace Modules\User\App\Repositories;

use App\Models\Setting;
use App\Repositories\Repository as BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SettingRepository extends BaseRepository
{
    /**
     * @return Collection<int, Setting>
     */
    public function all(): Collection
    {
        return parent::accessGet(
            fn () => Setting::query()->orderBy("key")->get(),
        );
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return Collection<int, Setting>
     */
    public function syncRows(array $rows): Collection
    {
        return parent::mutateUpdate(
            function () use ($rows) {
                foreach ($rows as $row) {
                    $id = $row["id"] ?? null;
                    $key = trim((string) ($row["key"] ?? ""));
                    $value = $row["value"] ?? null;
                    $delete = (bool) ($row["delete"] ?? false);

                    if ($delete) {
                        if ($id !== null && $id !== "") {
                            $setting = Setting::query()->whereKey((string) $id)->first();

                            if ($setting !== null) {
                                $raw = (string) ($setting->value ?? "");

                                if ($raw !== "" && str_starts_with($raw, "setting-files/")) {
                                    Storage::disk("public")->delete($raw);
                                }

                                $setting->delete();
                            }
                        }

                        continue;
                    }

                    if ($key === "" && ($value === null || $value === "")) {
                        continue;
                    }

                    if ($id !== null && $id !== "") {
                        $setting = Setting::query()->whereKey((string) $id)->first();

                        if ($setting !== null) {
                            $setting->update([
                                "key" => $key,
                                "value" => $value !== "" ? $value : null,
                            ]);

                            continue;
                        }
                    }

                    Setting::query()->create([
                        "key" => $key,
                        "value" => $value !== "" ? $value : null,
                    ]);
                }

                return Setting::query()->orderBy("key")->get();
            },
        );
    }
}
