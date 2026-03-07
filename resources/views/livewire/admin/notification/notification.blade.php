<div>
    <button type="button" data-dropdown-toggle="notification-dropdown"
        data-notification-data-url="{{ tenant_routes('admin.notifications.data') }}"
        data-mark-all-text="{{ __('common.mark_all_as_read') }}"
        data-no-notifications-text="{{ __('common.no_notifications') }}"
        class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700 transition-colors"
        aria-label="{{ __('common.notifications') }}">
        @if ($unreadCount > 0)
            <svg class="notification-bell-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24">
                <path
                    d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.955.955 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.955.955 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h12.924C19 18 19 17.4 19 16.807c0-1.193-1.867-1.789-1.867-4.175ZM6 6a1 1 0 0 1-.707-.293l-1-1a1 1 0 0 1 1.414-1.414l1 1A1 1 0 0 1 6 6Zm-2 4H3a1 1 0 0 1 0-2h1a1 1 0 1 1 0 2Zm14-4a1 1 0 0 1-.707-1.707l1-1a1 1 0 1 1 1.414 1.414l-1 1A1 1 0 0 1 18 6Zm3 4h-1a1 1 0 1 1 0-2h1a1 1 0 1 1 0 2ZM8.823 19a3.453 3.453 0 0 0 6.354 0H8.823Z" />
            </svg>
            <span
                class="notification-dot absolute top-0 right-0 h-2.5 w-2.5 translate-x-[35%] -translate-y-[35%] rounded-full shadow-[0_0_0_2px_white] dark:shadow-[0_0_0_2px_#1f2937]"
                aria-hidden="true"></span>
        @else
            <svg class="notification-bell-outline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.292-.538 1.292H5.538C5 18 5 17.301 5 16.708c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26 1.33-2.112h-6.92Z" />
            </svg>
        @endif
        <span class="sr-only">{{ __('common.notifications') }}</span>
    </button>

    <div id="notification-dropdown" wire:ignore.self
        class="absolute right-0 top-12 z-50 hidden w-80 min-w-[320px] max-w-[360px] max-h-[460px] flex flex-col rounded-[14px] border border-gray-200 dark:border-gray-600 bg-white text-sm shadow-lg dark:border-gray-600 dark:bg-gray-800">
        <div
            class="flex-shrink-0 sticky top-0 z-10 bg-white dark:bg-gray-800 px-2 py-2 border-b border-gray-200 dark:border-gray-600 rounded-t-[14px]">
            <div class="flex items-center justify-between px-2 pb-2">
                <span class="font-semibold text-gray-900 dark:text-white">{{ __('common.notifications') }}</span>
                @if ($unreadCount > 0)
                    <button type="button" wire:click="markAllAsRead"
                        class="text-xs text-[var(--tenant-primary)] hover:underline cursor-pointer">
                        {{ __('common.mark_all_as_read') }}
                    </button>
                @endif
            </div>

            <div class="px-2 pb-2">
                <div class="flex gap-1.5 items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-full p-1"
                    role="tablist" aria-label="{{ __('common.select_tab') }}">
                    <button type="button" role="tab" aria-selected="{{ $activeTab === 'all' ? 'true' : 'false' }}"
                        wire:click.prevent="setTab('all')" onclick="event.stopPropagation();"
                        class="flex-1 rounded-full px-3 py-1.5 text-xs font-medium leading-none transition-colors {{ $activeTab === 'all' ? 'tab-primary-active' : 'text-gray-600 dark:text-gray-400 tab-primary-hover' }}">
                        {{ __('common.all') }}
                    </button>
                    <button type="button" role="tab"
                        aria-selected="{{ $activeTab === 'unread' ? 'true' : 'false' }}"
                        wire:click.prevent="setTab('unread')" onclick="event.stopPropagation();"
                        class="flex-1 rounded-full px-3 py-1.5 text-xs font-medium leading-none transition-colors {{ $activeTab === 'unread' ? 'tab-primary-active' : 'text-gray-600 dark:text-gray-400 tab-primary-hover' }}">
                        {{ __('common.new') }}
                    </button>
                    <button type="button" role="tab"
                        aria-selected="{{ $activeTab === 'read' ? 'true' : 'false' }}"
                        wire:click.prevent="setTab('read')" onclick="event.stopPropagation();"
                        class="flex-1 rounded-full px-3 py-1.5 text-xs font-medium leading-none transition-colors {{ $activeTab === 'read' ? 'tab-primary-active' : 'text-gray-600 dark:text-gray-400 tab-primary-hover' }}">
                        {{ __('common.read') }}
                    </button>
                </div>
            </div>
        </div>
        <div id="notification-dropdown-content" class="flex-1 min-h-0 overflow-y-auto px-0 py-1" role="tabpanel"
            aria-label="{{ __('common.notifications') }}">
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
                        class="flex gap-3 items-start px-3.5 py-2.5 mx-1.5 mb-1 rounded-lg transition-colors {{ !$isRead ? 'bg-[color-mix(in_srgb,var(--tenant-primary)_12%,#ffffff_88%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_20%,#1f2937_80%)]' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
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
                <div class="px-2 py-3 text-center text-xs text-gray-500 dark:text-gray-400">
                    {{ __('common.no_notifications') }}
                </div>
            @endif
        </div>

        <div
            class="flex-shrink-0 sticky bottom-0 z-10 pt-1 pb-2 text-center border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 rounded-b-[14px]">
            <a href="{{ $notificationsIndexUrl }}"
                class="inline-flex items-center justify-center rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                {{ __('common.view_all_notifications') }}
            </a>
        </div>
    </div>
</div>

@push('styles')
    <link href="{{ asset('css/module/notification.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
    <script src="{{ asset('js/module/notification.js') }}"></script>
@endpush
