@props([
    'notification',
    'notificationData' => [],
    'isRead' => false,
    'targetUrl' => null,
    'createdAt' => null,
])

@php
    $theme = (($notificationData['failed_rows'] ?? 0) > 0) ? 'danger' : 'success';
    $icon = 'export';
    $title = $notificationData['title'] ?? __('module_base.export_completed');
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
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
</div>
<div class="flex-1 min-w-0 overflow-hidden">
    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-full">{{ $title }}</p>
    @if ($body)
        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate max-w-full">{{ $body }}</p>
    @elseif ($successfulRows > 0 || $failedRows > 0)
        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate max-w-full">
            {{ __('notifications.export.completed', ['successfulRows' => number_format($successfulRows)]) }}
            @if ($failedRows > 0)
                {{ __('notifications.export.failed', ['failedRows' => number_format($failedRows)]) }}
            @endif
        </p>
    @endif
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $createdAt?->diffForHumans() }}</p>
</div>
@unless ($isRead)
    <span class="notification-dot flex-shrink-0 w-2 h-2 rounded-full mt-2" aria-hidden="true"></span>
@endunless
