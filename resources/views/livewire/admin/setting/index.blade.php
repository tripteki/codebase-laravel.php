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
                                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __("module_setting.title") }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __("module_setting.title") }}</h1>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mt-8" x-data="{ activeTab: @js($activeTab) }" x-init="activeTab = @js($activeTab)">
                <div class="border-gray-200 dark:border-gray-700 p-5">
                    <div class="sm:hidden">
                        <label for="tabs-icons" class="sr-only">{{ __("module_setting.select_tab") }}</label>
                        <select id="tabs-icons" x-model="activeTab" @change="window.location.href = $event.target.value === 'personal' ? '{{ route('admin.settings.tab', ['tab' => 'personal']) }}' : '{{ route('admin.settings.tab', ['tab' => 'system']) }}'" class="block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                            <option value="personal">{{ __("module_setting.personal") }}</option>
                            <option value="system">{{ __("module_setting.system") }}</option>
                        </select>
                    </div>
                    <ul class="hidden text-sm font-medium text-center text-gray-500 sm:flex -space-x-px dark:text-gray-400">
                        <li class="w-full focus-within:z-10">
                            <a
                                href="{{ route('admin.settings.tab', ['tab' => 'personal']) }}"
                                wire:navigate
                                :class="activeTab === 'personal' ? 'text-blue-600 bg-blue-50 border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white'"
                                class="inline-flex items-center justify-center w-full border rounded-l-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800"
                            >
                                <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                </svg>
                                {{ __("module_setting.personal") }}
                            </a>
                        </li>
                        <li class="w-full focus-within:z-10">
                            <a
                                href="{{ route('admin.settings.tab', ['tab' => 'system']) }}"
                                wire:navigate
                                :class="activeTab === 'system' ? 'text-blue-600 bg-blue-50 border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-600' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white'"
                                class="inline-flex items-center justify-center w-full border rounded-r-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800"
                            >
                                <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M20 6H10m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4m16 6h-2m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4m16 6H10m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4"/>
                                </svg>
                                {{ __("module_setting.system") }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div x-show="activeTab === 'personal'" x-cloak class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <livewire:admin.setting.user-setting-index-personal-component />
                    </div>
                    <div x-show="activeTab === 'system'" x-cloak class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <livewire:admin.setting.user-setting-index-system-component />
                    </div>
                </div>
            </div>
        </main>

        @include("components.footer")
    </div>
</div>

@push('styles')
<link href="{{ asset('css/module/setting.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/module/setting.js') }}"></script>
@endpush
