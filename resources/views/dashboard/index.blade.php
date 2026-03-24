@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Sistema de finanzas personales</h1>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total de cuentas</h5>
                    <p class="fs-4 fw-bold">{{ $totalAccounts }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total de categorías</h5>
                    <p class="fs-4 fw-bold">{{ $totalCategories }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total de métodos de pago</h5>
                    <p class="fs-4 fw-bold">{{ $totalPaymentMethods }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total de transacciones</h5>
                    <p class="fs-4 fw-bold">{{ $totalTransactions }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total de transferencias</h5>
                    <p class="fs-4 fw-bold">{{ $totalTransfers }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5>Total de ingresos</h5>
                    <p class="fs-4 fw-bold text-success">${{ number_format($totalIncome, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h5>Total de gastos</h5>
                    <p class="fs-4 fw-bold text-danger">${{ number_format($totalExpense, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5>Balance general</h5>
                    <p class="fs-4 fw-bold">${{ number_format($balance, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection