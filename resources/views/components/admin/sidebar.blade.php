@php
    $currentRoute = request()->route()->getName();
    $isUsersRoute = str_starts_with($currentRoute, 'admin.users.');
    $isAccessManagementRoute = str_starts_with($currentRoute, 'admin.permissions.') || str_starts_with($currentRoute, 'admin.roles.');
    $isLogRoute = str_starts_with($currentRoute, 'admin.activities.');
    $latestVerifiedUsersCount = \App\Models\User::query()
        ->whereNotNull('email_verified_at')
        ->where('email_verified_at', '>=', now()->subMinutes(5))
        ->count();
@endphp

@push('styles')
<link href="{{ asset('css/components/sidebar.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/sidebar.js') }}"></script>
@endpush

<div
    id="sidebar-backdrop"
    data-sidebar-backdrop
    class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-sm hidden lg:hidden transition-opacity duration-200"
></div>

<aside
    id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 border-gray-200 bg-white dark:border-gray-600 dark:bg-gray-700 transform transition-transform duration-200 -translate-x-full lg:translate-x-0"
>
    <div class="flex h-full flex-col">
        <div class="flex h-20 flex-col items-center justify-center border-gray-200 px-4 dark:border-gray-600 relative">
            <button
                type="button"
                data-sidebar-toggle
                class="absolute right-4 top-1/2 -translate-y-1/2 lg:hidden inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-600"
            >
                <span class="sr-only">{{ __('common.close_sidebar') }}</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M14 6l-8 8" />
                </svg>
            </button>
            <div class="inline-flex h-8 w-24 items-center justify-center rounded-full">
                <img
                    src="{{ asset('asset/brand-light.png') }}"
                    alt="{{ config('app.name') }}"
                    class="block object-contain dark:hidden"
                />
                <img
                    src="{{ asset('asset/brand-dark.png') }}"
                    alt="{{ config('app.name') }}"
                    class="hidden object-contain dark:block"
                />
            </div>
            <div class="text-xs font-semibold text-gray-900 dark:text-white">
                {{ \Illuminate\Support\Str::headline(config('app.name')) }}
            </div>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4">
            <div class="mb-4 block lg:hidden">
                @include("components.admin.search", ["mobileOnly" => true])
            </div>
            <ul class="space-y-1 text-sm font-medium text-gray-700 dark:text-gray-200">
                <li>
                    <a
                        href="{{ route('admin.dashboard.index') }}"
                        class="flex items-center rounded-lg px-2 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 {{ in_array($currentRoute, ['admin.dashboard', 'admin.dashboard.index']) ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white' : '' }}"
                    >
                        <svg class="mr-3 h-5 w-5 {{ in_array($currentRoute, ['admin.dashboard', 'admin.dashboard.index']) ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 8 8">
                            <circle cx="4" cy="4" r="3" />
                        </svg>
                        {{ __('sidebar.dashboard') }}
                    </a>
                </li>
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600"
                        data-collapse-toggle="sidebar-layouts"
                        aria-controls="sidebar-layouts"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v2H3V4Zm0 4h8v8H4a1 1 0 0 1-1-1V8Zm10 0h4v7a1 1 0 0 1-1 1h-3V8Z" />
                            </svg>
                            {{ __('sidebar.account_management') }}
                        </span>
                        <svg
                            class="h-4 w-4 text-gray-500 transition-transform {{ $isUsersRoute ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-layouts" class="mt-1 space-y-1 pl-10 text-gray-600 dark:text-gray-300 {{ $isUsersRoute ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between gap-2 rounded-lg px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 {{ $isUsersRoute ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white' : '' }}">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 {{ $isUsersRoute ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 10a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm-7 7a7 7 0 0 1 14 0Z" />
                                    </svg>
                                    <span>{{ __('sidebar.users') }}</span>
                                </div>
                                @if ($latestVerifiedUsersCount > 0)
                                    <span class="inline-flex items-center justify-center rounded-full bg-blue-600 px-2 py-0.5 text-xs font-medium text-white dark:bg-blue-500">
                                        {{ $latestVerifiedUsersCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600"
                        data-collapse-toggle="sidebar-access-management"
                        aria-controls="sidebar-access-management"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.5 4A1.5 1.5 0 001 5.5V6a1 1 0 001 1h16a1 1 0 001-1v-.5A1.5 1.5 0 0017.5 3h-15zM19 8.5a1.5 1.5 0 01-1.5 1.5H18a1 1 0 00-1 1v4.5a1.5 1.5 0 01-1.5 1.5h-11A1.5 1.5 0 003 15.5V11a1 1 0 00-1-1h-.5A1.5 1.5 0 000 8.5v-3A1.5 1.5 0 001.5 4h15A1.5 1.5 0 0018 5.5v3z" clip-rule="evenodd" />
                            </svg>
                            {{ __('sidebar.access_management') }}
                        </span>
                        <svg
                            class="h-4 w-4 text-gray-500 transition-transform {{ $isAccessManagementRoute ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-access-management" class="mt-1 space-y-1 pl-10 text-gray-600 dark:text-gray-300 {{ $isAccessManagementRoute ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 {{ str_starts_with($currentRoute, 'admin.roles.') ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg class="h-4 w-4 {{ str_starts_with($currentRoute, 'admin.roles.') ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.roles') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.permissions.index') }}" class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 {{ str_starts_with($currentRoute, 'admin.permissions.') ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg class="h-4 w-4 {{ str_starts_with($currentRoute, 'admin.permissions.') ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.permissions') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-lg px-2 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-600"
                        data-collapse-toggle="sidebar-log"
                        aria-controls="sidebar-log"
                    >
                        <span class="inline-flex items-center">
                            <svg class="mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            {{ __('sidebar.log') }}
                        </span>
                        <svg
                            class="h-4 w-4 text-gray-500 transition-transform {{ $isLogRoute ? '' : 'rotate-90' }}"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 10 6"
                            data-collapse-icon
                        >
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                        </svg>
                    </button>
                    <ul id="sidebar-log" class="mt-1 space-y-1 pl-10 text-gray-600 dark:text-gray-300 {{ $isLogRoute ? '' : 'hidden' }}">
                        <li>
                            <a href="{{ route('admin.activities.index') }}" class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-gray-100 dark:hover:bg-gray-600 {{ str_starts_with($currentRoute, 'admin.activities.') ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white' : '' }}">
                                <svg class="h-4 w-4 {{ str_starts_with($currentRoute, 'admin.activities.') ? 'text-gray-700 dark:text-gray-300' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ __('sidebar.activities') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="relative px-4 py-4">
            <div class="flex items-center justify-center gap-6">
                <a href="{{ route('admin.settings.tab', ['tab' => 'system']) }}" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <a href="#" id="sidebar-filter-button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"></path>
                    </svg>
                </a>
                @include("components.i18n-switcher")
            </div>
        </div>
    </div>
</aside>
