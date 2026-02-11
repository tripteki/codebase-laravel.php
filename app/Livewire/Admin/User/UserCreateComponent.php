<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\User\Enums\PermissionEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;

class UserCreateComponent extends Component
{
    /**
     * @var string
     */
    public $name = "";

    /**
     * @var string
     */
    public $email = "";

    /**
     * @var string
     */
    public $password = "";

    /**
     * @var string
     */
    public $password_confirmation = "";

    /**
     * @var array<int|string>
     */
    public $roles = [];

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8|confirmed",
            "roles" => "nullable|array",
            "roles.*" => "exists:roles,id",
        ];
    }

    /**
     * Persist a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::USER_CREATE->value);

        $data = $this->validate();

        DB::beginTransaction();

        try {
            $roles = $data["roles"] ?? [];
            unset($data["roles"], $data["password_confirmation"]);

            $data["password"] = Hash::make($data["password"]);

            $user = User::query()->create($data);
            $user->markEmailAsVerified();

            if (! empty($roles)) {
                $user->syncRoles($roles);
            }

            DB::commit();

            session()->flash("message", __("module_user.user_created_successfully"));

            return redirect()->route("admin.users.index");
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_user.user_created_failed"));

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
        $availableRoles = Role::query()
            ->orderBy("name")
            ->get();

        return view("livewire.admin.user.create", [
            "availableRoles" => $availableRoles,
        ])->layout("layouts.app", [
            "title" => __("module_user.create_title"),
        ]);
    }
}
