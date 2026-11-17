/* ZaxSecure Desk V2 — Shared UI Components (Sidebar Drawer, Password Toggle, Dashboard Charts) */
(function () {
    'use strict';

    const $ = (s, r = document) => r.querySelector(s);
    const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

    function refreshIcons() {
        if (window.lucide) {
            try { window.lucide.createIcons(); } catch (e) {}
        }
    }

    function bindSidebarDrawer() {
        const sidebar = $('#sidebar');
        const scrim = $('#scrim');
        const togglers = $$('[data-sidebar-toggler], #sidebarToggleBtn, #menuBtn');

        if (!sidebar) return;

        const setOpen = (open) => {
            sidebar.classList.toggle('open', open);
        };

        togglers.forEach((btn) => {
            btn.addEventListener('click', () => setOpen(!sidebar.classList.contains('open')));
        });

        if (scrim) {
            scrim.addEventListener('click', () => setOpen(false));
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                setOpen(false);
            }
        });
    }

    function bindPasswordToggles() {
        $$('[data-toggle-pass]').forEach((btn) => {
            if (btn.dataset.boundToggle === '1') return;
            btn.dataset.boundToggle = '1';

            const targetId = btn.dataset.togglePass;
            const input = document.getElementById(targetId);
            if (!input) return;

            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                const iconName = show ? 'eye-off' : 'eye';
                btn.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                btn.innerHTML = `<i data-lucide="${iconName}"></i>`;
                refreshIcons();
            });
        });
    }

    function initDashboardCharts() {
        const trendCanvas = document.getElementById('trendChart') || document.getElementById('trend');
        if (!trendCanvas || !window.DASHBOARD_DATA) return;

        const renderChart = () => {
            if (!window.Chart) return;
            const data = window.DASHBOARD_DATA.trend || { labels: [], values: [] };
            new window.Chart(trendCanvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Jumlah Laporan Insiden',
                        data: data.values,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(220, 38, 38, 0.16)',
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 22, 36, 0.96)',
                            borderColor: 'rgba(255, 255, 255, 0.14)',
                            borderWidth: 1,
                            padding: 10,
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#94a3b8' },
                            grid: { color: 'rgba(255, 255, 255, 0.06)' }
                        },
                        x: {
                            ticks: { color: '#94a3b8' },
                            grid: { display: false }
                        }
                    }
                }
            });
        };

        if (window.Chart) {
            renderChart();
        } else {
            window.addEventListener('load', renderChart);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        bindSidebarDrawer();
        bindPasswordToggles();
        initDashboardCharts();
        refreshIcons();
    });
})();
