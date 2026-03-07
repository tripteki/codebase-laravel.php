@props([
    'notification',
    'notificationData' => [],
    'isRead' => false,
    'targetUrl' => null,
    'linkText' => null,
    'isFile' => false,
    'createdAt' => null,
])

@php
    $theme = (($notificationData['failed_rows'] ?? 0) > 0) ? 'danger' : 'success';
    $icon = 'import';
    $title = $notificationData['title'] ?? __('module_base.import_completed');
    $body = $notificationData['body'] ?? '';
    $successfulRows = $notificationData['successful_rows'] ?? 0;
    $failedRows = $notificationData['failed_rows'] ?? 0;
@endphp

@php
    $iconWrapperClasses = $theme === 'danger'
        ? 'bg-red-100 text-red-600 ring-2 ring-red-200 dark:bg-red-900/40 dark:text-red-300 dark:ring-red-700/80'
        : 'bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] text-[var(--tenant-primary)] ring-2 ring-[color-mix(in_srgb,var(--tenant-primary)_25%,#ffffff_75%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] dark:text-[var(--tenant-primary)] dark:ring-[color-mix(in_srgb,var(--tenant-primary)_40%,#1f2937_60%)]';
@endphp

<div
    class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $isRead ? str_replace([' ring-2', ' ring-red-200', ' dark:ring-red-700/80', ' ring-[color-mix(in_srgb,var(--tenant-primary)_25%,#ffffff_75%)]', ' dark:ring-[color-mix(in_srgb,var(--tenant-primary)_40%,#1f2937_60%)]'], '', $iconWrapperClasses) : $iconWrapperClasses }}"
    data-theme="{{ $theme }}"
    data-icon="{{ $icon }}"
>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="17 8 12 3 7 8"/>
        <line x1="12" y1="3" x2="12" y2="15"/>
    </svg>
</div>
<div class="flex-1 min-w-0 overflow-hidden">
    <p class="text-sm font-medium text-gray-900 dark:text-white break-words {{ !$isRead ? 'font-semibold' : '' }}">{{ $title }}</p>
    @if ($body)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 break-words">{{ $body }}</p>
    @elseif ($successfulRows > 0 || $failedRows > 0)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 break-words">
            {{ __('notifications.import.completed', ['successfulRows' => number_format($successfulRows)]) }}
            @if ($failedRows > 0)
                {{ __('notifications.import.failed', ['failedRows' => number_format($failedRows)]) }}
            @endif
        </p>
    @endif
    @if ($targetUrl && $linkText)
        <div class="mt-2">
            <a
                href="{{ $targetUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-400 dark:hover:text-blue-300 min-h-[2.75rem] sm:min-h-0 min-w-0 items-center"
            >
                @if ($isFile)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                @endif
                {{ $linkText }}
            </a>
        </div>
    @endif
    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
        {{ $createdAt?->format('d M Y, H:i') }} ({{ $createdAt?->diffForHumans() }})
    </p>
</div>
