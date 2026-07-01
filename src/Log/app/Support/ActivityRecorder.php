<?php

namespace Modules\Log\App\Support;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Modules\Acl\App\Models\Role;
use Modules\Log\App\Models\Activity;

class ActivityRecorder
{
    /**
     * @param \App\Models\User $user
     * @param array<string, mixed> $attributes
     * @return void
     */
    public static function userUpdated(User $user, array $attributes = []): void
    {
        self::log(
            description: "user updated",
            subject: $user,
            event: "updated",
            properties: $attributes === [] ? [] : [ "attributes" => $attributes, ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public static function userDeactivated(User $user): void
    {
        self::log(
            description: "user deactivated",
            subject: $user,
            event: "deactivated",
            properties: [],
        );
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public static function userActivated(User $user): void
    {
        self::log(
            description: "user activated",
            subject: $user,
            event: "activated",
            properties: [],
        );
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public static function userForceDeleted(User $user): void
    {
        self::log(
            description: "user force deleted",
            subject: $user,
            event: "deleted",
            properties: [],
        );
    }

    /**
     * @param \Modules\Acl\App\Models\Role $role
     * @param array<string, mixed> $attributes
     * @return void
     */
    public static function roleUpdated(Role $role, array $attributes = []): void
    {
        self::log(
            description: "role updated",
            subject: $role,
            event: "updated",
            properties: $attributes === [] ? [] : [ "attributes" => $attributes, ],
        );
    }

    /**
     * @param \App\Models\Setting $setting
     * @return void
     */
    public static function settingCreated(Setting $setting): void
    {
        self::log(
            description: "setting created",
            subject: $setting,
            event: "created",
            properties: [
                "attributes" => [
                    "key" => $setting->key,
                    "value" => $setting->value,
                    "value_kind" => $setting->value_kind ?? "text",
                ],
            ],
        );
    }

    /**
     * @param \App\Models\Setting $setting
     * @param array{key: string, value: string|null, value_kind?: string|null} $old
     * @return void
     */
    public static function settingUpdated(Setting $setting, array $old): void
    {
        self::log(
            description: "setting updated",
            subject: $setting,
            event: "updated",
            properties: [
                "old" => $old,
                "attributes" => [
                    "key" => $setting->key,
                    "value" => $setting->value,
                    "value_kind" => $setting->value_kind ?? "text",
                ],
            ],
        );
    }

    /**
     * @param \App\Models\Setting $setting
     * @param array{key: string, value: string|null, value_kind?: string|null} $old
     * @return void
     */
    public static function settingDeleted(Setting $setting, array $old): void
    {
        self::log(
            description: "setting deleted",
            subject: $setting,
            event: "deleted",
            properties: [
                "old" => $old,
            ],
        );
    }

    /**
     * @param \App\Models\User $user
     * @return void
     */
    public static function userEmailVerified(User $user): void
    {
        self::log(
            description: "user verified",
            subject: $user,
            event: "verified",
            properties: [],
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $subject
     * @param string $description
     * @param array<string, mixed> $properties
     * @return bool
     */
    public static function replaceLatestProperties(Model $subject, string $description, array $properties): bool
    {
        $activity = Activity::query()
            ->where("subject_type", $subject->getMorphClass())
            ->where("subject_id", $subject->getKey())
            ->where("description", $description)
            ->latest("id")
            ->first();

        if ($activity === null) {
            return false;
        }

        $activity->properties = $properties;
        $activity->save();

        return true;
    }

    /**
     * @param string $description
     * @param \Illuminate\Database\Eloquent\Model|null $subject
     * @param string $event
     * @param array<string, mixed> $properties
     * @return void
     */
    protected static function log(
        string $description,
        ?Model $subject,
        string $event,
        array $properties,
    ): void {
        if (! self::shouldRecord()) {
            return;
        }

        $logger = activity()
            ->event($event)
            ->withProperties($properties);

        if ($subject !== null) {
            $logger->performedOn($subject);
        }

        $causer = Auth::user();

        if ($causer instanceof User) {
            $logger->causedBy($causer);
        }

        $logger->log($description);

        AdminRefreshBroadcaster::scheduleForSubject(
            $subject !== null ? class_basename($subject) : null,
            $causer instanceof User ? (string) $causer->getKey() : null,
        );
    }

    /**
     * @return bool
     */
    protected static function shouldRecord(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        if (app()->runningInConsole()) {
            return Auth::check();
        }

        return true;
    }
}
