@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Crear Transacción</h1>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 10px;">
            <label for="date">Fecha:</label><br>
            <input type="date" name="date" id="date" value="{{ old('date') }}">
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
            <label for="amount">Monto:</label><br>
            <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="account_id">Cuenta:</label><br>
            <select name="account_id" id="account_id">
                <option value="">Seleccione una cuenta</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="category_id">Categoría:</label><br>
            <select name="category_id" id="category_id">
                <option value="">Seleccione una categoría</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="payment_method_id">Método de pago:</label><br>
            <select name="payment_method_id" id="payment_method_id">
                <option value="">Seleccione un método</option>
                @foreach($paymentMethods as $paymentMethod)
                    <option value="{{ $paymentMethod->id }}" {{ old('payment_method_id') == $paymentMethod->id ? 'selected' : '' }}>
                        {{ $paymentMethod->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="description">Descripción:</label><br>
            <textarea name="description" id="description" rows="3">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="is_historical">¿Es histórica?</label><br>
            <select name="is_historical" id="is_historical">
                <option value="0" {{ old('is_historical') == '0' ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_historical') == '1' ? 'selected' : '' }}>Sí</option>
            </select>
        </div>

        <button type="submit">Guardar</button>
        <a href="{{ route('transactions.index') }}">Cancelar</a>
    </form>
</div>
@endsection