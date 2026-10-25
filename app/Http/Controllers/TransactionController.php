<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class TransactionController extends Controller
{
    // Transacciones por página en el listado
    private const PER_PAGE = 20;

    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Transaction::with(['account', 'category', 'paymentMethod'])
            ->where('user_id', $userId);

        // filtro por tipo
        if (in_array($request->type, ['income', 'expense'], true)) {
            $query->where('type', $request->type);
        }

        // filtro por categoría
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // filtro por periodo (scope compartido con transferencias)
        $query->inPeriod($request);

        // búsqueda por descripción o nombre de la cuenta
        $search = trim((string) $request->get('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                    ->orWhereHas('account', function ($a) use ($search) {
                        $a->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Si la página pedida ya no existe (por ejemplo, tras eliminar), ir a la última
        if ($transactions->isEmpty() && $transactions->currentPage() > 1) {
            return redirect($transactions->url($transactions->lastPage()));
        }

        // Catálogos del usuario para los filtros y los modales (incluye inactivos para poder editar)
        $accounts = Account::where('user_id', $userId)->orderBy('name')->get();
        $categories = Category::where('user_id', $userId)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('user_id', $userId)->orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'accounts', 'categories', 'paymentMethods', 'search'));
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
        $validated = $request->validate($this->rules($request), $this->messages());

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
        $validated = $request->validate($this->rules($request), $this->messages());

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

        // back() conserva la página, los filtros y la búsqueda en los que estaba el usuario
        return redirect()
            ->back()
            ->with('success', 'Transacción actualizada correctamente.');
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return redirect()
            ->back()
            ->with('success', 'Transacción eliminada correctamente.');
    }

    // Reglas compartidas por crear y editar: los catálogos deben ser del usuario
    // y la categoría debe ser del mismo tipo que la transacción
    private function rules(Request $request): array
    {
        $userId = Auth::id();

        return [
            'date' => 'required|date',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'account_id' => ['required', Rule::exists('accounts', 'id')->where('user_id', $userId)],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where('user_id', $userId)->where('type', $request->type),
            ],
            'payment_method_id' => ['required', Rule::exists('payment_methods', 'id')->where('user_id', $userId)],
            'description' => 'nullable|string|max:255',
            'is_historical' => 'required|boolean',
        ];
    }

    private function messages(): array
    {
        return [
            'category_id.exists' => 'La categoría no corresponde al tipo de transacción.',
        ];
    }
}
