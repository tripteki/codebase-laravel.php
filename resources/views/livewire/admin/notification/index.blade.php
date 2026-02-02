<div>
    @include("components.admin.sidebar")

    <div class="min-h-screen flex flex-col bg-gray-100 lg:pl-64 dark:bg-gray-900" data-sidebar-content>
        @include("components.header")

        <main id="main-content" class="flex-1 px-4 py-6 lg:px-6">
            <div class="p-4 bg-white block sm:flex items-center justify-between rounded-t-lg border-gray-200 lg:mt-1.5 dark:bg-gray-800 dark:border-gray-700">
                <div class="w-full mb-1">
                    <nav class="flex mb-5" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                                    <svg class="w-5 h-5 mr-2.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.707 2.293a1 1 0 0 0-1.414 0l-7 7a 1 1 0 0 0 1.414 1.414L4 10.414V17a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-2a 1 1 0 0 1 1-1h2a 1 1 0 0 1 1 1v2a 1 1 0 0 0 1 1h2a 1 1 0 0 0 1-1v-6.586l.293.293a1 1 0 0 0 1.414-1.414l-7-7Z" />
                                    </svg>
                                    {{ __("common.home") }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M7.293 14.707a 1 1 0 0 1 0-1.414L10.586 10 7.293 6.707a 1 1 0 0 1 1.414-1.414l4 4a 1 1 0 0 1 0 1.414l-4 4a 1 1 0 0 1-1.414 0Z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">{{ __("common.notifications") }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl dark:text-white">{{ __("common.notifications") }}</h1>
                        <div class="flex items-center gap-2">
                            @if (auth()->user()->unreadNotifications()->count() > 0)
                                <button
                                    type="button"
                                    wire:click="markAllAsRead"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __("common.mark_all_as_read") }}
                                </button>
                            @endif
                            @if (auth()->user()->notifications()->count() > 0)
                                <button
                                    type="button"
                                    wire:click="deleteAll"
                                    wire:confirm="{{ __('common.confirm_delete_all_notifications') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-700"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    {{ __("common.delete_all") }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mt-8 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                <div class="border-gray-200 dark:border-gray-700 p-5">
                    <div class="sm:hidden">
                        <label for="notification-tabs" class="sr-only">{{ __('common.select_tab') }}</label>
                        <select id="notification-tabs" wire:model.live="activeTab" class="block w-full px-3 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                            <option value="unread">{{ __('common.new') }} ({{ __('common.unread') }})</option>
                            <option value="read">{{ __('common.read') }}</option>
                        </select>
                    </div>
                    <ul class="hidden text-sm font-medium text-center text-gray-500 sm:flex -space-x-px dark:text-gray-400">
                        <li class="w-full focus-within:z-10">
                            <button
                                type="button"
                                wire:click="setTab('unread')"
                                class="inline-flex items-center justify-center w-full border rounded-l-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 {{ $activeTab === 'unread' ? 'text-blue-600 bg-blue-50 border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-600' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                            >
                                <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z"/>
                                </svg>
                                {{ __('common.new') }} ({{ __('common.unread') }})
                            </button>
                        </li>
                        <li class="w-full focus-within:z-10">
                            <button
                                type="button"
                                wire:click="setTab('read')"
                                class="inline-flex items-center justify-center w-full border rounded-r-lg font-medium leading-5 text-sm px-4 py-2.5 focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-800 {{ $activeTab === 'read' ? 'text-blue-600 bg-blue-50 border-blue-300 dark:text-blue-400 dark:bg-blue-900/30 dark:border-blue-600' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-white' }}"
                            >
                                <svg class="w-4 h-4 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('common.read') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="p-4">
                    @if ($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach ($notifications as $notification)
                            @php
                                $isRead = !is_null($notification->read_at);
                                $notificationData = $notification->data ?? [];
                                $title = $notificationData['title'] ?? __('common.notification');
                                $body = $notificationData['body'] ?? '';
                                $url = $notificationData['url'] ?? null;
                                $createdAt = $notification->created_at;
                                $downloadUrl = null;
                                $isFile = false;
                                $linkText = null;

                                if ($url) {

                                    if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {

                                        $filePath = storage_path('app/public/' . $url);

                                        if (file_exists($filePath)) {

                                            $downloadUrl = asset('storage/' . $url);
                                            $isFile = true;
                                            $linkText = __('common.download');
                                        }
                                    } else {

                                        $downloadUrl = $url;
                                        $isFile = false;
                                        $linkText = __('common.view');
                                    }
                                }
                            @endphp
                            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 {{ !$isRead ? 'border-blue-300 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-600' : '' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="flex-shrink-0">
                                            @if (! $isRead)
                                                <div class="h-2 w-2 rounded-full bg-blue-600 mt-2"></div>
                                            @else
                                                <svg class="h-5 w-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white {{ !$isRead ? 'font-semibold' : '' }}">
                                                {{ $title }}
                                            </p>
                                            @if ($body)
                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $body }}
                                                </p>
                                            @endif
                                            @if ($downloadUrl && $linkText)
                                                <div class="mt-2">
                                                    <a
                                                        href="{{ $downloadUrl }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-400 dark:hover:text-blue-300"
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
                                                {{ $createdAt->format('d M Y, H:i') }} ({{ $createdAt->diffForHumans() }})
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if (! $isRead)
                                            <button
                                                type="button"
                                                wire:click="markAsRead('{{ $notification->id }}')"
                                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 hover:underline dark:text-blue-400 dark:hover:text-blue-300"
                                                title="{{ __('common.mark_as_read') }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                {{ __('common.mark_as_read') }}
                                            </button>
                                        @endif
                                        <button
                                            type="button"
                                            wire:click="delete('{{ $notification->id }}')"
                                            wire:confirm="{{ __('common.confirm_delete_notification') }}"
                                            class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 hover:underline dark:text-red-400 dark:hover:text-red-300"
                                            title="{{ __('module_base.delete') }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
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
                    <div class="rounded-lg border border-gray-200 bg-white p-12 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('common.no_notifications') }}</p>
                    </div>
                @endif
                </div>
            </div>
        </main>

        @include("components.footer")
    </div>
</div>
