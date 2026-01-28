"use strict";

(function () {

    var langSelect = document.querySelector("[data-lang-switcher]");

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

}) ();
