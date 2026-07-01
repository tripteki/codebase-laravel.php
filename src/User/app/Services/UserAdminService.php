<?php

namespace Modules\User\App\Services;

use App\Dtos\OffsetPaginationDto;
use App\Models\User;
use App\Services\Service as BaseService;
use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Log\App\Support\AdminRefreshBroadcaster;
use Modules\User\App\Dtos\UserChartStatsDto;
use Modules\User\App\Dtos\UserDto;
use Modules\User\App\Dtos\UserIdentifierDto;
use Modules\User\App\Dtos\UserTransformerDto;
use Modules\User\App\Dtos\UserUpdateDto;
use Modules\User\App\Events\UserAdminActivated;
use Modules\User\App\Events\UserAdminDeactivated;
use Modules\User\App\Jobs\UserAdminExportJob;
use Modules\User\App\Jobs\UserAdminImportJob;
use Modules\User\App\Repositories\UserAdminRepository;
use Modules\User\App\Support\AccountNotifier;
use Modules\User\App\Support\SecurityNotifier;

class UserAdminService extends BaseService
{
    /**
     * @var \Modules\User\App\Repositories\UserAdminRepository
     */
    protected UserAdminRepository $userAdminRepository;

    /**
     * @param \Modules\User\App\Repositories\UserAdminRepository $userAdminRepository
     * @return void
     */
    public function __construct(UserAdminRepository $userAdminRepository)
    {
        $this->userAdminRepository = $userAdminRepository;
    }

    /**
     * @return \App\Dtos\OffsetPaginationDto
     */
    public function all(): OffsetPaginationDto
    {
        $paginator = $this->userAdminRepository->all();

        return $this->toOffsetPagination(
            $paginator,
            fn ($user) => UserTransformerDto::fromUser($user),
        );
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function get(UserIdentifierDto $identifier): UserTransformerDto
    {
        return UserTransformerDto::fromUser($this->userAdminRepository->get($identifier->id));
    }

    /**
     * @param \Modules\User\App\Dtos\UserDto $userData
     * @param \Illuminate\Http\UploadedFile|null $avatar
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function create(UserDto $userData, ?UploadedFile $avatar = null): UserTransformerDto
    {
        $actor = Auth::user();
        $role = filled($userData->role) ? (string) $userData->role : RoleEnum::USER->value;

        if ($actor instanceof User) {
            $this->assertCanAssign($actor, $role);
        }

        $tenantId = null;

        if (is_tenancy()) {
            $tenantId = is_central()
                ? AdminTenancySupport::resolveTenantIdFromPayload([
                    "tenant" => $userData->tenant,
                ])
                : current_tenant_id();
        }

        $displayName = $this->displayName($userData->full_name, $userData->name);
        User::ensureNameIsAvailable($displayName, null, $tenantId);
        User::ensureEmailIsAvailable((string) $userData->email, null, $tenantId);
        $payload = $userData->createPayload();
        $payload["name"] = $displayName;
        $profile = $userData->profilePayload();
        $profile["full_name"] = $displayName;

        $created = $this->userAdminRepository->create(
            $payload,
            $profile,
            $tenantId,
            $role,
            $this->storeAvatar($avatar),
        );

        if ($created instanceof User) {
            AccountNotifier::accountCreated($created, $role);
        }

        $user = UserTransformerDto::fromUser($created);

        $this->refreshAdmins([ "users", ]);

        return $user;
    }

    /**
     * @param \Modules\User\App\Dtos\UserUpdateDto $userData
     * @param \Illuminate\Http\UploadedFile|null $avatar
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function update(UserUpdateDto $userData, ?UploadedFile $avatar = null): UserTransformerDto
    {
        $target = User::query()->findOrFail($userData->id);
        $actor = Auth::user();
        $role = filled($userData->role) ? (string) $userData->role : null;

        if ($actor instanceof User) {
            $this->assertCanMutate($actor, $target);

            if ($role !== null) {
                $this->assertCanAssign($actor, $role, $target);
            }
        }

        if ($avatar instanceof UploadedFile && filled($target->profile?->avatar)) {
            Storage::disk("public")->delete($target->profile->avatar);
        }

        $displayName = ($userData->full_name !== null || $userData->name !== null)
            ? $this->displayName($userData->full_name, $userData->name)
            : "";

        if ($displayName !== "" && $displayName !== (string) $target->name) {
            User::ensureNameIsAvailable(
                $displayName,
                (string) $target->getKey(),
                $target->tenant_id !== null ? (string) $target->tenant_id : null,
            );
        }

        $nextEmail = trim((string) ($userData->email ?? ""));

        if ($nextEmail !== "" && strcasecmp($nextEmail, (string) $target->email) !== 0) {
            User::ensureEmailIsAvailable(
                $nextEmail,
                (string) $target->getKey(),
                $target->tenant_id !== null ? (string) $target->tenant_id : null,
            );
        }

        $previousEmail = (string) $target->email;
        $passwordChanged = filled($userData->password);

        $previousRole = AdminTenancySupport::runWithPermissionsTeam(
            $target->tenant_id !== null ? (string) $target->tenant_id : null,
            fn (): ?string => UserTransformerDto::primaryRole($target),
        );

        $payload = $userData->updatePayload();
        $profile = $userData->profilePayload();

        if ($displayName !== "") {
            $payload["name"] = $displayName;
            $profile["full_name"] = $displayName;
        }

        $updated = $this->userAdminRepository->update(
            (string) $userData->id,
            $payload,
            $profile,
            $role,
            $this->storeAvatar($avatar),
        );

        if ($updated instanceof User && $role !== null && $role !== $previousRole) {
            AccountNotifier::roleChanged($updated, $role);
        }

        if ($updated instanceof User && strcasecmp($updated->email, $previousEmail) !== 0) {
            SecurityNotifier::emailChanged($updated, $previousEmail, true);
        }

        if ($updated instanceof User && $passwordChanged) {
            $actorId = Auth::id();
            SecurityNotifier::passwordChanged($updated, $actorId !== null ? (string) $actorId : null);
        }

        $user = UserTransformerDto::fromUser($updated);

        $this->refreshAdmins([ "users", ]);

        return $user;
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function delete(UserIdentifierDto $identifier): UserTransformerDto
    {
        $this->assertActorCanMutate($identifier->id);
        $user = $this->userAdminRepository->delete($identifier->id);
        event(new UserAdminDeactivated($user, (string) Auth::id()));
        $this->refreshAdmins([ "users", ]);

        return UserTransformerDto::fromUser($user);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function forceDelete(UserIdentifierDto $identifier): UserTransformerDto
    {
        $this->assertActorCanMutate($identifier->id);
        $user = $this->userAdminRepository->forceDelete($identifier->id);
        $this->refreshAdmins([ "users", ]);

        return UserTransformerDto::fromUser($user);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function restore(UserIdentifierDto $identifier): UserTransformerDto
    {
        $this->assertActorCanMutate($identifier->id);
        $user = $this->userAdminRepository->restore($identifier->id);
        event(new UserAdminActivated($user, (string) Auth::id()));
        $this->refreshAdmins([ "users", ]);

        return UserTransformerDto::fromUser($user);
    }

    /**
     * @param \Modules\User\App\Dtos\UserIdentifierDto $identifier
     * @return \Modules\User\App\Dtos\UserTransformerDto
     */
    public function verify(UserIdentifierDto $identifier): UserTransformerDto
    {
        $this->assertActorCanMutate($identifier->id);
        $verified = $this->userAdminRepository->verify($identifier->id);

        if ($verified instanceof User) {
            $actorId = Auth::id();
            AccountNotifier::afterEmailVerified($verified, $actorId !== null ? (string) $actorId : null);
        } else {
            $verified = $this->userAdminRepository->get($identifier->id);

            if (! $verified instanceof User || $verified->trashed()) {
                throw (new ModelNotFoundException)->setModel(User::class, [ $identifier->id, ]);
            }
        }

        $user = UserTransformerDto::fromUser($verified);
        $this->refreshAdmins([ "users", ]);

        return $user;
    }

    /**
     * @param string $path
     * @param string $filename
     * @return string
     */
    public function import(string $path, string $filename): string
    {
        UserAdminImportJob::dispatch(
            (string) Auth::id(),
            $path,
            $filename,
        );

        return __("user.import.started");
    }

    /**
     * @param string $type
     * @param array<string, mixed> $filters
     * @param array<int, array{field: string, direction: string}> $orders
     * @return string
     */
    public function export(string $type = "csv", array $filters = [], array $orders = []): string
    {
        UserAdminExportJob::dispatch(
            (string) Auth::id(),
            $type,
            $filters,
            $orders,
        );

        return __("user.export.started");
    }

    /**
     * @return \Modules\User\App\Dtos\UserChartStatsDto
     */
    public function registrationTrend(): UserChartStatsDto
    {
        $stats = $this->userAdminRepository->registrationTrend();

        return new UserChartStatsDto(
            labels: $stats["labels"],
            series: $stats["series"],
        );
    }

    /**
     * @return \Modules\User\App\Dtos\UserChartStatsDto
     */
    public function usersByRole(): UserChartStatsDto
    {
        $stats = $this->userAdminRepository->usersByRole();

        return new UserChartStatsDto(
            labels: $stats["labels"],
            series: $stats["series"],
        );
    }

    /**
     * @param string|null $fullName
     * @param string|null $name
     * @return string
     */
    protected function displayName(?string $fullName, ?string $name): string
    {
        $fullName = trim((string) $fullName);

        if ($fullName !== "") {
            return $fullName;
        }

        return trim((string) $name);
    }

    /**
     * @param \App\Models\User $actor
     * @param string $roleName
     * @param \App\Models\User|null $target
     * @return void
     */
    protected function assertCanAssign(User $actor, string $roleName, ?User $target = null): void
    {
        $actorRole = UserTransformerDto::primaryRole($actor);

        if ($actorRole !== RoleEnum::SUPERADMIN->value && $roleName !== RoleEnum::USER->value) {
            abort(403, __("user.admin.role_forbidden"));
        }

        if ($target === null) {
            return;
        }

        $currentRole = UserTransformerDto::primaryRole($target);

        if ((string) $actor->getKey() === (string) $target->getKey() && $currentRole !== $roleName) {
            abort(403, __("user.admin.role_self_forbidden"));
        }

        $this->assertCanMutate($actor, $target);
    }

    /**
     * @param string $targetId
     * @return void
     */
    protected function assertActorCanMutate(string $targetId): void
    {
        $actor = Auth::user();
        $target = User::withTrashed()->findOrFail($targetId);

        if ($actor instanceof User) {
            $this->assertCanMutate($actor, $target);
        }
    }

    /**
     * @param \App\Models\User $actor
     * @param \App\Models\User $target
     * @return void
     */
    protected function assertCanMutate(User $actor, User $target): void
    {
        if ((string) $actor->getKey() === (string) $target->getKey()) {
            abort(403, __("user.admin.account_locked"));
        }

        $actorRole = UserTransformerDto::primaryRole($actor);
        $targetRole = UserTransformerDto::primaryRole($target);

        if (
            in_array($targetRole, [ RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, ], true)
            && $actorRole !== RoleEnum::SUPERADMIN->value
        ) {
            abort(403, __("user.admin.account_locked"));
        }
    }

    /**
     * @param \Illuminate\Http\UploadedFile|null $avatar
     * @return string|null
     */
    protected function storeAvatar(?UploadedFile $avatar): ?string
    {
        if (! $avatar instanceof UploadedFile) {
            return null;
        }

        return $avatar->store("avatars", "public");
    }

    /**
     * @param list<string> $scopes
     * @return void
     */
    protected function refreshAdmins(array $scopes): void
    {
        $actorId = Auth::id();

        AdminRefreshBroadcaster::broadcast($scopes, $actorId !== null ? (string) $actorId : null);
    }
}
