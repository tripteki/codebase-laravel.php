"use strict";

(function ()
{
    document.addEventListener("DOMContentLoaded", function ()
    {
        var toggle = document.querySelector("[data-dropdown-toggle='user-dropdown']");
        var menu = document.getElementById("user-dropdown");

        if (! toggle || ! menu) {

            return;
        }

        toggle.addEventListener("click", function (event)
        {
            event.preventDefault();
            event.stopPropagation();
            menu.classList.toggle("hidden");
        });

        document.addEventListener("click", function (event)
        {
            if (! toggle.contains(event.target) && ! menu.contains(event.target)) {

                menu.classList.add("hidden");
            }
        });

        var filterButton = document.getElementById("sidebar-filter-button");
        var mainContent = document.getElementById("main-content");

        if (filterButton && mainContent) {

            filterButton.addEventListener("click", function (event)
            {
                event.preventDefault();
                mainContent.scrollIntoView({ behavior: "smooth", block: "start", });
            });
        }

        var searchInput = document.getElementById("search-input");
        var searchDropdown = document.getElementById("search-dropdown");

        if (searchInput && searchDropdown) {

            searchInput.addEventListener("click", function (event)
            {
                event.stopPropagation();
                searchDropdown.classList.remove("hidden");
            });

            searchInput.addEventListener("focus", function ()
            {
                searchDropdown.classList.remove("hidden");
            });

            document.addEventListener("click", function (event)
            {
                if (! searchInput.contains(event.target) && ! searchDropdown.contains(event.target)) {

                    searchDropdown.classList.add("hidden");
                }
            });
        }
    });
})();
