@props([
    'toastId',
    'message' => '',
    'title' => null,
    'readAndOpenUrl' => null,
    'linkText' => null,
])

@php
    $theme = 'success';
    $icon = 'export';
    $title = $title ?? __('module_base.export_completed');
@endphp

<div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] text-[var(--tenant-primary)] flex items-center justify-center">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    <span class="sr-only">{{ $title }}</span>
</div>
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
            <span class="mt-1 inline-flex items-center gap-1 text-xs text-[var(--tenant-primary)] hover:underline">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span class="break-words">{{ $linkText ?? __('common.download') }}</span>
            </span>
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
