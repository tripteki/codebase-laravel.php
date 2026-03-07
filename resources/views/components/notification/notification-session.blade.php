@props([
    'notification',
    'notificationData' => [],
    'isRead' => false,
    'targetUrl' => null,
    'createdAt' => null,
])

@php
    $theme = 'success';
    $icon = 'session';
    $title = $notificationData['title'] ?? __('common.notification');
    $body = $notificationData['body'] ?? '';
@endphp

<div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] text-[var(--tenant-primary)] flex items-center justify-center {{ !$isRead ? 'ring-2 ring-[color-mix(in_srgb,var(--tenant-primary)_25%,#ffffff_75%)] dark:ring-[color-mix(in_srgb,var(--tenant-primary)_40%,#1f2937_60%)]' : '' }}">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/>
    </svg>
</div>
<div class="flex-1 min-w-0 overflow-hidden">
    <p class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-full">{{ $title }}</p>
    @if ($body)
        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 truncate max-w-full">{{ $body }}</p>
    @endif
    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $createdAt?->diffForHumans() }}</p>
</div>
@unless ($isRead)
    <span class="notification-dot flex-shrink-0 w-2 h-2 rounded-full mt-2" aria-hidden="true"></span>
@endunless
