"use strict";

(function ()
{
    document.addEventListener("DOMContentLoaded", function ()
    {
        (function ()
        {
            var triggers = document.querySelectorAll("[data-collapse-toggle]");

            if (! triggers || ! triggers.length) {

                return;
            }

            triggers.forEach(function (trigger)
            {
                var targetId = trigger.getAttribute("data-collapse-toggle");

                if (! targetId) {

                    return;
                }

                var target = document.getElementById(targetId);

                if (! target) {

                    return;
                }

                var icon = trigger.querySelector("[data-collapse-icon]");

                if (! icon) {

                    return;
                }

                function syncIcon ()
                {
                    var isHidden = target.classList.contains("hidden");

                    if (isHidden) {

                        icon.classList.add("rotate-90");

                    } else {

                        icon.classList.remove("rotate-90");
                    }
                }

                syncIcon();

                var observer = new MutationObserver(function ()
                {
                    syncIcon();
                });

                observer.observe(target, {
                    attributes: true,
                    attributeFilter: ["class"],
                });

                trigger.addEventListener("click", function ()
                {
                    window.setTimeout(syncIcon, 0);
                });
            });
        })();

        (function ()
        {
            var sidebar = document.getElementById("admin-sidebar");
            var content = document.querySelector("[data-sidebar-content]");
            var toggles = document.querySelectorAll("[data-sidebar-toggle]");
            var backdrop = document.getElementById("sidebar-backdrop");

            if (! sidebar || ! content || ! toggles.length) {

                return;
            }

            function isMobile ()
            {
                return window.innerWidth < 1024;
            }

            function isCollapsed ()
            {
                if (isMobile()) {

                    return sidebar.classList.contains("-translate-x-full");
                }

                return sidebar.classList.contains("sidebar-collapsed");
            }

            function applyState (collapsed)
            {
                var openIcon = document.getElementById("sidebar-toggle-open-icon");
                var closeIcon = document.getElementById("sidebar-toggle-close-icon");

                if (isMobile()) {

                    if (collapsed) {

                        sidebar.classList.add("-translate-x-full");
                        sidebar.classList.remove("sidebar-open");

                        if (backdrop) {

                            backdrop.classList.add("hidden");
                        }

                    } else {

                        sidebar.classList.remove("-translate-x-full");
                        sidebar.classList.add("sidebar-open");

                        if (backdrop) {

                            backdrop.classList.remove("hidden");
                        }
                    }

                } else {

                    if (collapsed) {

                        sidebar.classList.add("sidebar-collapsed");
                        content.classList.remove("lg:pl-64");

                    } else {

                        sidebar.classList.remove("sidebar-collapsed");

                        if (! content.classList.contains("lg:pl-64")) {

                            content.classList.add("lg:pl-64");
                        }
                    }
                }

                if (openIcon && closeIcon) {

                    if (collapsed) {

                        openIcon.classList.add("hidden");
                        closeIcon.classList.remove("hidden");

                    } else {

                        openIcon.classList.remove("hidden");
                        closeIcon.classList.add("hidden");
                    }
                }
            }

            function handleResize ()
            {
                if (isMobile()) {

                    applyState(true);

                } else {

                    applyState(false);
                }
            }

            applyState(isMobile());

            toggles.forEach(function (button)
            {
                button.addEventListener("click", function ()
                {
                    applyState(! isCollapsed());
                });
            });

            if (backdrop) {

                backdrop.addEventListener("click", function ()
                {
                    applyState(true);
                });
            }

            window.addEventListener("resize", function ()
            {
                handleResize();
            });
        })();
    });
})();
