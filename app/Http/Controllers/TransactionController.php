<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['account', 'category', 'paymentMethod']);

        // filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // filtro por periodo
        if ($request->filled('period')) {
            switch ($request->period) {

                case 'day':
                    if ($request->filled('date')) {
                        $query->whereDate('date', $request->date);
                    }
                    break;

                case 'week':
                    if ($request->filled('week')) {
                        // formato recibido del input type="week": "2026-W27"
                        [$year, $week] = explode('-W', $request->week);
                        $startOfWeek = \Carbon\Carbon::now()->setISODate((int) $year, (int) $week)->startOfWeek();
                        $endOfWeek = $startOfWeek->copy()->endOfWeek();
                        $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
                    }
                    break;

                case 'month':
                    if ($request->filled('month')) {
                        // formato recibido del input type="month": "2026-07"
                        [$year, $month] = explode('-', $request->month);
                        $query->whereYear('date', $year)->whereMonth('date', $month);
                    }
                    break;

                case 'year':
                    if ($request->filled('year')) {
                        $query->whereYear('date', $request->year);
                    }
                    break;
            }
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $accounts = Account::all();
        $categories = Category::all();
        $paymentMethods = PaymentMethod::all();

        return view('transactions.index', compact('transactions', 'accounts', 'categories', 'paymentMethods'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', 1)->orderBy('name')->get();
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', 1)->orderBy('name')->get();

        return view('transactions.create', compact('accounts', 'categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'nullable|string|max:255',
            'is_historical' => 'required|boolean',
        ]);

        Transaction::create([
            'user_id' => Auth::id(),
            'date' => $validated['date'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'account_id' => $validated['account_id'],
            'category_id' => $validated['category_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'description' => $validated['description'] ?? null,
            'is_historical' => $validated['is_historical'],
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transacción creada correctamente.');
    }

    public function edit(Transaction $transaction)
    {
        $accounts = Account::where('is_active', 1)->orderBy('name')->get();
        $categories = Category::where('is_active', 1)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', 1)->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'required|exists:categories,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'nullable|string|max:255',
            'is_historical' => 'required|boolean',
        ]);

        $transaction->update([
            'date' => $validated['date'],
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'account_id' => $validated['account_id'],
            'category_id' => $validated['category_id'],
            'payment_method_id' => $validated['payment_method_id'],
            'description' => $validated['description'] ?? null,
            'is_historical' => $validated['is_historical'],
        ]);

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transacción actualizada correctamente.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()
            ->route('transactions.index')
            ->with('success', 'Transacción eliminada correctamente.');
    }
}
