<?php

namespace App\Livewire\Admin\Role;

use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;

class RoleEditComponent extends Component
{
    /**
     * @var \Src\V1\Api\Acl\Models\Role
     */
    public Role $role;

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
     * @param \Src\V1\Api\Acl\Models\Role $role
     * @return void
     */
    public function mount(Role $role): void
    {
        $this->role = $role;
        $this->name = (string) $role->name;
        $this->guard_name = (string) $role->guard_name;
        $this->permissions = $role->permissions->pluck("id")->toArray();
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
            "name" => "required|string|max:255|unique:roles,name," . $this->role->id,
            "guard_name" => "required|string|max:255",
            "permissions" => "nullable|array",
            "permissions.*" => "exists:permissions,id",
        ];
    }

    /**
     * Persist role updates.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::ROLE_UPDATE->value);

        $data = $this->validate();

        $permissions = $data["permissions"] ?? [];
        unset($data["permissions"]);

        DB::beginTransaction();

        try {
            $this->role->update($data);

            $this->role->syncPermissions($permissions);

            DB::commit();

            session()->flash("message", __("module_role.role_updated"));

            return redirect()->route("admin.roles.index");
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_role.role_updated_failed"));

            return redirect()->back();
        }
    }

    /**
     * Render the edit view.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $availablePermissions = Permission::query()
            ->where("guard_name", $this->guard_name)
            ->orderBy("name")
            ->get();

        return view("livewire.admin.role.edit", [
            "role" => $this->role,
            "availablePermissions" => $availablePermissions,
        ])->layout("layouts.app", [
            "title" => __("module_role.edit_title"),
        ]);
    }
}
