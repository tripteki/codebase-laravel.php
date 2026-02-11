<?php

namespace App\Livewire\Admin\Permission;

use Src\V1\Api\Acl\Enums\GuardEnum;
use Src\V1\Api\Acl\Enums\PermissionEnum;
use Src\V1\Api\Acl\Models\Permission;
use Illuminate\Support\Facades\DB;
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
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->guard_name = GuardEnum::WEB->value;
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "name" => "required|string|max:255|unique:permissions,name",
            "guard_name" => "required|string|max:255",
        ];
    }

    /**
     * Persist a new permission.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::PERMISSION_CREATE->value);

        $data = $this->validate();

        DB::beginTransaction();

        try {
            Permission::query()->create($data);

            DB::commit();

            session()->flash("message", __("module_permission.permission_created_successfully"));

            return redirect()->route("admin.permissions.index");
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_permission.permission_created_failed"));

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
        return view("livewire.admin.permission.create")
            ->layout("layouts.app", [
                "title" => __("module_permission.create_title"),
            ]);
    }
}
