@extends('layouts.app')

@section('content')
    
    {{-- Contenido del dashboard --}}
    <div class="dashboard-main container-fluid dashboard-wrapper">
        
        <div class="row g-0">
            <div class="col-md-9 col-lg-10 p-2">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h2 class="fw-bold mb-1">Bienvenido</h2>
                    </div>
                </div>

            
                <div class="col-md-6 col-xl-4">
                        {{-- Ingresos totales --}}
                        <div class="card stat-card shadow-sm mb-4">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Ingresos totales</p>

                                    <h4 class="fw-bold mb-0">${{ number_format($balanceTotal, 2) }} mxn</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Tarjetas principales -->
                <div class="row g-4 mb-4">
                    
                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Ingresos del mes</p>
                                    <h4 class="fw-bold text-success mb-0">${{ number_format($monthlyIncome, 2) }} mxn</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Gastos del mes</p>
                                    <h4 class="fw-bold text-danger mb-0">${{ number_format($monthlyExpense, 2) }} mxn</h4>
                                </div>
                               
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted mb-1">Balance total</p>

                                    <h4 class="fw-bold mb-0">${{ number_format($balanceTotal, 2) }} mxn</h4>
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
                                        ${{ number_format($netSavings, 2) }} mxn
                                    </h4>
                                </div>
                               
                            </div>
                        </div>
                    </div>


                </div>

             
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')
    <script>
        const incomeExpenseCtx = document.getElementById('incomeExpenseChart');

        new Chart(incomeExpenseCtx, {
            type: 'bar',
            data: {
                labels: ['Ingresos', 'Gastos'],
                datasets: [{
                    label: 'Monto del mes',
                    data: [
                        {{ $monthlyIncome }},
                        {{ $monthlyExpense }}
                    ],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgb(25, 135, 84)',
                        'rgb(220, 53, 69)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });


        const categoryCtx = document.getElementById('categoryChart');

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: @json($categorySummary->pluck('name')),
                datasets: [{
                    label: 'Gastos por categoría',
                    data: @json($categorySummary->pluck('total')),
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
@endpush
