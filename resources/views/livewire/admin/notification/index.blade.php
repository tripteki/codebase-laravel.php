<div class="relative z-0 min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
    @include('components.header')

    <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
        <div
            class="p-4 bg-white block sm:flex items-center justify-between rounded-t-lg border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
            <div class="w-full mb-1">
                <nav class="flex mb-5" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ tenant_routes('admin.dashboard.index') }}"
                                class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a 1 1 0 0 0 1.414 1.414L4 10.414V17a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-2a 1 1 0 0 1 1-1h2a 1 1 0 0 1 1 1v2a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z" />
                                </svg>
                                {{ __('common.home') }}
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="inline-flex items-center ml-1 text-gray-400 md:ml-2 dark:text-gray-500"
                                    aria-current="page">
                                    <svg class="w-4 h-4 mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z">
                                        </path>
                                    </svg>
                                    {{ __('common.notifications') }}
                                </span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">
                        {{ __('common.notifications') }}</h1>
                    <div class="flex items-center gap-2">
                        @if (auth()->user()->unreadNotifications()->count() > 0)
                            <button type="button" wire:click="markAllAsRead"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white rounded-lg btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('common.mark_all_as_read') }}
                            </button>
                        @endif
                        @if (auth()->user()->notifications()->count() > 0)
                            <button type="button" wire:click="deleteAll"
                                wire:confirm="{{ __('common.confirm_delete_all_notifications') }}"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-white rounded-lg btn-tertiary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                {{ __('common.delete_all') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                role="alert">
                {{ session('message') }}
            </div>
        @endif

        <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700"
            x-data="{ activeTab: @js($activeTab) }" x-init="activeTab = @js($activeTab)">
            <div class="border-gray-200 dark:border-gray-700 p-5">
                <div class="sm:hidden">
                    <label for="notification-tabs" class="sr-only">{{ __('common.select_tab') }}</label>
                    <select id="notification-tabs" x-model="activeTab"
                        @change="
                                window.location.href =
                                    $event.target.value === 'unread'
                                        ? '{{ tenant_routes('admin.notifications.tab', ['tab' => 'unread']) }}'
                                        : ($event.target.value === 'read'
                                            ? '{{ tenant_routes('admin.notifications.tab', ['tab' => 'read']) }}'
                                            : '{{ tenant_routes('admin.notifications.tab', ['tab' => 'all']) }}')
                            "
                        class="input-primary block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        <option value="all">{{ __('common.all') }}</option>
                        <option value="unread">{{ __('common.new') }}</option>
                        <option value="read">{{ __('common.read') }}</option>
                    </select>
                </div>
                <ul class="hidden sm:flex -space-x-px text-sm font-medium text-center text-gray-500 dark:text-gray-400"
                    role="tablist" aria-label="{{ __('common.select_tab') }}">
                    <li class="w-full focus-within:z-10" role="presentation">
                        <a id="notification-tab-all"
                            href="{{ tenant_routes('admin.notifications.tab', ['tab' => 'all']) }}" role="tab"
                            :aria-selected="activeTab === 'all' ? 'true' : 'false'"
                            :class="activeTab === 'all' ? 'tab-primary-active' :
                                'text-gray-500 bg-white border-gray-300 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 tab-primary-hover'"
                            class="inline-flex items-center justify-center w-full border rounded-l-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            {{ __('common.all') }}
                        </a>
                    </li>
                    <li class="w-full focus-within:z-10" role="presentation">
                        <a id="notification-tab-unread"
                            href="{{ tenant_routes('admin.notifications.tab', ['tab' => 'unread']) }}" role="tab"
                            :aria-selected="activeTab === 'unread' ? 'true' : 'false'"
                            :class="activeTab === 'unread' ? 'tab-primary-active' :
                                'text-gray-500 bg-white border-gray-300 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 tab-primary-hover'"
                            class="inline-flex items-center justify-center w-full border font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z" />
                            </svg>
                            {{ __('common.new') }}
                        </a>
                    </li>
                    <li class="w-full focus-within:z-10" role="presentation">
                        <a id="notification-tab-read"
                            href="{{ tenant_routes('admin.notifications.tab', ['tab' => 'read']) }}" role="tab"
                            :aria-selected="activeTab === 'read' ? 'true' : 'false'"
                            :class="activeTab === 'read' ? 'tab-primary-active' :
                                'text-gray-500 bg-white border border-gray-300 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 tab-primary-hover'"
                            class="inline-flex items-center justify-center w-full border rounded-r-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-inset">
                            <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ __('common.read') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="p-4" id="notification-tabpanel" role="tabpanel"
                aria-label="{{ __('common.notifications') }}">
                @if ($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach ($notifications as $notification)
                            @php
                                $isRead = !is_null($notification->read_at);
                                $notificationData = $notification->data ?? [];
                                $url = $notificationData['url'] ?? null;
                                $createdAt = $notification->created_at;
                                $targetUrl = null;
                                $isFile = false;
                                $linkText = null;

                                if (!empty($url)) {
                                    $isAbsoluteUrl = str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
                                    $pathForExtension = $isAbsoluteUrl ? (parse_url($url, PHP_URL_PATH) ?? $url) : $url;
                                    $extension = strtolower(pathinfo($pathForExtension, PATHINFO_EXTENSION));
                                    $hasFileExtension = $extension !== '' && preg_match('/^[a-z0-9]{2,6}$/', $extension);

                                    $isFile = $hasFileExtension;
                                    $linkText = $hasFileExtension ? __('common.download') : __('common.visit');
                                    $targetUrl = $isAbsoluteUrl ? $url : asset('storage/' . $url);
                                }
                                $iconForView = $notificationData['presentation_icon'] ?? $notificationData['icon'] ?? null;
                                $viewSlug = 'default';
                                if (is_string($iconForView)) {
                                    $normalized = strtolower(str_replace('_', '-', trim($iconForView)));
                                    if ($normalized !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $normalized)) {
                                        $viewSlug = $normalized;
                                    }
                                }
                                $pageView = 'components.notification.notification-page-' . $viewSlug;
                                if (!view()->exists($pageView)) {
                                    $pageView = 'components.notification.notification-page-default';
                                }
                            @endphp

                            <div
                                class="rounded-lg border p-4 shadow-sm transition-colors {{ !$isRead ? 'border-[color-mix(in_srgb,var(--tenant-primary)_35%,#e5e7eb)] bg-[color-mix(in_srgb,var(--tenant-primary)_12%,#ffffff_88%)] dark:border-[color-mix(in_srgb,var(--tenant-primary)_30%,#374151_70%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_20%,#1f2937_80%)]' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 flex-1">
                                        @include($pageView, [
                                            'notification' => $notification,
                                            'notificationData' => $notificationData,
                                            'isRead' => $isRead,
                                            'targetUrl' => $targetUrl,
                                            'linkText' => $linkText,
                                            'isFile' => $isFile,
                                            'createdAt' => $createdAt,
                                        ])
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if (!$isRead)
                                            <button type="button" wire:click="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-400 dark:hover:text-blue-300"
                                                title="{{ __('common.mark_as_read') }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                {{ __('common.mark_as_read') }}
                                            </button>
                                        @endif
                                        <button type="button" wire:click="delete('{{ $notification->id }}')"
                                            wire:confirm="{{ __('common.confirm_delete_notification') }}"
                                            class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 hover:underline dark:text-red-400 dark:hover:text-red-300"
                                            title="{{ __('module_base.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            {{ __('module_base.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div
                        class="rounded-lg border border-gray-200 bg-white p-12 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('common.no_notifications') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </main>

    @include('components.footer')
</div>
