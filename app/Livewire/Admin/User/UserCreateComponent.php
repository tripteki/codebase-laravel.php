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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class UserCreateComponent extends Component
{
    use WithFileUploads;

    /**
     * @var int
     */
    public $step = 1;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->authorize(PermissionEnum::USER_CREATE->value);
        $this->currentTenantId = config("tenancy.is_tenancy") && tenant() ? tenant()->id : null;
    }

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
    public $currentTenantId = null;

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        $tenant = config("tenancy.is_tenancy") ? tenant() : null;
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && $currentUser->hasRole(RoleEnum::SUPERADMIN->value);

        $emailRule = $tenant
            ? $tenant->unique("users", "email")
            : (config("tenancy.is_tenancy")
                ? (filled($this->tenant_id)
                    ? Rule::unique("users", "email")->where("tenant_id", $this->tenant_id)
                    : Rule::unique("users", "email")->whereNull("tenant_id"))
                : Rule::unique("users", "email"));

        $rules = [
            "fullName" => "nullable|string|max:255",
            "email" => [
                "required",
                "email",
                $emailRule,
            ],
            "password" => "required|string|min:8|confirmed",
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
        $this->authorize(PermissionEnum::USER_CREATE->value);

        $data = $this->validate();

        DB::beginTransaction();

        try {
            $roles = $data["roles"] ?? [];
            unset($data["roles"], $data["password_confirmation"], $data["fullName"], $data["avatar"]);

            $data["name"] = Str::before($data["email"], "@");
            $data["password"] = Hash::make($data["password"]);

            if (config("tenancy.is_tenancy")) {
                if (empty($data["tenant_id"]) && filled($this->currentTenantId)) {
                    $data["tenant_id"] = $this->currentTenantId;
                } elseif (config("tenancy.is_tenancy") && tenancy()->initialized && empty($data["tenant_id"])) {
                    $data["tenant_id"] = tenant("id");
                }
            } else {
                unset($data["tenant_id"]);
            }

            $user = User::query()->create($data);
            $user->markEmailAsVerified();

            $fullName = trim((string) $this->fullName);
            if ($fullName !== "" || $this->avatar || ! empty($this->interests)) {
                $profileData = ["user_id" => $user->id];
                if ($fullName !== "") {
                    $profileData["full_name"] = $fullName;
                }
                if ($this->avatar) {
                    $profileData["avatar"] = $this->avatar->store("avatars", "public");
                }
                if (! empty($this->interests)) {
                    $profileData["interests"] = array_values(array_map("trim", $this->interests));
                }
                Profile::query()->create($profileData);
            }

            if (config("tenancy.is_tenancy")) {
                $targetTenantId = $data["tenant_id"] ?? null;
                if (filled($targetTenantId)) {
                    $tenant = Tenant::find($targetTenantId);
                    if ($tenant) {
                        tenancy()->initialize($tenant);
                        $user->syncRoles($roles);
                        tenancy()->end();
                    }
                } else {
                    $user->syncRoles($roles);
                }
            } else {
                $user->syncRoles($roles);
            }

            DB::commit();

            session()->flash("message", __("module_user.user_created_successfully"));

            return redirect()->to(tenant_routes("admin.users.index"));
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash("error", __("module_user.user_created_failed"));

            return redirect()->to(tenant_routes("admin.users.create"));
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

        return view("livewire.admin.user.create", [
            "availableRoles" => $availableRoles,
            "availableTenants" => $availableTenants,
            "isSuperAdmin" => $isSuperAdmin,
            "existingInterests" => $existingInterests,
        ])->layout("layouts.app", [
            "title" => __("module_user.create_title"),
            "showSidebar" => true,
        ]);
    }
}
