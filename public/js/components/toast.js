"use strict";

(function () {

    function escapeHtml (str) {
        if (str == null) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function setupToastDismiss (toast, toastId) {
        const dismissButton = toast.querySelector('[data-dismiss-target="#' + toastId + '"]');
        if (dismissButton) {
            dismissButton.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                setTimeout(function () {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }
        setTimeout(function () {
            if (toast && toast.parentNode) {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');
                setTimeout(function () {
                    if (toast && toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }, 5000);
    }

    function showNotificationToast (data) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 left-4 right-4 sm:left-auto sm:right-5 sm:top-5 z-50 space-y-4';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const variant = data.variant || 'default';
        const theme = data.theme || 'success';
        const iconName = data.icon || null;
        const title = escapeHtml(data.title || '');
        const message = escapeHtml(data.message || '');
        const readAndOpenUrl = data.readAndOpenUrl || data.read_and_open_url || '#';
        const linkText = escapeHtml(data.linkText || data.link_text || 'Open');

        const iconSvgExport = '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>';
        const iconSvgImport = '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>';
        const iconSvgMeeting = '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>';
        const iconSvgSession = '<path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/>';
        const iconSvgBell = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>';
        const iconSvgMap = { import: iconSvgImport, export: iconSvgExport, meeting: iconSvgMeeting, session: iconSvgSession };
        const iconSvg = iconSvgMap[iconName] || (variant === 'import' ? iconSvgImport : variant === 'export' ? iconSvgExport : iconSvgBell);
        const linkIconSvgExport = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>';
        const linkIconSvgVisit = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        const linkIconSvg = variant === 'export' ? linkIconSvgExport : linkIconSvgVisit;

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'flex items-center gap-2 sm:gap-3 w-[calc(100%-2rem)] sm:w-full max-w-[min(100%,24rem)] sm:max-w-sm p-3 sm:p-4 text-gray-500 bg-white rounded-lg shadow border border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 transform translate-x-full transition-all duration-300 ease-in-out';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('data-theme', theme);
        toast.innerHTML =
            '<div class="flex-shrink-0 w-8 h-8 rounded-full bg-[color-mix(in_srgb,var(--tenant-primary)_10%,#ffffff_90%)] dark:bg-[color-mix(in_srgb,var(--tenant-primary)_30%,#1f2937_70%)] text-[var(--tenant-primary)] flex items-center justify-center">' +
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">' + iconSvg + '</svg>' +
            '<span class="sr-only">' + title + '</span></div>' +
            '<div class="flex-1 min-w-0 overflow-hidden ms-3">' +
            '<a href="' + escapeHtml(readAndOpenUrl) + '" target="_blank" rel="noopener noreferrer" class="block rounded focus:outline-none focus:ring-2 focus:ring-[var(--tenant-primary)] focus:ring-offset-2 dark:focus:ring-offset-gray-800 -m-1 p-1 min-h-[2.75rem] sm:min-h-0 flex flex-col justify-center">' +
            '<p class="text-sm font-medium text-gray-900 dark:text-white break-words">' + title + '</p>' +
            (message ? '<p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 break-words">' + message + '</p>' : '') +
            '<span class="mt-1 inline-flex items-center gap-1 text-xs text-[var(--tenant-primary)] hover:underline">' +
            '<svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' + linkIconSvg + '</svg>' +
            '<span class="break-words">' + linkText + '</span></span></a></div>' +
            '<button type="button" class="flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-gray-900 bg-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 rounded-lg text-sm min-h-[2.75rem] min-w-[2.75rem] sm:h-8 sm:w-8 dark:hover:bg-gray-600 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-700 -me-1" data-dismiss-target="#' + toastId + '" aria-label="Close">' +
            '<span class="sr-only">Close</span><svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg></button>';

        container.appendChild(toast);
        setTimeout(function () {
            if (toast) {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }
        }, 10);
        setupToastDismiss(toast, toastId);
    }

    function showToast (message, type = 'success') {

        let container = document.getElementById('toast-container');

        if (! container) {

            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-4 left-4 right-4 sm:left-auto sm:right-5 sm:top-5 z-50 space-y-4';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const safeMessage = escapeHtml(message);

        const colors = {

            success: {
                icon: 'text-green-500',
                bg: 'bg-green-100',
                darkBg: 'dark:bg-green-800',
                darkIcon: 'dark:text-green-200',
                iconSvg: '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5"/>'
            },

            error: {
                icon: 'text-red-500',
                bg: 'bg-red-100',
                darkBg: 'dark:bg-red-800',
                darkIcon: 'dark:text-red-200',
                iconSvg: '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>'
            },

            info: {
                icon: 'text-blue-500',
                bg: 'bg-blue-100',
                darkBg: 'dark:bg-blue-800',
                darkIcon: 'dark:text-blue-200',
                iconSvg: '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>'
            }
        };

        const color = colors[type] || colors.success;

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'flex items-center gap-2 sm:gap-3 w-full max-w-[min(100%,24rem)] sm:max-w-sm p-3 sm:p-4 text-gray-500 bg-white rounded-lg shadow border border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 transform translate-x-full transition-all duration-300 ease-in-out';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 ${color.icon} ${color.bg} ${color.darkBg} ${color.darkIcon} rounded">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    ${color.iconSvg}
                </svg>
                <span class="sr-only">${type} icon</span>
            </div>
            <div class="flex-1 min-w-0 ms-3 text-sm font-normal break-words">${safeMessage}</div>
            <button
                type="button"
                class="flex-shrink-0 flex items-center justify-center text-gray-400 hover:text-gray-900 bg-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 rounded-lg text-sm min-h-[2.75rem] min-w-[2.75rem] sm:h-8 sm:w-8 dark:hover:bg-gray-600 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-700"
                data-dismiss-target="#${toastId}"
                aria-label="Close"
            >
                <span class="sr-only">Close</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/>
                </svg>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {

            if (toast) {

                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            }
        }, 10);

        setupToastDismiss(toast, toastId);
    }

    const processedToasts = new Map();
    const TOAST_DEDUP_WINDOW = 100;

    function showToastWithDedup (message, type = 'success', eventId = null) {

        const key = eventId ? `id:${eventId}` : `msg:${message}-${type}-${Date.now()}`;
        const now = Date.now();
        const lastShown = processedToasts.get(key);

        if (lastShown && (now - lastShown) < TOAST_DEDUP_WINDOW) {

            return;
        }

        processedToasts.set(key, now);
        showToast(message, type);

        setTimeout(() => {

            processedToasts.delete(key);
        }, TOAST_DEDUP_WINDOW);
    }

    function showNotificationToastWithDedup (data) {
        const eventId = data.id || null;
        const key = eventId ? 'id:' + eventId : 'variant:' + (data.variant || '') + '-' + (data.title || '') + '-' + Date.now();
        const now = Date.now();
        const lastShown = processedToasts.get(key);
        if (lastShown && (now - lastShown) < TOAST_DEDUP_WINDOW) {
            return;
        }
        processedToasts.set(key, now);
        showNotificationToast(data);
        setTimeout(function () { processedToasts.delete(key); }, TOAST_DEDUP_WINDOW);
    }

    function isRichToastPayload (p) {
        if (!p || typeof p !== 'object') {
            return false;
        }
        const url = p.readAndOpenUrl || p.read_and_open_url;
        if (typeof url !== 'string' || url === '' || url === '#') {
            return false;
        }
        return typeof p.variant === 'string' && p.variant.length > 0;
    }

    let listenersRegistered = false;

    function registerToastListeners () {

        if (listenersRegistered) {

            return;
        }

        document.addEventListener('livewire:init', () => {

            if (typeof Livewire !== 'undefined' && !Livewire._toastListenerRegistered) {

                Livewire.on('toast', (...args) => {

                    const data = args.length > 0 ? args[0] : null;
                    let payload;
                    if (args.length >= 6 && typeof args[0] === 'string' && args[0].length > 0) {
                        payload = {
                            variant: args[0],
                            title: args[1],
                            message: args[2],
                            readAndOpenUrl: args[3],
                            linkText: args[4],
                            id: args[5],
                            icon: (args[6] && typeof args[6] === 'string') ? args[6] : args[0],
                        };
                    } else {
                        payload = Array.isArray(data) && data[0] != null && typeof data[0] === 'object'
                            ? data[0]
                            : (typeof data === 'object' && data !== null ? data : { message: data });
                    }
                    if (payload && isRichToastPayload(payload)) {
                        showNotificationToastWithDedup(payload);
                    } else {
                        let message = payload && (payload.message !== undefined) ? payload.message : (typeof data === 'string' ? data : (payload && payload.title));
                        if (message != null && typeof message === 'object') {
                            message = (payload && payload.title && payload.message) ? (payload.title + ': ' + payload.message) : (payload && payload.title) || 'Notification';
                        }
                        message = (message != null && typeof message === 'string') ? message : 'Notification';
                        const type = (payload && payload.type) || 'success';
                        const eventId = (payload && payload.id) || null;
                        showToastWithDedup(message, type, eventId);
                    }
                });
                Livewire._toastListenerRegistered = true;
            }
        });

        if (typeof window.Livewire !== 'undefined' && !window.Livewire._toastListenerRegistered) {

            window.Livewire.on('toast', (...args) => {

                const data = args.length > 0 ? args[0] : null;
                let payload;
                if (args.length >= 6 && typeof args[0] === 'string' && args[0].length > 0) {
                    payload = {
                        variant: args[0],
                        title: args[1],
                        message: args[2],
                        readAndOpenUrl: args[3],
                        linkText: args[4],
                        id: args[5],
                        icon: (args[6] && typeof args[6] === 'string') ? args[6] : args[0],
                    };
                } else {
                    payload = Array.isArray(data) && data[0] != null && typeof data[0] === 'object'
                        ? data[0]
                        : (typeof data === 'object' && data !== null ? data : { message: data });
                }
                if (payload && isRichToastPayload(payload)) {
                    showNotificationToastWithDedup(payload);
                } else {
                    let message = payload && (payload.message !== undefined) ? payload.message : (typeof data === 'string' ? data : (payload && payload.title));
                    if (message != null && typeof message === 'object') {
                        message = (payload && payload.title && payload.message) ? (payload.title + ': ' + payload.message) : (payload && payload.title) || 'Notification';
                    }
                    message = (message != null && typeof message === 'string') ? message : 'Notification';
                    const type = (payload && payload.type) || 'success';
                    const eventId = (payload && payload.id) || null;
                    showToastWithDedup(message, type, eventId);
                }
            });
            window.Livewire._toastListenerRegistered = true;
        }

        listenersRegistered = true;
    }

    if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', registerToastListeners);

    } else {

        registerToastListeners();
    }

    window.showToast = showToast;

}) ();

(function () {

    document.addEventListener('DOMContentLoaded', function () {

        const toasts = document.querySelectorAll('[id^="toast-"]');

        toasts.forEach(function (toast) {

            setTimeout(function () {

                if (toast) {

                    toast.classList.remove('translate-x-full');
                    toast.classList.add('translate-x-0');
                }
            }, 10);

            const dismissButton = toast.querySelector('[data-dismiss-target]');

            if (dismissButton) {

                dismissButton.addEventListener('click', function (e) {

                    e.preventDefault();
                    e.stopPropagation();

                    toast.classList.remove('translate-x-0');
                    toast.classList.add('translate-x-full');

                    setTimeout(function () {

                        if (toast && toast.parentNode) {

                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                });
            }

            setTimeout(function () {

                if (toast && toast.parentNode) {

                    toast.classList.remove('translate-x-0');
                    toast.classList.add('translate-x-full');

                    setTimeout(function () {

                        if (toast && toast.parentNode) {

                            toast.parentNode.removeChild(toast);
                        }
                    }, 300);
                }
            }, 5000);
        });
    });

}) ();
