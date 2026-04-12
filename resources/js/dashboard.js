import Chart from 'chart.js/auto';

const grid = (dark) => (dark ? 'rgba(148,163,184,0.15)' : 'rgba(15,23,42,0.08)');
const tick = (dark) => (dark ? '#94a3b8' : '#64748b');

function parseJson(id) {
    const el = document.getElementById(id);
    if (!el?.textContent) {
        return null;
    }
    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

const trend = parseJson('chart-trend-data');
const pie = parseJson('chart-pie-data');
const bar = parseJson('chart-bar-data');
const dark = document.documentElement.classList.contains('dark');

if (trend?.labels?.length) {
    const ctx = document.getElementById('chartTrend');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trend.labels,
                datasets: [
                    {
                        label: 'Expenses',
                        data: trend.values,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.12)',
                        fill: true,
                        tension: 0.35,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { color: grid(dark) },
                        ticks: { color: tick(dark) },
                    },
                    y: {
                        grid: { color: grid(dark) },
                        ticks: { color: tick(dark) },
                    },
                },
            },
        });
    }
}

if (pie?.labels?.length) {
    const ctx = document.getElementById('chartPie');
    if (ctx) {
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: pie.labels,
                datasets: [
                    {
                        data: pie.values,
                        backgroundColor: pie.colors?.length ? pie.colors : undefined,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: tick(dark), boxWidth: 12 },
                    },
                },
            },
        });
    }
}

if (bar?.labels?.length) {
    const ctx = document.getElementById('chartBar');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: bar.labels,
                datasets: [
                    {
                        label: 'Amount',
                        data: bar.values,
                        backgroundColor: ['#22c55e', '#f43f5e'],
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: tick(dark) },
                    },
                    y: {
                        grid: { color: grid(dark) },
                        ticks: { color: tick(dark) },
                    },
                },
            },
        });
    }
}
