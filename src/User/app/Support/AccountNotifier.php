<?php

namespace Modules\User\App\Support;

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Auth\App\Mail\NoticeMail;
use Modules\Event\App\Support\AuthAddOnsHelper;
use Modules\Event\App\Support\TenantMailHelper;
use Modules\User\App\Dtos\UserTransformerDto;

class AccountNotifier
{
    /**
     * @param \App\Models\User $user
     * @param string $role
     * @return void
     */
    public static function accountCreated(User $user, string $role): void
    {
        $roleLabel = self::roleLabel($role);

        self::notify((string) $user->getKey(), "user.account.created", [
            "title" => __("user.account_created.title"),
            "body" => __("user.account_created.body", [ "role" => $roleLabel, ]),
            "url" => self::dashboardUrl($user),
        ]);
        self::sendAccountCreatedMail($user, $roleLabel);
    }

    /**
     * @param \App\Models\User $user
     * @param string $role
     * @return void
     */
    public static function roleChanged(User $user, string $role): void
    {
        self::notify((string) $user->getKey(), "user.role.changed", [
            "title" => __("user.role_changed.title"),
            "body" => __("user.role_changed.body", [ "role" => self::roleLabel($role), ]),
            "url" => self::dashboardUrl($user),
        ]);
    }

    /**
     * @param \App\Models\User $user
     * @param string|null $actorId
     * @return void
     */
    public static function afterEmailVerified(User $user, ?string $actorId = null): void
    {
        $user->loadMissing("profile");

        $role = AdminTenancySupport::runWithPermissionsTeam(
            $user->tenant_id !== null ? (string) $user->tenant_id : null,
            fn (): ?string => UserTransformerDto::primaryRole($user),
        );
        $roleLabel = $role !== null ? self::roleLabel($role) : "-";
        $fullName = $user->profile?->full_name ?: $user->name ?: "-";
        $userId = (string) $user->getKey();

        if ($actorId === null || $userId !== $actorId) {
            self::notify($userId, "user.registration.verified", [
                "title" => __("user.registration.verified_title"),
                "body" => __("user.registration.verified_body"),
                "url" => self::dashboardUrl($user),
            ]);
        }

        $body = __("user.registration.new_body", [
            "full_name" => $fullName,
            "email" => $user->email,
            "role" => $roleLabel,
        ]);

        foreach (self::staffIds($user) as $staffId) {
            if ($staffId === $userId || ($actorId !== null && $staffId === $actorId)) {
                continue;
            }

            self::notify($staffId, "user.registration.new", [
                "title" => __("user.registration.new_title"),
                "body" => $body,
                "url" => self::userUrl($user),
                "user_id" => $userId,
            ]);
        }
    }

    /**
     * @param string $recipientId
     * @param string $type
     * @param array<string, string> $data
     * @return void
     */
    public static function notify(string $recipientId, string $type, array $data): void
    {
        try {
            $recipient = User::query()->whereNull("deleted_at")->find($recipientId);

            if ($recipient === null) {
                return;
            }

            $recipient->notifications()->create([
                "id" => Str::uuid()->toString(),
                "type" => $type,
                "data" => $data,
            ]);
        } catch (\Throwable $exception) {
            Log::warning("notify failed", [
                "type" => $type,
                "recipient_id" => $recipientId,
                "error" => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param \App\Models\User $user
     * @param string $roleLabel
     * @return void
     */
    protected static function sendAccountCreatedMail(User $user, string $roleLabel): void
    {
        try {
            AuthAddOnsHelper::initializeForUser($user);

            if (! AuthAddOnsHelper::isMailingEnabled()) {
                return;
            }

            $tenantId = $user->tenant_id !== null ? (string) $user->tenant_id : null;
            $name = $user->displayName() ?: $user->email;

            TenantMailHelper::send(
                $user->email,
                new NoticeMail(
                    subjectLine: __("user.account_created.subject"),
                    heading: __("user.account_created.subject"),
                    line: __("user.account_created.line1", [ "role" => $roleLabel, ]),
                    userName: $name,
                    userEmail: $user->email,
                    line2: __("user.account_created.line2"),
                    actionUrl: frontend_url("login"),
                    actionLabel: __("user.account_created.login"),
                    secondaryUrl: frontend_url("forgot-password"),
                    secondaryLabel: __("user.account_created.forgot"),
                ),
                $tenantId,
            );
        } catch (\Throwable $exception) {
            Log::warning("account created mail failed", [
                "email" => $user->email,
                "error" => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param string $role
     * @return string
     */
    protected static function roleLabel(string $role): string
    {
        $key = "user.roles.".$role;
        $label = __($key);

        return $label === $key ? $role : $label;
    }

    /**
     * @param \App\Models\User $user
     * @return string
     */
    protected static function dashboardUrl(User $user): string
    {
        return self::frontendBase($user)."/dashboard";
    }

    /**
     * @param \App\Models\User $user
     * @return string
     */
    protected static function userUrl(User $user): string
    {
        return self::frontendBase($user)."/users/".$user->getKey();
    }

    /**
     * @param \App\Models\User $user
     * @return string
     */
    protected static function frontendBase(User $user): string
    {
        if (is_tenancy() && filled($user->tenant_id)) {
            return admin_frontend_prefix((string) $user->tenant_id);
        }

        return "/admin";
    }

    /**
     * @param \App\Models\User $user
     * @return list<string>
     */
    protected static function staffIds(User $user): array
    {
        $roleQuery = Role::query()
            ->withoutGlobalScopes()
            ->whereIn("name", [ RoleEnum::SUPERADMIN->value, RoleEnum::ADMIN->value, ])
            ->where("guard_name", GuardEnum::WEB->value);

        if (is_tenancy() && filled($user->tenant_id)) {
            $roleQuery->where("tenant_id", $user->tenant_id);
        } else {
            $roleQuery->where(function ($nested): void {
                $nested->whereNull("tenant_id")->orWhere("tenant_id", "");
            });
        }

        $roleIds = $roleQuery->pluck("id");

        if ($roleIds->isEmpty()) {
            return [];
        }

        $table = (string) config("permission.table_names.model_has_roles", "model_has_roles");
        $ids = DB::table($table)
            ->whereIn("role_id", $roleIds)
            ->where("model_type", $user->getMorphClass())
            ->pluck("model_id")
            ->map(fn ($id): string => (string) $id)
            ->unique()
            ->all();

        if ($ids === []) {
            return [];
        }

        return User::query()
            ->whereIn("id", $ids)
            ->whereNull("deleted_at")
            ->pluck("id")
            ->map(fn ($id): string => (string) $id)
            ->all();
    }
}
