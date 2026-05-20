<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>GarridoFinance</title>

    {{-- CSS GENERAL --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Librería de Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    {{-- NAVBAR SUPERIOR --}}
    <nav class="content-nav navbar p-3">
        <div class="content-nav-container d-flex align-items-center">
            <img class="content-nav-img" src="{{ asset('Images/LOGO-GARRIDO-FINANCE.png') }}"
                alt="Logo-garrido-finance">
            <h2 class="content-nav-title navbar-brand mb-0 text-white">GarridoFinance</h2>
        </div>
    </nav>

    {{-- ESTRUCTURA PRINCIPAL --}}
    <div class="d-flex">

        {{-- MENÚ LATERAL --}}
        <aside class="vh-100 bg-white shadow ">
            <ul class="nav flex-column">

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('dashboard.index') }}" class="nav-link text-dark">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('accounts.index') }}" class="nav-link text-dark">
                        <i class="bi bi-wallet"></i> Cuentas
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('categories.index') }}" class="nav-link text-dark">
                        <i class="bi bi-tag"></i> Categorías
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('payment-methods.index') }}" class="nav-link text-dark">
                        <i class="bi bi-credit-card"></i> Métodos de pago
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('transactions.index') }}" class="nav-link text-dark">
                        <i class="bi bi-currency-dollar"></i> Transacciones
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('transfers.index') }}" class="nav-link text-dark">
                        <i class="bi bi-arrow-left-right"></i> Transferencias
                    </a>
                </li>

            </ul>
        </aside>

        {{-- CONTENIDO DE CADA VISTA --}}
        <main class="content-main">
            @yield('content')
        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
    
</body>

</html>
