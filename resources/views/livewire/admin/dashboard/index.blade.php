<div>
    @include("components.admin.sidebar")

    <div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
        @include("components.header")

        <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
            <div class="p-4 bg-white block sm:flex items-center justify-between rounded-t-lg border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
                <div class="w-full mb-1">
                    <nav class="flex mb-5" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                    <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a 1 1 0 0 0 1.414 1.414L4 10.414V17a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-2a 1 1 0 0 1 1-1h2a 1 1 0 0 1 1 1v2a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z" />
                                    </svg>
                                    {{ __("common.home") }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __("common.dashboard") }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __("common.dashboard_title") }}</h1>
                </div>
            </div>

            <div class="mt-8 space-y-6">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="mb-4 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __("common.user_status_chart") }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __("common.user_status_chart_description") }}</p>
                        </div>
                    </div>
                    <div
                        id="user-status-chart"
                        style="min-height: 300px;"
                        data-label-unverified="{{ __('common.unverified_users') }}"
                        data-label-verified="{{ __('common.verified_users') }}"
                        data-unverified-count="{{ $userStats['unverified_users'] ?? 0 }}"
                        data-verified-count="{{ $userStats['verified_users'] ?? 0 }}"
                    ></div>
                </div>

                <div>
                    <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{{ __("common.system_stats") }}</h2>
                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __("common.total_settings") }}</p>
                                    <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $systemStats['total_settings'] }}</p>
                                </div>
                                <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __("common.active_settings") }}</p>
                                    <p class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400">{{ $systemStats['active_settings'] }}</p>
                                </div>
                                <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __("common.deleted_settings") }}</p>
                                    <p class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400">{{ $systemStats['deleted_settings'] }}</p>
                                </div>
                                <div class="rounded-full bg-red-100 p-3 dark:bg-red-900">
                                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include("components.footer")
    </div>
</div>

@push('styles')
<link href="{{ asset('css/module/dashboard.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/module/dashboard.js') }}"></script>
@endpush
