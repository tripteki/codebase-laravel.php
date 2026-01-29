@php
    $searchId = isset($mobileOnly) && $mobileOnly ? 'sidebar-search' : 'search';
@endphp

<div class="relative w-full max-w-md {{ isset($mobileOnly) && $mobileOnly ? 'block lg:hidden' : 'hidden lg:block' }}">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none z-10">
        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
        </svg>
    </div>
    <input
        type="text"
        id="{{ $searchId }}-input"
        class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 pl-10 text-sm text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
        placeholder="{{ __('common.search_placeholder') }}"
        data-search-url="{{ route('admin.search.index') }}"
        data-search-not-found="{{ __('common.search_not_found') }}"
    />

    <div
        id="{{ $searchId }}-dropdown"
        class="absolute left-0 right-0 top-full z-50 mt-2 hidden w-full divide-y divide-gray-100 rounded-lg border border-gray-200 bg-white text-sm shadow dark:divide-gray-600 dark:border-gray-600 dark:bg-gray-700"
    >
        <div id="{{ $searchId }}-results">
            <div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('common.search_not_found') }}
            </div>
        </div>
    </div>
</div>
