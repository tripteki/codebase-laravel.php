<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Profile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserSettingIndexPersonalComponent extends Component
{
    use WithFileUploads;

    /**
     * @var string|null
     */
    public $name = null;

    /**
     * @var string|null
     */
    public $email = null;

    /**
     * @var string|null
     */
    public $fullName = null;

    /**
     * @var \Illuminate\Http\UploadedFile|null
     */
    public $avatar = null;

    /**
     * @var string|null
     */
    public $avatarUrl = null;

    /**
     * @var string|null
     */
    public $password = null;

    /**
     * @var string|null
     */
    public $passwordConfirmation = null;

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;

        $profile = $user->profile;

        if ($profile) {
            $this->fullName = $profile->full_name;
            $this->avatarUrl = $profile->avatar;
        }
    }

    /**
     * Validation rules.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255"],
            "fullName" => ["nullable", "string", "max:255"],
            "avatar" => ["nullable", "image", "max:2048"],
            "password" => ["nullable", "string", "min:8", "max:255"],
            "passwordConfirmation" => ["nullable", "string", "same:password"],
        ];
    }

    /**
     * Save the profile.
     *
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        $user = auth()->user();

        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        $profile = $user->profile ?? new Profile();
        $profile->user_id = $user->id;

        if ($this->fullName !== null) {
            $profile->full_name = $this->fullName;
        }

        if ($this->avatar) {
            if ($profile->avatar) {
                Storage::disk("public")->delete($profile->avatar);
            }

            $profile->avatar = $this->avatar->store("avatars", "public");

            $this->avatarUrl = $profile->avatar;

            $this->reset("avatar");
        }

        $profile->save();

        if ($this->password) {
            auth()->user()->update([
                "password" => Hash::make($this->password),
            ]);

            $this->reset("password", "passwordConfirmation");
        }

        session()->flash("message", __("module_setting.personal_updated_successfully"));
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view("livewire.admin.setting.setting-personal-component");
    }
}
