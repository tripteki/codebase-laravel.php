"use strict";

(function () {

    let userStatusChart = null;
    let usersByRoleChart = null;

    function getTenantPrimaryColor () {

        const fallback = "#2563eb";

        try {

            const raw = getComputedStyle(document.documentElement)
                .getPropertyValue("--tenant-primary")
                .trim();

            if (raw !== "") {

                return raw;
            }
        } catch (e) {

            //
        }

        return fallback;
    }

    function initCharts () {

        if (typeof ApexCharts === "undefined") {

            console.warn("ApexCharts is not loaded!");

            return;
        }

        const isDarkMode = document.documentElement.classList.contains("dark");
        const textColor = isDarkMode ? "#f3f4f6" : "#1f2937";

        const userStatusChartEl = document.getElementById("user-status-chart");

        if (userStatusChartEl) {

            if (userStatusChart) {

                userStatusChart.destroy();
            }

            const labelUnverified = userStatusChartEl.dataset.labelUnverified || "Unverified Users";
            const labelVerified = userStatusChartEl.dataset.labelVerified || "Verified Users";
            const unverifiedCount = parseInt(userStatusChartEl.dataset.unverifiedCount || "0", 10);
            const verifiedCount = parseInt(userStatusChartEl.dataset.verifiedCount || "0", 10);

            const userStatusOptions = {
                chart: {
                    type: "pie",
                    height: 300,
                    fontFamily: "Inter, sans-serif",
                    toolbar: {
                        show: false,
                    },
                },
                labels: [
                    labelUnverified,
                    labelVerified,
                ],
                series: [
                    unverifiedCount,
                    verifiedCount,
                ],
                colors: ["#f59e0b", "#10b981"],
                legend: {
                    show: true,
                    position: "bottom",
                    labels: {
                        colors: textColor,
                    },
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [textColor],
                    },
                },
                tooltip: {
                    theme: isDarkMode ? "dark" : "light",
                },
            };

            userStatusChart = new ApexCharts(userStatusChartEl, userStatusOptions);
            userStatusChart.render();
        }

        const usersByRoleEl = document.querySelector("[data-chart-role=\"users-by-role\"]");

        if (usersByRoleEl) {

            if (usersByRoleChart) {

                usersByRoleChart.destroy();
            }

            let labels = [];
            let series = [];

            try {

                labels = JSON.parse(usersByRoleEl.dataset.labels || "[]");
                series = JSON.parse(usersByRoleEl.dataset.series || "[]");
            } catch (e) {

                labels = [];
                series = [];
            }

            const formatCount = function (val) {

                const n = Number(val);

                return Number.isFinite(n) ? String(Math.round(n)) : String(val);
            };

            const escapeTooltipHtml = function (str) {

                if (str == null) {

                    return "";
                }

                const div = document.createElement("div");

                div.textContent = String(str);

                return div.innerHTML;
            };

            const usersByRoleBarColor = getTenantPrimaryColor();
            const axisLabelColor = isDarkMode ? "#e5e7eb" : "#374151";
            const dataLabelColor = isDarkMode ? "#f9fafb" : "#111827";

            const usersByRoleOptions = {
                theme: {
                    mode: isDarkMode ? "dark" : "light",
                },
                chart: {
                    type: "bar",
                    height: 320,
                    fontFamily: "Inter, sans-serif",
                    foreColor: axisLabelColor,
                    background: "transparent",
                    toolbar: {
                        show: false,
                    },
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        columnWidth: "55%",
                    },
                },
                series: [
                    {
                        name: "Users",
                        data: Array.isArray(series) ? series : [],
                    },
                ],
                xaxis: {
                    categories: Array.isArray(labels) ? labels : [],
                    labels: {
                        style: {
                            colors: Array.isArray(labels) && labels.length > 0
                                ? labels.map(function () {

                                    return axisLabelColor;
                                })
                                : axisLabelColor,
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: axisLabelColor,
                        },
                    },
                    min: 0,
                    tickAmount: 5,
                },
                grid: {
                    borderColor: isDarkMode ? "#4b5563" : "#e5e7eb",
                },
                colors: [usersByRoleBarColor],
                dataLabels: {
                    enabled: true,
                    style: {
                        colors: [dataLabelColor],
                    },
                },
                legend: {
                    show: false,
                },
                tooltip: {
                    enabled: true,
                    theme: isDarkMode ? "dark" : "light",
                    intersect: true,
                    shared: false,
                    followCursor: true,
                    fillSeriesColor: false,
                    custom: function ({ series, seriesIndex, dataPointIndex, w }) {

                        const cats = w?.config?.xaxis?.categories;
                        let label = "";

                        if (Array.isArray(labels) && labels[dataPointIndex] != null && labels[dataPointIndex] !== "") {

                            label = String(labels[dataPointIndex]);
                        } else if (Array.isArray(cats) && cats[dataPointIndex] != null && cats[dataPointIndex] !== "") {

                            label = String(cats[dataPointIndex]);
                        } else if (Array.isArray(w.globals.categoryLabels) && w.globals.categoryLabels[dataPointIndex] != null) {

                            label = String(w.globals.categoryLabels[dataPointIndex]);
                        } else if (Array.isArray(w.globals.labels) && w.globals.labels[dataPointIndex] != null) {

                            label = String(w.globals.labels[dataPointIndex]);
                        }

                        const raw = series[seriesIndex][dataPointIndex];
                        const count = formatCount(raw);
                        const bg = isDarkMode ? "#1f2937" : "#ffffff";
                        const border = isDarkMode ? "#9ca3af" : "#cbd5e1";
                        const titleColor = isDarkMode ? "#f9fafb" : "#0f172a";
                        const muted = isDarkMode ? "#d1d5db" : "#64748b";
                        const shadow = isDarkMode
                            ? "0 0 0 1px rgba(255,255,255,.12), 0 8px 24px rgba(0,0,0,.5)"
                            : "0 4px 14px rgba(15,23,42,.12)";
                        const hasTitle = label.trim() !== "";

                        const rowHtml =
                            "<div style=\"" +
                            "display:flex;" +
                            "align-items:center;" +
                            "gap:10px;" +
                            (hasTitle ? "padding:10px 2px 0 2px;" : "padding:0 2px;") +
                            "\">" +
                            "<span style=\"" +
                            "width:8px;" +
                            "height:8px;" +
                            "border-radius:0;" +
                            "background:" + usersByRoleBarColor + ";" +
                            "flex-shrink:0;" +
                            "\"></span>" +
                            "<span style=\"" +
                            "font-size:12px;" +
                            "font-weight:500;" +
                            "color:" + muted + ";" +
                            "\">Users</span>" +
                            "<span style=\"" +
                            "margin-left:auto;" +
                            "font-size:14px;" +
                            "font-weight:700;" +
                            "letter-spacing:-0.02em;" +
                            "color:" + titleColor + ";" +
                            "font-variant-numeric:tabular-nums;" +
                            "\">" +
                            escapeTooltipHtml(count) +
                            "</span>" +
                            "</div>";

                        const titleBlock = hasTitle
                            ? (
                                "<div style=\"" +
                                "font-weight:600;" +
                                "font-size:12px;" +
                                "line-height:1.4;" +
                                "letter-spacing:0.02em;" +
                                "text-transform:uppercase;" +
                                "color:" + muted + ";" +
                                "margin:0 0 8px 0;" +
                                "padding:0 2px;" +
                                "\">" +
                                escapeTooltipHtml(label) +
                                "</div>" +
                                "<div style=\"height:1px;background:" + border + ";margin:0 0 8px 0;\"></div>"
                            )
                            : "";

                        return (
                            "<div style=\"" +
                            "padding:" + (hasTitle ? "12px 14px 10px 14px" : "10px 14px") + ";" +
                            "min-width:148px;" +
                            "max-width:220px;" +
                            "border-radius:0;" +
                            "background:" + bg + ";" +
                            "border:1px solid " + border + ";" +
                            "box-shadow:" + shadow + ";" +
                            "\">" +
                            titleBlock +
                            rowHtml +
                            "</div>"
                        );
                    },
                },
            };

            usersByRoleChart = new ApexCharts(usersByRoleEl, usersByRoleOptions);
            usersByRoleChart.render();
        }
    }

    function waitForApexCharts () {

        if (typeof ApexCharts !== "undefined") {

            setTimeout(initCharts, 100);

        } else {

            setTimeout(waitForApexCharts, 100);
        }
    }

    if (document.readyState === "loading") {

        document.addEventListener("DOMContentLoaded", waitForApexCharts);

    } else {

        waitForApexCharts();
    }

    document.addEventListener("livewire:load", function () {
        setTimeout(initCharts, 200);
    });

    document.addEventListener("livewire:update", function () {
        setTimeout(initCharts, 200);
    });

    let themeDebounceTimer = null;

    new MutationObserver(function () {

        clearTimeout(themeDebounceTimer);
        themeDebounceTimer = setTimeout(initCharts, 120);
    }).observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"],
    });

}) ();
