<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear cuenta</title>
</head>
<body>

    <h1>Crear cuenta</h1>

    @if ($errors->any())
        <ul style="color:red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('accounts.store') }}" method="POST">
        @csrf

        <div>
            <label for="name">Nombre:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
        </div>

        <br>

        <div>
            <label for="type">Tipo:</label>
            <select name="type" id="type">
                <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank</option>
                <option value="card" {{ old('type') == 'card' ? 'selected' : '' }}>Card</option>
                <option value="saving" {{ old('type') == 'saving' ? 'selected' : '' }}>Saving</option>
            </select>
        </div>

        <br>

        <div>
            <label for="is_active">¿Está activa?</label>
            <select name="is_active" id="is_active">
                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Sí</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <br>

        <button type="submit">Guardar cuenta</button>
    </form>

    <br>

    <a href="{{ route('accounts.index') }}">Volver a la lista</a>

</body>
</html>