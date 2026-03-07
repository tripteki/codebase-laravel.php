<div class="relative z-0 min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
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
                                <span class="inline-flex items-center ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                    aria-current="page">
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('module_setting.title') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                    {{ __('module_setting.title') }}</h1>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700 mt-8"
            x-data="{ activeTab: @js($activeTab) }" x-init="activeTab = @js($activeTab)">
            <div class="border-gray-200 dark:border-gray-700 p-5">
                <div class="sm:hidden">
                    <label for="tabs-icons" class="sr-only">{{ __('module_setting.select_tab') }}</label>
                    <select id="tabs-icons" x-model="activeTab"
                        @change="window.location.href = $event.target.value === 'personal' ? '{{ tenant_routes('admin.settings.tab', ['tab' => 'personal']) }}' : '{{ tenant_routes('admin.settings.tab', ['tab' => 'system']) }}'"
                        class="input-primary block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        <option value="personal">{{ __('module_setting.personal') }}</option>
                        @if ($canSeeSystemTab)
                            <option value="system">{{ __('module_setting.system') }}</option>
                        @endif
                    </select>
                </div>
                <ul class="hidden sm:flex -space-x-px text-sm font-medium text-center text-gray-500 dark:text-gray-400"
                    role="tablist" aria-label="{{ __('module_setting.select_tab') }}">
                    <li class="w-full focus-within:z-10" role="presentation">
                        <a id="settings-tab-personal"
                            href="{{ tenant_routes('admin.settings.tab', ['tab' => 'personal']) }}" role="tab"
                            :aria-selected="activeTab === 'personal' ? 'true' : 'false'"
                            :class="activeTab === 'personal' ? 'tab-primary-active' :
                                'text-gray-500 bg-white border-gray-300 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 tab-primary-hover'"
                            class="inline-flex items-center justify-center w-full border {{ $canSeeSystemTab ? 'rounded-l-lg' : 'rounded-lg' }} font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ __('module_setting.personal') }}
                        </a>
                    </li>
                    @if ($canSeeSystemTab)
                        <li class="w-full focus-within:z-10" role="presentation">
                            <a id="settings-tab-system"
                                href="{{ tenant_routes('admin.settings.tab', ['tab' => 'system']) }}" role="tab"
                                :aria-selected="activeTab === 'system' ? 'true' : 'false'"
                                :class="activeTab === 'system' ? 'tab-primary-active' :
                                    'text-gray-500 bg-white border border-gray-300 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 tab-primary-hover'"
                                class="inline-flex items-center justify-center w-full border rounded-r-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset">
                                <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                        d="M20 6H10m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4m16 6h-2m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4m16 6H10m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4" />
                                </svg>
                                {{ __('module_setting.system') }}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            <div role="tabpanels">
                <div x-show="activeTab === 'personal'" x-cloak class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                    id="settings-panel-personal" role="tabpanel" aria-labelledby="settings-tab-personal">
                    <livewire:admin.setting.user-setting-index-personal-component />
                </div>
                @if ($canSeeSystemTab)
                    <div x-show="activeTab === 'system'" x-cloak class="pt-4 pl-4 pr-4 rounded-lg bg-gray-50 dark:bg-gray-800"
                        id="settings-panel-system" role="tabpanel" aria-labelledby="settings-tab-system">
                        <livewire:admin.setting.user-setting-index-system-component />
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('components.footer')
</div>

@push('styles')
    <link href="{{ asset('css/module/setting.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/setting.js') }}"></script>
@endpush
