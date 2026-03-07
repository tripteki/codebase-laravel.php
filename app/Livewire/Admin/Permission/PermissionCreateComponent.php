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

class PermissionCreateComponent extends Component
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
                $tenant ? $tenant->unique("permissions", "name") : Rule::unique("permissions", "name"),
            ],
            "guard_name" => "required|string|max:255",
        ];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::PERMISSION_CREATE->value);

        $data = $this->validate();

        DB::beginTransaction();

        try {
            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                $tenant = Tenant::find($this->currentTenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            Permission::query()->create($data);

            if (config("tenancy.is_tenancy") && filled($this->currentTenantId)) {
                tenancy()->end();
            }

            DB::commit();

            session()->flash("message", __("module_permission.permission_created_successfully"));

            return redirect()->to(tenant_routes("admin.permissions.index"));
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_permission.permission_created_failed"));

            return redirect()->to(tenant_routes("admin.permissions.create"));
        }
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.permission.create")
            ->layout("layouts.app", [
                "title" => __("module_permission.create_title"),
                "showSidebar" => true,
            ]);
    }
}
