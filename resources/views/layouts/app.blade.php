<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GarridoFinance</title>

    {{-- Tema: claro por defecto. Si el usuario eligió el oscuro, se aplica antes de pintar (sin parpadeo).
         data-bs-theme hace que Bootstrap (modales, selects, botón cerrar) también use su versión oscura. --}}
    <script>
        try {
            if (localStorage.getItem('gf-theme') === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            }
        } catch (e) {}
    </script>

    {{-- Tipografías del mockup --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    {{-- Phosphor Icons: íconos del menú lateral (duotone) y del botón del menú (regular) --}}
    <link href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/duotone/style.css" rel="stylesheet">

    {{-- CSS GENERAL (después de Bootstrap para que la paleta tenga prioridad) --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}?v={{ filemtime(public_path('css/dashboard.css')) }}">
    {{-- Piezas compartidas por las vistas de listado: tablas, pastillas, paginación, modales --}}
    <link rel="stylesheet" href="{{ asset('css/components.css') }}?v={{ filemtime(public_path('css/components.css')) }}">

    {{-- CSS propio de cada vista --}}
    @stack('styles')

    {{-- Modo oscuro: al final para que sus colores tengan prioridad (solo actúa con data-theme="dark") --}}
    <link rel="stylesheet" href="{{ asset('css/dark.css') }}?v={{ filemtime(public_path('css/dark.css')) }}">

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
                <i class="ph ph-list"></i>
            </button>
            {{-- Logo y nombre: llevan al dashboard --}}
            <a href="{{ route('dashboard.index') }}" class="content-nav-brand" aria-label="Ir al dashboard">
                <img class="content-nav-img" src="{{ asset('Images/LOGO-GARRIDO-FINANCE.png') }}"
                    alt="Logo-garrido-finance">
                <h2 class="content-nav-title navbar-brand mb-0">Garrido<span>Finance</span></h2>
            </a>
        </div>

        <div class="content-nav-actions">
        {{-- Botón de tema: luna en modo claro (activar oscuro), sol en modo oscuro (volver a claro) --}}
        <button type="button" id="themeToggle" class="content-nav-theme" aria-pressed="false"
            title="Activar modo oscuro" aria-label="Activar modo oscuro">
            <i class="ph ph-moon"></i>
        </button>

        {{-- Menú del usuario: se abre al pasar el cursor por la foto (o al tocarla en el cel) --}}
        @php $authUser = auth()->user(); @endphp
        <div class="gf-user-menu" id="userMenu">
            <button type="button" class="gf-user-trigger" id="userMenuTrigger" aria-haspopup="true"
                aria-expanded="false" aria-controls="userMenuPanel" aria-label="Menú de usuario">
                <span class="gf-user-trigger-avatar"><x-avatar :user="$authUser" size="sm" /></span>
                <span class="gf-user-trigger-name">{{ $authUser->name }}</span>
                <i class="ph ph-caret-down gf-user-trigger-caret"></i>
            </button>

            {{-- El contenedor deja un espacio invisible arriba para que el menú no se cierre al bajar el cursor --}}
            <div class="gf-user-panel" id="userMenuPanel">
                <div class="gf-user-card" role="menu" aria-labelledby="userMenuTrigger">
                    <div class="gf-user-card-head">
                        <x-avatar :user="$authUser" size="md" />
                        <div>
                            <strong>{{ $authUser->full_name }}</strong>
                            <small>{{ $authUser->email }}</small>
                        </div>
                    </div>

                    <a href="{{ route('profile.index') }}" class="gf-user-item" role="menuitem">
                        <i class="ph-duotone ph-user-circle"></i> Mi perfil
                    </a>
                    <a href="" class="gf-user-item" role="menuitem">
                        <i class="ph-duotone ph-gear-six"></i> Ajustes
                    </a>

                    <div class="gf-user-divider"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="gf-user-item gf-user-item-danger" role="menuitem">
                            <i class="ph-duotone ph-sign-out"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
        </div>{{-- /.content-nav-actions --}}
    </nav>

    {{-- ESTRUCTURA PRINCIPAL --}}
    <div class="d-flex">

        {{-- MENÚ LATERAL FIJO
             Íconos: Phosphor Icons (duotone). Cada sección agrupa vistas relacionadas. --}}
        @php
            $menuSections = [
                'General' => [
                    ['route' => 'dashboard.index', 'match' => 'dashboard.*', 'icon' => 'ph-chart-pie-slice', 'label' => 'Dashboard'],
                ],
                'Movimientos' => [
                    ['route' => 'transactions.index', 'match' => 'transactions.*', 'icon' => 'ph-receipt', 'label' => 'Transacciones'],
                    ['route' => 'transfers.index', 'match' => 'transfers.*', 'icon' => 'ph-arrows-left-right', 'label' => 'Transferencias'],
                ],
                'Administración' => [
                    ['route' => 'accounts.index', 'match' => 'accounts.*', 'icon' => 'ph-wallet', 'label' => 'Cuentas'],
                    ['route' => 'categories.index', 'match' => 'categories.*', 'icon' => 'ph-tag', 'label' => 'Categorías'],
                    ['route' => 'payment-methods.index', 'match' => 'payment-methods.*', 'icon' => 'ph-credit-card', 'label' => 'Métodos de pago'],
                ],
            ];
        @endphp

        <aside id="appSidebar" class="sidebar gf-side" aria-label="Menú principal">
            <nav class="gf-side-nav">
                @foreach ($menuSections as $sectionLabel => $items)
                    <div class="gf-side-section">
                        <p class="gf-side-label">{{ $sectionLabel }}</p>
                        <ul class="gf-side-list">
                            @foreach ($items as $item)
                                @php $isActive = request()->routeIs($item['match']); @endphp
                                <li>
                                    <a href="{{ route($item['route']) }}" class="gf-side-link {{ $isActive ? 'active' : '' }}"
                                        @if ($isActive) aria-current="page" @endif>
                                        <span class="gf-side-icon"><i class="ph-duotone {{ $item['icon'] }}"></i></span>
                                        <span class="gf-side-text">{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            {{-- OPCIONES INFERIORES --}}
            <div class="gf-side-footer">
                <ul class="gf-side-list">
                    <li>
                        <a href="{{ route('profile.index') }}"
                            class="gf-side-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                            @if (request()->routeIs('profile.*')) aria-current="page" @endif>
                            <span class="gf-side-icon"><i class="ph-duotone ph-user-circle"></i></span>
                            <span class="gf-side-text">Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a href="" class="gf-side-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <span class="gf-side-icon"><i class="ph-duotone ph-gear-six"></i></span>
                            <span class="gf-side-text">Ajustes</span>
                        </a>
                    </li>
                </ul>

                {{-- Tarjeta del usuario: lleva al perfil --}}
                <a href="{{ route('profile.index') }}" class="gf-side-user" title="Ver perfil">
                    <span class="gf-side-user-avatar"><x-avatar :user="auth()->user()" size="sm" /></span>
                    <span class="gf-side-user-info">
                        <strong>{{ auth()->user()->full_name }}</strong>
                        <small>{{ auth()->user()->email }}</small>
                    </span>
                </a>
            </div>
        </aside>

        {{-- Fondo para cerrar el menú en móvil --}}
        <div class="sidebar-backdrop" id="sidebarBackdrop" aria-hidden="true"></div>

        {{-- CONTENIDO DE CADA VISTA --}}
        <main class="content-main">
            @yield('content')

            <footer class="gf-footer">
                <span>© {{ now()->year }} GarridoFinance. Todos los derechos reservados.</span>
                <span>Versión {{ config('app.version') }}</span>
            </footer>
        </main>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Abrir / cerrar el menú lateral --}}
    <script>
        (function () {
            const toggle = document.getElementById('sidebarToggle');
            const backdrop = document.getElementById('sidebarBackdrop');
            const body = document.body;
            // Mismo corte que el @media de dashboard.css
            const mobile = window.matchMedia('(max-width: 768px)');

            function isClosed() {
                return mobile.matches
                    ? !body.classList.contains('sidebar-open')
                    : body.classList.contains('sidebar-collapsed');
            }

            function sync() {
                const closed = isClosed();
                const label = closed ? 'Abrir menú' : 'Cerrar menú';
                toggle.setAttribute('aria-expanded', String(!closed));
                toggle.setAttribute('title', label);
                toggle.setAttribute('aria-label', label);

                // En móvil, con el menú abierto el botón muestra una "X"
                const icon = toggle.querySelector('i');
                const showClose = mobile.matches && !closed;
                icon.classList.toggle('ph-list', !showClose);
                icon.classList.toggle('ph-x', showClose);
            }

            function closeMobile() {
                body.classList.remove('sidebar-open');
                sync();
            }

            toggle.addEventListener('click', function () {
                if (mobile.matches) {
                    // En móvil se abre encima del contenido y no se guarda el estado
                    body.classList.toggle('sidebar-open');
                } else {
                    const collapsed = body.classList.toggle('sidebar-collapsed');
                    try {
                        localStorage.setItem('gf-sidebar', collapsed ? 'collapsed' : 'open');
                    } catch (e) {}
                }
                sync();
            });

            // En móvil se cierra al tocar el fondo, al elegir una opción o con Escape
            backdrop.addEventListener('click', closeMobile);
            document.querySelectorAll('#appSidebar a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (mobile.matches) closeMobile();
                });
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && mobile.matches) closeMobile();
            });

            // Al cambiar entre móvil y escritorio (girar el cel, redimensionar) se reinicia
            mobile.addEventListener('change', closeMobile);

            // Al terminar la animación, las gráficas se ajustan al nuevo ancho
            document.querySelector('.content-main').addEventListener('transitionend', function (e) {
                if (e.propertyName === 'margin-left' && window.Chart) {
                    Object.values(Chart.instances).forEach(chart => chart.resize());
                }
            });

            sync();
        })();
    </script>

    {{-- Modo oscuro: alterna data-theme en <html>, guarda la elección y avisa a las gráficas (evento gf-themechange) --}}
    <script>
        (function () {
            const root = document.documentElement;
            const button = document.getElementById('themeToggle');
            const icon = button.querySelector('i');

            function isDark() {
                return root.getAttribute('data-theme') === 'dark';
            }

            function sync() {
                const dark = isDark();
                const label = dark ? 'Activar modo claro' : 'Activar modo oscuro';
                icon.classList.toggle('ph-moon', !dark);
                icon.classList.toggle('ph-sun', dark);
                button.setAttribute('aria-pressed', String(dark));
                button.setAttribute('title', label);
                button.setAttribute('aria-label', label);
            }

            button.addEventListener('click', function () {
                const dark = !isDark();
                if (dark) {
                    root.setAttribute('data-theme', 'dark');
                    root.setAttribute('data-bs-theme', 'dark');
                } else {
                    root.removeAttribute('data-theme');
                    root.removeAttribute('data-bs-theme');
                }
                try {
                    localStorage.setItem('gf-theme', dark ? 'dark' : 'light');
                } catch (e) {}
                sync();
                window.dispatchEvent(new CustomEvent('gf-themechange', { detail: { dark: dark } }));
            });

            sync();
        })();
    </script>

    {{-- Menú del usuario: en escritorio se abre con el cursor (CSS :hover);
         al hacer clic o tocar la foto se abre/cierra, y se cierra al tocar fuera o con Escape --}}
    <script>
        (function () {
            const menu = document.getElementById('userMenu');
            const trigger = document.getElementById('userMenuTrigger');

            function setOpen(open) {
                menu.classList.toggle('is-open', open);
                trigger.setAttribute('aria-expanded', String(open));
            }

            trigger.addEventListener('click', function () {
                setOpen(!menu.classList.contains('is-open'));
            });

            document.addEventListener('click', function (e) {
                if (!menu.contains(e.target)) setOpen(false);
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && menu.classList.contains('is-open')) {
                    setOpen(false);
                    trigger.focus();
                }
            });

            // Con el cursor encima, aria-expanded refleja que el menú está visible
            menu.addEventListener('mouseenter', () => trigger.setAttribute('aria-expanded', 'true'));
            menu.addEventListener('mouseleave', () => {
                if (!menu.classList.contains('is-open')) trigger.setAttribute('aria-expanded', 'false');
            });
        })();
    </script>

    @stack('scripts')

</body>

</html>