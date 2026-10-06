(function () {
    const periodSelect = document.getElementById('period');
    const wrappers = {
        week: document.getElementById('period_week_wrapper'),
        month: document.getElementById('period_month_wrapper'),
        year: document.getElementById('period_year_wrapper'),
    };

    function toggleWrapper() {
        Object.keys(wrappers).forEach(function (key) {
            wrappers[key].style.display = (key === periodSelect.value) ? 'flex' : 'none';
        });
    }

    periodSelect.addEventListener('change', toggleWrapper);
    toggleWrapper();
})();

(function () {
    const modeSelect = document.getElementById('chart_mode');
    const monthWrapper = document.getElementById('chart_month_wrapper');

    modeSelect.addEventListener('change', function () {
        monthWrapper.style.display = (modeSelect.value === 'month') ? 'flex' : 'none';
    });
})();

// Paleta del mockup para las gráficas
const GF_COLORS = {
    income: '#5B3DF5',
    expense: '#F2618A',
    grid: '#F0EEF8',
    tick: '#8B87A0',
    ink: '#1B1633',
};

const gfMoney = (value) => '$' + Number(value).toLocaleString('es-MX', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
}) + ' MXN';

Chart.defaults.font.family = "'Open Sans', system-ui, sans-serif";
Chart.defaults.color = GF_COLORS.tick;

// Opciones comunes de las gráficas de barras
function gfBarOptions(showLegend) {
    return {
        responsive: true,
        plugins: {
            legend: { display: showLegend },
            tooltip: {
                backgroundColor: GF_COLORS.ink,
                padding: 10,
                cornerRadius: 8,
                callbacks: { label: (ctx) => ' ' + ctx.dataset.label + ': ' + gfMoney(ctx.parsed.y) },
            },
        },
        scales: {
            x: {
                grid: { display: false },
                border: { display: false },
            },
            y: {
                beginAtZero: true,
                grid: { color: GF_COLORS.grid },
                border: { display: false },
                ticks: { callback: (v) => Number(v).toLocaleString('es-MX') },
            },
        },
    };
}

function gfBarDataset(label, data, color, thickness = 28) {
    return {
        label: label,
        data: data,
        backgroundColor: color,
        borderRadius: 5,
        borderSkipped: false,
        maxBarThickness: thickness,
    };
}

function gfDoughnut(canvasId, categories) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !categories.length) return;

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: categories.map(c => c.name),
            datasets: [{
                data: categories.map(c => c.total),
                backgroundColor: categories.map(c => c.color),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: GF_COLORS.ink,
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: { label: (ctx) => ' ' + gfMoney(ctx.parsed) },
                },
            },
        }
    });
}

function initDashboardCharts(data) {
    new Chart(document.getElementById('incomeByMonthChart'), {
        type: 'bar',
        data: {
            labels: data.monthLabels,
            datasets: [gfBarDataset('Ingresos', data.incomeByMonth, GF_COLORS.income)]
        },
        options: gfBarOptions(false)
    });

    new Chart(document.getElementById('expenseByMonthChart'), {
        type: 'bar',
        data: {
            labels: data.monthLabels,
            datasets: [gfBarDataset('Gastos', data.expenseByMonth, GF_COLORS.expense)]
        },
        options: gfBarOptions(false)
    });

    gfDoughnut('incomeByCategoryChart', data.incomeByCategory);
    gfDoughnut('expenseByCategoryChart', data.expenseByCategory);

    // La leyenda de esta gráfica va en el encabezado de la tarjeta
    const yearly = gfBarOptions(false);

    new Chart(document.getElementById('yearlyComparisonChart'), {
        type: 'bar',
        data: {
            labels: data.yearlyLabels,
            datasets: [
                gfBarDataset('Ingresos', data.yearlyIncomeData, GF_COLORS.income, 90),
                gfBarDataset('Gastos', data.yearlyExpenseData, GF_COLORS.expense, 90),
            ]
        },
        options: yearly
    });
}
