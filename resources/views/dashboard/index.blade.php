@extends('layouts.app')

@section('content')

    {{-- Contenido del dashboard --}}
    <div class="dashboard-main container-fluid dashboard-wrapper">

        <div class="row g-0">
            <div class="col-md-9 col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-1">Bienvenido</h2>
                    </div>
                </div>

                @include('dashboard.partials._period-filter-form')

                {{-- Tarjeta de ingresos totales --}}
                <div class="col-md-6 col-xl-4 mb-2">
                    <x-stat-card label="Ingresos totales" :value="$balanceTotal" :shadow="false" class="mb-4" />
                </div>

                <!-- Tarjetas principales -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-4">
                        <x-stat-card label="Ingresos del mes" :value="$periodIncome" color="success" />
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <x-stat-card label="Gastos del mes" :value="$periodExpense" color="danger" />
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <x-stat-card label="Balance total" :value="$balanceTotal" />
                    </div>
                </div>
            </div>
        </div>

        @include('dashboard.partials._chart-filter-form')

        {{-- Las 5 gráficas, sueltas --}}
        <div class="container-grafic row g-4 mt-2">

            <div class="col-md-5">
                <x-chart-card title="Ingresos por meses" canvas-id="incomeByMonthChart" />
            </div>

            <div class="col-md-5">
                <x-chart-card title="Gastos por meses" canvas-id="expenseByMonthChart" />
            </div>

            <div class="col-md-5">
                <x-chart-card title="Ingresos por categorías" canvas-id="incomeByCategoryChart"
                    :categories="$incomeByCategory" text-class="text-success" />
            </div>

            <div class="col-md-5">
                <x-chart-card title="Gastos por categorías" canvas-id="expenseByCategoryChart"
                    :categories="$expenseByCategory" text-class="text-danger" />
            </div>

            <div class="col-md-10">
                <x-chart-card title="Ingresos vs Gastos por año" canvas-id="yearlyComparisonChart" />
            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
    <script>
        initDashboardCharts({
            monthLabels: @json($monthLabels),
            incomeByMonth: @json($incomeByMonth),
            expenseByMonth: @json($expenseByMonth),
            incomeByCategory: @json($incomeByCategory),
            expenseByCategory: @json($expenseByCategory),
            yearlyLabels: @json($yearlyLabels),
            yearlyIncomeData: @json($yearlyIncomeData),
            yearlyExpenseData: @json($yearlyExpenseData),
        });
    </script>
@endpush
