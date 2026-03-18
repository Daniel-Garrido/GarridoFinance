<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de cuentas</title>
</head>
<body>

<h1>Lista de cuentas</h1>

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<a href="{{ route('accounts.create') }}">Crear nueva cuenta</a>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Activa</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
        @forelse($accounts as $account)
            <tr>
                <td>{{ $account->id }}</td>
                <td>{{ $account->name }}</td>
                <td>{{ $account->type }}</td>
                <td>{{ $account->is_active ? 'Sí' : 'No' }}</td>
                <td>
                    <a href="{{ route('accounts.edit', $account) }}">Editar</a>

                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">No hay cuentas registradas</td>
            </tr>
        @endforelse
    </tbody>

</table>

</body>
</html>