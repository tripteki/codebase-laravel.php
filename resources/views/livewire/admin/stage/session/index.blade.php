<div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        @php
            $AddOnsHelper = App\Helpers\AddOnsHelper::class;
            $AddOnEnum = App\Enum\Event\AddOnEnum::class;
            $StageSessionPermissionEnum = App\Enum\Stage\StageSessionPermissionEnum::class;

            $sessionActiveTab = request()->routeIs('admin.stage.sessions.board') ? 'board' : 'table';
        @endphp

        <div
            class="p-4 bg-white block sm:flex items-center justify-between rounded-t-lg border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
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
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a1 1 0 0 1 1.414-1.414l4 4a1 1 0 0 1 0 1.414l-4 4a1 1 0 0 1-1.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ tenant_routes('admin.stage.sessions.index') }}"
                                    class="inline-flex items-center ml-1 text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('module_stage.session_title') }}
                                </a>
                            </div>
                        </li>
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
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $sessionActiveTab === 'board' ? __('module_stage.tab_board') : __('module_base.list') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                        {{ $sessionActiveTab === 'board' ? __('module_stage.sessions_board_title') : __('module_stage.all_sessions') }}
                    </h1>
                    @if ($sessionActiveTab === 'table')
                        <div class="flex items-center gap-2">
                            @can($StageSessionPermissionEnum::STAGE_SESSION_IMPORT->value)
                                @if ($AddOnsHelper::has($AddOnEnum::FEATURES_IMPORT))
                                    <livewire:admin.stage.session-import-component />
                                @endif
                            @endcan
                            @can($StageSessionPermissionEnum::STAGE_SESSION_EXPORT->value)
                                @if ($AddOnsHelper::has($AddOnEnum::FEATURES_EXPORT))
                                    <livewire:admin.stage.session-export-component />
                                @endif
                            @endcan
                            @can($StageSessionPermissionEnum::STAGE_SESSION_CREATE->value)
                                <a href="{{ tenant_routes('admin.stage.sessions.create') }}"
                                    class="inline-flex items-center justify-center px-2 py-1 text-xs font-medium text-white rounded-lg btn-secondary">
                                    <svg class="w-3.5 h-3.5 mr-1.5 -ml-0.5" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10.75 2.75a.75.75 0 0 0-1.5 0V9H2.75a.75.75 0 0 0 0 1.5H9.25v6.25a.75.75 0 0 0 1.5 0V10.5h6.25a.75.75 0 0 0 0-1.5H10.75V2.75Z" />
                                    </svg>
                                    {{ __('module_base.create') }}
                                </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white border-x border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:text-gray-400 dark:border-gray-700"
                role="tablist">
                <li class="m-1">
                    <a href="{{ tenant_routes('admin.stage.sessions.table') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 border-b-2 rounded-t-lg transition-colors {{ $sessionActiveTab === 'table'
                            ? 'text-primary-600 border-primary-600 dark:text-primary-400 dark:border-primary-400 bg-primary-50/40 dark:bg-primary-900/10'
                            : 'border-transparent hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-200' }}"
                        aria-current="{{ $sessionActiveTab === 'table' ? 'page' : 'false' }}">
                        <svg class="w-4 h-4 {{ $sessionActiveTab === 'table' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h10M4 18h6" />
                        </svg>
                        <span>{{ __('module_stage.tab_table') }}</span>
                    </a>
                </li>
                <li class="hidden m-1">
                    <a href="{{ tenant_routes('admin.stage.sessions.board') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 border-b-2 rounded-t-lg transition-colors {{ $sessionActiveTab === 'board'
                            ? 'text-primary-600 border-primary-600 dark:text-primary-400 dark:border-primary-400 bg-primary-50/40 dark:bg-primary-900/10'
                            : 'border-transparent hover:text-gray-700 hover:border-gray-300 dark:hover:text-gray-200' }}"
                        aria-current="{{ $sessionActiveTab === 'board' ? 'page' : 'false' }}">
                        <svg class="w-4 h-4 {{ $sessionActiveTab === 'board' ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 5h4v14H5zM10 5h4v9h-4zM15 5h4v6h-4z" />
                        </svg>
                        <span>{{ __('module_stage.tab_board') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mt-4">
            @if ($sessionActiveTab === 'table')
                <livewire:admin.stage.session-index-data-table-component wire:poll.5s />
            @else
                <div></div>
            @endif
        </div>
    </main>
    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/stage.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/stage.js') }}"></script>
@endpush
