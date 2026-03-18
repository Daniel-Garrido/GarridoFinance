@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Listado de Transferencias</h1>

    @if(session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('transfers.create') }}">Nueva Transferencia</a>

    <table border="1" cellpadding="10" cellspacing="0" style="margin-top: 15px; width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Monto</th>
                <th>Cuenta origen</th>
                <th>Cuenta destino</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transfers as $transfer)
                <tr>
                    <td>{{ $transfer->id }}</td>
                    <td>{{ $transfer->date }}</td>
                    <td>${{ number_format($transfer->amount, 2) }}</td>
                    <td>{{ $transfer->fromAccount?->name }}</td>
                    <td>{{ $transfer->toAccount?->name }}</td>
                    <td>{{ $transfer->description }}</td>
                    <td>
                        <a href="{{ route('transfers.edit', $transfer->id) }}">Editar</a>

                        <form action="{{ route('transfers.destroy', $transfer->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Deseas eliminar esta transferencia?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No hay transferencias registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection