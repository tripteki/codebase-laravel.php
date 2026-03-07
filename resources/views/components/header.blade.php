@php
    $AddOnsHelper = App\Helpers\AddOnsHelper::class;
    $AddOnEnum = App\Enum\Event\AddOnEnum::class;

    $isAdminRoute = request()->routeIs('admin.*') || str_starts_with(request()->path(), 'admin/');
    if (auth()->check() && $isAdminRoute) {
        session()->put('admin_page', true);
    }
    $isAdminPage = auth()->check() && (
        $isAdminRoute
        || (request()->header('referer') && str_contains((parse_url(request()->header('referer'), PHP_URL_PATH) ?? ''), '/admin/'))
        || (request()->hasHeader('X-Livewire') && session()->has('admin_page'))
    );
@endphp

<header
    @class([
        'relative z-10 px-4 py-4 lg:px-6',
        'bg-[color-mix(in_srgb,var(--tenant-primary)_11%,#f9fafb_89%)] dark:border-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_14%,#030712_86%)]' => config('tenancy.is_tenancy') && hasTenant() && ! $isAdminPage,
    ])>
    <div class="flex items-center justify-between p-2 rounded-lg bg-white/80 border-gray-200 backdrop-blur shadow-sm dark:border-gray-600 dark:bg-gray-700/80">
        @if ($isAdminPage)
            <div class="flex items-center gap-2 flex-1">
                <button
                    type="button"
                    data-sidebar-toggle
                    class="hidden lg:inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700 transition-colors"
                    aria-expanded="true"
                    aria-controls="sidebarCol"
                    title="{{ __('common.open_sidebar') }}"
                >
                    <span class="sr-only">{{ __('common.open_sidebar') }}</span>
                    <svg id="sidebar-toggle-open-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M14 2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zM2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z"/>
                        <path d="M3 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
                    </svg>
                    <svg id="sidebar-toggle-close-icon" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z"/>
                        <path d="M13 4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z"/>
                    </svg>
                </button>
                <button
                    type="button"
                    class="inline-flex lg:hidden h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700 transition-colors"
                    data-sidebar-toggle
                    aria-controls="admin-sidebar"
                    title="{{ __('common.menu') }}"
                >
                    <span class="sr-only">{{ __('common.menu') }}</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                @include("components.admin.search")
            </div>
        @else
            @php
                $defaultBrandLight = asset('asset/brand-light.png');
                $defaultBrandDark = asset('asset/brand-dark.png');
                $brandLightSrc = config('tenancy.is_tenancy') && hasTenant() && tenant('brand_light')
                    ? asset('storage/' . tenant('brand_light'))
                    : $defaultBrandLight;
                $brandDarkSrc = config('tenancy.is_tenancy') && hasTenant() && tenant('brand_dark')
                    ? asset('storage/' . tenant('brand_dark'))
                    : $defaultBrandDark;
            @endphp

            <div class="inline-flex h-8 w-28 items-center justify-center rounded-full">
                <img
                    src="{{ $brandLightSrc }}"
                    alt="{{ config('app.name') }}"
                    class="block object-contain dark:hidden"
                />
                <img
                    src="{{ $brandDarkSrc }}"
                    alt="{{ config('app.name') }}"
                    class="hidden object-contain dark:block"
                />
            </div>
        @endif

        <div class="flex items-center gap-2">
            @if (auth()->check())
                @include("components.notification")
                <a href="{{ hasTenant() ? tenant_routes('admin.settings.tab', ['tab' => 'personal']) : tenant_routes('admin.settings.tab', ['tab' => 'system']) }}" class="hidden lg:inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700 transition-colors" aria-label="{{ __('common.settings') }}" title="{{ __('common.settings') }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            @endif

            @include("components.theme-toggle")

            @if (! auth()->check() && (!config('tenancy.is_tenancy') || !hasTenant() || $AddOnsHelper::has($AddOnEnum::FEATURES_MULTI_LANGUAGE)))
                @include("components.i18n-switcher")
            @endif

            @if (auth()->check())
                <div class="relative">
                    @include("components.user-dropdown")
                </div>
            @endif
        </div>
    </div>
</header>
