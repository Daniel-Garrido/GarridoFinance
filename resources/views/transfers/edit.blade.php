@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Editar Transferencia</h1>

    @if ($errors->any())
        <div style="color: red; margin-bottom: 15px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transfers.update', $transfer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 10px;">
            <label for="date">Fecha:</label><br>
            <input type="date" name="date" id="date" value="{{ old('date', $transfer->date) }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="amount">Monto:</label><br>
            <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount', $transfer->amount) }}">
        </div>

        <div style="margin-bottom: 10px;">
            <label for="from_account_id">Cuenta origen:</label><br>
            <select name="from_account_id" id="from_account_id">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('from_account_id', $transfer->from_account_id) == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="to_account_id">Cuenta destino:</label><br>
            <select name="to_account_id" id="to_account_id">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ old('to_account_id', $transfer->to_account_id) == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 10px;">
            <label for="description">Descripción:</label><br>
            <textarea name="description" id="description" rows="3">{{ old('description', $transfer->description) }}</textarea>
        </div>

        <button type="submit">Actualizar</button>
        <a href="{{ route('transfers.index') }}">Cancelar</a>
    </form>
</div>
@endsection