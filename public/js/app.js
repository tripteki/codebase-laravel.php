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

        function initSearch(searchId)
        {
            var searchInput = document.getElementById(searchId + "-input");
            var searchDropdown = document.getElementById(searchId + "-dropdown");
            var searchResults = document.getElementById(searchId + "-results");
            var searchTimeout = null;

            if (searchInput && searchDropdown && searchResults) {

                var searchUrl = searchInput.getAttribute("data-search-url");
                var searchNotFound = searchInput.getAttribute("data-search-not-found");

                searchInput.addEventListener("click", function (event)
                {
                    event.stopPropagation();
                    searchDropdown.classList.remove("hidden");
                });

                searchInput.addEventListener("focus", function ()
                {
                    searchDropdown.classList.remove("hidden");
                });

                searchInput.addEventListener("input", function ()
                {
                    var query = this.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        searchResults.innerHTML = '<div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">' + searchNotFound + "</div>";
                        return;
                    }

                    searchTimeout = setTimeout(function ()
                    {
                        fetch(searchUrl + "?q=" + encodeURIComponent(query), {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json",
                            },
                        })
                        .then(function (response) { return response.json(); })
                        .then(function (data)
                        {
                            if (data.results && data.results.length > 0) {
                                var html = "";
                                data.results.forEach(function (category)
                                {
                                    html += '<div class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">' + category.category + "</div>";
                                    category.items.forEach(function (item)
                                    {
                                        html += '<a href="' + item.url + '" class="block px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-600">';
                                        html += '<div class="font-medium text-gray-900 dark:text-white">' + item.title + "</div>";
                                        if (item.subtitle) {
                                            html += '<div class="text-sm text-gray-500 dark:text-gray-400">' + item.subtitle + "</div>";
                                        }
                                        html += "</a>";
                                    });
                                });
                                searchResults.innerHTML = html;
                            } else {
                                searchResults.innerHTML = '<div class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400">' + searchNotFound + "</div>";
                            }
                        })
                        .catch(function (error)
                        {
                            console.error("Search error:", error);
                            searchResults.innerHTML = '<div class="px-4 py-3 text-center text-sm text-red-500 dark:text-red-400">' + searchNotFound + "</div>";
                        });
                    }, 300);
                });

                document.addEventListener("click", function (event)
                {
                    if (! searchInput.contains(event.target) && ! searchDropdown.contains(event.target)) {

                        searchDropdown.classList.add("hidden");
                    }
                });
            }
        }

        initSearch("search");
        initSearch("sidebar-search");
    });
})();
