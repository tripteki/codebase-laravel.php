<div>
    <button
        type="button"
        data-dropdown-toggle="notification-dropdown"
        data-notification-data-url="{{ route('admin.notifications.data') }}"
        data-mark-all-text="{{ __('common.mark_all_as_read') }}"
        data-no-notifications-text="{{ __('common.no_notifications') }}"
        class="relative inline-flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-800 dark:focus:ring-gray-700 text-sm p-2.5"
    >
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        <span class="sr-only">{{ __('common.notifications') }}</span>
        @if ($unreadCount > 0)
            <div data-notification-badge class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 dark:border-gray-800 pt-[2px] pb-[1px]" style="display: block;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</div>
        @else
            <div data-notification-badge class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 dark:border-gray-800 pt-[2px] pb-[1px]" style="display: none;">0</div>
        @endif
    </button>

    <div
        id="notification-dropdown"
        wire:ignore.self
        class="absolute border right-0 top-12 z-50 hidden w-80 divide-y divide-gray-100 rounded-lg bg-white text-sm shadow dark:divide-gray-600 dark:bg-gray-700 dark:border-gray-600"
    >
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('common.notifications') }}</p>
                @if ($unreadCount > 0)
                    <button
                        type="button"
                        wire:click="markAllAsRead"
                        class="text-xs text-blue-600 hover:underline dark:text-blue-400"
                    >
                        {{ __('common.mark_all_as_read') }}
                    </button>
                @endif
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-600 px-4 py-2">
            <ul class="flex text-xs font-medium text-center text-gray-500 -space-x-px dark:text-gray-400">
                <li class="w-full focus-within:z-10">
                    <button
                        type="button"
                        wire:click.prevent="setTab('unread')"
                        onclick="event.stopPropagation();"
                        class="inline-flex items-center justify-center w-full border rounded-l-lg font-medium leading-5 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 {{ $activeTab === 'unread' ? 'text-blue-600 bg-blue-50 border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                    >
                        <svg class="w-3 h-3 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z"/>
                        </svg>
                        {{ __('common.new') }}
                    </button>
                </li>
                <li class="w-full focus-within:z-10">
                    <button
                        type="button"
                        wire:click.prevent="setTab('read')"
                        onclick="event.stopPropagation();"
                        class="inline-flex items-center justify-center w-full border rounded-r-lg font-medium leading-5 px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 {{ $activeTab === 'read' ? 'text-blue-600 bg-blue-50 border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-600' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                    >
                        <svg class="w-3 h-3 me-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('common.read') }}
                    </button>
                </li>
            </ul>
        </div>
        <div id="notification-dropdown-content" class="max-h-96 overflow-y-auto">
            @if ($notifications->count() > 0)
                <ul class="py-2">
                    @foreach ($notifications as $notification)
                        @php
                            $isRead = !is_null($notification->read_at);
                            $notificationData = $notification->data ?? [];
                            $title = $notificationData['title'] ?? __('common.notification');
                            $body = $notificationData['body'] ?? '';
                            $url = $notificationData['url'] ?? null;
                            $createdAt = $notification->created_at;
                            $targetUrl = null;

                            if ($url) {
                                if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                                    $filePath = storage_path('app/public/' . $url);

                                    if (file_exists($filePath)) {
                                        $targetUrl = asset('storage/' . $url);
                                    }
                                } else {
                                    $targetUrl = $url;
                                }
                            }
                        @endphp
                        <li>
                            <a
                                href="#"
                                wire:click.prevent="markAsRead('{{ $notification->id }}')"
                                @if ($targetUrl)
                                    data-notification-url="{{ $targetUrl }}"
                                    data-notification-id="{{ $notification->id }}"
                                @endif
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 {{ !$isRead ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                            >
                                <div class="flex items-start gap-2">
                                    <div class="flex-shrink-0 mt-0.5">
                                        @if (! $isRead)
                                            <div class="h-2 w-2 rounded-full bg-blue-600"></div>
                                        @else
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white {{ !$isRead ? 'font-semibold' : '' }}">
                                            {{ $title }}
                                        </p>
                                        @if ($body)
                                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                                {{ $body }}
                                            </p>
                                        @endif
                                        <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                            {{ $createdAt->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('common.no_notifications') }}</p>
                </div>
            @endif
        </div>
        <div id="notification-dropdown-footer" class="py-2">
            <a
                href="{{ route('admin.notifications.index') }}"
                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600"
            >
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
