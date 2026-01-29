<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
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
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "password" => "required|string|min:8|confirmed",
        ];
    }

    /**
     * Persist a new user.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $data = $this->validate();

        DB::beginTransaction();

        try {
            $data["password"] = Hash::make($data["password"]);
            unset($data["password_confirmation"]);

            User::query()->create($data)?->markEmailAsVerified();

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
        return view("livewire.admin.user.create")
            ->layout("layouts.app", ["title" => __("module_user.create_title")]);
    }
}
