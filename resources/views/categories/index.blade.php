@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Listado de Categorías</h1>

    @if(session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('categories.create') }}">Nueva Categoría</a>

    <table border="1" cellpadding="10" cellspacing="0" style="margin-top: 15px; width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }}</td>
                    <td>{{ $category->is_active ? 'Activa' : 'Inactiva' }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category->id) }}">Editar</a>

                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('¿Deseas eliminar esta categoría?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay categorías registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection