<?php

namespace App\Helpers\Tenant;

use App\Enum\Event\AddOnEnum;
use App\Enum\Stage\StageMeetingPermissionEnum;
use App\Enum\Stage\StageSessionPermissionEnum;
use App\Enum\Tenant\PermissionEnum;
use App\Helpers\AddOnsHelper;
use App\Models\User;
use Src\V1\Api\Acl\Enums\RoleEnum;
use Src\V1\Api\Log\Enums\PermissionEnum as LogPermissionEnum;

final class TenantAccess
{
    /**
     * @internal
     */
    private function __construct(
        public readonly bool $hasTenantEvent,
        public readonly bool $canTenantEvent,
        public readonly bool $hasStageMeeting,
        public readonly bool $hasStageSession,
        public readonly bool $canStageMeeting,
        public readonly bool $canStageSession,
        public readonly bool $canAccessStage,
        public readonly bool $canAccountUsers,
        public readonly bool $canAccessRoles,
        public readonly bool $canAccessPermissions,
        public readonly bool $canLogActivities,
    ) {}

    /**
     * @param \App\Models\User|null $user Authenticated admin user, or null when unauthenticated.
     * @return \App\Helpers\Tenant\TenantAccess
     */
    public static function forUser(?User $user): self
    {
        $isTenancyEnabled = config("tenancy.is_tenancy");
        $hasTenantEvent = $isTenancyEnabled;
        $canTenantEvent = $user?->can(PermissionEnum::EVENT_VIEW->value) ?? false;

        $hasStageMeeting = AddOnsHelper::has(AddOnEnum::MODULES_STAGE_MEETING);
        $hasStageSession = AddOnsHelper::has(AddOnEnum::MODULES_STAGE_SESSION);
        $hasAnyStageAddOn = $hasStageMeeting || $hasStageSession;
        $canStageMeeting = $user?->can(StageMeetingPermissionEnum::STAGE_MEETING_VIEW->value) ?? false;
        $canStageSession = $user?->can(StageSessionPermissionEnum::STAGE_SESSION_VIEW->value) ?? false;
        $canAccessStage = $isTenancyEnabled && hasTenant() && $hasAnyStageAddOn && ($canStageMeeting || $canStageSession);

        $isSuperAdmin = $user?->hasRole(RoleEnum::SUPERADMIN->value) ?? false;
        $isAdmin = $user?->hasRole(RoleEnum::ADMIN->value) ?? false;
        $canAccountUsers = $isSuperAdmin || $isAdmin;

        $canAccessRoles = $isSuperAdmin;
        $canAccessPermissions = $isSuperAdmin;

        $canLogActivities = $user?->can(LogPermissionEnum::ACTIVITY_VIEW->value) ?? false;

        return new self(
            hasTenantEvent: $hasTenantEvent,
            canTenantEvent: $canTenantEvent,
            hasStageMeeting: $hasStageMeeting,
            hasStageSession: $hasStageSession,
            canStageMeeting: $canStageMeeting,
            canStageSession: $canStageSession,
            canAccessStage: $canAccessStage,
            canAccountUsers: $canAccountUsers,
            canAccessRoles: $canAccessRoles,
            canAccessPermissions: $canAccessPermissions,
            canLogActivities: $canLogActivities,
        );
    }

    /**
     * @return bool
     */
    public function canShowTenantEventsSection(): bool
    {
        return $this->hasTenantEvent && $this->canTenantEvent;
    }

    /**
     * @return bool
     */
    public function canShowStageMeetingNavItem(): bool
    {
        return $this->hasStageMeeting && $this->canStageMeeting;
    }

    /**
     * @return bool
     */
    public function canShowStageSessionNavItem(): bool
    {
        return $this->hasStageSession && $this->canStageSession;
    }

    /**
     * @return bool
     */
    public function canShowAccessManagementSection(): bool
    {
        return $this->canAccessRoles || $this->canAccessPermissions;
    }

    /**
     * @return bool
     */
    public function canSearchEventsCategory(): bool
    {
        return $this->canShowTenantEventsSection();
    }

    /**
     * @return bool
     */
    public function canSearchMeetingsCategory(): bool
    {
        return $this->canAccessStage && $this->canShowStageMeetingNavItem();
    }

    /**
     * @return bool
     */
    public function canSearchSessionsCategory(): bool
    {
        return $this->canAccessStage && $this->canShowStageSessionNavItem();
    }
}
