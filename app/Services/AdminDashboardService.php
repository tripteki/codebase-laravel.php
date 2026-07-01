<?php

namespace App\Services;

use App\Models\User;
use App\Support\AdminTenancySupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Acl\App\Enums\GuardEnum;
use Modules\Acl\App\Enums\RoleEnum;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Enums\PermissionEnum as LogPermissionEnum;
use Modules\Log\App\Models\Activity;
use Modules\User\App\Enums\PermissionEnum as UserPermissionEnum;

class AdminDashboardService
{
    /**
     * @param \App\Models\User $user
     * @param string $period
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array<string, mixed>
     */
    public function get(User $user, string $period = "30d", ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $bounds = $this->periodBounds($period, $dateFrom, $dateTo);
        $ownerId = $this->activityOwnerId($user);
        $canViewUsers = $this->userCan($user, UserPermissionEnum::USER_VIEW->value)
            || $user->hasRole(RoleEnum::SUPERADMIN->value, GuardEnum::WEB->value);
        $canViewActivities = $this->userCan($user, LogPermissionEnum::ACTIVITY_VIEW->value)
            || $user->hasRole(RoleEnum::SUPERADMIN->value, GuardEnum::WEB->value);

        $notifications = $this->recentNotifications($user, $bounds);

        return [
            "role" => $this->roleBucket($user),
            "period" => [
                "key" => $bounds["key"],
                "date_from" => $bounds["date_from"],
                "date_to" => $bounds["date_to"],
            ],
            "users" => $canViewUsers ? $this->usersPayload($bounds) : null,
            "recent_activities" => $canViewActivities ? $this->recentActivities($bounds, $ownerId) : null,
            "activities_scope" => $ownerId === null ? "all" : "own",
            "recent_notifications" => $notifications["items"],
            "unread_notifications" => $notifications["unread"],
            "links" => [
                "users" => admin_frontend_prefix()."/users",
                "activities" => admin_frontend_prefix()."/activities",
                "notifications" => "/notifications",
            ],
        ];
    }

    /**
     * @param \App\Models\User $user
     * @return string
     */
    private function roleBucket(User $user): string
    {
        if ($user->hasRole(RoleEnum::SUPERADMIN->value, GuardEnum::WEB->value)) {
            return RoleEnum::SUPERADMIN->value;
        }

        if ($user->hasRole(RoleEnum::ADMIN->value, GuardEnum::WEB->value)) {
            return RoleEnum::ADMIN->value;
        }

        return RoleEnum::USER->value;
    }

    /**
     * @param \App\Models\User $user
     * @return string|null
     */
    private function activityOwnerId(User $user): ?string
    {
        if ($user->hasAnyRole([
            RoleEnum::SUPERADMIN->value,
            RoleEnum::ADMIN->value,
        ])) {
            return null;
        }

        return (string) $user->getKey();
    }

    /**
     * @param \App\Models\User $user
     * @param string $permission
     * @return bool
     */
    private function userCan(User $user, string $permission): bool
    {
        try {
            return $user->hasPermissionTo($permission, GuardEnum::WEB->value);
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @param string $period
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array{key: string, date_from: \Illuminate\Support\Carbon|null, date_to: \Illuminate\Support\Carbon|null}
     */
    private function periodBounds(string $period, ?string $dateFrom, ?string $dateTo): array
    {
        $now = now();
        $key = strtolower(trim($period !== "" ? $period : "30d"));
        $start = null;
        $end = $now->copy();

        if ($key === "today") {
            $start = $now->copy()->startOfDay();
        } elseif ($key === "7d") {
            $start = $now->copy()->subDays(7);
        } elseif ($key === "30d") {
            $start = $now->copy()->subDays(30);
        } elseif ($key === "custom") {
            $start = $this->parseDate($dateFrom, false);
            $end = $this->parseDate($dateTo, true) ?? $now->copy();
        } else {
            $key = "30d";
            $start = $now->copy()->subDays(30);
        }

        return [
            "key" => $key,
            "date_from" => $start,
            "date_to" => $end,
        ];
    }

    /**
     * @param string|null $value
     * @param bool $endOfDay
     * @return \Illuminate\Support\Carbon|null
     */
    private function parseDate(?string $value, bool $endOfDay): ?Carbon
    {
        $raw = trim((string) $value);

        if ($raw === "") {
            return null;
        }

        try {
            $date = Carbon::parse($raw);
        } catch (\Throwable) {
            return null;
        }

        if ($endOfDay && strlen($raw) <= 10) {
            return $date->endOfDay();
        }

        return $date;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $query
     * @param array{key: string, date_from: \Illuminate\Support\Carbon|null, date_to: \Illuminate\Support\Carbon|null} $period
     * @param string $column
     * @return void
     */
    private function whereCreatedInPeriod(Builder|Relation $query, array $period, string $column = "created_at"): void
    {
        if ($period["date_from"] !== null) {
            $query->where($column, ">=", $period["date_from"]);
        }

        if ($period["date_to"] !== null) {
            $query->where($column, "<=", $period["date_to"]);
        }
    }

    /**
     * @param array{key: string, date_from: \Illuminate\Support\Carbon|null, date_to: \Illuminate\Support\Carbon|null} $period
     * @return array<string, int|array<string, int>>
     */
    private function usersPayload(array $period): array
    {
        $total = User::withTrashed();
        $active = User::query()->whereNotNull("email_verified_at");
        $deactivated = User::onlyTrashed();
        $unverified = User::query()->whereNull("email_verified_at");
        $created = User::withTrashed();

        foreach ([ $total, $active, $deactivated, $unverified, $created, ] as $query) {
            AdminTenancySupport::applyActiveTenantScope($query);
        }

        $this->whereCreatedInPeriod($created, $period);

        return [
            "total" => $total->count(),
            "active" => $active->count(),
            "deactivated" => $deactivated->count(),
            "unverified" => $unverified->count(),
            "new_in_period" => $created->count(),
            "by_role" => $this->usersByRole(),
        ];
    }

    /**
     * @return array<string, int>
     */
    private function usersByRole(): array
    {
        $roles = Role::query()->orderBy("name");
        AdminTenancySupport::applyActiveTenantScope($roles);
        $roleRows = $roles->get([ "id", "name", ]);

        if ($roleRows->isEmpty()) {
            return [];
        }

        $table = (string) config("permission.table_names.model_has_roles", "model_has_roles");
        $counts = DB::table($table)
            ->join("users", "users.id", "=", "{$table}.model_id")
            ->where("{$table}.model_type", User::class)
            ->whereNull("users.deleted_at")
            ->whereIn("{$table}.role_id", $roleRows->modelKeys())
            ->groupBy("{$table}.role_id")
            ->select("{$table}.role_id", DB::raw("count(*) as aggregate"))
            ->pluck("aggregate", "role_id");

        $byRole = [];

        foreach ($roleRows as $role) {
            $byRole[(string) $role->name] = (int) ($counts[$role->getKey()] ?? 0);
        }

        return $byRole;
    }

    /**
     * @param \App\Models\User $user
     * @param array{key: string, date_from: \Illuminate\Support\Carbon|null, date_to: \Illuminate\Support\Carbon|null} $period
     * @return array{items: array<int, array<string, mixed>>, unread: int}
     */
    private function recentNotifications(User $user, array $period): array
    {
        $query = $user->notifications()->reorder();
        $this->whereCreatedInPeriod($query, $period);

        $items = $query
            ->orderByDesc("created_at")
            ->limit(8)
            ->get()
            ->map(function ($notification): array {
                $data = is_array($notification->data) ? $notification->data : (array) $notification->data;
                $title = trim((string) ($data["title"] ?? ""));
                $body = trim((string) ($data["body"] ?? $data["message"] ?? ""));
                $url = trim((string) ($data["url"] ?? ""));

                return [
                    "id" => (string) $notification->id,
                    "type" => (string) $notification->type,
                    "title" => $title !== "" ? $title : null,
                    "body" => $body !== "" ? $body : null,
                    "url" => $url !== "" ? $url : null,
                    "created_at" => $notification->created_at,
                    "read_at" => $notification->read_at,
                ];
            })
            ->values()
            ->all();

        return [
            "items" => $items,
            "unread" => $user->unreadNotifications()->count(),
        ];
    }

    /**
     * @param array{key: string, date_from: \Illuminate\Support\Carbon|null, date_to: \Illuminate\Support\Carbon|null} $period
     * @param string|null $ownerId
     * @return array<int, array<string, mixed>>
     */
    private function recentActivities(array $period, ?string $ownerId): array
    {
        $query = Activity::query()->with("causer");
        AdminTenancySupport::applyActiveTenantScope($query);
        $this->whereCreatedInPeriod($query, $period);

        if ($ownerId !== null) {
            $query->where(function ($nested) use ($ownerId): void {
                $nested->where("causer_id", $ownerId)
                    ->orWhere(function ($subject) use ($ownerId): void {
                        $subject->where("subject_id", $ownerId)
                            ->whereIn("subject_type", [ User::class, "User", ]);
                    });
            });
        }

        return $query
            ->orderByDesc("created_at")
            ->orderByDesc("id")
            ->limit(8)
            ->get()
            ->map(function (Activity $activity): array {
                $subjectType = $activity->subject_type;
                $label = $subjectType !== null && $subjectType !== "" ? class_basename($subjectType) : null;
                $causer = $activity->causer;

                return [
                    "id" => (string) $activity->getKey(),
                    "description" => (string) $activity->description,
                    "event" => $activity->event,
                    "subject_type" => $label,
                    "subject_id" => $activity->subject_id !== null ? (string) $activity->subject_id : null,
                    "causer_id" => $activity->causer_id !== null ? (string) $activity->causer_id : null,
                    "causer_email" => $causer instanceof User ? $causer->email : null,
                    "created_at" => $activity->created_at,
                ];
            })
            ->values()
            ->all();
    }
}
