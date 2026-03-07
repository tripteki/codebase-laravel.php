<?php

namespace App\Livewire\Admin\Event;

use App\Models\Tenant;
use App\Enum\Tenant\PermissionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class EventIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey("id");
        $this
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage(10)
            ->setPaginationEnabled()
            ->setSearchEnabled()
            ->setColumnSelectDisabled()
            ->setDefaultSort("created_at", "desc")
            ->setAdditionalSelects(["tenants.id", "tenants.data", "tenants.created_at"]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function builder(): Builder
    {
        if (! auth()->user()?->can(PermissionEnum::EVENT_VIEW->value)) {
            return Tenant::query()->whereRaw("1 = 0");
        }

        return Tenant::query()->with("domains");
    }

    /**
     * @return string
     */
    public function customView(): string
    {
        return "livewire.admin.event.partials.delete-modal";
    }

    /**
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        return [
            Column::make(__("module_event.slug"), "id")
                ->label(fn (Tenant $row) => $row->id ? e($row->id) : '<span class="text-gray-500 dark:text-gray-400">-</span>')
                ->sortable()
                ->searchable()
                ->html(),

            Column::make(__("module_event.title"), "data->title")
                ->label(fn (Tenant $row) => $row->getAttribute("title") ? e($row->getAttribute("title")) : '<span class="text-gray-500 dark:text-gray-400">-</span>')
                ->sortable(function (Builder $query, string $direction): void {
                    $query->orderBy("tenants.data->title", $direction);
                })
                ->searchable(function (Builder $query, string $term): void {
                    $query->orWhere("tenants.data->title", "like", "%" . $term . "%");
                })
                ->html(),

            Column::make(__("module_event.domains"), "id")
                ->label(function (Tenant $row) {
                    $centralDomains = config("tenancy.central_domains", []);
                    $host = is_array($centralDomains) && isset($centralDomains[0]) ? $centralDomains[0] : "";

                    if (config("tenancy.type") === "path") {
                        $slug = (string) ($row->id ?? "");
                        if ($slug === "") {
                            return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                        }
                        $url = tenant_public_path_url($slug);
                        $label = e(tenant_public_path_label($slug));

                        return '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">'
                            . $label . '</a>';
                    }

                    $domains = $row->domains;
                    if ($domains->isEmpty()) {
                        return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                    }

                    $items = $domains->map(function ($d) use ($host) {
                        $domainPart = trim((string) $d->domain);
                        if ($domainPart === "") {
                            return '';
                        }
                        $displayHost = str_contains($domainPart, ".") ? $domainPart : $domainPart . "." . $host;
                        $url = tenant_public_domain_url($domainPart, $host);
                        $label = e($displayHost);

                        return '<a href="' . e($url) . '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">'
                            . $label . '</a>';
                    })->filter()->values();

                    if ($items->isEmpty()) {
                        return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                    }

                    return '<div class="flex flex-col gap-1">' . $items->join("") . '</div>';
                })
                ->sortable(function (Builder $query, string $direction): void {
                    $query->orderByRaw(
                        "(SELECT MIN(domains.domain) FROM domains WHERE domains.tenant_id = tenants.id) " . $direction
                    );
                })
                ->searchable(function (Builder $query, string $term): void {
                    $query->orWhereHas("domains", function (Builder $q) use ($term): void {
                        $q->where("domain", "like", "%" . $term . "%");
                    });
                })
                ->html(),

            Column::make(__("module_event.datetime"), "created_at")
                ->sortable()
                ->format(function ($value, Tenant $row) {
                    $startDate = (string) ($row->getAttribute("event_start_date") ?? "");
                    $endDate = (string) ($row->getAttribute("event_end_date") ?? "");
                    $startTime = (string) ($row->getAttribute("event_start_time") ?? "");
                    $endTime = (string) ($row->getAttribute("event_end_time") ?? "");

                    $hasAny = $startDate !== "" || $endDate !== "" || $startTime !== "" || $endTime !== "";
                    if (! $hasAny) {
                        return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                    }

                    $startDateText = $startDate !== "" ? e($startDate) : "-";
                    $startTimeText = $startTime !== "" ? e(\Carbon\Carbon::parse("today " . $startTime)->format("g:i A")) : "-";
                    $endDateText = $endDate !== "" ? e($endDate) : "-";
                    $endTimeText = $endTime !== "" ? e(\Carbon\Carbon::parse("today " . $endTime)->format("g:i A")) : "-";

                    return '<div class="text-xs text-gray-900 dark:text-gray-200">'
                        . '<div class="flex items-center gap-2">'
                            . '<span>' . $startDateText . '</span>'
                            . '<span>' . $startTimeText . '</span>'
                        . '</div>'
                        . '<div class="flex items-center gap-2 mt-0.5">'
                            . '<span>' . $endDateText . '</span>'
                            . '<span>' . $endTimeText . '</span>'
                        . '</div>'
                    . '</div>';
                })
                ->html(),

            Column::make(__("module_base.column_actions"))
                ->label(fn (Tenant $row) => view("livewire.admin.event.partials.actions", ["tenant" => $row]))
                ->html(),
        ];
    }

    /**
     * @param string $tenantId
     * @return void
     */
    public function confirmDelete(string $tenantId): void
    {
        $tenant = Tenant::query()->findOrFail($tenantId);
        $domain = $tenant->domains->first();
        $name = $tenant->getAttribute("title") ?: ($domain ? $domain->domain : $tenantId);
        $this->dispatch("open-delete-modal", [
            "tenantId" => $tenantId,
            "tenantName" => $name,
        ]);
    }

    /**
     * @param array|string $data
     * @return void
     */
    public function deleteTenant($data): void
    {
        $this->authorize(PermissionEnum::EVENT_DELETE->value);

        $tenantId = is_array($data) ? ($data["tenantId"] ?? null) : $data;
        if (! $tenantId) {
            return;
        }

        DB::beginTransaction();
        try {
            $tenant = Tenant::query()->findOrFail($tenantId);
            $tenant->delete();
            DB::commit();
            session()->flash("message", __("module_event.deleted_successfully"));
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash("error", $e->getMessage());
        }
    }
}
