@php
    $showLogout = $showLogout ?? false;
@endphp

<header class="bg-white/80 px-4 py-4 backdrop-blur dark:bg-gray-900/80">
    <div class="container mx-auto flex items-center justify-between">
        <h1 class="text-xl font-bold">
            {{ __('common.welcome') }}
        </h1>

        <div class="flex items-center gap-3">
            @include("components.theme-toggle")
            @include("components.i18n-switcher")

            @if ($showLogout && auth()->check())
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex h-9 items-center gap-1 rounded-md border border-gray-300 bg-white px-3 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700"
                    >
                        <span>{{ __('auth.logout') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M6 10a.75.75 0 0 1 .75-.75h9.546l-1.048-.943a.75.75 0 1 1 1.004-1.114l2.5 2.25a.75.75 0 0 1 0 1.114l-2.5 2.25a.75.75 0 1 1-1.004-1.114l1.048-.943H6.75A.75.75 0 0 1 6 10Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</header>
