@extends('layouts.app')

@section('content')
<h2>Registro</h2>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <label>Nombre:</label>
    <input type="text" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required>

    <label>Password:</label>
    <input type="password" name="password" required>

    <label>Confirmar Password:</label>
    <input type="password" name="password_confirmation" required>

    <button type="submit">Registrarse</button>
</form>
@endsection