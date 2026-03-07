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
     * @var string|null
     */
    public $currentTenantId = null;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->guard_name = GuardEnum::WEB->value;
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
                $tenant ? $tenant->unique("roles", "name") : Rule::unique("roles", "name"),
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
        $this->authorize(PermissionEnum::ROLE_CREATE->value);

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

            $role = Role::query()->create($data);

            if (! empty($permissions)) {
                $role->syncPermissions($permissions);
            }

            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                tenancy()->end();
            }

            DB::commit();

            session()->flash("message", __("module_role.role_created_successfully"));

            return redirect()->to(tenant_routes("admin.roles.index"));
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_role.role_created_failed"));

            return redirect()->to(tenant_routes("admin.roles.create"));
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

        return view("livewire.admin.role.create", [
            "availablePermissions" => $availablePermissions,
        ])->layout("layouts.app", [
            "title" => __("module_role.create_title"),
            "showSidebar" => true,
        ]);
    }
}
