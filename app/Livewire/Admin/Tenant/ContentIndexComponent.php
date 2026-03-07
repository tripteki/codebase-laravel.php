<?php

namespace App\Livewire\Admin\Tenant;

use App\Models\ContentTrans;
use App\Models\Tenant;
use App\Enum\Tenant\PermissionEnum;
use Locale as PhpLocale;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Src\V1\Api\I18N\Services\I18NService;

class ContentIndexComponent extends Component
{
    use WithFileUploads;

    /**
     * @var \App\Models\Tenant
     */
    public Tenant $tenant;

    /**
     * @var string|null
     */
    public ?string $activeLocale = null;

    /**
     * @var string|null
     */
    public ?string $activeLocaleLabel = null;

    /**
     * @var array
     */
    public array $contentModalRows = [];

    /**
     * @var array<int, TemporaryUploadedFile|null>
     */
    public array $contentValueFiles = [];

    /**
     * @param \App\Models\Tenant $tenant
     * @return void
     */
    public function mount(Tenant $tenant): void
    {
        $this->authorize(PermissionEnum::EVENT_VIEW->value);

        $this->tenant = $tenant;
    }

    /**
     * @param string $locale
     * @return void
     */
    #[On("openLocaleModal")]
    public function openLocaleModal(string $locale): void
    {
        $this->authorize(PermissionEnum::EVENT_UPDATE->value);

        $i18n = app(I18NService::class);
        $available = $i18n->availableLangs();
        if (! in_array($locale, $available, true)) {
            abort(404);
        }

        $this->resetValidation();

        $this->contentValueFiles = [];
        $this->activeLocale = $locale;
        $this->activeLocaleLabel = $this->localeDisplayName($locale);
        $this->contentModalRows = $this->buildContentModalRowsFromDb($locale);
        if ($this->contentModalRows === []) {
            $this->contentModalRows[] = $this->blankContentModalRow();
        }

        $this->dispatch("open-content-locale-modal", locale: $locale);
    }

    /**
     * @return array{id: null, group: string, key: string, value: string, value_kind: string}
     */
    protected function blankContentModalRow(): array
    {
        return [
            "id" => null,
            "group" => "",
            "key" => "",
            "value" => "",
            "value_kind" => "text",
        ];
    }

    /**
     * @param string $locale
     * @return array<int, array{id: string|null, group: string, key: string, value: string, value_kind: string}>
     */
    protected function buildContentModalRowsFromDb(string $locale): array
    {
        $tenantId = (string) $this->tenant->id;

        $items = ContentTrans::query()
            ->where("tenant_id", $tenantId)
            ->orderBy("group")
            ->orderBy("key")
            ->get();

        $list = [];
        foreach ($items as $item) {
            $value = "";
            if ($item->hasTranslation("value", $locale)) {
                $value = (string) ($item->getTranslation("value", $locale, false) ?? "");
            }

            $list[] = [
                "id" => (string) $item->getKey(),
                "group" => (string) $item->group,
                "key" => (string) $item->key,
                "value" => $value,
                "value_kind" => $this->inferContentValueKind($value),
            ];
        }

        return $list;
    }

    /**
     * @param string $storedValue
     * @return string
     */
    protected function inferContentValueKind(string $storedValue): string
    {
        return str_starts_with($storedValue, "tenant-contents/") ? "file" : "text";
    }

    /**
     * @return void
     */
    public function addContentRow(): void
    {
        $this->authorize(PermissionEnum::EVENT_UPDATE->value);

        if (! $this->activeLocale) {
            return;
        }

        $this->contentModalRows[] = $this->blankContentModalRow();
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeContentRow(int $index): void
    {
        $this->authorize(PermissionEnum::EVENT_UPDATE->value);

        if (! $this->activeLocale) {
            return;
        }

        $row = $this->contentModalRows[$index] ?? null;
        if ($row === null) {
            return;
        }

        $tenantId = (string) $this->tenant->id;
        if ($row["id"] !== null && $row["id"] !== "") {
            ContentTrans::query()
                ->where("tenant_id", $tenantId)
                ->whereKey((string) $row["id"])
                ->delete();
        }

        unset($this->contentModalRows[$index]);
        $this->contentModalRows = array_values($this->contentModalRows);

        $this->contentValueFiles = [];

        if ($this->contentModalRows === []) {
            $this->contentModalRows[] = $this->blankContentModalRow();
        }
    }

    /**
     * @return void
     */
    public function saveLocaleContent(): void
    {
        $this->authorize(PermissionEnum::EVENT_UPDATE->value);

        if (! $this->activeLocale) {
            return;
        }

        $this->resetValidation();

        $tenantId = (string) $this->tenant->id;
        $locale = $this->activeLocale;

        $hasRowValidationError = false;
        foreach ($this->contentModalRows as $i => $row) {
            $g = trim((string) ($row["group"] ?? ""));
            $k = trim((string) ($row["key"] ?? ""));
            $rowValueRaw = (string) ($row["value"] ?? "");
            $upload = $this->contentValueFiles[$i] ?? null;
            $hasUpload = $upload instanceof TemporaryUploadedFile;
            $kind = (($row["value_kind"] ?? "text") === "file") ? "file" : "text";

            if ($g === "" && $k === "" && trim($rowValueRaw) === "" && ! $hasUpload) {
                continue;
            }

            if ($g === "") {
                $this->addError("contentModalRows.$i.group", __("validation.required"));
                $hasRowValidationError = true;
            }
            if ($k === "") {
                $this->addError("contentModalRows.$i.key", __("validation.required"));
                $hasRowValidationError = true;
            }

            if ($kind === "file" && $g !== "" && $k !== "" && ! $hasUpload && trim($rowValueRaw) === "") {
                $this->addError("contentModalRows.$i.value", __("module_content.value_file_required"));
                $hasRowValidationError = true;
            }
        }

        if ($hasRowValidationError) {
            return;
        }

        $this->validate([
            "contentValueFiles.*" => ["nullable", "file", "max:10240"],
        ]);

        foreach ($this->contentModalRows as $i => $row) {
            $g = trim((string) ($row["group"] ?? ""));
            $k = trim((string) ($row["key"] ?? ""));
            $rowValueRaw = (string) ($row["value"] ?? "");
            $upload = $this->contentValueFiles[$i] ?? null;
            $hasUpload = $upload instanceof TemporaryUploadedFile;
            $kind = (($row["value_kind"] ?? "text") === "file") ? "file" : "text";

            if ($g === "" && $k === "" && trim($rowValueRaw) === "" && ! $hasUpload) {
                continue;
            }

            $id = $row["id"] ?? null;

            $previousFromDb = "";
            if ($id !== null && $id !== "") {
                $existingRow = ContentTrans::query()
                    ->where("tenant_id", $tenantId)
                    ->whereKey((string) $id)
                    ->first();
                if ($existingRow !== null) {
                    $previousFromDb = (string) ($existingRow->getTranslation("value", $locale, false) ?? "");
                }
            }

            if ($kind === "text") {
                $v = trim($rowValueRaw);
                if ($previousFromDb !== "" && str_starts_with($previousFromDb, "tenant-contents/") && $previousFromDb !== $v) {
                    Storage::disk("public")->delete($previousFromDb);
                }
            } elseif ($hasUpload) {
                $v = $upload->store("tenant-contents/" . $tenantId, "public");
                if ($previousFromDb !== "" && str_starts_with($previousFromDb, "tenant-contents/") && $previousFromDb !== $v) {
                    Storage::disk("public")->delete($previousFromDb);
                }
            } else {
                $v = trim($rowValueRaw);
            }

            if ($g === "" && $k === "" && $v === "") {
                continue;
            }

            $duplicateQuery = ContentTrans::query()
                ->where("tenant_id", $tenantId)
                ->where("group", $g)
                ->where("key", $k);

            if ($id !== null && $id !== "") {
                $duplicateQuery->where("id", "!=", $id);
            }

            if ($duplicateQuery->exists()) {
                $this->addError("contentModalRows.$i.key", __("module_content.duplicate_group_key"));

                return;
            }

            if ($id !== null && $id !== "") {
                $contentTrans = ContentTrans::query()
                    ->where("tenant_id", $tenantId)
                    ->whereKey((string) $id)
                    ->first();

                if ($contentTrans === null) {
                    continue;
                }

                $contentTrans->group = $g;
                $contentTrans->key = $k;
                $contentTrans->setTranslation("value", $locale, $v);
                $contentTrans->save();
            } else {
                $contentTrans = new ContentTrans();
                $contentTrans->tenant_id = $tenantId;
                $contentTrans->group = $g;
                $contentTrans->key = $k;
                $contentTrans->setTranslation("value", $locale, $v);
                $contentTrans->save();
            }
        }

        $this->contentValueFiles = [];
        $this->contentModalRows = $this->buildContentModalRowsFromDb($locale);
        if ($this->contentModalRows === []) {
            $this->contentModalRows[] = $this->blankContentModalRow();
        }

        $this->dispatch("content-locale-saved");
    }

    /**
     * @param string $code
     * @return string
     */
    protected function localeDisplayName(string $code): string
    {
        if (class_exists(PhpLocale::class)) {
            $name = PhpLocale::getDisplayName($code, app()->getLocale());
            if ($name !== "") {
                return $name;
            }
        }

        return $code;
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $tenantLabel = $this->tenant->getAttribute("title") ?: $this->tenant->id;

        return view("livewire.admin.tenant.content-index", [
            "tenant" => $this->tenant,
        ])
            ->layout("layouts.app", [
                "title" => __("module_content.module_title") . " - " . $tenantLabel,
                "showSidebar" => true,
            ]);
    }
}
