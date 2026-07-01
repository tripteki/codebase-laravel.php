<?php

namespace Modules\Event\App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Event\App\Enums\AddOnEnum;
use Modules\Event\App\Models\Event;

final class EventFormSupport
{
    /**
     * @param Request $request
     * @return array{
     *     id: string,
     *     payload: array<string, mixed>,
     *     files: array<string, UploadedFile|null>
     * }
     */
    public static function validateStore(Request $request): array
    {
        $validated = Validator::make($request->all(), self::storeRules($request))->validate();

        return [
            "id" => (string) $validated["id"],
            "payload" => self::payloadFromValidated($validated),
            "files" => self::filesFromRequest($request),
        ];
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array{
     *     payload: array<string, mixed>,
     *     files: array<string, UploadedFile|null>
     * }
     */
    public static function validateUpdate(Request $request, string $id): array
    {
        $event = Event::query()->findOrFail($id);
        $validated = Validator::make(
            array_merge($request->all(), ["id" => $id]),
            self::updateRules($request, $event),
        )->validate();

        return [
            "payload" => self::payloadFromValidated($validated, $event),
            "files" => self::filesFromRequest($request),
        ];
    }

    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    private static function storeRules(Request $request): array
    {
        return array_merge(self::sharedFieldRules($request), [
            "id" => [
                "required",
                "string",
                "min:2",
                "max:64",
                "regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/",
                Rule::unique(Event::class, "id"),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Event $event
     * @return array<string, mixed>
     */
    private static function updateRules(Request $request, Event $event): array
    {
        return array_merge(self::sharedFieldRules($request, isUpdate: true, event: $event), [
            "id" => ["required", "string", Rule::exists(Event::class, "id")],
        ]);
    }

    /**
     * @param Request $request
     * @param bool $isUpdate
     * @param Event|null $event
     * @return array<string, mixed>
     */
    private static function sharedFieldRules(
        Request $request,
        bool $isUpdate = false,
        ?Event $event = null,
    ): array {
        $sometimes = $isUpdate ? ["sometimes"] : [];

        return [
            "title" => array_merge($isUpdate ? ["sometimes"] : ["required"], ["string", "min:2", "max:128"]),
            "description" => array_merge($sometimes, ["nullable", "string", "max:1000"]),
            "copyright_text" => array_merge($sometimes, ["nullable", "string", "max:500"]),
            "copyright_mode" => array_merge($sometimes, ["nullable", "string", Rule::in(["none", "text", "image"])]),
            "primary_color" => array_merge($sometimes, ["nullable", "string", "max:32"]),
            "secondary_color" => array_merge($sometimes, ["nullable", "string", "max:32"]),
            "tertiary_color" => array_merge($sometimes, ["nullable", "string", "max:32"]),
            "add_ons_features" => array_merge($sometimes, ["nullable"]),
            "add_ons_modules" => array_merge($sometimes, ["nullable"]),
            "add_ons_config" => array_merge($sometimes, ["nullable", "array"]),
            "icon" => array_merge($sometimes, ["nullable", "image", "max:2048"]),
            "favicon_ico" => array_merge($sometimes, ["nullable", "file", "max:2048"]),
            "favicon_png" => array_merge($sometimes, ["nullable", "image", "max:2048"]),
            "brand_light" => array_merge($sometimes, ["nullable", "image", "max:4096"]),
            "brand_dark" => array_merge($sometimes, ["nullable", "image", "max:4096"]),
            "copyright_image" => array_merge($sometimes, [
                "nullable",
                "image",
                "max:4096",
                Rule::requiredIf(
                    static fn (): bool => self::requiresCopyrightImageUpload($request, $event),
                ),
            ]),
        ];
    }

    /**
     * @param Request $request
     * @param Event|null $event
     * @return bool
     */
    private static function requiresCopyrightImageUpload(Request $request, ?Event $event = null): bool
    {
        if ($request->input("copyright_mode") !== "image") {
            return false;
        }

        if ($request->hasFile("copyright_image")) {
            return false;
        }

        if ($event instanceof Event && filled($event->getAttribute("copyright_image"))) {
            return false;
        }

        return true;
    }

    /**
     * @param array<string, mixed> $validated
     * @param Event|null $event
     * @return array<string, mixed>
     */
    private static function payloadFromValidated(array $validated, ?Event $event = null): array
    {
        $payload = [];

        foreach (["title", "description", "copyright_text", "primary_color", "secondary_color", "tertiary_color"] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }

        if (array_key_exists("copyright_mode", $validated)) {
            $payload["copyright_mode"] = (string) $validated["copyright_mode"];
        } elseif ($event === null) {
            $payload["copyright_mode"] = "none";
        }

        if ($event === null) {
            foreach (EventBrandingSupport::DEFAULT_COLORS as $field => $default) {
                if (! isset($payload[$field]) || trim((string) ($payload[$field] ?? "")) === "") {
                    $payload[$field] = $default;
                }
            }
        }

        if (array_key_exists("add_ons_features", $validated)) {
            $payload["add_ons_features"] = AddOnsHelper::normalizeFeatureValues(
                self::parseAddOnInput($validated["add_ons_features"]),
            );
        } elseif ($event === null) {
            $payload["add_ons_features"] = AddOnEnum::defaultFeatureValues();
        }

        if (array_key_exists("add_ons_modules", $validated)) {
            $payload["add_ons_modules"] = AddOnsHelper::normalizeModuleValues(
                self::parseAddOnInput($validated["add_ons_modules"]),
            );
        } elseif ($event === null) {
            $payload["add_ons_modules"] = AddOnEnum::defaultModuleValues();
        }

        if (array_key_exists("add_ons_config", $validated)) {
            $features = array_key_exists("add_ons_features", $payload)
                ? $payload["add_ons_features"]
                : ($event instanceof Event ? AddOnsHelper::featureValues($event) : AddOnEnum::featureValues());

            $payload["add_ons_config"] = self::buildAddOnsConfig(
                $features,
                is_array($validated["add_ons_config"]) ? $validated["add_ons_config"] : [],
            );
        }

        return $payload;
    }

    /**
     * @param Request $request
     * @return array<string, UploadedFile|null>
     */
    private static function filesFromRequest(Request $request): array
    {
        $files = [];

        foreach (EventBrandingSupport::FILE_FIELDS as $field) {
            $files[$field] = $request->file($field);
        }

        return $files;
    }

    /**
     * @param mixed $input
     * @return list<string>
     */
    public static function parseAddOnInput(mixed $input): array
    {
        if (is_array($input)) {
            return AddOnsHelper::parseList($input);
        }

        if (is_string($input)) {
            $decoded = json_decode($input, true);

            if (is_array($decoded)) {
                return AddOnsHelper::parseList($decoded);
            }

            return AddOnsHelper::parseList($input);
        }

        return [];
    }

    /**
     * @param list<string> $enabledFeatures
     * @param array<string, mixed> $rawConfig
     * @return array<string, array<string, string|null>>
     */
    public static function buildAddOnsConfig(array $enabledFeatures, array $rawConfig): array
    {
        $config = [];

        foreach (AddOnEnum::features() as $feature) {
            if (! in_array($feature->value, $enabledFeatures, true)) {
                continue;
            }

            if (! $feature->hasFeatureConfiguration()) {
                continue;
            }

            $rows = $rawConfig[$feature->value] ?? [];

            if (! is_array($rows)) {
                continue;
            }

            $rawAssoc = [];

            foreach ($rows as $key => $value) {
                if (is_array($value) && array_key_exists("key", $value)) {
                    $rowKey = trim((string) ($value["key"] ?? ""));

                    if ($rowKey === "") {
                        continue;
                    }

                    $rawAssoc[$rowKey] = $value["value"] ?? "";

                    continue;
                }

                $rowKey = trim((string) $key);

                if ($rowKey === "") {
                    continue;
                }

                $rawAssoc[$rowKey] = $value;
            }

            if ($rawAssoc === []) {
                continue;
            }

            $assoc = match ($feature) {
                AddOnEnum::FEATURES_MAILING => self::normalizeMailingConfig($rawAssoc),
                default => array_map(static fn ($value) => (string) $value, $rawAssoc),
            };

            if (isset($assoc["password"]) && $assoc["password"] !== "") {
                $assoc["password"] = Crypt::encryptString((string) $assoc["password"]);
            }

            $finalAssoc = [];

            foreach ($assoc as $key => $value) {
                $value = (string) $value;
                $finalAssoc[$key] = $value !== "" ? $value : null;
            }

            if ($finalAssoc !== []) {
                $config[$feature->value] = $finalAssoc;
            }
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $rawAssoc
     * @return array<string, string>
     */
    private static function normalizeMailingConfig(array $rawAssoc): array
    {
        $assoc = [];

        foreach ($rawAssoc as $key => $value) {
            $value = (string) $value;

            switch ($key) {
                case "MAIL_HOST":
                    $assoc["host"] = $value;
                    break;
                case "MAIL_PORT":
                    $assoc["port"] = $value;
                    break;
                case "MAIL_USERNAME":
                    $assoc["username"] = $value;
                    break;
                case "MAIL_PASSWORD":
                    $assoc["password"] = $value;
                    break;
                case "MAIL_FROM_ADDRESS":
                    $assoc["from_address"] = $value;
                    break;
                case "MAIL_FROM_NAME":
                    $assoc["from_name"] = $value;
                    break;
                case "MAIL_ENCRYPTION":
                    $assoc["encryption"] = $value;
                    break;
                default:
                    $assoc[$key] = $value;
                    break;
            }
        }

        return $assoc;
    }
}
