@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Listado de Transacciones</h1>

    @if(session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('transactions.create') }}">Nueva Transacción</a>

    <table border="1" cellpadding="10" cellspacing="0" style="margin-top: 15px; width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Cuenta</th>
                <th>Categoría</th>
                <th>Método de pago</th>
                <th>Descripción</th>
                <th>Histórica</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->id }}</td>
                    <td>{{ $transaction->date }}</td>
                    <td>{{ $transaction->type === 'income' ? 'Ingreso' : 'Gasto' }}</td>
                    <td>${{ number_format($transaction->amount, 2) }}</td>
                    <td>{{ $transaction->account?->name }}</td>
                    <td>{{ $transaction->category?->name }}</td>
                    <td>{{ $transaction->paymentMethod?->name }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->is_historical ? 'Sí' : 'No' }}</td>
                    <td>
                        <a href="{{ route('transactions.edit', $transaction->id) }}">Editar</a>

                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Deseas eliminar esta transacción?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">No hay transacciones registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection