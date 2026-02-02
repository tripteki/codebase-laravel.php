"use strict";

(function () {

    document.addEventListener('livewire:init', function () {

        const dropdown = document.getElementById('notification-dropdown');
        const toggleButton = document.querySelector('[data-dropdown-toggle="notification-dropdown"]');
        let isDropdownOpen = false;

        if (dropdown && toggleButton) {

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
                }, 100);
            });

            document.addEventListener('click', function (e) {

                if (! dropdown.contains(e.target) && (! toggleButton || ! toggleButton.contains(e.target))) {

                    setTimeout(function () {

                        isDropdownOpen = !dropdown.classList.contains('hidden');
                    }, 50);
                }
            });
        }

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

        if (dropdown && typeof Livewire !== 'undefined') {

            document.addEventListener('livewire:update', function () {

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

            document.addEventListener('click', function (e) {

                const link = e.target.closest('a[data-notification-url]');

                if (link && link.hasAttribute('data-notification-url')) {

                    const url = link.getAttribute('data-notification-url');
                    const notificationId = link.getAttribute('data-notification-id');

                    if (url && notificationId) {

                        e.preventDefault();
                        e.stopPropagation();

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

                                component.call('markAsRead', notificationId).then(function () {

                                    if (url) {

                                        window.open(url, '_blank');
                                    }
                                });
                            }
                        }
                    }
                }
            });
        }
    });

}) ();
