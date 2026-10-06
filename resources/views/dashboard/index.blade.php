@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $initials = collect(explode(' ', trim($user->name ?? '')))
            ->filter()
            ->take(2)
            ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        // Estilo de cada tipo de cuenta: [etiqueta, tono, icono]
        $accountStyles = [
            'cash' => ['Efectivo', 'income', 'bi-cash-stack'],
            'bank' => ['Banco', 'primary', 'bi-bank'],
            'card' => ['Tarjeta', 'info', 'bi-credit-card'],
            'saving' => ['Ahorro', 'primary', 'bi-piggy-bank'],
        ];
    @endphp

    <div class="gf-dashboard">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Bienvenido{{ $user ? ', ' . explode(' ', $user->name)[0] : '' }}</h1>
                <p>{{ $todayLabel }} · Resumen de tus finanzas</p>
            </div>
            <div class="gf-header-actions">
                <a href="{{ route('transactions.index') }}" class="gf-btn gf-btn-primary">
                    <i class="bi bi-plus-lg"></i> Nueva transacción
                </a>
                <span class="gf-avatar">{{ $initials ?: 'GF' }}</span>
            </div>
        </header>

        @include('dashboard.partials._period-filter-form')

        {{-- Tarjetas principales --}}
        <section class="gf-stats">
            <x-stat-card label="Ingresos totales" :value="$totalIncome" tone="primary" icon="bi-bar-chart" />
            <x-stat-card label="Ingresos {{ $periodSuffix }}" :value="$periodIncome" tone="income" icon="bi-arrow-up" />
            <x-stat-card label="Gastos {{ $periodSuffix }}" :value="$periodExpense" tone="expense" icon="bi-arrow-down" />
            <x-stat-card label="Balance total" :value="$balanceTotal" tone="balance" icon="bi-wallet2" />
        </section>

        @include('dashboard.partials._chart-filter-form')

        {{-- Ingresos y gastos por mes --}}
        <section class="gf-grid-2">
            <x-chart-card title="Ingresos por meses" :subtitle="$chartYear . ' · MXN'" canvas-id="incomeByMonthChart" />
            <x-chart-card title="Gastos por meses" :subtitle="$chartYear . ' · MXN'" canvas-id="expenseByMonthChart" />
        </section>

        {{-- Donas por categoría --}}
        <section class="gf-grid-2">
            <x-chart-card title="Ingresos por categorías" canvas-id="incomeByCategoryChart"
                :subtitle="$chartRangeLabel . ' · total $' . number_format(collect($incomeByCategory)->sum('total'), 2) . ' MXN'"
                :categories="$incomeByCategory" type="income" />
            <x-chart-card title="Gastos por categorías" canvas-id="expenseByCategoryChart"
                :subtitle="$chartRangeLabel . ' · total $' . number_format(collect($expenseByCategory)->sum('total'), 2) . ' MXN'"
                :categories="$expenseByCategory" type="expense" />
        </section>

        {{-- Comparativa anual --}}
        <x-chart-card title="Ingresos vs gastos por año" subtitle="Comparativa anual · MXN" canvas-id="yearlyComparisonChart">
            <x-slot:actions>
                <div class="gf-chart-legend">
                    <span><i style="background: var(--gf-primary)"></i>Ingresos</span>
                    <span><i style="background: var(--gf-expense-chart)"></i>Gastos</span>
                </div>
            </x-slot:actions>
        </x-chart-card>

        {{-- Últimas transacciones y cuentas --}}
        <section class="gf-grid-2">
            <div class="gf-card">
                <div class="gf-card-head">
                    <h2 class="gf-card-title">Últimas transacciones</h2>
                    <a href="{{ route('transactions.index') }}" class="gf-link">Ver todas</a>
                </div>

                @forelse ($latestTransactions as $tx)
                    @php $isIncome = $tx->type === 'income'; @endphp
                    <div class="gf-list-row">
                        <span class="gf-icon {{ $isIncome ? 'gf-tone-income' : 'gf-tone-expense' }}">
                            <i class="bi {{ $isIncome ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                        </span>
                        <div class="gf-list-body">
                            <p class="gf-list-title">{{ $tx->description ?: ($tx->category->name ?? 'Sin descripción') }}</p>
                            <p class="gf-list-meta">
                                {{ $tx->category->name ?? 'Sin categoría' }} ·
                                {{ rtrim(\Carbon\Carbon::parse($tx->date)->locale('es')->translatedFormat('j M'), '.') }}
                            </p>
                        </div>
                        <span class="gf-list-amount {{ $isIncome ? 'gf-amount-income' : 'gf-amount-expense' }}">
                            {{ $isIncome ? '+' : '−' }}${{ number_format($tx->amount, 2) }}
                        </span>
                    </div>
                @empty
                    <p class="gf-empty">Aún no tienes transacciones registradas.</p>
                @endforelse
            </div>

            <div class="gf-accounts">
                <div class="gf-card-head mb-0">
                    <h2 class="gf-card-title">Mis cuentas</h2>
                    <a href="{{ route('accounts.index') }}" class="gf-link">Gestionar</a>
                </div>

                @forelse ($dashboardAccounts as $account)
                    @php [$typeLabel, $tone, $icon] = $accountStyles[$account->type] ?? [ucfirst($account->type), 'primary', 'bi-wallet2']; @endphp
                    <div class="gf-card">
                        <span class="gf-icon gf-tone-{{ $tone }}"><i class="bi {{ $icon }}"></i></span>
                        <div class="gf-list-body">
                            <p class="gf-list-title fw-bold">{{ $account->name }}</p>
                            <p class="gf-list-meta">{{ $typeLabel }}</p>
                        </div>
                        <span class="gf-list-amount {{ $account->balance < 0 ? 'gf-amount-expense' : '' }}">
                            {{ $account->balance < 0 ? '−' : '' }}${{ number_format(abs($account->balance), 2) }}
                        </span>
                    </div>
                @empty
                    <div class="gf-card">
                        <p class="gf-empty w-100 mb-0">No tienes cuentas activas.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/dashboard-charts.js') }}?v={{ filemtime(public_path('js/dashboard-charts.js')) }}"></script>
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
