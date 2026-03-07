<?php

namespace App\Livewire\Admin\Role;

use App\Models\Tenant;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
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
     * @var string|null
     */
    public $currentTenantId = null;

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
        $this->currentTenantId = config("tenancy.is_tenancy") && tenant() ? tenant()->id : null;
    }

    /**
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
        $tenant = config("tenancy.is_tenancy") ? tenant() : null;

        return [
            "name" => [
                "required",
                "string",
                "max:255",
                $tenant ? $tenant->unique("roles", "name")->ignore($this->role->id) : Rule::unique("roles", "name")->ignore($this->role->id),
            ],
            "guard_name" => "required|string|max:255",
            "permissions" => "nullable|array",
            "permissions.*" => [
                $tenant ? $tenant->exists("permissions", "id") : "exists:permissions,id",
            ],
        ];
    }

    /**
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
            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                $tenant = Tenant::find($this->currentTenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            $this->role->update($data);
            $this->role->syncPermissions($permissions);

            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                tenancy()->end();
            }

            DB::commit();

            session()->flash("message", __("module_role.role_updated"));

            return redirect()->to(tenant_routes("admin.roles.index"));
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_role.role_updated_failed"));

            return redirect()->to(tenant_routes("admin.roles.edit", $this->role));
        }
    }

    /**
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
            "showSidebar" => true,
        ]);
    }
}
