<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    // Cuentas por página en el listado
    private const PER_PAGE = 10;

    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        // Cuentas del usuario autenticado con los cálculos de saldo, filtradas y paginadas
        $accounts = Account::where('user_id', Auth::id())
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->latest()
            ->withSum(['transactions as income_sum' => function ($q) {
                $q->where('type', 'income');
            }], 'amount')
            ->withSum(['transactions as expense_sum' => function ($q) {
                $q->where('type', 'expense');
            }], 'amount')
            ->withSum('incomingTransfers as transfers_in_sum', 'amount')
            ->withSum('outgoingTransfers as transfers_out_sum', 'amount')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Si la página pedida ya no existe (por ejemplo, tras eliminar), ir a la última
        if ($accounts->isEmpty() && $accounts->currentPage() > 1) {
            return redirect($accounts->url($accounts->lastPage()));
        }

        return view('accounts.index', compact('accounts', 'search'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

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
        $validated = $request->validate($this->rules());

        $account->update($validated);

        // back() conserva la página y la búsqueda en las que estaba el usuario
        return redirect()->back()
            ->with('success', 'Cuenta actualizada correctamente');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->back()
            ->with('success', 'Cuenta eliminada correctamente');
    }

    // Reglas compartidas por crear y editar
    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(array_keys(Account::TYPES))],
            'is_active' => 'required|boolean',
        ];
    }
}
