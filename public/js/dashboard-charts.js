(function () {
    const periodSelect = document.getElementById('period');
    const wrappers = {
        week: document.getElementById('period_week_wrapper'),
        month: document.getElementById('period_month_wrapper'),
        year: document.getElementById('period_year_wrapper'),
    };

    function toggleWrapper() {
        Object.keys(wrappers).forEach(function (key) {
            wrappers[key].style.display = (key === periodSelect.value) ? 'block' : 'none';
        });
    }

    periodSelect.addEventListener('change', toggleWrapper);
    toggleWrapper();
})();

(function () {
    const modeSelect = document.getElementById('chart_mode');
    const monthWrapper = document.getElementById('chart_month_wrapper');

    modeSelect.addEventListener('change', function () {
        monthWrapper.style.display = (modeSelect.value === 'month') ? 'block' : 'none';
    });
})();

function initDashboardCharts(data) {
    new Chart(document.getElementById('incomeByMonthChart'), {
        type: 'bar',
        data: {
            labels: data.monthLabels,
            datasets: [{
                label: 'Ingresos',
                data: data.incomeByMonth,
                backgroundColor: 'rgba(25, 135, 84, 0.7)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('expenseByMonthChart'), {
        type: 'bar',
        data: {
            labels: data.monthLabels,
            datasets: [{
                label: 'Gastos',
                data: data.expenseByMonth,
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('incomeByCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: data.incomeByCategory.map(c => c.name),
            datasets: [{
                data: data.incomeByCategory.map(c => c.total),
                backgroundColor: data.incomeByCategory.map(c => c.color),
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('expenseByCategoryChart'), {
        type: 'doughnut',
        data: {
            labels: data.expenseByCategory.map(c => c.name),
            datasets: [{
                data: data.expenseByCategory.map(c => c.total),
                backgroundColor: data.expenseByCategory.map(c => c.color),
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    new Chart(document.getElementById('yearlyComparisonChart'), {
        type: 'bar',
        data: {
            labels: data.yearlyLabels,
            datasets: [
                {
                    label: 'Ingresos',
                    data: data.yearlyIncomeData,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                },
                {
                    label: 'Gastos',
                    data: data.yearlyExpenseData,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}
