<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
    // Métodos de pago por página en el listado
    private const PER_PAGE = 10;

    public function index()
    {
        // Métodos de pago del usuario autenticado, paginados
        $paymentMethods = PaymentMethod::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(self::PER_PAGE);

        // Si la página pedida ya no existe (por ejemplo, tras eliminar), ir a la última
        if ($paymentMethods->isEmpty() && $paymentMethods->currentPage() > 1) {
            return redirect($paymentMethods->url($paymentMethods->lastPage()));
        }

        return view('payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        PaymentMethod::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Método de pago creado correctamente');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'required|boolean',
        ]);

        $paymentMethod->update($validated);

        // back() conserva la página en la que estaba el usuario
        return redirect()->back()
            ->with('success', 'Método de pago actualizado correctamente');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->back()
            ->with('success', 'Método de pago eliminado correctamente');
    }
}
