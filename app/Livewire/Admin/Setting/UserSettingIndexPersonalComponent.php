<?php

namespace App\Livewire\Admin\Setting;

use App\Models\Profile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
     * @var array<int, string>
     */
    public $interests = [];

    /**
     * @var string
     */
    public $newInterest = "";

    /**
     * @var string|null
     */
    public $password = null;

    /**
     * @var string|null
     */
    public $passwordConfirmation = null;

    /**
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
            $this->interests = $profile->interests ?? [];
        }
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        $tenant = config("tenancy.is_tenancy") ? tenant() : null;
        $user = auth()->user();

        return [
            "name" => ["required", "string", "max:255"],
            "email" => [
                "required",
                "string",
                "email",
                "max:255",
                $tenant
                    ? $tenant->unique("users", "email")->ignore($user->id)
                    : (config("tenancy.is_tenancy")
                        ? (filled($user->tenant_id)
                            ? Rule::unique("users", "email")->where("tenant_id", $user->tenant_id)->ignore($user->id)
                            : Rule::unique("users", "email")->whereNull("tenant_id")->ignore($user->id))
                        : Rule::unique("users", "email")->ignore($user->id)),
            ],
            "fullName" => ["nullable", "string", "max:255"],
            "avatar" => ["nullable", "image", "max:2048"],
            "interests" => ["nullable", "array"],
            "interests.*" => ["string", "max:100"],
            "password" => ["nullable", "string", "min:8", "max:255"],
            "passwordConfirmation" => ["nullable", "string", "same:password"],
        ];
    }

    /**
     * @return void
     */
    public function addInterest(): void
    {
        $tag = trim($this->newInterest);
        if ($tag !== "" && ! in_array($tag, $this->interests, true)) {
            $this->interests[] = $tag;
            $this->newInterest = "";
        }
    }

    /**
     * @param string $value
     * @return void
     */
    public function addInterestValue(string $value): void
    {
        $tag = trim($value);
        if ($tag !== "" && ! in_array($tag, $this->interests, true)) {
            $this->interests[] = $tag;
        }
        $this->newInterest = "";
    }

    /**
     * @param int $index
     * @return void
     */
    public function removeInterest(int $index): void
    {
        if (isset($this->interests[$index])) {
            array_splice($this->interests, $index, 1);
        }
    }

    /**
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

        $profile->interests = array_values(array_map("trim", $this->interests));

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
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $existingInterests = Profile::query()
            ->whereNotNull("interests")
            ->get()
            ->pluck("interests")
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return view("livewire.admin.setting.setting-personal-component", [
            "existingInterests" => $existingInterests,
        ]);
    }
}
