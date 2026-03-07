<div
    class="flex h-full min-h-0 flex-col rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="mb-4 flex shrink-0 flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('common.notifications') }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('common.dashboard_notifications_description') }}</p>
        </div>
        @if ($unreadCount > 0)
            <button type="button" wire:click="markAllAsRead"
                class="inline-flex shrink-0 items-center justify-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                {{ __('common.mark_all_as_read') }}
            </button>
        @endif
    </div>

    <div class="mb-4 shrink-0">
        <div class="flex gap-1.5 items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full p-1"
            role="tablist" aria-label="{{ __('common.select_tab') }}">
            <button type="button" role="tab" aria-selected="{{ $activeTab === 'all' ? 'true' : 'false' }}"
                wire:click="setTab('all')"
                class="flex-1 rounded-full px-3 py-1.5 text-xs font-medium leading-none transition-colors {{ $activeTab === 'all' ? 'tab-primary-active' : 'text-gray-600 dark:text-gray-400 tab-primary-hover' }}">
                {{ __('common.all') }}
            </button>
            <button type="button" role="tab" aria-selected="{{ $activeTab === 'unread' ? 'true' : 'false' }}"
                wire:click="setTab('unread')"
                class="flex-1 rounded-full px-3 py-1.5 text-xs font-medium leading-none transition-colors {{ $activeTab === 'unread' ? 'tab-primary-active' : 'text-gray-600 dark:text-gray-400 tab-primary-hover' }}">
                {{ __('common.new') }}
            </button>
            <button type="button" role="tab" aria-selected="{{ $activeTab === 'read' ? 'true' : 'false' }}"
                wire:click="setTab('read')"
                class="flex-1 rounded-full px-3 py-1.5 text-xs font-medium leading-none transition-colors {{ $activeTab === 'read' ? 'tab-primary-active' : 'text-gray-600 dark:text-gray-400 tab-primary-hover' }}">
                {{ __('common.read') }}
            </button>
        </div>
    </div>

    <div class="min-h-0 flex-1 space-y-1 overflow-y-auto rounded-lg border border-gray-100 dark:border-gray-600 p-2 dark:bg-gray-900/30"
        role="tabpanel" aria-label="{{ __('common.notifications') }}">
        @if ($notifications->count() > 0)
            @foreach ($notifications as $notification)
                @php
                    $isRead = !is_null($notification->read_at);
                    $notificationData = $notification->data ?? [];
                    $url = $notificationData['url'] ?? null;
                    $createdAt = $notification->created_at;
                    $targetUrl = null;

                    if (!empty($url)) {
                        $targetUrl = str_starts_with($url, 'http://') || str_starts_with($url, 'https://')
                            ? $url
                            : asset('storage/' . $url);
                    }
                    $iconForView = $notificationData['presentation_icon'] ?? $notificationData['icon'] ?? null;
                    $viewSlug = 'default';
                    if (is_string($iconForView)) {
                        $normalized = strtolower(str_replace('_', '-', trim($iconForView)));
                        if ($normalized !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $normalized)) {
                            $viewSlug = $normalized;
                        }
                    }
                    $notificationView = 'components.notification.notification-' . $viewSlug;
                    if (!view()->exists($notificationView)) {
                        $notificationView = 'components.notification.notification-default';
                    }
                    $readAndOpenUrl =
                        tenant_routes('admin.notifications.read-and-redirect', ['id' => $notification->id]) .
                        '?url=' .
                        rawurlencode($targetUrl ?: tenant_routes('admin.notifications.index'));
                @endphp

                <a href="{{ $readAndOpenUrl }}"
                    @if ($targetUrl) target="_blank"
                        rel="noopener noreferrer"
                    @else
                        target="_self" @endif
                    class="flex gap-3 items-start rounded-lg px-3 py-2.5 transition-colors {{ !$isRead ? 'bg-[color-mix(in_srgb,var(--tenant-primary)_12%,#ffffff_88%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_20%,#1f2937_80%)]' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                    @include($notificationView, [
                        'notification' => $notification,
                        'notificationData' => $notificationData,
                        'isRead' => $isRead,
                        'targetUrl' => $targetUrl,
                        'createdAt' => $createdAt,
                    ])
                </a>
            @endforeach
        @else
            <div class="py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('common.no_notifications') }}
            </div>
        @endif
    </div>

    <div class="mt-4 shrink-0 text-center">
        <a href="{{ $notificationsIndexUrl }}"
            class="inline-flex items-center justify-center rounded-full border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
            {{ __('common.view_all_notifications') }}
        </a>
    </div>
</div>

@push('styles')
    <link href="{{ asset('css/components/notification.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/components/notification.js') }}"></script>
@endpush
