<?php

namespace Modules\Event\App\Repositories;

use App\Exports\AdminArrayExport;
use App\Imports\AdminArrayImport;
use App\Models\User;
use App\Repositories\Repository as BaseRepository;
use App\Support\AdminSpreadsheetSupport;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Modules\Event\App\Models\Event;
use Modules\Event\App\Services\EventBootstrapService;
use Modules\Event\App\Support\AddOnsHelper;
use Modules\Event\App\Support\EventBrandingSupport;
use Modules\Event\App\Support\EventFormSupport;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class EventAdminRepository extends BaseRepository
{
    use AdminSpreadsheetSupport;

    /**
     * @return LengthAwarePaginator
     */
    public function all(): LengthAwarePaginator
    {
        return parent::accessAll(
            fn () => Event::query(),
            sortables: [
                AllowedSort::field("id"),
                AllowedSort::field("created_at"),
                AllowedSort::field("updated_at"),
                AllowedSort::callback("title", function ($query, bool $descending): void {
                    $direction = $descending ? "desc" : "asc";
                    $driver = $query->getConnection()->getDriverName();

                    if ($driver === "mysql") {
                        $query->orderByRaw(
                            "JSON_UNQUOTE(JSON_EXTRACT(`data`, '$.title')) {$direction}",
                        );

                        return;
                    }

                    if ($driver === "sqlite") {
                        $query->orderByRaw(
                            "json_extract(`data`, '$.title') {$direction}",
                        );

                        return;
                    }

                    $query->orderBy("id", $direction);
                }),
            ],
            defaultSorts: ["-created_at"],
            filterables: [
                AllowedFilter::callback("q", function ($query, $value): void {
                    $term = trim((string) $value);

                    if ($term === "") {
                        return;
                    }

                    $query->where(function ($nested) use ($term, $query): void {
                        $nested->where("id", "like", "%{$term}%");

                        $driver = $query->getConnection()->getDriverName();

                        if ($driver === "mysql") {
                            $nested->orWhereRaw(
                                "JSON_UNQUOTE(JSON_EXTRACT(`data`, '$.title')) LIKE ?",
                                ["%{$term}%"],
                            );

                            return;
                        }

                        if ($driver === "sqlite") {
                            $nested->orWhereRaw(
                                "json_extract(`data`, '$.title') LIKE ?",
                                ["%{$term}%"],
                            );
                        }
                    });
                }),
                AllowedFilter::partial("id"),
            ],
            defaultFilters: [],
        );
    }

    /**
     * @param string $id
     * @return Event
     */
    public function get(string $id): Event
    {
        return parent::accessGet(
            fn () => Event::query()->findOrFail($id),
        );
    }

    /**
     * @param string $id
     * @param array<string, mixed> $payload
     * @param array<string, UploadedFile|null> $files
     * @return Event
     */
    public function create(string $id, array $payload, array $files = []): Event
    {
        $event = new Event;
        $event->setAttribute("id", $id);

        $copyrightMode = array_key_exists("copyright_mode", $payload)
            ? (string) $payload["copyright_mode"]
            : null;
        unset($payload["copyright_mode"]);

        foreach ($payload as $key => $value) {
            $event->setAttribute($key, $value);
        }

        if ($copyrightMode !== null) {
            EventBrandingSupport::applyCopyrightMode($event, $copyrightMode);
        }

        foreach (EventBrandingSupport::DEFAULT_COLORS as $field => $default) {
            $current = $event->getAttribute($field);

            if ($current === null || trim((string) $current) === "") {
                $event->setAttribute($field, $default);
            }
        }

        EventBrandingSupport::applyFiles($event, $files);
        $event->save();

        app(EventBootstrapService::class)->bootstrap(
            Event::query()->findOrFail($id),
        );

        return Event::query()->findOrFail($id);
    }

    /**
     * @param string $id
     * @param array<string, mixed> $payload
     * @param array<string, UploadedFile|null> $files
     * @return Event
     */
    public function update(string $id, array $payload, array $files = []): Event
    {
        return parent::mutateUpdate(function () use ($id, $payload, $files): Event {
            $event = Event::query()->findOrFail($id);
            $previousRawModules = AddOnsHelper::parseList(
                $event->getAttribute("add_ons_modules"),
            );

            if (isset($payload["add_ons_config"])) {
                $payload["add_ons_config"] = $this->mergeAddOnsConfig(
                    $event,
                    $payload["add_ons_config"],
                );
            }

            $copyrightMode = array_key_exists("copyright_mode", $payload)
                ? (string) $payload["copyright_mode"]
                : null;
            unset($payload["copyright_mode"]);

            foreach ($payload as $key => $value) {
                $event->setAttribute($key, $value);
            }

            if ($copyrightMode !== null) {
                EventBrandingSupport::applyCopyrightMode($event, $copyrightMode);
            }

            $event->setAttribute(
                "add_ons_config",
                AddOnsHelper::pruneConfig(
                    AddOnsHelper::config($event),
                    AddOnsHelper::featureValues($event),
                ),
            );

            EventBrandingSupport::applyFiles($event, $files);
            $event->save();

            $freshEvent = Event::query()->findOrFail($id);
            $bootstrapService = app(EventBootstrapService::class);

            $bootstrapService->syncBrandingSettings($freshEvent);
            $bootstrapService->syncNewlyEnabledModules($freshEvent, $previousRawModules);

            return $freshEvent;
        });
    }

    /**
     * @param string $id
     * @return Event
     */
    public function delete(string $id): Event
    {
        return parent::mutateDelete(function () use ($id): Event {
            $event = Event::query()->findOrFail($id);
            EventBrandingSupport::deleteFiles($event);
            $event->delete();

            return $event;
        });
    }

    /**
     * @param string $path
     * @return array{imported: int, skipped: int}
     */
    public function importFromFile(string $path): array
    {
        $rows = $this->readAdminImport(new AdminArrayImport, $path);
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                $skipped++;

                continue;
            }

            $normalized = [];

            foreach ($row as $key => $value) {
                $normalized[strtolower(trim((string) $key))] = is_string($value) ? trim($value) : $value;
            }

            $slug = $this->importColumnValue($normalized, "slug", "event.import.column.slug");
            $title = $this->importColumnValue($normalized, "title", "event.import.column.title");

            if ($slug === null || $title === null) {
                $skipped++;

                continue;
            }

            if (! preg_match("/^[a-z0-9]+(?:-[a-z0-9]+)*$/", $slug)) {
                $skipped++;

                continue;
            }

            if (Event::query()->where("id", $slug)->exists()) {
                $skipped++;

                continue;
            }

            $features = EventFormSupport::parseAddOnInput(
                $this->importColumnValue($normalized, "add_ons_features", "event.import.column.add_ons_features"),
            );
            $modules = EventFormSupport::parseAddOnInput(
                $this->importColumnValue($normalized, "add_ons_modules", "event.import.column.add_ons_modules"),
            );

            $this->create($slug, array_filter([
                "title" => $title,
                "description" => $this->importColumnValue($normalized, "description", "event.import.column.description"),
                "primary_color" => $this->importColumnValue($normalized, "primary_color", "event.import.column.primary_color"),
                "secondary_color" => $this->importColumnValue($normalized, "secondary_color", "event.import.column.secondary_color"),
                "tertiary_color" => $this->importColumnValue($normalized, "tertiary_color", "event.import.column.tertiary_color"),
                "add_ons_features" => $features !== [] ? AddOnsHelper::normalizeFeatureValues($features) : null,
                "add_ons_modules" => $modules !== [] ? AddOnsHelper::normalizeModuleValues($modules) : null,
            ], static fn ($value) => $value !== null && $value !== ""));

            $imported++;
        }

        return [
            "imported" => $imported,
            "skipped" => $skipped,
        ];
    }

    /**
     * @param string $type
     * @param array<string, mixed> $filters
     * @return string
     */
    public function exportToFile(string $type = "csv", array $filters = []): string
    {
        $type = in_array($type, ["csv", "xls", "xlsx"], true) ? $type : "csv";
        $filename = "events_export_".now()->timestamp.".".$type;
        $relativePath = "exports/".$filename;

        Storage::disk("public")->makeDirectory("exports");

        $headings = [
            __("event.export.column.slug"),
            __("event.export.column.title"),
            __("event.export.column.description"),
            __("event.export.column.primary_color"),
            __("event.export.column.secondary_color"),
            __("event.export.column.tertiary_color"),
            __("event.export.column.add_ons_features"),
            __("event.export.column.add_ons_modules"),
            __("event.export.column.created_at"),
            __("event.export.column.updated_at"),
        ];

        $query = Event::query();
        $this->applyExportFilters($query, $filters);

        $rows = $query->orderBy("created_at")->get()->map(fn (Event $event): array => [
            (string) $event->getKey(),
            (string) ($event->getAttribute("title") ?? $event->getKey()),
            $event->getAttribute("description"),
            $event->getAttribute("primary_color"),
            $event->getAttribute("secondary_color"),
            $event->getAttribute("tertiary_color"),
            implode(", ", AddOnsHelper::featureValues($event)),
            implode(", ", AddOnsHelper::moduleValues($event)),
            $event->created_at?->toIso8601String(),
            $event->updated_at?->toIso8601String(),
        ])->all();

        $this->storeAdminExport(
            new AdminArrayExport($rows, $headings, __("event.export.sheet_name")),
            $relativePath,
            $type,
        );

        return Storage::disk("public")->path($relativePath);
    }

    /**
     * @param Event $event
     * @param array<string, array<string, string|null>> $incoming
     * @return array<string, array<string, string|null>>
     */
    private function mergeAddOnsConfig(Event $event, array $incoming): array
    {
        $existing = AddOnsHelper::config($event);
        $merged = $existing;

        foreach ($incoming as $feature => $rows) {
            if (! is_array($rows)) {
                continue;
            }

            $merged[$feature] = array_merge($existing[$feature] ?? [], $rows);
        }

        foreach ($merged as $feature => $rows) {
            if (! is_array($rows)) {
                continue;
            }

            if (
                array_key_exists("password", $rows)
                && ($rows["password"] === null || $rows["password"] === "")
                && isset($existing[$feature]["password"])
            ) {
                $merged[$feature]["password"] = $existing[$feature]["password"];
            }
        }

        return $merged;
    }

    /**
     * @param int $limit
     * @return array{labels: list<string>, series: list<int>}
     */
    public function overview(int $limit = 12): array
    {
        $events = Event::query()
            ->orderByDesc("created_at")
            ->limit($limit)
            ->get();

        $rows = [];

        foreach ($events as $event) {
            $eventId = (string) $event->getKey();
            $title = trim((string) ($event->getAttribute("title") ?? $eventId));

            if ($title === "") {
                $title = $eventId;
            }

            $rows[] = [
                "label" => $title,
                "total" => User::query()
                    ->where("tenant_id", $eventId)
                    ->whereNull("deleted_at")
                    ->count(),
            ];
        }

        usort(
            $rows,
            static fn (array $left, array $right): int => $right["total"] <=> $left["total"],
        );

        return [
            "labels" => array_column($rows, "label"),
            "series" => array_map(
                static fn (array $row): int => (int) $row["total"],
                $rows,
            ),
        ];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array<string, mixed> $filters
     * @return void
     */
    protected function applyExportFilters($query, array $filters): void
    {
        $search = trim((string) ($filters["q"] ?? ""));

        if ($search !== "") {
            $query->where(function ($nested) use ($search, $query): void {
                $nested->where("id", "like", "%{$search}%");

                $driver = $query->getConnection()->getDriverName();

                if ($driver === "mysql") {
                    $nested->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(`data`, '$.title')) LIKE ?",
                        ["%{$search}%"],
                    );

                    return;
                }

                if ($driver === "sqlite") {
                    $nested->orWhereRaw(
                        "json_extract(`data`, '$.title') LIKE ?",
                        ["%{$search}%"],
                    );
                }
            });
        }

        $id = trim((string) ($filters["id"] ?? ""));

        if ($id !== "") {
            $query->where("id", "like", "%{$id}%");
        }
    }
}
