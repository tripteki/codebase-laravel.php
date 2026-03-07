<?php

namespace App\Livewire\Admin\Permission;

use App\Models\Tenant;
use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class PermissionEditComponent extends Component
{
    /**
     * @var \Src\V1\Api\Acl\Models\Permission
     */
    public Permission $permission;

    /**
     * @var string
     */
    public $name = "";

    /**
     * @var string
     */
    public $guard_name = "";

    /**
     * @var string|null
     */
    public $currentTenantId = null;

    /**
     * @param \Src\V1\Api\Acl\Models\Permission $permission
     * @return void
     */
    public function mount(Permission $permission): void
    {
        $this->permission = $permission;
        $this->name = (string) $permission->name;
        $this->guard_name = (string) $permission->guard_name;
        $this->currentTenantId = config("tenancy.is_tenancy") && tenant() ? tenant()->id : null;
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
                $tenant ? $tenant->unique("permissions", "name")->ignore($this->permission->id) : Rule::unique("permissions", "name")->ignore($this->permission->id),
            ],
            "guard_name" => "required|string|max:255",
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::PERMISSION_UPDATE->value);

        $data = $this->validate();

        DB::beginTransaction();

        try {
            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                $tenant = Tenant::find($this->currentTenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            $this->permission->update($data);

            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                tenancy()->end();
            }

            DB::commit();

            session()->flash("message", __("module_permission.permission_updated"));

            return redirect()->to(tenant_routes("admin.permissions.index"));
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_permission.permission_updated_failed"));

            return redirect()->to(tenant_routes("admin.permissions.edit", $this->permission));
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.permission.edit", [
            "permission" => $this->permission,
        ])->layout("layouts.app", [
            "title" => __("module_permission.edit_title"),
            "showSidebar" => true,
        ]);
    }
}
