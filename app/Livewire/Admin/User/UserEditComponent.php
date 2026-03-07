<?php

namespace App\Livewire\Admin\User;

use App\Models\Profile;
use App\Models\User;
use App\Models\Tenant;
use Src\V1\Api\Acl\Models\Role;
use Illuminate\Support\Str;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\User\Enums\PermissionEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserEditComponent extends Component
{
    use WithFileUploads;

    /**
     * @var \App\Models\User
     */
    public User $user;

    /**
     * @var int
     */
    public $step = 1;

    /**
     * @var string
     */
    public $fullName = "";

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
     * @var string|null
     */
    public $tenant_id = null;

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
     * @param \App\Models\User $user
     * @return void
     */
    public function mount(User $user): void
    {
        $this->authorize(PermissionEnum::USER_UPDATE->value, $user);

        $this->user = $user;
        $this->fullName = (string) ($user->profile?->full_name ?? "");
        $this->email = (string) $user->email;
        $this->roles = $user->roles->pluck("id")->toArray();
        $this->tenant_id = $user->tenant_id;
        $this->avatarUrl = $user->profile?->avatar;
        $this->interests = $user->profile?->interests ?? [];
    }

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        $tenant = config("tenancy.is_tenancy") ? tenant() : null;
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && $currentUser->hasRole(RoleEnum::SUPERADMIN->value);

        $targetTenantId = filled($this->tenant_id) ? $this->tenant_id : $this->user->tenant_id;
        $emailRule = $tenant
            ? $tenant->unique("users", "email")->ignore($this->user->id)
            : (config("tenancy.is_tenancy")
                ? (filled($targetTenantId)
                    ? Rule::unique("users", "email")->where("tenant_id", $targetTenantId)->ignore($this->user->id)
                    : Rule::unique("users", "email")->whereNull("tenant_id")->ignore($this->user->id))
                : Rule::unique("users", "email")->ignore($this->user->id));

        $rules = [
            "email" => [
                "required",
                "email",
                $emailRule,
            ],
            "password" => "nullable|string|min:8|confirmed",
            "roles" => "nullable|array",
            "roles.*" => [
                $tenant ? $tenant->exists("roles", "id") : "exists:roles,id",
            ],
            "avatar" => ["nullable", "image", "max:2048"],
            "interests" => ["nullable", "array"],
            "interests.*" => ["string", "max:100"],
        ];

        if (config("tenancy.is_tenancy") && $isSuperAdmin) {
            $rules["tenant_id"] = "nullable|exists:tenants,id";
        }

        return $rules;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        $this->authorize(PermissionEnum::USER_UPDATE->value);

        $data = $this->validate();

        $payload = [
            "name" => Str::before($data["email"], "@"),
            "email" => $data["email"],
        ];

        if (! empty($data["password"])) {
            $payload["password"] = Hash::make($data["password"]);
        }

        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && $currentUser->hasRole(RoleEnum::SUPERADMIN->value);
        if (config("tenancy.is_tenancy") && $isSuperAdmin && isset($data["tenant_id"])) {
            $payload["tenant_id"] = $data["tenant_id"];
        } elseif (config("tenancy.is_tenancy") && tenancy()->initialized && empty($payload["tenant_id"])) {
            $payload["tenant_id"] = tenant("id");
        } elseif (! config("tenancy.is_tenancy")) {
            unset($payload["tenant_id"]);
        }

        DB::beginTransaction();

        try {
            $roles = $data["roles"] ?? [];
            $oldTenantId = $this->user->tenant_id;

            $this->user->update($payload);

            $fullName = trim((string) $this->fullName);
            $profile = $this->user->profile;
            if ($fullName !== "" || $this->avatar || ! empty($this->interests) || $profile) {
                if (! $profile) {
                    $profile = new Profile();
                    $profile->user_id = $this->user->id;
                }
                if ($fullName !== "") {
                    $profile->full_name = $fullName;
                } else {
                    $profile->full_name = null;
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
            } elseif ($profile) {
                $profile->full_name = null;
                $profile->interests = null;
                $profile->save();
            }

            if (config("tenancy.is_tenancy")) {
                $targetTenantId = $payload["tenant_id"] ?? $this->user->tenant_id;
                if (filled($targetTenantId)) {
                    $tenant = Tenant::find($targetTenantId);
                    if ($tenant) {
                        tenancy()->initialize($tenant);
                        $this->user->syncRoles($roles);
                        tenancy()->end();
                    }
                } else {
                    if (filled($oldTenantId)) {
                        $oldTenant = Tenant::find($oldTenantId);
                        if ($oldTenant) {
                            tenancy()->initialize($oldTenant);
                            $this->user->syncRoles([]);
                            tenancy()->end();
                        }
                    }
                    $this->user->syncRoles($roles);
                }
            } else {
                $this->user->syncRoles($roles);
            }

            DB::commit();

            session()->flash("message", __("module_user.user_updated"));

            return redirect()->to(tenant_routes("admin.users.index"));
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_user.user_updated_failed"));

            return redirect()->to(tenant_routes("admin.users.edit", $this->user));
        }
    }

    /**
     * @return void
     */
    public function nextStep(): void
    {
        if ($this->step < 3) {
            $this->step++;
        }
    }

    /**
     * @return void
     */
    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
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
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && $currentUser->hasRole(RoleEnum::SUPERADMIN->value);

        $availableRoles = Role::query()
            ->orderBy("name")
            ->where("name", "!=", RoleEnum::SUPERADMIN->value)
            ->get();

        $availableTenants = (config("tenancy.is_tenancy") && $isSuperAdmin)
            ? Tenant::query()->orderBy("id")->get()
            : collect();

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

        return view("livewire.admin.user.edit", [
            "user" => $this->user,
            "availableRoles" => $availableRoles,
            "availableTenants" => $availableTenants,
            "isSuperAdmin" => $isSuperAdmin,
            "existingInterests" => $existingInterests,
        ])->layout("layouts.app", [
            "title" => __("module_user.edit_title"),
            "showSidebar" => true,
        ]);
    }
}
