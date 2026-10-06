<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GarridoFinance</title>

    {{-- Tipografías del mockup --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    {{-- CSS GENERAL (después de Bootstrap para que la paleta tenga prioridad) --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">

    {{-- Librería de Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    {{-- Aplica el estado guardado del menú antes de pintar, para evitar un parpadeo --}}
    <script>
        try {
            if (localStorage.getItem('gf-sidebar') === 'collapsed') {
                document.body.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>

    {{-- NAVBAR SUPERIOR --}}
    <nav class="content-nav navbar">
        <div class="content-nav-container d-flex align-items-center">
            <button type="button" id="sidebarToggle" class="content-nav-toggle" aria-controls="appSidebar"
                aria-expanded="true" title="Cerrar menú" aria-label="Cerrar menú">
                <i class="bi bi-list"></i>
            </button>
            <img class="content-nav-img" src="{{ asset('Images/LOGO-GARRIDO-FINANCE.png') }}"
                alt="Logo-garrido-finance">
            <h2 class="content-nav-title navbar-brand mb-0">GarridoFinance</h2>
        </div>

        {{-- Acceso a perfil (la ruta profile.index aún no existe; mientras tanto apunta a #) --}}
        <a href="{{ Route::has('profile.index') ? route('profile.index') : '#' }}" class="content-nav-profile"
            title="Ver perfil" aria-label="Ver perfil">
            <i class="bi bi-person-circle"></i>
        </a>
    </nav>

    {{-- ESTRUCTURA PRINCIPAL --}}
    <div class="d-flex">

        {{-- MENÚ LATERAL FIJO --}}
        <aside id="appSidebar" class="sidebar bg-white d-flex flex-column">
            <ul class="nav flex-column flex-grow-1">

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('dashboard.index') }}"
                        class="nav-link text-dark {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('accounts.index') }}"
                        class="nav-link text-dark {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet"></i> Cuentas
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('categories.index') }}"
                        class="nav-link text-dark {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tag"></i> Categorías
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('payment-methods.index') }}"
                        class="nav-link text-dark {{ request()->routeIs('payment-methods.*') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i> Métodos de pago
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('transactions.index') }}"
                        class="nav-link text-dark {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        <i class="bi bi-currency-dollar"></i> Transacciones
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href="{{ route('transfers.index') }}"
                        class="nav-link text-dark {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-left-right"></i> Transferencias
                    </a>
                </li>

            </ul>

            {{-- SEPARADOR + OPCIONES INFERIORES --}}
            <ul class="nav flex-column border-top pt-2 mb-2">

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href=""
                        class="nav-link text-dark {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                </li>

                <li class="nav-item nav-item-main-li mb-2 p-2">
                    <a href=""
                        class="nav-link text-dark {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Ajustes
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

    {{-- Abrir / cerrar el menú lateral --}}
    <script>
        (function () {
            const toggle = document.getElementById('sidebarToggle');

            function sync() {
                const collapsed = document.body.classList.contains('sidebar-collapsed');
                const label = collapsed ? 'Abrir menú' : 'Cerrar menú';
                toggle.setAttribute('aria-expanded', String(!collapsed));
                toggle.setAttribute('title', label);
                toggle.setAttribute('aria-label', label);
            }

            toggle.addEventListener('click', function () {
                const collapsed = document.body.classList.toggle('sidebar-collapsed');
                try {
                    localStorage.setItem('gf-sidebar', collapsed ? 'collapsed' : 'open');
                } catch (e) {}
                sync();
            });

            // Al terminar la animación, las gráficas se ajustan al nuevo ancho
            document.querySelector('.content-main').addEventListener('transitionend', function (e) {
                if (e.propertyName === 'margin-left' && window.Chart) {
                    Object.values(Chart.instances).forEach(chart => chart.resize());
                }
            });

            sync();
        })();
    </script>

    @stack('scripts')

</body>

</html>