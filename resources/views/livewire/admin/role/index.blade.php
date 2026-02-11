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
                                    <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a1 1 0 0 0 1.414 1.414L4 10.414V17a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7z"></path></svg>
                                    {{ __("common.home") }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <a href="{{ route('admin.roles.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">{{ __("module_role.title") }}</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __("module_base.list") }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __("module_role.all_roles") }}</h1>
                        <div class="flex items-center gap-2">
                            <livewire:admin.role.role-import-component />
                            <livewire:admin.role.role-export-component />
                            <a
                                href="{{ route('admin.roles.create') }}"
                                class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white rounded-lg bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 dark:bg-green-400 dark:hover:bg-green-500 dark:focus:ring-green-700"
                            >
                                <svg class="w-4 h-4 mr-2 -ml-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10.75 2.75a.75.75 0 0 0-1.5 0V9H2.75a.75.75 0 0 0 0 1.5H9.25v6.25a.75.75 0 0 0 1.5 0V10.5h6.25a.75.75 0 0 0 0-1.5H10.75V2.75Z" />
                                </svg>
                                {{ __("module_base.create") }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <livewire:admin.role.role-index-data-table-component />
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
