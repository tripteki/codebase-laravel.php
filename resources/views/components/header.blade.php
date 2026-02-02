<header class="bg-white/80 border-gray-200 px-4 py-4 backdrop-blur dark:border-gray-600 dark:bg-gray-700/80">
    <div class="container mx-auto flex items-center justify-between">
        @php
            $isAdminRoute = request()->routeIs('admin.*') || str_starts_with(request()->path(), 'admin/'); if (auth()->check() && $isAdminRoute) session()->put('admin_page', true);
            $isAdminPage = auth()->check() && ($isAdminRoute || (request()->header('referer') && str_contains(parse_url(request()->header('referer'), PHP_URL_PATH) ?? '', '/admin/')) || (request()->hasHeader('X-Livewire') && session()->has('admin_page')));
        @endphp

        @if ($isAdminPage)
            <div class="flex items-center gap-3 flex-1">
                <button
                    type="button"
                    data-sidebar-toggle
                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700"
                >
                    <span class="sr-only">{{ __('common.open_sidebar') }}</span>
                    <svg id="sidebar-toggle-open-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h14M3 10h14M3 15h10" />
                    </svg>
                    <svg id="sidebar-toggle-close-icon" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l-5-5m0 10l5-5" />
                    </svg>
                </button>
                @include("components.admin.search")
            </div>
        @else
            <h1 class="text-xl font-bold">
                {{ __('common.welcome') }}
            </h1>
        @endif

        <div class="flex items-center gap-3">
            @include("components.theme-toggle")

            @if (! auth()->check())
                @include("components.i18n-switcher")
            @endif

            @if (auth()->check())
                @include("components.notification")
                <div class="relative">
                    @include("components.user-dropdown")
                </div>
            @endif
        </div>
    </div>
</header>
