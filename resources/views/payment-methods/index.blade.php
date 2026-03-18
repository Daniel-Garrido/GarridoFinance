@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Listado de Métodos de Pago</h1>

    @if(session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('payment-methods.create') }}">Nuevo Método de Pago</a>

    <table border="1" cellpadding="10" cellspacing="0" style="margin-top: 15px; width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentMethods as $paymentMethod)
                <tr>
                    <td>{{ $paymentMethod->id }}</td>
                    <td>{{ $paymentMethod->name }}</td>
                    <td>{{ $paymentMethod->is_active ? 'Activo' : 'Inactivo' }}</td>
                    <td>
                        <a href="{{ route('payment-methods.edit', $paymentMethod->id) }}">Editar</a>

                        <form action="{{ route('payment-methods.destroy', $paymentMethod->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Deseas eliminar este método de pago?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay métodos de pago registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection