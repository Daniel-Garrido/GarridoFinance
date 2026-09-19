<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Paleta fija para asignar color a categorías por índice (no depende de la BD)
    private array $palette = [
        '#0d6efd',
        '#198754',
        '#dc3545',
        '#ffc107',
        '#6f42c1',
        '#20c997',
        '#fd7e14',
        '#0dcaf0',
        '#d63384',
        '#6610f2',
    ];

    public function index(Request $request)
    {
        $userId = Auth::id();

        // ============================================================
        // BLOQUE 1: Filtro principal (tarjetas de arriba) — sin cambios
        // ============================================================
        $period = $request->get('period', 'month');
        [$startDate, $endDate] = $this->getPeriodRange($period, $request);

        $currentWeekValue  = Carbon::now()->format('o-\WW');
        $currentMonthValue = Carbon::now()->format('Y-m');
        $currentYearValue  = Carbon::now()->format('Y');

        $totalAccounts = Account::where('user_id', $userId)->count();
        $totalCategories = Category::where('user_id', $userId)->count();
        $totalPaymentMethods = PaymentMethod::where('user_id', $userId)->count();
        $totalTransactions = Transaction::where('user_id', $userId)->count();
        $totalTransfers = Transfer::where('user_id', $userId)->count();

        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        $balanceTotal = $balance;

        $periodIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $periodExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $netSavings = $periodIncome - $periodExpense;
        $activeAccounts = Account::where('user_id', $userId)->count();

        $latestTransactions = Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // ============================================================
        // BLOQUE 2: Selector Año/Mes — independiente, solo para gráficas
        // ============================================================
        $chartMode = $request->get('chart_mode', 'year'); // year | month
        $chartYear = (int) $request->get('chart_year', Carbon::now()->year);
        $chartMonth = (int) $request->get('chart_month', Carbon::now()->month);

        // Rango real que usan las gráficas 3 y 4 (categorías), según el modo
        if ($chartMode === 'month') {
            $chartRangeStart = Carbon::createFromDate($chartYear, $chartMonth, 1)->startOfMonth();
            $chartRangeEnd = $chartRangeStart->copy()->endOfMonth();
        } else {
            $chartRangeStart = Carbon::createFromDate($chartYear, 1, 1)->startOfYear();
            $chartRangeEnd = $chartRangeStart->copy()->endOfYear();
        }

        // Años disponibles para poblar el selector (basado en transacciones reales)
        $availableChartYears = Transaction::where('user_id', $userId)
            ->selectRaw('DISTINCT YEAR(date) as year')
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (empty($availableChartYears)) {
            $availableChartYears = [Carbon::now()->year];
        }

        // ---- Gráfica 1 y 2: Ingresos/Gastos por mes, 12 meses del $chartYear ----
        $incomeByMonthRaw = Transaction::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->whereYear('date', $chartYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        $expenseByMonthRaw = Transaction::selectRaw('MONTH(date) as month, SUM(amount) as total')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereYear('date', $chartYear)
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthLabels = collect(range(1, 12))->map(
            fn($m) => Carbon::create(null, $m, 1)->translatedFormat('M')
        );

        $incomeByMonth = collect(range(1, 12))
            ->map(fn($m) => (float) ($incomeByMonthRaw[$m] ?? 0))
            ->toArray();

        $expenseByMonth = collect(range(1, 12))
            ->map(fn($m) => (float) ($expenseByMonthRaw[$m] ?? 0))
            ->toArray();

        // ---- Gráfica 3: Ingresos por categoría (dona + lista), según $chartMode ----
        $incomeByCategory = $this->getCategorySummary($userId, 'income', $chartRangeStart, $chartRangeEnd);

        // ---- Gráfica 4: Gastos por categoría (dona + lista), según $chartMode ----
        $expenseByCategory = $this->getCategorySummary($userId, 'expense', $chartRangeStart, $chartRangeEnd);

        // ---- Gráfica 5: Ingresos vs Gastos por año — fija, histórico completo ----
        $yearlyIncome = Transaction::selectRaw('YEAR(date) as year, SUM(amount) as total')
            ->where('user_id', $userId)
            ->where('type', 'income')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        $yearlyExpense = Transaction::selectRaw('YEAR(date) as year, SUM(amount) as total')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        $allYears = collect($yearlyIncome->keys())
            ->merge($yearlyExpense->keys())
            ->unique()
            ->sort()
            ->values();

        $yearlyLabels = $allYears->toArray();
        $yearlyIncomeData = $allYears->map(fn($y) => (float) ($yearlyIncome[$y] ?? 0))->toArray();
        $yearlyExpenseData = $allYears->map(fn($y) => (float) ($yearlyExpense[$y] ?? 0))->toArray();

        return view('dashboard.index', compact(
            'totalAccounts',
            'totalCategories',
            'totalPaymentMethods',
            'totalTransactions',
            'totalTransfers',
            'totalIncome',
            'totalExpense',
            'balance',
            'balanceTotal',
            'periodIncome',
            'periodExpense',
            'netSavings',
            'activeAccounts',
            'latestTransactions',
            'period',
            'currentWeekValue',
            'currentMonthValue',
            'currentYearValue',
            'chartMode',
            'chartYear',
            'chartMonth',
            'availableChartYears',
            'monthLabels',
            'incomeByMonth',
            'expenseByMonth',
            'incomeByCategory',
            'expenseByCategory',
            'yearlyLabels',
            'yearlyIncomeData',
            'yearlyExpenseData'
        ));
    }

    /**
     * Resumen de una categoría (income o expense) en un rango de fechas,
     * con color asignado por índice y porcentaje del total, listo para
     * alimentar tanto la gráfica de dona como la lista debajo de ella.
     */
    private function getCategorySummary(int $userId, string $type, Carbon $start, Carbon $end): array
    {
        $rows = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name', DB::raw('SUM(transactions.amount) as total'))
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', $type)
            ->whereBetween('transactions.date', [$start, $end])
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $rows->sum('total');

        return $rows->values()->map(function ($row, $index) use ($grandTotal) {
            return [
                'name' => $row->name,
                'total' => (float) $row->total,
                'percent' => $grandTotal > 0 ? round(($row->total / $grandTotal) * 100, 1) : 0,
                'color' => $this->palette[$index % count($this->palette)],
            ];
        })->toArray();
    }

    private function getPeriodRange(string $period, Request $request): array
    {
        switch ($period) {
            case 'week':
                $weekValue = $request->get('week');
                if ($weekValue && preg_match('/(\d{4})-W(\d{2})/', $weekValue, $matches)) {
                    $date = Carbon::now()->setISODate((int) $matches[1], (int) $matches[2]);
                } else {
                    $date = Carbon::now();
                }
                return [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()];

            case 'year':
                $year = $request->get('year') ?: Carbon::now()->year;
                $date = Carbon::createFromDate((int) $year, 1, 1);
                return [$date->copy()->startOfYear(), $date->copy()->endOfYear()];

            case 'month':
            default:
                $monthValue = $request->get('month');
                if ($monthValue && preg_match('/(\d{4})-(\d{2})/', $monthValue, $matches)) {
                    $date = Carbon::createFromDate((int) $matches[1], (int) $matches[2], 1);
                } else {
                    $date = Carbon::now();
                }
                return [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()];
        }
    }
}
