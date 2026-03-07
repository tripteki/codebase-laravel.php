<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        @php
            $UserModel = App\Models\User::class;
        @endphp
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
                                        d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a 1 1 0 0 0 1.414 1.414L4 10.414V17a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-2a 1 1 0 0 1 1-1h2a 1 1 0 0 1 1 1v2a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-6.586l.293.293A1 1 0 0 0 18.707 9.293l-7-7Z" />
                                </svg>
                                {{ __('common.home') }}
                            </a>
                        </li>
                        @can(\Src\V1\Api\Log\Enums\PermissionEnum::ACTIVITY_VIEW->value)
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <a href="{{ tenant_routes('admin.activities.index') }}"
                                        class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zm6-4a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zm6-3a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('module_activity.title') }}
                                    </a>
                                </div>
                            </li>
                        @endcan
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z"
                                        clip-rule="evenodd" />
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
                    {{ __('module_activity.view_activity') }}
                </h1>
            </div>

            <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.description') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.description_label') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $activity->description }}
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.log_name') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.log_name_label') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $activity->log_name ?? '-' }}
                            </div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.causer') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.causer_label') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                @if ($activity->causer)
                                    {{ $activity->causer instanceof $UserModel ? $activity->causer->name : $activity->causer_id }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.subject_type') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.subject_type_label') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $activity->subject_type ? class_basename($activity->subject_type) : '-' }}
                            </div>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.subject_id') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.subject_id_label') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                {{ $activity->subject_id ?? '-' }}
                            </div>
                        </div>
                    </div>
                    @if (config('tenancy.is_tenancy'))
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6">
                                <label
                                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.tenant') }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('module_activity.tenant_label') }}</p>
                                <div class="bg-gray-50 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700">
                                    @if ($activity->tenant_id)
                                        @php
                                            $tenant = $activity->tenant;
                                            $domain = $tenant && $tenant->domains ? $tenant->domains->first() : null;
                                            $displayText = $domain ? $domain->domain : $activity->tenant_id;
                                        @endphp
                                        <span
                                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium badge-primary dark:badge-primary-dark">
                                            {{ $displayText }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.properties') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.properties_label') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                <pre class="whitespace-pre-wrap text-xs">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-3">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('module_activity.created_at') }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('module_activity.created_at_description') }}</p>
                            <div
                                class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                @if ($activity->created_at)
                                    {{ \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d H:i:s') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        @can(\Src\V1\Api\Log\Enums\PermissionEnum::ACTIVITY_VIEW->value)
                            <a href="{{ tenant_routes('admin.activities.index') }}"
                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                {{ __('module_base.back_to_list') }}
                            </a>
                        @else
                            <span></span>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </main>
    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/activity.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/activity.js') }}"></script>
@endpush
