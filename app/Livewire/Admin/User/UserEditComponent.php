<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use Src\V1\Api\Acl\Models\Role;
use Src\V1\Api\User\Enums\PermissionEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Component;

class UserEditComponent extends Component
{
    /**
     * @var \App\Models\User
     */
    public User $user;

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
     * @param \App\Models\User $user
     * @return void
     */
    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = (string) $user->name;
        $this->email = (string) $user->email;
        $this->roles = $user->roles->pluck("id")->toArray();
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email," . $this->user->id,
            "password" => "nullable|string|min:8|confirmed",
            "roles" => "nullable|array",
            "roles.*" => "exists:roles,id",
        ];
    }

    /**
     * Persist user updates.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::USER_UPDATE->value);

        $data = $this->validate();

        $payload = [
            "name" => $data["name"],
            "email" => $data["email"],
        ];

        if (! empty($data["password"])) {
            $payload["password"] = Hash::make($data["password"]);
        }

        DB::beginTransaction();

        try {
            $roles = $data["roles"] ?? [];

            $this->user->update($payload);

            $this->user->syncRoles($roles);

            DB::commit();

            session()->flash("message", __("module_user.user_updated"));

            return redirect()->route("admin.users.index");
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_user.user_updated_failed"));

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
        $availableRoles = Role::query()
            ->orderBy("name")
            ->get();

        return view("livewire.admin.user.edit", [
            "user" => $this->user,
            "availableRoles" => $availableRoles,
        ])->layout("layouts.app", [
            "title" => __("module_user.edit_title"),
        ]);
    }
}
