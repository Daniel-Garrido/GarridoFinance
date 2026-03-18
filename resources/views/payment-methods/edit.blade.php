@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Método de Pago</h1>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payment-methods.update', $paymentMethod->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 10px;">
            <label for="name">Nombre:</label><br>
            <input type="text" name="name" id="name" value="{{ old('name', $paymentMethod->name) }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="is_active">Estado:</label><br>
            <select name="is_active" id="is_active">
                <option value="1" {{ old('is_active', $paymentMethod->is_active) == 1 ? 'selected' : '' }}>Activo</option>
                <option value="0" {{ old('is_active', $paymentMethod->is_active) == 0 ? 'selected' : '' }}>Inactivo</option>
            </select>
        </div>

        <button type="submit">Actualizar</button>
        <a href="{{ route('payment-methods.index') }}">Cancelar</a>
    </form>
</div>
@endsection