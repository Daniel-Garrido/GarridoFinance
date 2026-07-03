<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function index()
    {
        $transfers = Transfer::with(['fromAccount', 'toAccount'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $accounts = Account::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('transfers.index', compact('transfers', 'accounts'));
    }

    public function create()
    {
        $accounts = Account::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('transfers.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => [
                'required',
                'exists:accounts,id',
                'different:from_account_id',
            ],
            'description' => 'nullable|string|max:255',
        ]);

        Transfer::create([
            'user_id' => Auth::id(),
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'from_account_id' => $validated['from_account_id'],
            'to_account_id' => $validated['to_account_id'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('transfers.index')
            ->with('success', 'Transferencia creada correctamente.');
    }

    public function edit(Transfer $transfer)
    {
        $accounts = Account::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('transfers.edit', compact('transfer', 'accounts'));
    }

    public function update(Request $request, Transfer $transfer)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => [
                'required',
                'exists:accounts,id',
                'different:from_account_id',
            ],
            'description' => 'nullable|string|max:255',
        ]);

        $transfer->update([
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'from_account_id' => $validated['from_account_id'],
            'to_account_id' => $validated['to_account_id'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('transfers.index')
            ->with('success', 'Transferencia actualizada correctamente.');
    }

    public function destroy(Transfer $transfer)
    {
        $transfer->delete();

        return redirect()
            ->route('transfers.index')
            ->with('success', 'Transferencia eliminada correctamente.');
    }
}