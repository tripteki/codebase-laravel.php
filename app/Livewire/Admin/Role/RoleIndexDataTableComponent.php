<?php

namespace App\Livewire\Admin\Role;

use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class RoleIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = Role::class;

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
        return Role::query()
            ->with("permissions")
            ->select("id", "name", "guard_name", "created_at");
    }

    /**
     * Custom view for modals.
     */
    public function customView(): string
    {
        return "livewire.admin.role.partials.delete-modal";
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
                ->label(function (Role $row) {
                    return view("livewire.admin.role.partials.role-info", [
                        "role" => $row,
                    ]);
                })
                ->html(),

            Column::make(__("module_role.column_name"), "name")
                ->searchable()
                ->hideIf(true),

            Column::make(__("module_role.column_guard_name"), "guard_name")
                ->sortable()
                ->searchable()
                ->format(function ($value) {
                    return '<span class="text-gray-900 dark:text-gray-200">' . e($value) . '</span>';
                })
                ->html(),

            Column::make(__("module_role.permissions"), "permissions")
                ->label(function (Role $row) {
                    $permissions = $row->permissions;
                    $totalPermissions = $permissions->count();

                    if ($totalPermissions === 0) {
                        return '<span class="text-gray-500 dark:text-gray-400">—</span>';
                    }

                    $displayPermissions = $permissions->take(2);
                    $badges = $displayPermissions->map(function ($permission) {
                        $name = e($permission->name);
                        return '<span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">' . $name . '</span>';
                    })->join(' ');

                    if ($totalPermissions > 2) {
                        $remaining = $totalPermissions - 2;
                        $badges .= ' <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 dark:bg-gray-700 dark:text-gray-300">+' . e((string) $remaining) . '...</span>';
                    }

                    return '<div class="flex flex-wrap gap-1">' . $badges . '</div>';
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
                ->label(function (Role $row) {
                    return view("livewire.admin.role.partials.actions", [
                        "role" => $row,
                    ]);
                })
                ->html(),
        ];
    }

    /**
     * Open delete confirmation modal.
     *
     * @param int|string $roleId
     * @return void
     */
    public function confirmDelete($roleId): void
    {
        $role = Role::query()->findOrFail($roleId);

        $this->dispatch("open-delete-modal", [
            "roleId" => $roleId,
            "roleName" => $role->name,
        ]);
    }

    /**
     * Delete a role.
     *
     * @param array|int $data
     * @return void
     */
    public function deleteRole($data): void
    {
        $roleId = is_array($data) ? ($data["roleId"] ?? null) : $data;

        if (! $roleId) {
            return;
        }

        DB::beginTransaction();

        try {
            $role = Role::query()->findOrFail($roleId);

            $role->delete();

            DB::commit();

            session()->flash("message", __("module_role.role_deleted_successfully"));
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_role.role_deleted_failed"));
        }
    }
}
