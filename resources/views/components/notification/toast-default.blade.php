@props([
    'toastId',
    'type' => 'success',
    'message' => '',
    'title' => null,
    'readAndOpenUrl' => null,
    'linkText' => null,
])

@php
    $theme = in_array($type ?? '', ['success', 'warning', 'info', 'danger'], true) ? $type : ($type === 'error' ? 'danger' : 'success');
    $icon = 'default';
    $title = $title ?? __('common.notification');
@endphp

@if ($theme === 'success')
    <div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] text-[var(--tenant-primary)] flex items-center justify-center">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
        </svg>
        <span class="sr-only">{{ $title }}</span>
    </div>
@elseif ($theme === 'danger')
    <div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-red-100 dark:bg-red-800/50 text-red-500 dark:text-red-200 flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
        </svg>
        <span class="sr-only">{{ $title }}</span>
    </div>
@elseif ($theme === 'warning')
    <div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-amber-100 dark:bg-amber-800/50 text-amber-500 dark:text-amber-200 flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z"/>
        </svg>
        <span class="sr-only">{{ $title }}</span>
    </div>
@else
    <div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-800/50 text-blue-500 dark:text-blue-200 flex items-center justify-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        <span class="sr-only">{{ $title }}</span>
    </div>
@endif
<div class="flex-1 min-w-0 overflow-hidden ms-2 sm:ms-3">
    @if ($readAndOpenUrl)
        <a
            href="{{ $readAndOpenUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="block rounded focus:outline-none focus:ring-2 focus:ring-[var(--tenant-primary)] focus:ring-offset-2 dark:focus:ring-offset-gray-800 -m-1 p-1 min-h-[2.75rem] sm:min-h-0 flex flex-col justify-center"
        >
            <p class="text-sm font-medium text-gray-900 dark:text-white break-words">{{ $title }}</p>
            @if ($message)
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 break-words">{{ $message }}</p>
            @endif
            <span class="mt-1 inline-flex items-center gap-1 text-xs text-[var(--tenant-primary)] hover:underline">{{ $linkText ?? __('common.visit') }}</span>
        </a>
    @else
        <p class="text-sm font-medium text-gray-900 dark:text-white break-words">{{ $title }}</p>
        @if ($message)
            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 break-words">{{ $message }}</p>
        @endif
    @endif
</div>
<button
    type="button"
    class="flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-gray-900 bg-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 rounded-lg text-sm min-h-[2.75rem] min-w-[2.75rem] sm:h-8 sm:w-8 dark:hover:bg-gray-600 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-700 -me-1"
    data-dismiss-target="#{{ $toastId }}"
    aria-label="{{ __('common.close') }}"
>
    <span class="sr-only">{{ __('common.close') }}</span>
    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
    </svg>
</button>
