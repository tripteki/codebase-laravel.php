<?php

namespace App\Livewire\Admin\Activity;

use Src\V1\Api\Log\Enums\PermissionEnum;
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
     * Configure the datatable.
     *
     * @return void
     */
    public function configure(): void
    {
        $this->setPrimaryKey("id");

        $this
            ->setPerPageAccepted([ 5, 10, 25, 50, ])
            ->setPerPage(10)
            ->setPaginationEnabled()
            ->setSearchEnabled()
            ->setColumnSelectDisabled();
    }

    /**
     * Base query for the datatable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function builder(): Builder
    {
        return Activity::query()
            ->with(["causer", "subject"])
            ->select("id", "log_name", "description", "subject_type", "subject_id", "causer_type", "causer_id", "properties", "created_at");
    }

    /**
     * Columns definition.
     *
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        return [
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
                        return '<span class="text-gray-500 dark:text-gray-400">—</span>';
                    }

                    $causerName = $row->causer instanceof \App\Models\User ? $row->causer->name : (string) $row->causer_id;

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($causerName) . '</span>';
                })
                ->html(),

            Column::make(__("module_activity.subject_type"), "subject_type")
                ->sortable()
                ->format(function ($value) {
                    if (! $value) {
                        return '<span class="text-gray-500 dark:text-gray-400">—</span>';
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
    }
}
