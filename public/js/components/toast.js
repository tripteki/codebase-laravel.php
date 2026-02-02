"use strict";

(function () {

    function showToast (message, type = 'success') {

        let container = document.getElementById('toast-container');

        if (! container) {

            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-5 end-5 z-50 space-y-4';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);

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
        toast.className = 'flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow border border-gray-200 dark:text-gray-400 dark:bg-gray-800 dark:border-gray-700 transform translate-x-full transition-all duration-300 ease-in-out';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="inline-flex items-center justify-center shrink-0 w-7 h-7 ${color.icon} ${color.bg} ${color.darkBg} ${color.darkIcon} rounded">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    ${color.iconSvg}
                </svg>
                <span class="sr-only">${type} icon</span>
            </div>
            <div class="ms-3 text-sm font-normal">${message}</div>
            <button
                type="button"
                class="ms-auto flex items-center justify-center text-gray-400 hover:text-gray-900 bg-transparent hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 rounded-lg text-sm h-8 w-8 dark:hover:bg-gray-600 dark:text-gray-500 dark:hover:text-white dark:focus:ring-gray-700"
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

        const dismissButton = toast.querySelector(`[data-dismiss-target="#${toastId}"]`);

        if (dismissButton) {

            dismissButton.addEventListener('click', (e) => {

                e.preventDefault();
                e.stopPropagation();

                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');

                setTimeout(() => {

                    if (toast && toast.parentNode) {

                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            });
        }

        setTimeout(() => {

            if (toast && toast.parentNode) {

                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full');

                setTimeout(() => {

                    if (toast && toast.parentNode) {

                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }, 5000);
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

    let listenersRegistered = false;

    function registerToastListeners () {

        if (listenersRegistered) {

            return;
        }

        document.addEventListener('livewire:init', () => {

            if (typeof Livewire !== 'undefined' && !Livewire._toastListenerRegistered) {

                Livewire.on('toast', (data) => {

                    const message = data.message || data;
                    const type = data.type || 'success';
                    const eventId = data.id || null;
                    showToastWithDedup(message, type, eventId);
                });
                Livewire._toastListenerRegistered = true;
            }
        });

        if (typeof window.Livewire !== 'undefined' && !window.Livewire._toastListenerRegistered) {

            window.Livewire.on('toast', (data) => {

                const message = data.message || data;
                const type = data.type || 'success';
                const eventId = data.id || null;
                showToastWithDedup(message, type, eventId);
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
