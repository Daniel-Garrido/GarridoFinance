<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        
        // Obtener todas las cuentas del usuario autenticado con los cálculos de saldo
        $accounts = Account::where('user_id', Auth::id())
            ->latest()
            ->withSum(['transactions as income_sum' => function ($q) {
                $q->where('type', 'income');
            }], 'amount')
            ->withSum(['transactions as expense_sum' => function ($q) {
                $q->where('type', 'expense');
            }], 'amount')
            ->withSum('incomingTransfers as transfers_in_sum', 'amount')
            ->withSum('outgoingTransfers as transfers_out_sum', 'amount')
            ->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $validated['user_id'] = Auth::id();

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Cuenta creada correctamente');
    }

    public function show(Account $account)
    {
        //
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Cuenta actualizada correctamente');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Cuenta eliminada correctamente');
    }
}
