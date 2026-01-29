<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
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
            Column::make(__("module_user.column_name"), "name")
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

            Column::make(__("module_user.column_created_at"), "created_at")
                ->sortable()
                ->format(function ($value) {
                    $formatted = $value ? $value->format("Y-m-d H:i") : "";

                    return '<span class="text-gray-900 dark:text-gray-200">' . e($formatted) . '</span>';
                })
                ->html(),

            Column::make(__("module_user.column_status"))
                ->label(function (User $row) {
                    $verified = (bool) $row->email_verified_at;

                    return view("livewire.admin.user.partials.status", [
                        "verified" => $verified,
                    ]);
                }),

            Column::make(__("module_user.column_actions"))
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
