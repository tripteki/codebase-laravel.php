"use strict";

(function () {

    let userStatusChart = null;

    function initCharts () {

        if (typeof ApexCharts === 'undefined') {

            console.warn('ApexCharts is not loaded!');

            return;
        }

        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#f3f4f6' : '#1f2937';

        const userStatusChartEl = document.getElementById('user-status-chart');

        if (userStatusChartEl) {

            if (userStatusChart) {

                userStatusChart.destroy();
            }

            const labelUnverified = userStatusChartEl.dataset.labelUnverified || 'Unverified Users';
            const labelVerified = userStatusChartEl.dataset.labelVerified || 'Verified Users';
            const unverifiedCount = parseInt(userStatusChartEl.dataset.unverifiedCount || '0', 10);
            const verifiedCount = parseInt(userStatusChartEl.dataset.verifiedCount || '0', 10);

            const userStatusOptions = {
                chart: {
                    type: 'pie',
                    height: 300,
                    fontFamily: 'Inter, sans-serif',
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
                colors: ['#f59e0b', '#10b981'],
                legend: {
                    show: true,
                    position: 'bottom',
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
                    theme: isDarkMode ? 'dark' : 'light',
                },
            };

            userStatusChart = new ApexCharts(userStatusChartEl, userStatusOptions);
            userStatusChart.render();
        }
    }

    function waitForApexCharts () {

        if (typeof ApexCharts !== 'undefined') {

            setTimeout(initCharts, 100);

        } else {

            setTimeout(waitForApexCharts, 100);
        }
    }

    if (document.readyState === 'loading') {

        document.addEventListener('DOMContentLoaded', waitForApexCharts);

    } else {

        waitForApexCharts();
    }

    document.addEventListener('livewire:load', function () {
        setTimeout(initCharts, 200);
    });

    document.addEventListener('livewire:update', function () {
        setTimeout(initCharts, 200);
    });

}) ();
