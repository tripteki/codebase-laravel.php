<?php

namespace Modules\User\App\Services;

use App\Helpers\SettingHelper;
use App\Services\Service as BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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
        "COLOR_PRIMARY",
        "COLOR_SECONDARY",
        "COLOR_TERTIARY",
    ];

    /**
     * @param SettingRepository $settingRepository
     * @return void
     */
    public function __construct(
        protected SettingRepository $settingRepository,
    ) {}

    /**
     * @return DataCollection<int, SettingTransformerDto>
     */
    public function all(): DataCollection
    {
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
            $settings[$key] = $value === null ? null : (string) $value;
        }

        $settings["EMAIL_SERVER"] = trim((string) config("app.email_server"));

        return $settings;
    }

    /**
     * @param SettingBatchUpdateDto $payload
     * @return DataCollection<int, SettingTransformerDto>
     */
    public function sync(SettingBatchUpdateDto $payload): DataCollection
    {
        $normalizedRows = [];
        $seenKeys = [];

        foreach ($payload->rows as $index => $row) {
            $key = trim((string) ($row->key ?? ""));
            $valueKind = ($row->value_kind ?? "text") === "file" ? "file" : "text";
            $storedValue = trim((string) ($row->value ?? ""));
            $previousValue = null;

            if ($row->id !== null && $row->id !== "") {
                $existing = $this->settingRepository->all()->firstWhere("id", $row->id);
                $previousValue = $existing?->value;
            }

            if ($key === "" && $storedValue === "" && ! ($row->file instanceof UploadedFile)) {
                if ($row->id !== null && $row->id !== "") {
                    $normalizedRows[] = [
                        "id" => $row->id,
                        "delete" => true,
                    ];
                }

                continue;
            }

            if ($key === "") {
                throw ValidationException::withMessages([
                    "rows.$index.key" => [ __("validation.required", [ "attribute" => "key", ]), ],
                ]);
            }

            if (isset($seenKeys[$key])) {
                throw ValidationException::withMessages([
                    "rows.$index.key" => [ __("validation.unique", [ "attribute" => "key", ]), ],
                ]);
            }

            $seenKeys[$key] = true;

            if ($valueKind === "file") {
                if ($row->file instanceof UploadedFile) {
                    $storedValue = $row->file->store("setting-files", "public");

                    if (
                        filled($previousValue)
                        && str_starts_with((string) $previousValue, "setting-files/")
                        && $previousValue !== $storedValue
                    ) {
                        Storage::disk("public")->delete($previousValue);
                    }
                } elseif ($storedValue === "") {
                    throw ValidationException::withMessages([
                        "rows.$index.value" => [ "A file is required for file settings.", ],
                    ]);
                }
            } elseif (
                filled($previousValue)
                && str_starts_with((string) $previousValue, "setting-files/")
                && $previousValue !== $storedValue
            ) {
                Storage::disk("public")->delete($previousValue);
            }

            $duplicateQuery = $this->settingRepository->all()->where("key", $key);

            if ($row->id !== null && $row->id !== "") {
                $duplicateQuery = $duplicateQuery->where("id", "!=", $row->id);
            }

            if ($duplicateQuery->isNotEmpty()) {
                throw ValidationException::withMessages([
                    "rows.$index.key" => [ __("validation.unique", [ "attribute" => "key", ]), ],
                ]);
            }

            $normalizedRows[] = [
                "id" => $row->id,
                "key" => $key,
                "value" => $storedValue !== "" ? $storedValue : null,
            ];
        }

        $settings = $this->settingRepository->syncRows($normalizedRows);

        return SettingTransformerDto::collect(
            $settings->map(static fn ($setting) => SettingTransformerDto::fromSetting($setting)),
            DataCollection::class,
        );
    }
}
