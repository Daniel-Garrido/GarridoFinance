@extends('layouts.app')

@section('content')
   
    <div class="container-fluid dashboard-wrapper">
        <div class="row g-0">

            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="brand">💰 Finanzas</div>
                {{-- opciones de navegacion --}}
                <nav class="nav flex-column">
                    <a href="{{ route('dashboard.index') }}" class="nav-link active"> Dashboard</a>
                    <a href="{{ route('accounts.index') }}" class="nav-link"> Cuentas</a>
                    <a href="{{ route('transactions.index') }}" class="nav-link"> Transacciones</a>
                    <a href="{{ route('categories.index') }}" class="nav-link"> Categorías</a>
                    <a href="{{ route('payment-methods.index') }}" class="nav-link"> Métodos de pago</a>
                    <a href="{{ route('transfers.index') }}" class="nav-link"> Transferencias</a>
                </nav>

                <hr class="border-secondary my-4">

                {{-- opcion para cerrar sesión --}}
                <a href="#" class="nav-link text-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    🚪 Cerrar sesión
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="fw-bold mb-1">Bienvenido</h2>
                    </div>
                </div>

                <!-- Tarjetas principales -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Balance total</p>
                                  
                                    <h4 class="fw-bold mb-0">${{ number_format($balanceTotal, 2) }}</h4>
                                </div>
                                <div class="icon-box bg-primary-subtle text-primary">
                                    💼
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Ingresos del mes</p>
                                    <h4 class="fw-bold text-success mb-0">${{ number_format($monthlyIncome, 2) }}</h4> 
                                </div>
                                <div class="icon-box bg-success-subtle text-success">
                                    📈
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Gastos del mes</p>
                                    <h4 class="fw-bold text-danger mb-0">${{ number_format($monthlyExpense, 2) }}</h4>
                                </div>
                                <div class="icon-box bg-danger-subtle text-danger">
                                    📉
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Ahorro neto</p>
                                    <h4 class="fw-bold {{ $netSavings >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                        ${{ number_format($netSavings, 2) }}
                                    </h4>
                                </div>
                                <div class="icon-box bg-info-subtle text-info">
                                    🪙
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Cuentas activas</p>
                                    <h4 class="fw-bold mb-0">{{ $activeAccounts }}</h4>
                                </div>
                                <div class="icon-box bg-warning-subtle text-warning">
                                    🏦
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Últimas transacciones -->
                    <div class="col-lg-8">
                        <div class="card section-card shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0">Últimas transacciones</h5>
                                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">
                                        Ver todas
                                    </a>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Descripción</th>
                                                <th>Categoría</th>
                                                <th>Cuenta</th>
                                                <th>Tipo</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($latestTransactions as $transaction)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}</td>
                                                    <td>{{ $transaction->description }}</td>
                                                    <td>{{ $transaction->category->name ?? 'Sin categoría' }}</td>
                                                    <td>{{ $transaction->account->name ?? 'Sin cuenta' }}</td>
                                                    <td>
                                                        @if($transaction->type === 'income')
                                                            <span class="badge bg-success">Ingreso</span>
                                                        @else
                                                            <span class="badge bg-danger">Gasto</span>
                                                        @endif
                                                    </td>
                                                    <td class="fw-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                                        {{ $transaction->type === 'income' ? '+' : '-' }}
                                                        ${{ number_format($transaction->amount, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        No hay transacciones registradas todavía.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen por categoría -->
                    <div class="col-lg-4">
                        <div class="card section-card shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Resumen por categoría</h5>

                                @forelse($categorySummary as $category)
                                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                        <div>
                                            <span class="category-badge bg-light text-dark">
                                                {{ $category->name }}
                                            </span>
                                        </div>
                                        <div class="fw-semibold text-danger">
                                            ${{ number_format($category->total, 2) }}
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No hay datos por categoría disponibles.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Bloque extra opcional -->
                        <div class="card section-card shadow-sm mt-4">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">Acciones rápidas</h5>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Nueva transacción</a>
                                    <a href="{{ route('accounts.create') }}" class="btn btn-outline-secondary">+ Nueva cuenta</a>
                                    <a href="{{ route('categories.create') }}" class="btn btn-outline-secondary">+ Nueva categoría</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métricas secundarias opcionales -->
                <div class="row g-4 mt-2">
                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <p class="text-muted mb-1">Total de categorías</p>
                                <h5 class="fw-bold mb-0">{{ $totalCategories }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <p class="text-muted mb-1">Métodos de pago</p>
                                <h5 class="fw-bold mb-0">{{ $totalPaymentMethods }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body">
                                <p class="text-muted mb-1">Transferencias</p>
                                <h5 class="fw-bold mb-0">{{ $totalTransfers }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection