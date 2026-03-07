@props([
    'notification',
    'notificationData' => [],
    'isRead' => false,
    'targetUrl' => null,
    'createdAt' => null,
])

@php
    $theme = 'success';
    $icon = 'meeting';
    $title = $notificationData['title'] ?? __('common.notification');
    $body = $notificationData['body'] ?? '';
@endphp

<div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] text-[var(--tenant-primary)] flex items-center justify-center {{ !$isRead ? 'ring-2 ring-[color-mix(in_srgb,var(--tenant-primary)_25%,#ffffff_75%)] dark:ring-[color-mix(in_srgb,var(--tenant-primary)_40%,#1f2937_60%)]' : '' }}">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
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
