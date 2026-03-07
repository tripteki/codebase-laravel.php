@props([
    'notification',
    'notificationData' => [],
    'isRead' => false,
    'targetUrl' => null,
    'createdAt' => null,
])

@php
    $theme = 'success';
    $icon = 'default';
    $title = $notificationData['title'] ?? __('common.notification');
    $body = $notificationData['body'] ?? '';
@endphp

<div class="flex-shrink-0 shrink-0 w-8 h-8 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] text-[var(--tenant-primary)] flex items-center justify-center {{ !$isRead ? 'ring-2 ring-[color-mix(in_srgb,var(--tenant-primary)_25%,#ffffff_75%)] dark:ring-[color-mix(in_srgb,var(--tenant-primary)_40%,#1f2937_60%)]' : '' }}">
    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
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
