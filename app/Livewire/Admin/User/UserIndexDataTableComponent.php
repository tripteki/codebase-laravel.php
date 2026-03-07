<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Src\V1\Api\User\Enums\PermissionEnum;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class UserIndexDataTableComponent extends DataTableComponent
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
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
            ->setColumnSelectDisabled()
            ->setDefaultSort("created_at", "desc")
            ->setFiltersEnabled()
            ->setAdditionalSelects(array_values(array_filter([
                "users.id",
                "users.name",
                "users.email",
                "users.email_verified_at",
                config("tenancy.is_tenancy") ? "users.tenant_id" : null,
                "users.created_at",
            ])));
    }

    /**
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Filter>
     */
    public function filters(): array
    {
        $roleOptions = [ "" => "All" ];

        $rolesQuery = Role::query()
            ->where("guard_name", GuardEnum::WEB->value)
            ->orderBy("name");

        $currentUser = auth()->user();
        if ($currentUser && ! $currentUser->hasRole(RoleEnum::SUPERADMIN->value)) {
            $rolesQuery->where("name", "!=", RoleEnum::SUPERADMIN->value);
        }

        foreach ($rolesQuery->get() as $role) {
            $roleOptions[$role->name] = ucfirst($role->name);
        }

        return [
            SelectFilter::make(__("module_user.roles"), "role")
                ->options($roleOptions)
                ->filter(function (Builder $builder, string $value): void {
                    if ($value !== "") {
                        $builder->whereHas("roles", fn ($q) => $q->where("name", $value));
                    }
                }),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function builder(): Builder
    {
        $query = User::query()
            ->with("roles", "profile")
            ->select("id", "name", "email", "email_verified_at", "created_at");

        if (config("tenancy.is_tenancy")) {
            $query->with("tenant.domains")->addSelect("tenant_id");
        }

        $currentUser = auth()->user();

        if (! $currentUser) {
            return $query->whereRaw('1 = 0');
        }

        if ($currentUser->hasRole(RoleEnum::SUPERADMIN->value)) {
            return $query;
        }

        return $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', RoleEnum::SUPERADMIN->value);
        });
    }

    /**
     * @return string
     */
    public function customView(): string
    {
        return "livewire.admin.user.partials.delete-modal";
    }

    /**
     * @return array<int, \Rappasoft\LaravelLivewireTables\Views\Column>
     */
    public function columns(): array
    {
        $columns = [
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
                        return '<span class="text-gray-500 dark:text-gray-400">-</span>';
                    }

                    $displayRoles = $roles->take(2);
                    $badges = $displayRoles->map(function ($role) {
                        $name = e($role->name);
                        return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">' . $name . '</span>';
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

        if (config("tenancy.is_tenancy")) {
            array_splice($columns, 3, 0, [
                Column::make(__("module_user.tenant"), "tenant_id")
                    ->label(function (User $row) {
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

    /**
     * @param int|string $userId
     * @return void
     */
    public function confirmDelete($userId): void
    {
        $user = User::query()->with("profile")->findOrFail($userId);
        $displayName = $user->profile?->full_name ?? $user->name;

        $this->dispatch("open-delete-modal", [
            "userId" => $userId,
            "userName" => $displayName,
        ]);
    }

    /**
     * @param array|int $data
     * @return void
     */
    public function deleteUser($data): void
    {
        $userId = is_array($data) ? ($data["userId"] ?? null) : $data;

        if (! $userId) {
            return;
        }

        $user = User::query()->findOrFail($userId);
        $this->authorize(PermissionEnum::USER_DELETE->value, $user);

        DB::beginTransaction();

        try {
            $user->delete();

            DB::commit();

            session()->flash("message", __("module_user.user_deleted_successfully"));
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_user.user_deleted_failed"));
        }
    }
}
