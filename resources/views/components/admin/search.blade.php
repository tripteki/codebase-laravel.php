@php
    use App\Models\User;
    use App\Helpers\Tenant\TenantAccess;

    $isMobile = isset($mobileOnly) && $mobileOnly;
    $isDesktop = ! $isMobile;

    $searchId = isset($mobileOnly) && $mobileOnly ? 'sidebar-search' : 'search';
    $categoryDropdownId = $searchId . '-category-dropdown';
    $searchAuthUser = auth()->user();
    $searchAccess = TenantAccess::forUser($searchAuthUser instanceof User ? $searchAuthUser : null);
@endphp

<div class="relative lg:w-full lg:max-w-2xl flex-grow-1 mx-3 {{ $isMobile ? 'block lg:hidden' : 'hidden lg:block' }}">
    <input type="hidden" id="{{ $searchId }}-category" value="all" />

    @if ($isMobile)
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-10">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <div x-data="{ searchValue: '' }" class="relative">
            <input
                type="text"
                id="{{ $searchId }}-input"
                x-model="searchValue"
                class="input-primary search-input block w-full rounded-full border border-gray-300 bg-gray-50 px-5 py-2.5 pl-10 pr-10 text-sm text-gray-900 placeholder-gray-500 transition-all focus:bg-white dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400"
                placeholder="{{ __('common.search_placeholder') }}"
                data-search-url="{{ tenant_routes('admin.search.index') }}"
                data-search-not-found="{{ __('common.search_not_found') }}"
            />
            <button
                type="button"
                x-show="searchValue.length > 0"
                x-on:click="searchValue = ''; document.getElementById('{{ $searchId }}-input').value = ''; document.getElementById('{{ $searchId }}-input').dispatchEvent(new Event('input'));"
                class="absolute inset-y-0 right-0 flex items-center pr-3 z-10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                aria-label="{{ __('common.clear') }}"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @else
        <form class="flex rounded-lg shadow-sm -space-x-px" role="search" onsubmit="return false;">
            <label for="{{ $searchId }}-input" class="sr-only">{{ __('common.search_placeholder') }}</label>
            <button
                id="{{ $categoryDropdownId }}-button"
                type="button"
                data-dropdown-toggle="{{ $categoryDropdownId }}"
                class="inline-flex shrink-0 z-10 items-center text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-600 font-medium text-sm px-4 py-2.5 rounded-l-lg focus:outline-none"
            >
                <span id="{{ $searchId }}-category-label">{{ __('common.all_categories') }}</span>
                <svg class="w-4 h-4 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7"/>
                </svg>
            </button>
            <div id="{{ $categoryDropdownId }}" class="z-10 hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg w-44">
                <ul class="py-1 text-sm text-gray-700 dark:text-gray-300" aria-labelledby="{{ $categoryDropdownId }}-button">
                    <li>
                        <button type="button" data-search-category-option data-category="all" data-label="{{ __('common.all_categories') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                            {{ __('common.all_categories') }}
                        </button>
                    </li>
                    @if ($searchAccess->canAccountUsers)
                        <li>
                            <button type="button" data-search-category-option data-category="users" data-label="{{ __('sidebar.users') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                {{ __('sidebar.users') }}
                            </button>
                        </li>
                    @endif
                    @if ($searchAccess->canSearchEventsCategory())
                        <li>
                            <button type="button" data-search-category-option data-category="events" data-label="{{ __('sidebar.event') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                {{ __('sidebar.event') }}
                            </button>
                        </li>
                    @endif
                    @if ($searchAccess->canAccessRoles)
                        <li>
                            <button type="button" data-search-category-option data-category="roles" data-label="{{ __('sidebar.roles') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                {{ __('sidebar.roles') }}
                            </button>
                        </li>
                    @endif
                    @if ($searchAccess->canAccessPermissions)
                        <li>
                            <button type="button" data-search-category-option data-category="permissions" data-label="{{ __('sidebar.permissions') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                {{ __('sidebar.permissions') }}
                            </button>
                        </li>
                    @endif
                    @if ($searchAccess->canSearchMeetingsCategory())
                        <li>
                            <button type="button" data-search-category-option data-category="meetings" data-label="{{ __('sidebar.meeting') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                {{ __('sidebar.meeting') }}
                            </button>
                        </li>
                    @endif
                    @if ($searchAccess->canSearchSessionsCategory())
                        <li>
                            <button type="button" data-search-category-option data-category="sessions" data-label="{{ __('sidebar.session') }}" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md">
                                {{ __('sidebar.session') }}
                            </button>
                        </li>
                    @endif
                </ul>
            </div>
            <div x-data="{ searchValue: '' }" class="relative flex-1 min-w-0">
                <input
                    type="search"
                    id="{{ $searchId }}-input"
                    x-model="searchValue"
                    class="input-primary search-input block w-full rounded-none border-y border-r border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 px-3 py-2.5 text-sm text-gray-900 placeholder-gray-500 focus:bg-white dark:focus:bg-gray-800 dark:text-white dark:placeholder-gray-400 focus:ring-0"
                    placeholder="{{ __('common.search_placeholder') }}"
                    data-search-url="{{ tenant_routes('admin.search.index') }}"
                    data-search-not-found="{{ __('common.search_not_found') }}"
                />
                <button
                    type="button"
                    x-show="searchValue.length > 0"
                    x-on:click="searchValue = ''; document.getElementById('{{ $searchId }}-input').value = ''; document.getElementById('{{ $searchId }}-input').dispatchEvent(new Event('input'));"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 z-10 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                    aria-label="{{ __('common.clear') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <button
                type="button"
                onclick="document.getElementById('{{ $searchId }}-input').focus();"
                class="inline-flex items-center text-white bg-[var(--tenant-primary)] hover:opacity-90 focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-600 font-medium text-sm px-4 py-2.5 rounded-r-lg focus:outline-none"
            >
                <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
                </svg>
                {{ __('common.search') }}
            </button>
        </form>
    @endif

    <div
        id="{{ $searchId }}-dropdown"
        class="absolute left-0 right-0 top-[calc(100%+8px)] z-50 hidden w-full max-h-[550px] overflow-y-auto overflow-x-hidden divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm shadow-lg dark:divide-gray-600 dark:border-gray-600 dark:bg-gray-800 {{ $isMobile ? 'rounded-[14px] border-0' : '' }}"
    >
        <div id="{{ $searchId }}-results">
            <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('common.search_not_found') }}
            </div>
        </div>
    </div>
</div>
