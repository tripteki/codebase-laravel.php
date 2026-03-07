"use strict";

(function () {

    document.addEventListener('DOMContentLoaded', function () {

        const badgeElement = document.querySelector('[data-notification-badge]');
        const dropdown = document.getElementById('notification-dropdown');
        const dropdownContent = document.getElementById('notification-dropdown-content');
        const footerSection = document.getElementById('notification-dropdown-footer');
        const toggleButton = document.querySelector('[data-dropdown-toggle="notification-dropdown"]');
        const dataUrl = toggleButton ? toggleButton.getAttribute('data-notification-data-url') || '/admin/notifications/data' : '/admin/notifications/data';
        const markAllText = toggleButton ? toggleButton.getAttribute('data-mark-all-text') || 'Mark all as read' : 'Mark all as read';
        const noNotificationsText = toggleButton ? toggleButton.getAttribute('data-no-notifications-text') || 'No notifications' : 'No notifications';
        let isDropdownOpen = false;

        if (dropdown) {

            const toggleButton = document.querySelector('[data-dropdown-toggle="notification-dropdown"]');

            if (toggleButton) {

                toggleButton.addEventListener('click', function () {

                    setTimeout(function () {

                        isDropdownOpen = !dropdown.classList.contains('hidden');

                        if (isDropdownOpen && typeof Livewire !== 'undefined') {

                            try {

                                let element = dropdown;
                                let livewireId = null;
                                let maxDepth = 10;
                                let depth = 0;

                                while (element && !livewireId && depth < maxDepth) {

                                    livewireId = element.getAttribute('wire:id');
                                    if (! livewireId) {

                                        element = element.parentElement;
                                        depth++;
                                    }
                                }

                                if (livewireId && Livewire.find) {

                                    const component = Livewire.find(livewireId);
                                    if (component && typeof component.call === 'function') {

                                        component.call('refresh');
                                    }
                                }
                            } catch (error) {

                                console.warn('Could not refresh notification component:', error);
                            }
                        }
                    }, 50);
                });
            }

            document.addEventListener('click', function (e) {

                if (! dropdown.contains(e.target) && (! toggleButton || ! toggleButton.contains(e.target))) {

                    setTimeout(function () {

                        isDropdownOpen = !dropdown.classList.contains('hidden');
                    }, 50);
                }
            });
        }

        function renderNotificationItem (notification) {

            const unreadClass = notification.is_read ? '' : 'bg-blue-50 dark:bg-blue-900/20';
            const unreadIndicator = notification.is_read
                ? '<svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>'
                : '<div class="h-2 w-2 rounded-full bg-blue-600"></div>';
            const titleClass = notification.is_read ? '' : 'font-semibold';
            const bodyHtml = notification.body ? `<p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">${escapeHtml(notification.body)}</p>` : '';

            return `
                <li>
                    <a
                        href="${notification.mark_read_url}"
                        class="block px-4 py-2 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600 ${unreadClass}"
                    >
                        <div class="flex items-start gap-2">
                            <div class="flex-shrink-0 mt-0.5">
                                ${unreadIndicator}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white ${titleClass}">
                                    ${escapeHtml(notification.title)}
                                </p>
                                ${bodyHtml}
                                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                                    ${notification.created_at}
                                </p>
                            </div>
                        </div>
                    </a>
                </li>
            `;
        }

        function escapeHtml (text) {

            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function updateDropdownContent (notifications, unreadCount) {

            if (! dropdownContent) {

                return;
            }

            const headerSection = dropdown ? dropdown.querySelector('.px-4.py-3') : null;

            if (headerSection) {

                const headerDiv = headerSection.querySelector('.flex.items-center.justify-between');
                const existingButton = headerDiv ? headerDiv.querySelector('button[wire\\:click="markAllAsRead"]') : null;

                if (unreadCount > 0) {

                    if (! existingButton && headerDiv) {

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.setAttribute('wire:click', 'markAllAsRead');
                        button.className = 'text-xs text-blue-600 hover:underline dark:text-blue-400';
                        button.textContent = markAllText;
                        headerDiv.appendChild(button);

                        if (typeof Livewire !== 'undefined') {

                            try {

                                let element = dropdown;
                                let livewireId = null;

                                while (element && !livewireId) {

                                    livewireId = element.getAttribute('wire:id');
                                    if (! livewireId) {

                                        element = element.parentElement;
                                    }
                                }

                                if (livewireId && Livewire.find) {

                                    const component = Livewire.find(livewireId);
                                    if (component && typeof component.call === 'function') {

                                        button.addEventListener('click', function (e) {

                                            e.preventDefault();
                                            e.stopPropagation();
                                            component.call('markAllAsRead');
                                        });
                                    }
                                }
                            } catch (error) {

                                console.warn('Could not attach Livewire event handler:', error);
                            }
                        }
                    }
                } else {

                    if (existingButton) {

                        existingButton.remove();
                    }
                }
            }

            if (notifications.length > 0) {

                const ul = dropdownContent.querySelector('ul');
                if (ul) {

                    ul.innerHTML = notifications.map(renderNotificationItem).join('');
                } else {

                    dropdownContent.innerHTML = `<ul class="py-2">${notifications.map(renderNotificationItem).join('')}</ul>`;
                }

                if (footerSection) {

                    footerSection.style.display = 'block';
                }
            } else {

                dropdownContent.innerHTML = `
                    <div class="px-4 py-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A1.932 1.932 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">${noNotificationsText}</p>
                    </div>
                `;

                if (footerSection) {

                    footerSection.style.display = 'none';
                }
            }
        }

        var bellFilledSvg = '<svg class="notification-bell-icon" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"><path d="M17.133 12.632v-1.8a5.406 5.406 0 0 0-4.154-5.262.955.955 0 0 0 .021-.106V3.1a1 1 0 0 0-2 0v2.364a.955.955 0 0 0 .021.106 5.406 5.406 0 0 0-4.154 5.262v1.8C6.867 15.018 5 15.614 5 16.807 5 17.4 5 18 5.538 18h12.924C19 18 19 17.4 19 16.807c0-1.193-1.867-1.789-1.867-4.175ZM6 6a1 1 0 0 1-.707-.293l-1-1a1 1 0 0 1 1.414-1.414l1 1A1 1 0 0 1 6 6Zm-2 4H3a1 1 0 0 1 0-2h1a1 1 0 1 1 0 2Zm14-4a1 1 0 0 1-.707-1.707l1-1a1 1 0 1 1 1.414 1.414l-1 1A1 1 0 0 1 18 6Zm3 4h-1a1 1 0 1 1 0-2h1a1 1 0 1 1 0 2ZM8.823 19a3.453 3.453 0 0 0 6.354 0H8.823Z"/></svg>';
        var bellOutlineSvg = '<svg class="notification-bell-outline" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5.365V3m0 2.365a5.338 5.338 0 0 1 5.133 5.368v1.8c0 2.386 1.867 2.982 1.867 4.175 0 .593 0 1.292-.538 1.292H5.538C5 18 5 17.301 5 16.708c0-1.193 1.867-1.789 1.867-4.175v-1.8A5.338 5.338 0 0 1 12 5.365ZM8.733 18c.094.852.306 1.54.944 2.112a3.48 3.48 0 0 0 4.646 0c.638-.572 1.236-1.26 1.33-2.112h-6.92Z"/></svg>';

        function updateBellIcon(unreadCount) {

            if (! toggleButton) return;

            var bellSvg = toggleButton.querySelector('svg');
            var redDot = toggleButton.querySelector('span.notification-dot');

            if (unreadCount > 0) {
                if (bellSvg && !bellSvg.classList.contains('notification-bell-icon')) {
                    bellSvg.outerHTML = bellFilledSvg;
                }
                if (!redDot) {
                    redDot = document.createElement('span');
                    redDot.className = 'notification-dot absolute top-0 right-0 h-2.5 w-2.5 translate-x-[35%] -translate-y-[35%] rounded-full shadow-[0_0_0_2px_white] dark:shadow-[0_0_0_2px_#1f2937]';
                    redDot.setAttribute('aria-hidden', 'true');
                    toggleButton.appendChild(redDot);
                } else {
                    redDot.style.display = '';
                }
            } else {
                if (bellSvg && !bellSvg.classList.contains('notification-bell-outline')) {
                    bellSvg.outerHTML = bellOutlineSvg;
                }
                if (redDot) {
                    redDot.style.display = 'none';
                }
            }
        }

        function pollNotifications () {

            fetch(dataUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
            .then(function (response) {

                return response.json();
            })
            .then(function (data) {

                const unreadCount = data.unread_count || 0;

                if (badgeElement && data.unread_count !== undefined) {

                    if (data.unread_count > 0) {

                        badgeElement.textContent = data.unread_count > 9 ? '9+' : data.unread_count.toString();
                        badgeElement.style.display = 'block';

                    } else {

                        badgeElement.style.display = 'none';
                    }
                }

                updateBellIcon(unreadCount);

                if (isDropdownOpen && typeof Livewire !== 'undefined') {
                    try {
                        let element = dropdown;
                        let livewireId = null;
                        let maxDepth = 10;
                        let depth = 0;

                        while (element && !livewireId && depth < maxDepth) {
                            livewireId = element.getAttribute('wire:id');
                            if (! livewireId) {
                                element = element.parentElement;
                                depth++;
                            }
                        }

                        if (livewireId && Livewire.find) {
                            const component = Livewire.find(livewireId);
                            if (component && typeof component.call === 'function') {
                                component.call('refresh');
                            }
                        }
                    } catch (error) {
                        console.warn('Could not refresh notification component:', error);
                    }
                }
            })
            .catch(function (error) {

                console.error('Error polling notifications:', error);
            });

            setTimeout(pollNotifications, 5000);
        }

        pollNotifications();

        if (dropdown && typeof Livewire !== 'undefined') {

            document.addEventListener('livewire:update', function () {
                setTimeout(function() {
                    pollNotifications();
                }, 100);

                if (isDropdownOpen && dropdown.classList.contains('hidden')) {

                    dropdown.classList.remove('hidden');

                    if (typeof Flowbite !== 'undefined' && Flowbite.Dropdown) {

                        const dropdownInstance = Flowbite.Dropdown.getOrCreateInstance(dropdown, {
                            triggerType: 'click',
                            offsetSkidding: 0,
                            offsetDistance: 10,
                            delay: 300,
                        });

                        if (dropdownInstance) {

                            dropdownInstance.show();
                        }
                    }
                }
            });

            document.addEventListener('livewire:before-update', function () {

                if (dropdown) {

                    isDropdownOpen = !dropdown.classList.contains('hidden');
                }
            });

            Livewire.on('notification-tab-changed', function () {

                if (dropdown && !dropdown.classList.contains('hidden')) {

                    setTimeout(function() {

                        if (typeof Flowbite !== 'undefined' && Flowbite.Dropdown) {

                            const dropdownInstance = Flowbite.Dropdown.getOrCreateInstance(dropdown);
                            if (dropdownInstance) {

                                dropdownInstance.show();
                            }
                        }
                    }, 100);
                }
            });
        }
    });

}) ();
