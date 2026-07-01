<?php

namespace Modules\User\App\Services;

use App\Helpers\SettingHelper;
use App\Models\User;
use App\Services\Service as BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\User\App\Dtos\SettingBatchUpdateDto;
use Modules\User\App\Dtos\SettingTransformerDto;
use Modules\User\App\Repositories\SettingRepository;
use Spatie\LaravelData\DataCollection;

class SettingService extends BaseService
{
    /**
     * @var array<int, string>
     */
    public const VARIABLE_KEYS = [
        "APP_NAME",
        "COLOR_PRIMARY",
        "COLOR_SECONDARY",
        "COLOR_TERTIARY",
        "DEFAULT_LOCALE",
    ];

    /**
     * @param \Modules\User\App\Repositories\SettingRepository $settingRepository
     * @return void
     */
    public function __construct(
        protected SettingRepository $settingRepository,
    ) {}

    /**
     * @return \Spatie\LaravelData\DataCollection<int, \Modules\User\App\Dtos\SettingTransformerDto>
     */
    public function all(): DataCollection
    {
        $this->assertSystemAccess();

        $settings = $this->settingRepository->all();

        return SettingTransformerDto::collect(
            $settings->map(static fn ($setting) => SettingTransformerDto::fromSetting($setting)),
            DataCollection::class,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function variables(): array
    {
        $settings = [];

        foreach (self::VARIABLE_KEYS as $key) {
            $value = SettingHelper::get($key);
            $settings[$key] = $value === null || $value === "" ? null : (string) $value;
        }

        return $settings;
    }

    /**
     * @param \Modules\User\App\Dtos\SettingBatchUpdateDto $payload
     * @return \Spatie\LaravelData\DataCollection<int, \Modules\User\App\Dtos\SettingTransformerDto>
     */
    public function sync(SettingBatchUpdateDto $payload): DataCollection
    {
        $this->assertSystemAccess();

        $normalizedRows = [];
        $seenKeys = [];

        foreach ($payload->rows as $row) {
            $key = strtoupper(trim((string) ($row->key ?? "")));
            $valueKind = ($row->value_kind ?? "text") === "file" ? "file" : "text";
            $storedValue = trim((string) ($row->value ?? ""));
            $previousValue = null;

            if ($row->id !== null && $row->id !== "") {
                $existing = $this->settingRepository->all()->firstWhere("id", $row->id);
                $previousValue = $existing?->value;
            }

            if ($row->id !== null && $row->id !== "" && $key === "") {
                $normalizedRows[] = [
                    "id" => $row->id,
                    "delete" => true,
                ];

                continue;
            }

            if ($key === "" && $storedValue === "" && ! ($row->file instanceof UploadedFile)) {
                continue;
            }

            if ($key === "") {
                abort(422, __("setting.key_required"));
            }

            if (isset($seenKeys[$key])) {
                abort(422, __("setting.key_duplicate"));
            }

            $seenKeys[$key] = true;

            if ($valueKind === "file") {
                if ($row->file instanceof UploadedFile) {
                    $storedValue = $row->file->store("settings", "public");

                    if (
                        filled($previousValue)
                        && is_setting_file_path((string) $previousValue)
                        && $previousValue !== $storedValue
                    ) {
                        Storage::disk("public")->delete($previousValue);
                    }
                }
            } elseif (
                filled($previousValue)
                && is_setting_file_path((string) $previousValue)
                && $previousValue !== $storedValue
            ) {
                Storage::disk("public")->delete($previousValue);
            }

            if ($row->id !== null && $row->id !== "" && $this->settingRepository->all()
                ->where("key", $key)
                ->where("id", "!=", $row->id)
                ->isNotEmpty()) {
                abort(422, __("setting.key_duplicate"));
            }

            $normalizedRows[] = [
                "id" => $row->id,
                "key" => $key,
                "value" => $storedValue !== "" ? $storedValue : null,
                "value_kind" => $valueKind,
            ];
        }

        $settings = $this->settingRepository->syncRows($normalizedRows);

        return SettingTransformerDto::collect(
            $settings->map(static fn ($setting) => SettingTransformerDto::fromSetting($setting)),
            DataCollection::class,
        );
    }

    /**
     * @return void
     */
    protected function assertSystemAccess(): void
    {
        $actor = Auth::user();

        if (! $actor instanceof User || ! is_central_superadmin($actor)) {
            abort(403, __("setting.forbidden"));
        }
    }
}
