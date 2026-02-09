"use strict";

(function () {

    var langSelect = document.querySelector("select[data-lang-switcher]");

    if (langSelect) {

        langSelect.addEventListener("change", function (event) {

            var target = event.target;
            var lang = target.value;
            var base = target.getAttribute("data-i18n-base") || "/i18n";

            if (lang) {

                window.location.href = base.replace(/\/+$/, "") + "/" + encodeURIComponent(lang);
            }
        });
    }

    var langItems = document.querySelectorAll("[data-lang-switcher-item]");

    langItems.forEach(function (item) {

        item.addEventListener("click", function (event) {

            event.preventDefault();

            var lang = item.getAttribute("data-lang");
            var base = item.getAttribute("data-i18n-base") || "/i18n";

            if (lang) {

                window.location.href = base.replace(/\/+$/, "") + "/" + encodeURIComponent(lang);
            }
        });
    });

}) ();
