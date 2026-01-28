"use strict";

(function () {

    var root = document.documentElement;
    var storedTheme = null;

    try {

        storedTheme = window.localStorage.getItem("theme");

    } catch (e) {

        storedTheme = null;
    }

    var prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;

    function applyTheme(isDark) {

        if (isDark) {

            root.classList.add("dark");

        } else {

            root.classList.remove("dark");
        }

        document.querySelectorAll("[data-theme-icon]").forEach(function (icon) {

            icon.textContent = isDark ? "🌙" : "☀️";
        });
    }

    var initialDark = storedTheme === "dark" || (! storedTheme && prefersDark);

    applyTheme(initialDark);

    document.querySelectorAll("[data-theme-toggle]").forEach(function (button) {

        button.addEventListener("click", function () {

            var isDark = ! root.classList.contains("dark");

            applyTheme(isDark);

            try {

                window.localStorage.setItem("theme", isDark ? "dark" : "light");

            } catch (e) {

                //
            }
        });
    });

}) ();
