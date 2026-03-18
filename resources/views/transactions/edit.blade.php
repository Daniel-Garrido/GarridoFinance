@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Transacción</h1>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 10px;">
            <label for="date">Fecha:</label><br>
            <input type="date" name="date" id="date" value="{{ old('date', $transaction->date) }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="type">Tipo:</label><br>
            <select name="type" id="type">
                <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Ingreso</option>
                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Gasto</option>
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="amount">Monto:</label><br>
            <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $transaction->amount) }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="account_id">Cuenta:</label><br>
            <select name="account_id" id="account_id">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="category_id">Categoría:</label><br>
            <select name="category_id" id="category_id">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} ({{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="payment_method_id">Método de pago:</label><br>
            <select name="payment_method_id" id="payment_method_id">
                @foreach($paymentMethods as $paymentMethod)
                    <option value="{{ $paymentMethod->id }}" {{ old('payment_method_id', $transaction->payment_method_id) == $paymentMethod->id ? 'selected' : '' }}>
                        {{ $paymentMethod->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="description">Descripción:</label><br>
            <textarea name="description" id="description" rows="3">{{ old('description', $transaction->description) }}</textarea>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="is_historical">¿Es histórica?</label><br>
            <select name="is_historical" id="is_historical">
                <option value="0" {{ old('is_historical', $transaction->is_historical) == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_historical', $transaction->is_historical) == 1 ? 'selected' : '' }}>Sí</option>
            </select>
        </div>

        <button type="submit">Actualizar</button>
        <a href="{{ route('transactions.index') }}">Cancelar</a>
    </form>
</div>
@endsection