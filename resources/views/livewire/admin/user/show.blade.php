<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        <div class="mx-auto">
            <div class="mb-4">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ tenant_routes('admin.dashboard.index') }}"
                                class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7z">
                                    </path>
                                </svg>
                                {{ __('common.home') }}
                            </a>
                        </li>
                        @can(\Src\V1\Api\User\Enums\PermissionEnum::USER_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    <a href="{{ tenant_routes('admin.users.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                            </path>
                                        </svg>
                                        {{ __('module_user.title') }}
                                    </a>
                                </div>
                            </li>
                        @endcan
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="inline-flex items-center ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                    aria-current="page">
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ __('module_base.view') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ __('module_user.view_user') }}</h1>
            </div>

            <div x-data="{ activeStep: 'profile' }" class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="flex justify-center w-full">
                    <ol
                        class="flex flex-wrap justify-center items-center w-full max-w-4xl p-4 text-sm font-medium text-center text-primary-600 dark:text-primary-500 sm:text-base sm:p-6 gap-x-3 gap-y-3 sm:gap-x-0 sm:gap-y-0">
                        <li
                            class="flex flex-none sm:flex-1 items-center after:content-[''] after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:hidden sm:after:inline-block after:mx-2 xl:after:mx-4 dark:after:border-gray-700">
                            <button type="button" @click="activeStep = 'profile'"
                                class="flex items-center after:content-['/'] sm:after:hidden after:mx-2 after:text-gray-200 dark:after:text-gray-500">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="inline-flex text-xs sm:text-sm whitespace-nowrap"
                                    :class="activeStep === 'profile' ? 'text-primary-600 dark:text-primary-400' :
                                        'text-gray-500 dark:text-gray-400'">{{ __('module_user.tab_profile') }}</span>
                            </button>
                        </li>
                        <li class="flex flex-none sm:flex-1 items-center">
                            <button type="button" @click="activeStep = 'access'" class="flex items-center">
                                <span
                                    class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 shrink-0 me-2">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="currentColor"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <span class="inline-flex text-xs sm:text-sm whitespace-nowrap"
                                    :class="activeStep === 'access' ? 'text-primary-600 dark:text-primary-400' :
                                        'text-gray-500 dark:text-gray-400'">{{ __('module_user.tab_access') }}</span>
                            </button>
                        </li>
                    </ol>
                </div>

                <div class="p-6 pt-0 space-y-8">
                    <section id="user-profile" x-show="activeStep === 'profile'">
                        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('module_user.tab_profile') }}</h2>
                        <div class="space-y-4">
                            <div class="w-full max-w-md mx-auto mb-8">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white text-center">{{ __('module_user.avatar') }}</label>
                                <div class="flex flex-col items-center gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="relative w-24 h-24">
                                            @if ($user->profile?->avatar)
                                                <img src="{{ asset('storage/' . $user->profile->avatar) }}"
                                                    alt="Avatar"
                                                    class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700 shadow-sm" />
                                            @else
                                                <div
                                                    class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-300 border-4 border-gray-200 dark:border-gray-300 flex items-center justify-center shadow-sm">
                                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.full_name') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $user->profile?->full_name ?? '-' }}</div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.email') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.interests') }}</label>
                                    <div class="bg-gray-50 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700">
                                        @if (!empty($user->profile?->interests))
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($user->profile->interests as $interest)
                                                    <span
                                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">{{ $interest }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.name') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $user->name }}</div>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.email_verified') }}</label>
                                    <div class="bg-gray-50 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700">
                                        @if ($user->email_verified_at)
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">{{ __('module_user.verified') }}
                                                -
                                                {{ \Carbon\Carbon::parse($user->email_verified_at)->format('Y-m-d H:i') }}</span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">{{ __('module_user.unverified') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.created_at') }}</label>
                                    <div
                                        class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                        {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('Y-m-d H:i:s') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="user-access" x-show="activeStep === 'access'">
                        <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('module_user.tab_access') }}</h2>
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.roles') }}</label>
                                <div class="bg-gray-50 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700">
                                    @if ($user->roles->isEmpty())
                                        <span
                                            class="text-sm text-gray-500 dark:text-gray-400">{{ __('module_user.no_roles_assigned') }}</span>
                                    @else
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">{{ $role->name }}
                                                    <span
                                                        class="ml-1 text-gray-500 dark:text-gray-400">({{ $role->guard_name }})</span></span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if (config('tenancy.is_tenancy'))
                                <div>
                                    <label
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_user.tenant') }}</label>
                                    <div class="bg-gray-50 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700">
                                        @if ($user->tenant_id)
                                            @php
                                                $tenant = $user->tenant;
                                                $domain =
                                                    $tenant && $tenant->domains ? $tenant->domains->first() : null;
                                                $displayText = $domain ? $domain->domain : $user->tenant_id;
                                            @endphp
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">{{ $displayText }}</span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">-</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        @can(\Src\V1\Api\User\Enums\PermissionEnum::USER_VIEW->value)
                            <a href="{{ tenant_routes('admin.users.index') }}"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">{{ __('module_base.back_to_list') }}</a>
                        @else
                            <span></span>
                        @endcan
                        @can(\Src\V1\Api\User\Enums\PermissionEnum::USER_UPDATE->value, $user)
                            <a href="{{ tenant_routes('admin.users.edit', $user) }}"
                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-center text-white rounded-lg btn-tertiary">{{ __('module_user.edit_user') }}</a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/user.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/user.js') }}"></script>
@endpush
