"use strict";

(function () {

    var root = document.documentElement;
    var storedTheme = null;

    try {

        storedTheme = window.localStorage.getItem("theme");

    } catch (e) {

        storedTheme = null;
    }

    var themeToggleBtn = document.getElementById("theme-toggle");
    var darkIcon = document.getElementById("theme-toggle-dark-icon");
    var lightIcon = document.getElementById("theme-toggle-light-icon");

    function setIcon(isDark) {

        if (! darkIcon || ! lightIcon) {

            return;
        }

        if (isDark) {

            darkIcon.classList.remove("hidden");
            lightIcon.classList.add("hidden");

        } else {

            darkIcon.classList.add("hidden");
            lightIcon.classList.remove("hidden");
        }
    }

    function applyTheme(isDark) {

        if (isDark) {

            root.classList.add("dark");

        } else {

            root.classList.remove("dark");
        }

        setIcon(isDark);
    }

    var defaultTheme = (root.getAttribute("data-default-theme") || "light").toLowerCase();
    var storedValue = (storedTheme || "").toLowerCase();
    var initialDark = defaultTheme === "dark" || storedValue === "dark";

    applyTheme(initialDark);

    if (themeToggleBtn) {

        themeToggleBtn.addEventListener("click", function () {

            var isDark = !root.classList.contains("dark");

            applyTheme(isDark);

            try {

                window.localStorage.setItem("theme", isDark ? "dark" : "light");

            } catch (e) {

                //
            }
        });
    }
})();
