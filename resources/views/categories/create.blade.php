@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Categoría</h1>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 10px;">
            <label for="name">Nombre:</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="type">Tipo:</label><br>
            <select name="type" id="type">
                <option value="">Seleccione</option>
                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Ingreso</option>
                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Gasto</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="is_active">Estado:</label><br>
            <select name="is_active" id="is_active">
                <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Activa</option>
                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactiva</option>
            </select>
        </div>

        <button type="submit">Guardar</button>
        <a href="{{ route('categories.index') }}">Cancelar</a>
    </form>
</div>
@endsection