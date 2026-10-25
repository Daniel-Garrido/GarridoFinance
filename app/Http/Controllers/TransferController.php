<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransferController extends Controller
{
    // Transferencias por página en el listado
    private const PER_PAGE = 20;

    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', $userId)
            ->inPeriod($request);

        // búsqueda por descripción, cuenta origen o cuenta destino
        $search = trim((string) $request->get('q', ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('description', 'like', $like)
                    ->orWhereHas('fromAccount', fn($a) => $a->where('name', 'like', $like))
                    ->orWhereHas('toAccount', fn($a) => $a->where('name', 'like', $like));
            });
        }

        $transfers = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Si la página pedida ya no existe (por ejemplo, tras eliminar), ir a la última
        if ($transfers->isEmpty() && $transfers->currentPage() > 1) {
            return redirect($transfers->url($transfers->lastPage()));
        }

        // Cuentas del usuario para los modales (incluye inactivas para poder editar)
        $accounts = Account::where('user_id', $userId)
            ->orderBy('name')
            ->get();

        return view('transfers.index', compact('transfers', 'accounts', 'search'));
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
        $validated = $request->validate($this->rules(), $this->messages());

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
        $validated = $request->validate($this->rules(), $this->messages());

        $transfer->update([
            'date' => $validated['date'],
            'amount' => $validated['amount'],
            'from_account_id' => $validated['from_account_id'],
            'to_account_id' => $validated['to_account_id'],
            'description' => $validated['description'] ?? null,
        ]);

        // back() conserva la página, el periodo y la búsqueda en los que estaba el usuario
        return redirect()
            ->back()
            ->with('success', 'Transferencia actualizada correctamente.');
    }

    public function destroy(Transfer $transfer)
    {
        $transfer->delete();

        return redirect()
            ->back()
            ->with('success', 'Transferencia eliminada correctamente.');
    }

    // Reglas compartidas por crear y editar: ambas cuentas deben ser del usuario y distintas
    private function rules(): array
    {
        $ownAccount = Rule::exists('accounts', 'id')->where('user_id', Auth::id());

        return [
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'from_account_id' => ['required', $ownAccount],
            'to_account_id' => ['required', $ownAccount, 'different:from_account_id'],
            'description' => 'nullable|string|max:255',
        ];
    }

    private function messages(): array
    {
        return [
            'from_account_id.exists' => 'La cuenta origen seleccionada no es válida.',
            'to_account_id.exists' => 'La cuenta destino seleccionada no es válida.',
            'to_account_id.different' => 'La cuenta destino debe ser distinta de la cuenta origen.',
        ];
    }
}
