@php
    $toastId = 'toast-' . uniqid();
@endphp

@push('styles')
<link href="{{ asset('css/components/toast.css') }}" rel="stylesheet" />
@endpush

@push('scripts-end')
<script src="{{ asset('js/components/toast.js') }}"></script>
@endpush

@if (session()->has('toast'))
    @php
        $toast = session('toast');
        $rawType = $toast['type'] ?? 'success';
        $type = in_array($rawType, ['success', 'warning', 'info', 'danger'], true) ? $rawType : ($rawType === 'error' ? 'danger' : 'success');
        $message = $toast['message'] ?? '';
        $variant = $toast['variant'] ?? null;
        $title = $toast['title'] ?? null;
        $url = $toast['url'] ?? null;
        $notificationId = $toast['notification_id'] ?? null;
        $readAndOpenUrl = null;
        $toastLinkText = null;

        if ($notificationId && $url) {
            $readAndOpenUrl = tenant_routes('admin.notifications.read-and-redirect', ['id' => $notificationId]) . '?url=' . rawurlencode($url);
        } elseif ($url) {
            $readAndOpenUrl = $url;
        } else {
            $readAndOpenUrl = $notificationId
                ? tenant_routes('admin.notifications.read-and-redirect', ['id' => $notificationId]) . '?url=' . rawurlencode(tenant_routes('admin.notifications.index'))
                : tenant_routes('admin.notifications.index');
        }

        if ($readAndOpenUrl && $url) {
            $pathForExt = str_starts_with($url, 'http') ? (parse_url($url, PHP_URL_PATH) ?? $url) : $url;
            $ext = strtolower(pathinfo($pathForExt, PATHINFO_EXTENSION));
            $toastLinkText = ($ext !== '' && preg_match('/^[a-z0-9]{2,6}$/', $ext)) ? __('common.download') : __('common.visit');
        } elseif ($readAndOpenUrl) {
            $toastLinkText = __('common.visit');
        }

        $toastView = null;
        if ($variant && view()->exists('components.notification.toast-' . $variant)) {
            $toastView = 'components.notification.toast-' . $variant;
        } elseif (view()->exists('components.notification.toast-default')) {
            $toastView = 'components.notification.toast-default';
        }
    @endphp

    <div
        id="{{ $toastId }}"
        class="fixed flex items-center gap-2 sm:gap-3 w-[calc(100%-2rem)] sm:w-full max-w-[min(100%,24rem)] sm:max-w-sm p-3 sm:p-4 text-gray-500 bg-white rounded-lg shadow border border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 top-4 left-4 right-4 sm:left-auto sm:right-5 sm:top-5 z-50 transform translate-x-full transition-all duration-300 ease-in-out"
        role="alert"
    >
        @if ($toastView)
            @include($toastView, [
                'toastId' => $toastId,
                'type' => $type,
                'message' => $message,
                'title' => $title,
                'readAndOpenUrl' => $readAndOpenUrl,
                'linkText' => $toastLinkText,
            ])
        @else
            @if ($type === 'success')
                <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-green-500 bg-green-100 rounded dark:bg-green-800 dark:text-green-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>
                    </svg>
                    <span class="sr-only">{{ __('common.success') }}</span>
                </div>
            @elseif ($type === 'danger')
                <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-red-500 bg-red-100 rounded dark:bg-red-800 dark:text-red-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                    </svg>
                    <span class="sr-only">{{ __('common.danger') }}</span>
                </div>
            @elseif ($type === 'warning')
                <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-amber-500 bg-amber-100 rounded dark:bg-amber-800 dark:text-amber-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3Z"/>
                    </svg>
                    <span class="sr-only">{{ __('common.warning') }}</span>
                </div>
            @elseif ($type === 'info')
                <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 text-blue-500 bg-blue-100 rounded dark:bg-blue-800 dark:text-blue-200">
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <span class="sr-only">{{ __('common.info') }}</span>
                </div>
            @endif
            <div class="ms-3 text-sm font-normal">{{ $message }}</div>
            <button
                type="button"
                class="ms-auto flex items-center justify-center text-gray-400 hover:text-gray-900 bg-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 rounded-lg text-sm h-8 w-8 dark:hover:bg-gray-600 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-700"
                data-dismiss-target="#{{ $toastId }}"
                aria-label="{{ __('common.close') }}"
            >
                <span class="sr-only">{{ __('common.close') }}</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            </button>
        @endif
    </div>
@endif
