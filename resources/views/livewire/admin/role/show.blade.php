<div>
    @include("components.admin.sidebar")

    <div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
        @include("components.header")

        <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
            <div class="mx-auto">
                <div class="mb-4">
                    <nav class="flex mb-5" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                    <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a 1 1 0 0 0 1.414 1.414L4 10.414V17a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-2a 1 1 0 0 1 1-1h2a 1 1 0 0 1 1 1v2a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-6.586l.293.293A1 1 0 0 0 18.707 9.293l-7-7Z" />
                                    </svg>
                                    {{ __("common.home") }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z" clip-rule="evenodd" /></svg>
                                    <a href="{{ route('admin.roles.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                        {{ __("module_role.title") }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">
                                        {{ __("module_base.view") }}
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                        {{ __("module_role.view_role") }}
                    </h1>
                </div>

                <div class="bg-white rounded-lg shadow dark:bg-gray-800">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __("module_role.name") }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ __("module_role.name_label") }}</p>
                                <div class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                    {{ $role->name }}
                                </div>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __("module_role.guard_name") }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ __("module_role.guard_name_label") }}</p>
                                <div class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                    {{ $role->guard_name }}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __("module_role.created_at") }}</label>
                                <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ __("module_role.created_at_description") }}</p>
                                <div class="bg-gray-50 text-gray-900 sm:text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:text-white">
                                    @if ($role->created_at)
                                        {{ \Carbon\Carbon::parse($role->created_at)->format('Y-m-d H:i:s') }}
                                    @else
                                        &mdash;
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-span-6">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __("module_role.permissions") }}</label>
                            <p class="mb-2 text-xs text-gray-500 dark:text-gray-400">{{ __("module_role.permissions_label") }}</p>
                            <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-lg p-4 bg-white dark:bg-gray-700 dark:border-gray-600">
                                @if ($role->permissions->isEmpty())
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __("module_role.no_permissions_assigned") }}</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($role->permissions as $permission)
                                            <div class="flex items-center space-x-2 p-2 rounded bg-gray-50 dark:bg-gray-600">
                                                <span class="text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $permission->guard_name }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                                {{ __("module_base.back_to_list") }}
                            </a>
                            <a
                                href="{{ route('admin.roles.edit', $role) }}"
                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-center text-white rounded-lg bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:ring-yellow-300 dark:bg-yellow-400 dark:hover:bg-yellow-500 dark:focus:ring-yellow-700"
                            >
                                {{ __("module_role.edit_role") }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('styles')
<link href="{{ asset('css/module/role.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/module/role.js') }}"></script>
@endpush
