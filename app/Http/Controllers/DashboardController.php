<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // usuario dinamico

        //mes y año actual para mostrar solo las transacciones del mes en curso
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        //Totales generales
        $totalAccounts = Account::where('user_id', $userId)->count();
        $totalCategories = Category::where('user_id', $userId)->count();
        $totalPaymentMethods = PaymentMethod::where('user_id', $userId)->count();
        $totalTransactions = Transaction::where('user_id', $userId)->count();
        $totalTransfers = Transfer::where('user_id', $userId)->count();

        //Totales historicos
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;


        //
        $balanceTotal = $balance;

        //ingreso mensual
        $monthlyIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        //Gasto mensual
        $monthlyExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        //Ahorros netos 
        $netSavings = $monthlyIncome - $monthlyExpense;

        //
        $activeAccounts = Account::where('user_id', $userId)
            ->count();

        // ultimas transacciones
        $latestTransactions = Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // Resumen de las categorias 
        $categorySummary = Transaction::join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                DB::raw('SUM(transactions.amount) as total')
            )
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', 'expense')
            ->whereMonth('transactions.date', $currentMonth)
            ->whereYear('transactions.date', $currentYear)
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Retornar la vista del dashboard con los datos
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
            'monthlyIncome',
            'monthlyExpense',
            'netSavings',
            'activeAccounts',
            'latestTransactions',
            'categorySummary'
        ));
    }
}
