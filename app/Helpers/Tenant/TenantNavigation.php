<?php

namespace App\Helpers\Tenant;

final class TenantNavigation
{
    /**
     * @internal
     */
    private function __construct(
        public readonly string $routeName,
        public readonly bool $isDashboardRoute,
        public readonly bool $isTenantEventRoute,
        public readonly bool $isTenantSection,
        public readonly bool $isStageMeetingRoute,
        public readonly bool $isStageSessionRoute,
        public readonly bool $isStageSection,
        public readonly bool $isAccountUsersRoute,
        public readonly bool $isAccountSection,
        public readonly bool $isAccessRolesRoute,
        public readonly bool $isAccessPermissionsRoute,
        public readonly bool $isAccessSection,
        public readonly bool $isLogActivitiesRoute,
        public readonly bool $isLogSection,
    ) {}

    /**
     * @param string $routeName Laravel route name (e.g. from request()->route()?->getName()).
     * @return \App\Helpers\Tenant\TenantNavigation
     */
    public static function forRouteName(string $routeName): self
    {
        $startsWith = static fn (string $prefix): bool => str_starts_with($routeName, $prefix);

        $isTenantEventRoute = $startsWith("admin.tenants.events.");
        $isStageMeetingRoute = $startsWith("admin.stage.meetings.");
        $isStageSessionRoute = $startsWith("admin.stage.sessions.");
        $isStageSection = $startsWith("admin.stage.");
        $isAccountUsersRoute = $startsWith("admin.users.");
        $isAccessRolesRoute = $startsWith("admin.roles.");
        $isAccessPermissionsRoute = $startsWith("admin.permissions.");
        $isLogActivitiesRoute = $startsWith("admin.activities.");

        return new self(
            routeName: $routeName,
            isDashboardRoute: in_array($routeName, ["admin.dashboard", "admin.dashboard.index"], true),
            isTenantEventRoute: $isTenantEventRoute,
            isTenantSection: $isTenantEventRoute,
            isStageMeetingRoute: $isStageMeetingRoute,
            isStageSessionRoute: $isStageSessionRoute,
            isStageSection: $isStageSection,
            isAccountUsersRoute: $isAccountUsersRoute,
            isAccountSection: $isAccountUsersRoute,
            isAccessRolesRoute: $isAccessRolesRoute,
            isAccessPermissionsRoute: $isAccessPermissionsRoute,
            isAccessSection: $isAccessRolesRoute || $isAccessPermissionsRoute,
            isLogActivitiesRoute: $isLogActivitiesRoute,
            isLogSection: $isLogActivitiesRoute,
        );
    }
}
