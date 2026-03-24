<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // usuario dinemico

        $totalAccounts = Account::where('user_id', $userId)->count();
        $totalCategories = Category::where('user_id', $userId)->count();
        $totalPaymentMethods = PaymentMethod::where('user_id', $userId)->count();
        $totalTransactions = Transaction::where('user_id', $userId)->count();
        $totalTransfers = Transfer::where('user_id', $userId)->count();

        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->sum('amount');

        $balance = $totalIncome - $totalExpense;

        return view('dashboard.index', compact(
            'totalAccounts',
            'totalCategories',
            'totalPaymentMethods',
            'totalTransactions',
            'totalTransfers',
            'totalIncome',
            'totalExpense',
            'balance'
        ));
    }
}