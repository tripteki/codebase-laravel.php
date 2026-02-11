<?php

namespace App\Livewire\Admin\Role;

use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class RoleCreateComponent extends Component
{
    /**
     * @var string
     */
    public $name = "";

    /**
     * @var string
     */
    public $guard_name = "";

    /**
     * @var array<int|string>
     */
    public $permissions = [];

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->guard_name = GuardEnum::WEB->value;
    }

    /**
     * Updated guard name handler.
     *
     * @return void
     */
    public function updatedGuardName(): void
    {
        $this->permissions = [];
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "name" => "required|string|max:255|unique:roles,name",
            "guard_name" => "required|string|max:255",
            "permissions" => "nullable|array",
            "permissions.*" => "exists:permissions,id",
        ];
    }

    /**
     * Persist a new role.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::ROLE_CREATE->value);

        $data = $this->validate();

        $permissions = $data["permissions"] ?? [];
        unset($data["permissions"]);

        DB::beginTransaction();

        try {
            $role = Role::query()->create($data);

            if (! empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            DB::commit();

            session()->flash("message", __("module_role.role_created_successfully"));

            return redirect()->route("admin.roles.index");
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_role.role_created_failed"));

            return redirect()->back();
        }
    }

    /**
     * Render the create view.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $availablePermissions = Permission::query()
            ->where("guard_name", $this->guard_name)
            ->orderBy("name")
            ->get();

        return view("livewire.admin.role.create", [
            "availablePermissions" => $availablePermissions,
        ])->layout("layouts.app", [
            "title" => __("module_role.create_title"),
        ]);
    }
}
