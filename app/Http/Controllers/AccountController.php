<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::latest()->get();
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

        // temporal mientras no tengamos login
        $validated['user_id'] = 1;

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