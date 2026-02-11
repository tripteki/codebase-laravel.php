<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Src\V1\Api\User\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class UserIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = User::class;

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
        return User::query()
            ->with("roles")
            ->select("id", "name", "email", "email_verified_at", "created_at");
    }

    /**
     * Custom view for modals.
     */
    public function customView(): string
    {
        return "livewire.admin.user.partials.delete-modal";
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
                ->label(function (User $row) {
                    return view("livewire.admin.user.partials.user-info", [
                        "user" => $row,
                    ]);
                })
                ->html(),

            Column::make(__("module_user.column_email"), "email")
                ->searchable()
                ->hideIf(true),

            Column::make(__("module_user.roles"), "roles")
                ->label(function (User $row) {
                    $roles = $row->roles;
                    $totalRoles = $roles->count();

                    if ($totalRoles === 0) {
                        return '<span class="text-gray-500 dark:text-gray-400">—</span>';
                    }

                    $displayRoles = $roles->take(2);
                    $badges = $displayRoles->map(function ($role) {
                        $name = e($role->name);
                        return '<span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">' . $name . '</span>';
                    })->join(' ');

                    if ($totalRoles > 2) {
                        $remaining = $totalRoles - 2;
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

            Column::make(__("module_base.column_status"))
                ->label(function (User $row) {
                    $verified = (bool) $row->email_verified_at;

                    return view("livewire.admin.user.partials.status", [
                        "verified" => $verified,
                    ]);
                }),

            Column::make(__("module_base.column_actions"))
                ->label(function (User $row) {
                    return view("livewire.admin.user.partials.actions", [
                        "user" => $row,
                    ]);
                })
                ->html(),
        ];
    }

    /**
     * Open delete confirmation modal.
     *
     * @param int|string $userId
     * @return void
     */
    public function confirmDelete($userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->dispatch("open-delete-modal", [
            "userId" => $userId,
            "userName" => $user->name,
        ]);
    }

    /**
     * Delete a user.
     *
     * @param array|int $data
     * @return void
     */
    public function deleteUser($data): void
    {
        $this->authorize(PermissionEnum::USER_DELETE->value);

        $userId = is_array($data) ? ($data["userId"] ?? null) : $data;

        if (! $userId) {
            return;
        }

        DB::beginTransaction();

        try {
            $user = User::query()->findOrFail($userId);

            $user->delete();

            DB::commit();

            session()->flash("message", __("module_user.user_deleted_successfully"));
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_user.user_deleted_failed"));
        }
    }
}
