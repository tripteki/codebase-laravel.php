@php
    use App\Models\User;
    use App\Helpers\Tenant\TenantAccess;
    use App\Helpers\Tenant\TenantNavigation;

    $AddOnEnum = App\Enum\Event\AddOnEnum::class;
    $SettingHelper = App\Helpers\SettingHelper::class;
    $AddOnsHelper = App\Helpers\AddOnsHelper::class;
    $UserModel = App\Models\User::class;

    $currentRoute = request()->route()?->getName() ?? '';

    $user = auth()->user();
    $access = TenantAccess::forUser($user instanceof User ? $user : null);
    $navigation = TenantNavigation::forRouteName($currentRoute);

    if (isset($currentRouteOverride)) {
        $currentRoute = $currentRouteOverride;
    }



    $sidebarNavigationActiveClass = 'text-white sidebar-active-pill dark:bg-gray-700/50 dark:shadow-none';
    $sidebarSectionOpenClass = 'text-white bg-white/10';

    $defaultBrandLight = asset('asset/brand-light.png');
    $brandLightSrc = config('tenancy.is_tenancy') && hasTenant() && tenant('brand_light')
        ? asset('storage/' . tenant('brand_light'))
        : $defaultBrandLight;
    $footerCompanyName = config('tenancy.is_tenancy') && hasTenant()
        ? $AddOnsHelper::copyrightSidebarCompanyName()
        : null;

    $latestVerifiedUsersCount = $access->canAccountUsers ? $UserModel::query()
        ->whereNotNull('email_verified_at')
        ->where('email_verified_at', '>=', now()->subMinutes(5))
        ->count() : 0;
@endphp

<div
    id="sidebar-backdrop"
    data-sidebar-backdrop
    class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm hidden lg:hidden transition-opacity duration-200"
></div>

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 transform transition-transform duration-200 -translate-x-full lg:translate-x-0 overflow-y-auto rounded-r-[20px] sidebar-gradient-bg"
>
    <div class="flex h-full flex-col">
        <div class="flex h-16 flex-col items-center justify-center px-4 py-3 relative">
            <button
                type="button"
                data-sidebar-toggle
                class="absolute right-3 top-1/2 -translate-y-1/2 lg:hidden inline-flex h-8 w-8 items-center justify-center my-2 rounded-lg text-white/80 hover:bg-white/10 transition-colors"
            >
                <span class="sr-only">{{ __('common.close_sidebar') }}</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M14 6l-8 8" />
                </svg>
            </button>

            <div class="inline-flex h-8 w-28 items-center justify-center rounded-full">
                <img
                    src="{{ $brandLightSrc }}"
                    alt="{{ config('app.name') }}"
                    class="block object-contain max-h-10"
                    style="filter: brightness(0) invert(1);"
                />
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-3 py-4">
            <div class="mb-4 block lg:hidden">
                @include("components.admin.search", ["mobileOnly" => true])
            </div>
            <ul class="space-y-1 text-sm font-medium">
                <li>
                    <a
                        href="{{ tenant_routes('admin.dashboard.index') }}"
                        class="flex items-center rounded-full px-3 py-2 text-[#e9f0ff] hover:bg-white/10 hover:text-white transition-all {{ $navigation->isDashboardRoute ? $sidebarNavigationActiveClass : '' }}"
                    >
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                @if ($access->canShowTenantEventsSection())
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-full px-3 py-2 text-left text-[#e9f0ff] hover:bg-white/10 hover:text-white transition-all {{ $navigation->isTenantSection ? $sidebarSectionOpenClass : '' }}"
                        data-collapse-toggle="sidebar-tenant"
                        aria-controls="sidebar-tenant"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            {{ __('sidebar.tenant') }}
                        </span>
                        <svg
                            class="h-4 w-4 transition-transform {{ $navigation->isTenantSection ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-tenant" class="mt-1 space-y-1 pl-10 {{ $navigation->isTenantSection ? '' : 'hidden' }}">
                        @if ($access->canShowTenantEventsSection())
                        <li>
                            <a href="{{ tenant_routes('admin.tenants.events.index') }}" class="flex items-center gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isTenantEventRoute ? $sidebarNavigationActiveClass : '' }}">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885 6.006 6.006 0 00-9.716 0 1 1 0 011.885 2.566A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm10.532 4.927a1 1 0 01.766 1.327l-.5 1a1 1 0 11-1.796-.894l.5-1a1 1 0 011.03-.433zM2 9a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm14 0a1 1 0 01-1 1h-1a1 1 0 110 2h1a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.event') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($access->canAccessStage)
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-full px-3 py-2 text-left text-[#e9f0ff] hover:bg-white/10 hover:text-white transition-all {{ $navigation->isStageSection ? $sidebarSectionOpenClass : '' }}"
                        data-collapse-toggle="sidebar-stage"
                        aria-controls="sidebar-stage"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2 5a1 1 0 011-1h14a1 1 0 011 1v7a1 1 0 01-1 1H3a1 1 0 01-1-1V5zm3 9h10v1H5v-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('sidebar.stage') }}
                        </span>
                        <svg
                            class="h-4 w-4 transition-transform {{ $navigation->isStageSection ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-stage" class="mt-1 space-y-1 pl-10 {{ $navigation->isStageSection ? '' : 'hidden' }}">
                        @if ($access->canShowStageMeetingNavItem())
                        <li>
                            <a href="{{ tenant_routes('admin.stage.meetings.index') }}" class="flex items-center gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isStageMeetingRoute ? $sidebarNavigationActiveClass : '' }}">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.meeting') }}</span>
                            </a>
                        </li>
                        @endif
                        @if ($access->canShowStageSessionNavItem())
                        <li>
                            <a href="{{ tenant_routes('admin.stage.sessions.index') }}" class="flex items-center gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isStageSessionRoute ? $sidebarNavigationActiveClass : '' }}">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.session') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($access->canAccountUsers)
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-full px-3 py-2 text-left text-[#e9f0ff] hover:bg-white/10 hover:text-white transition-all {{ $navigation->isAccountSection ? $sidebarSectionOpenClass : '' }}"
                        data-collapse-toggle="sidebar-layouts"
                        aria-controls="sidebar-layouts"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v2H3V4Zm0 4h8v8H4a1 1 0 0 1-1-1V8Zm10 0h4v7a1 1 0 0 1-1 1h-3V8Z" />
                            </svg>
                            {{ __('sidebar.account_management') }}
                        </span>
                        <svg
                            class="h-4 w-4 transition-transform {{ $navigation->isAccountSection ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-layouts" class="mt-1 space-y-1 pl-10 {{ $navigation->isAccountSection ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ tenant_routes('admin.users.index') }}" class="flex items-center justify-between gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isAccountUsersRoute ? $sidebarNavigationActiveClass : '' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 10a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm-7 7a7 7 0 0 1 14 0Z" />
                                    </svg>
                                    <span>{{ __('sidebar.users') }}</span>
                                </div>
                                @if ($latestVerifiedUsersCount > 0)
                                    <span class="inline-flex items-center justify-center rounded-full {{ $navigation->isAccountUsersRoute ? 'bg-white/25' : 'bg-white/20' }} px-2 py-0.5 text-xs font-medium text-white">
                                        {{ $latestVerifiedUsersCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($access->canShowAccessManagementSection())
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-full px-3 py-2 text-left text-[#e9f0ff] hover:bg-white/10 hover:text-white transition-all {{ $navigation->isAccessSection ? $sidebarSectionOpenClass : '' }}"
                        data-collapse-toggle="sidebar-access-management"
                        aria-controls="sidebar-access-management"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6a1 1 0 001 1h16a1 1 0 001-1v-.5A1.5 1.5 0 0017.5 3h-15zM19 8.5a1.5 1.5 0 01-1.5 1.5H18a1 1 0 00-1 1v4.5a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 003 15.5V11a1 1 0 00-1-1h-.5A1.5 1.5 0 000 8.5v-3A1.5 1.5 0 001.5 4h15A1.5 1.5 0 0018 5.5v3z" clip-rule="evenodd" />
                            </svg>
                            {{ __('sidebar.access_management') }}
                        </span>
                        <svg
                            class="h-4 w-4 transition-transform {{ $navigation->isAccessSection ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-access-management" class="mt-1 space-y-1 pl-10 {{ $navigation->isAccessSection ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ tenant_routes('admin.roles.index') }}" class="flex items-center gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isAccessRolesRoute ? $sidebarNavigationActiveClass : '' }}">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.roles') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ tenant_routes('admin.permissions.index') }}" class="flex items-center gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isAccessPermissionsRoute ? $sidebarNavigationActiveClass : '' }}">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.permissions') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if ($access->canLogActivities)
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-full px-3 py-2 text-left text-[#e9f0ff] hover:bg-white/10 hover:text-white transition-all {{ $navigation->isLogSection ? $sidebarSectionOpenClass : '' }}"
                        data-collapse-toggle="sidebar-log"
                        aria-controls="sidebar-log"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('sidebar.log') }}
                        </span>
                        <svg
                            class="h-4 w-4 transition-transform {{ $navigation->isLogSection ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-log" class="mt-1 space-y-1 pl-10 {{ $navigation->isLogSection ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ tenant_routes('admin.activities.index') }}" class="flex items-center gap-2 rounded-full px-3 py-1.5 text-white/80 hover:bg-white/10 hover:text-white transition-all {{ $navigation->isLogActivitiesRoute ? $sidebarNavigationActiveClass : '' }}">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.activities') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            </ul>
        </div>

        <div class="relative px-4 py-4 border-t border-white/10">
            <div class="flex items-center justify-center gap-6 mb-3">
                <a href="{{ hasTenant() ? tenant_routes('admin.settings.tab', ['tab' => 'personal']) : tenant_routes('admin.settings.tab', ['tab' => 'system']) }}" class="text-white/70 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20 rounded-lg text-sm p-2.5 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="#" id="sidebar-filter-button" class="text-white/70 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/20 rounded-lg text-sm p-2.5 transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path>
                    </svg>
                </a>
                @if (! hasTenant() || $AddOnsHelper::has($AddOnEnum::FEATURES_MULTI_LANGUAGE))
                    @include("components.i18n-switcher", ['position' => 'top', 'onDark' => true])
                @endif
            </div>
            <div class="text-center text-xs text-white/80">
                &copy; {{ date('Y') }} {{ $footerCompanyName ?? $SettingHelper::get('OWNER', config('app.name')) }}
            </div>
        </div>
    </div>
</aside>
