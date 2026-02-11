<?php

namespace App\Livewire\Admin\Permission;

use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PermissionIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = Permission::class;

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
        return Permission::query()
            ->select("id", "name", "guard_name", "created_at");
    }

    /**
     * Custom view for modals.
     */
    public function customView(): string
    {
        return "livewire.admin.permission.partials.delete-modal";
    }

    /**
     * Columns definition.
     *
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        return [
            Column::make(__("module_base.column_name"), "name")
                ->sortable()
                ->searchable()
                ->label(function (Permission $row) {
                    return view("livewire.admin.permission.partials.permission-info", [
                        "permission" => $row,
                    ]);
                })
                ->html(),

            Column::make(__("module_permission.column_name"), "name")
                ->searchable()
                ->hideIf(true),

            Column::make(__("module_permission.column_guard_name"), "guard_name")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<span class="text-gray-900 dark:text-gray-200">' . e($value) . '</span>';
                })
                ->html(),

            Column::make(__("module_base.column_created_at"), "created_at")
                ->sortable()
                ->format(function ($value) {
                    $formatted = $value ? $value->format("Y-m-d H:i") : "";

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($formatted) . '</span>';
                })
                ->html(),

            Column::make(__("module_base.column_actions"))
                ->label(function (Permission $row) {
                    return view("livewire.admin.permission.partials.actions", [
                        "permission" => $row,
                    ]);
                })
                ->html(),
        ];
    }

    /**
     * Open delete confirmation modal.
     *
     * @param int|string $permissionId
     * @return void
     */
    public function confirmDelete($permissionId): void
    {
        $permission = Permission::query()->findOrFail($permissionId);

        $this->dispatch("open-delete-modal", [
            "permissionId" => $permissionId,
            "permissionName" => $permission->name,
        ]);
    }

    /**
     * Delete a permission.
     *
     * @param array|int $data
     * @return void
     */
    public function deletePermission($data): void
    {
        $this->authorize(PermissionEnum::PERMISSION_DELETE->value);

        $permissionId = is_array($data) ? ($data["permissionId"] ?? null) : $data;

        if (! $permissionId) {
            return;
        }

        DB::beginTransaction();

        try {
            $permission = Permission::query()->findOrFail($permissionId);

            $permission->delete();

            DB::commit();

            session()->flash("message", __("module_permission.permission_deleted_successfully"));
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_permission.permission_deleted_failed"));
        }
    }
}
