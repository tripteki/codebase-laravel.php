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
            var categoryInput = document.getElementById(searchId + "-category");
            var searchTimeout = null;

            if (searchInput && searchDropdown && searchResults) {

                var searchUrl = searchInput.getAttribute("data-search-url");
                var searchNotFound = searchInput.getAttribute("data-search-not-found");
                var category = categoryInput ? categoryInput.value : "all";

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
                        category = categoryInput ? categoryInput.value : "all";
                        var url = searchUrl + "?q=" + encodeURIComponent(query) + "&category=" + encodeURIComponent(category);
                        fetch(url, {
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
                                        html += '<div class="flex items-center gap-3">';

                                        if (item.avatar) {
                                            html += '<img src="' + item.avatar + '" alt="" class="h-8 w-8 rounded-full object-cover flex-shrink-0">';
                                        } else {
                                            html += '<div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-600">';
                                            html += '<svg class="h-4 w-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
                                            html += '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>';
                                            html += '</svg></div>';
                                        }

                                        html += '<div class="min-w-0">';
                                        html += '<div class="font-medium text-gray-900 dark:text-white truncate">' + item.title + "</div>";
                                        if (item.subtitle) {
                                            html += '<div class="text-sm text-gray-500 dark:text-gray-400 truncate">' + item.subtitle + "</div>";
                                        }
                                        html += "</div></div>";
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

        document.addEventListener("click", function (event)
        {
            var option = event.target.closest("[data-search-category-option]");
            if (! option) { return; }
            var catDropdown = option.closest("[id$='-category-dropdown']");
            if (! catDropdown || ! catDropdown.id) { return; }
            var sid = catDropdown.id.replace(/-category-dropdown$/, "");
            var catInput = document.getElementById(sid + "-category");
            var catLabelEl = document.getElementById(sid + "-category-label");
            var sidInput = document.getElementById(sid + "-input");
            var cat = option.getAttribute("data-category");
            var label = option.getAttribute("data-label");
            if (catInput) { catInput.value = cat; }
            if (catLabelEl) { catLabelEl.textContent = label || ""; }
            catDropdown.classList.add("hidden");
            if (sidInput && sidInput.value.trim().length >= 2) {
                var resultsDropdown = document.getElementById(sid + "-dropdown");
                if (resultsDropdown) { resultsDropdown.classList.remove("hidden"); }
                sidInput.dispatchEvent(new Event("input", { bubbles: true }));
            }
        });
    });

    function formatYyyyMmDd(date) {

        if (! date) {

            return "";
        }

        var d = date instanceof Date ? date : new Date(date);

        if (isNaN(d.getTime())) {

            return "";
        }

        var y = d.getFullYear();
        var m = String(d.getMonth() + 1).padStart(2, "0");
        var day = String(d.getDate()).padStart(2, "0");

        return y + "-" + m + "-" + day;
    }

    function initInlineDatepickers() {

        if (typeof window.Datepicker === "undefined") {

            return;
        }

        document.querySelectorAll(".inline-datepicker").forEach(function (div) {

            var input = div.previousElementSibling;

            if (! input || input.type !== "hidden" || ! input.id) {

                return;
            }

            var inputId = div.getAttribute("data-inline-datepicker");

            if (inputId !== input.id) {

                return;
            }

            if (div._inlineDatepickerInstance) {

                div._inlineDatepickerInstance.destroyAndRemoveInstance();
                div._inlineDatepickerInstance = null;
            }

            var minDate = div.getAttribute("data-min-date") || null;
            var maxDate = div.getAttribute("data-max-date") || null;
            var options = {
                format: "yyyy-mm-dd",
                minDate: minDate || undefined,
                maxDate: maxDate || undefined,
            };

            var fp = new window.Datepicker(div, options);

            div._inlineDatepickerInstance = fp;

            if (input.value) {

                fp.setDate(input.value);
            }

            div.addEventListener("changeDate", function () {

                var date = fp.getDate();
                var value = formatYyyyMmDd(date);

                input.value = value;
                input.dispatchEvent(new Event("input", { bubbles: true }));
                input.dispatchEvent(new Event("blur", { bubbles: true }));
            });
        });
    }

    function reinitInlineDatepickers() {

        initInlineDatepickers();
    }

    document.addEventListener("livewire:navigated", reinitInlineDatepickers);
    document.addEventListener("DOMContentLoaded", reinitInlineDatepickers);
    document.addEventListener("livewire:load", function () {

        if (window.Livewire) {

            window.Livewire.hook("morph.updated", function () {

                setTimeout(reinitInlineDatepickers, 0);
            });
        }
    });

    (function observeInlineDatepickers() {

        var reinitScheduled = false;

        function scheduleReinit() {

            if (reinitScheduled) {

                return;
            }

            reinitScheduled = true;

            setTimeout(function () {

                reinitScheduled = false;
                reinitInlineDatepickers();
            }, 50);
        }

        function nodeHasInlineDatepicker(node) {

            if (node.nodeType !== 1) {

                return false;
            }

            if (node.classList && node.classList.contains("inline-datepicker")) {

                return true;
            }

            if (node.querySelector && node.querySelector(".inline-datepicker")) {

                return true;
            }

            return false;
        }

        var observer = new MutationObserver(function (mutations) {

            for (var i = 0; i < mutations.length; i++) {

                var added = mutations[i].addedNodes;

                for (var j = 0; j < added.length; j++) {

                    if (nodeHasInlineDatepicker(added[j])) {

                        scheduleReinit();
                        return;
                    }
                }
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    })();
})();
