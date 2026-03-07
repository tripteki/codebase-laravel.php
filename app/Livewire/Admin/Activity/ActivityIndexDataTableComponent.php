<?php

namespace App\Livewire\Admin\Activity;

use App\Models\User;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Log\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ActivityIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = Activity::class;

    /**
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey("id");

        $table = config("activitylog.table_name");

        $this
            ->setPerPageAccepted([ 5, 10, 25, 50, ])
            ->setPerPage(10)
            ->setPaginationEnabled()
            ->setSearchEnabled()
            ->setColumnSelectDisabled()
            ->setDefaultSort("created_at", "desc")
            ->setAdditionalSelects(array_values(array_filter([
                "{$table}.id",
                config("tenancy.is_tenancy") ? "{$table}.tenant_id" : null,
                "{$table}.log_name",
                "{$table}.description",
                "{$table}.subject_type",
                "{$table}.subject_id",
                "{$table}.causer_type",
                "{$table}.causer_id",
                "{$table}.properties",
                "{$table}.created_at",
            ])));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function builder(): Builder
    {
        $query = Activity::query()
            ->with(["causer", "subject"])
            ->select("id", "log_name", "description", "subject_type", "subject_id", "causer_type", "causer_id", "properties", "created_at");

        if (config("tenancy.is_tenancy")) {
            $query->with("tenant.domains")->addSelect("tenant_id");
        }

        $currentUser = auth()->user();
        if (! $currentUser) {
            return $query->whereRaw("1 = 0");
        }

        if ($currentUser->hasRole(RoleEnum::SUPERADMIN->value)) {
            return $query;
        }

        if ($currentUser->hasRole(RoleEnum::ADMIN->value)) {
            if (! config("tenancy.is_tenancy")) {
                return $query;
            }

            return $currentUser->tenant_id
                ? $query->where("tenant_id", $currentUser->tenant_id)
                : $query->whereRaw("1 = 0");
        }

        if (config("tenancy.is_tenancy") && $currentUser->tenant_id) {
            return $query->where("causer_id", $currentUser->id)->where("tenant_id", $currentUser->tenant_id);
        }

        return $query->whereRaw("1 = 0");
    }

    /**
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        $columns = [
            Column::make(__("module_activity.description"), "description")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<span class="text-gray-900 dark:text-gray-200">' . e($value) . '</span>';
                })
                ->html(),

            Column::make(__("module_activity.log_name"), "log_name")
                ->searchable()
                ->hideIf(true),

            Column::make(__("module_activity.causer"), "causer")
                ->label(function (Activity $row) {
                    if (! $row->causer) {
                        return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                    }

                    $causerName = $row->causer instanceof User ? $row->causer->name : (string) $row->causer_id;

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($causerName) . '</span>';
                })
                ->html(),

            Column::make(__("module_activity.subject_type"), "subject_type")
                ->sortable()
                ->format(function ($value) {
                    if (! $value) {
                        return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                    }

                    $shortName = class_basename($value);

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($shortName) . '</span>';
                })
                ->html(),

            Column::make(__("module_activity.created_at"), "created_at")
                ->sortable()
                ->format(function ($value) {
                    $formatted = $value ? $value->format("Y-m-d H:i:s") : "";

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($formatted) . '</span>';
                })
                ->html(),

            Column::make(__("module_base.column_actions"))
                ->label(function (Activity $row) {
                    return view("livewire.admin.activity.partials.actions", [
                        "activity" => $row,
                    ]);
                })
                ->html(),
        ];

        if (config("tenancy.is_tenancy")) {
            array_splice($columns, 4, 0, [
                Column::make(__("module_activity.tenant"), "tenant_id")
                    ->sortable()
                    ->label(function (Activity $row) {
                        if (! $row->tenant_id) {
                            return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                        }

                        $tenant = $row->tenant;
                        if (! $tenant) {
                            return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">' . e($row->tenant_id) . '</span>';
                        }

                        $domain = $tenant->domains->first();
                        $displayText = $domain ? $domain->domain : $row->tenant_id;

                        return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">' . e($displayText) . '</span>';
                    })
                    ->html(),
            ]);
        }

        return $columns;
    }
}
