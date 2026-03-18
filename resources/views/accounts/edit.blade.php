<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar cuenta</title>
</head>
<body>

<h1>Editar cuenta</h1>

@if ($errors->any())
    <ul style="color:red;">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('accounts.update', $account) }}" method="POST">
    @csrf
    @method('PUT')

    <div>
        <label for="name">Nombre:</label>
        <input type="text" name="name" id="name" value="{{ old('name', $account->name) }}">
    </div>

    <br>

    <div>
        <label for="type">Tipo:</label>
        <select name="type" id="type">
            <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Cash</option>
            <option value="bank" {{ old('type', $account->type) == 'bank' ? 'selected' : '' }}>Bank</option>
            <option value="card" {{ old('type', $account->type) == 'card' ? 'selected' : '' }}>Card</option>
            <option value="saving" {{ old('type', $account->type) == 'saving' ? 'selected' : '' }}>Saving</option>
        </select>
    </div>

    <br>

    <div>
        <label for="is_active">¿Está activa?</label>
        <select name="is_active" id="is_active">
            <option value="1" {{ old('is_active', $account->is_active) == 1 ? 'selected' : '' }}>Sí</option>
            <option value="0" {{ old('is_active', $account->is_active) == 0 ? 'selected' : '' }}>No</option>
        </select>
    </div>

    <br>

    <button type="submit">Actualizar cuenta</button>

</form>

<br>

<a href="{{ route('accounts.index') }}">Volver</a>

</body>
</html>