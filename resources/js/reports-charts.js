import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const palette = {
    indigo: 'rgb(99, 102, 241)',
    emerald: 'rgb(16, 185, 129)',
    amber: 'rgb(245, 158, 11)',
    rose: 'rgb(244, 63, 94)',
    sky: 'rgb(14, 165, 233)',
    zinc: 'rgb(161, 161, 170)',
};

const charts = new Map();

function destroyChart(id) {
    if (charts.has(id)) {
        charts.get(id).destroy();
        charts.delete(id);
    }
}

function baseOptions(type = 'line') {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: type !== 'bar', rtl: true, labels: { font: { family: 'Vazirmatn' } } },
        },
        scales: type === 'doughnut' ? {} : {
            x: { ticks: { font: { family: 'Vazirmatn', size: 11 } }, grid: { display: false } },
            y: { beginAtZero: true, ticks: { font: { family: 'Vazirmatn', size: 11 } } },
        },
    };
}

function initChart(canvas) {
    const id = canvas.id;
    if (!id) return;

    destroyChart(id);

    const config = JSON.parse(canvas.dataset.config || '{}');
    const type = canvas.dataset.type || 'line';

    charts.set(id, new Chart(canvas, {
        type,
        data: config,
        options: { ...baseOptions(type), ...(config.options || {}) },
    }));

    canvas.addEventListener('chart:click', (event) => {
        const detail = event.detail || {};
        if (detail.dimension && detail.value !== undefined) {
            window.Livewire?.dispatch('report-drilldown', detail);
        }
    });
}

function wireDrilldown(canvas, chart) {
    canvas.onclick = (evt) => {
        const points = chart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
        if (!points.length) return;

        const index = points[0].index;
        const dimension = canvas.dataset.drilldown;
        const values = JSON.parse(canvas.dataset.drilldownValues || '[]');
        const value = values[index];

        if (dimension && value !== undefined && window.Livewire) {
            const component = canvas.closest('[wire\\:id]');
            if (component) {
                window.Livewire.find(component.getAttribute('wire:id'))?.call('drilldown', dimension, String(value));
            }
        }
    };
}

export function initReportCharts() {
    document.querySelectorAll('[data-report-chart]').forEach((canvas) => {
        initChart(canvas);
        const chart = charts.get(canvas.id);
        if (chart && canvas.dataset.drilldown) {
            wireDrilldown(canvas, chart);
        }
    });
}

document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', () => {
        requestAnimationFrame(initReportCharts);
    });
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initReportCharts);
} else {
    initReportCharts();
}

export { palette };
