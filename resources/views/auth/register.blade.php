<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro GarridoFinance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}" rel="stylesheet">
</head>

<body class="auth-body">

    <div class="auth-wrapper min-vh-100 container-fluid d-flex align-items-center justify-content-center">
        <div class="row g-0 justify-content-center w-100">

            {{-- CONTENEDOR PRINCIPAL --}}
            <div
                class="auth-card col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 bg-white d-flex align-items-center justify-content-center rounded-3">
                <div class="w-100">
                    {{-- LOGO --}}
                    <div class="text-center mb-3">
                        <img class="login-img" src="{{ asset('Images/Logo.png') }}" alt="Logo de GarridoFinance">
                    </div>

                    {{-- TÍTULOS --}}
                    <div class="text-center">
                        <h2 class="auth-title fw-medium mb-1">Crea tu cuenta</h2>
                        <p class="text-secondary mb-4">
                            Completa los datos para comenzar y probar la mejor app web de finanzas personales
                        </p>
                    </div>


                    {{-- ALERTAS DE ERROR --}}
                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small rounded-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- FORMULARIO --}}
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        {{-- Nombre --}}
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-medium text-secondary">
                                Nombre completo
                            </label>
                            <input type="text" id="name" name="name"
                                class="form-control auth-input @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                placeholder="Juan García" autocomplete="name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-medium text-secondary">
                                Correo electrónico
                            </label>
                            <input type="email" id="email" name="email"
                                class="form-control auth-input @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="tu@correo.com" autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Contraseña --}}
                        <div class="mb-1">
                            <label for="password" class="form-label small fw-medium text-secondary">
                                Contraseña
                            </label>
                            <div class="auth-password">
                                <input type="password" id="password" name="password"
                                    class="form-control auth-input @error('password') is-invalid @enderror"
                                    placeholder="Mínimo 8 caracteres" autocomplete="new-password"
                                    oninput="updateStrength(this.value)" required>
                                <button type="button" class="auth-eye" data-toggle-password="password"
                                    aria-label="Mostrar contraseña" title="Mostrar contraseña" aria-pressed="false">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Barra de fortaleza --}}
                        <div class="d-flex gap-1 mb-1" id="strength-bars">
                            <div class="flex-fill rounded" style="height:3px; background:#e9e9f5;" id="sb1"></div>
                            <div class="flex-fill rounded" style="height:3px; background:#e9e9f5;" id="sb2"></div>
                            <div class="flex-fill rounded" style="height:3px; background:#e9e9f5;" id="sb3"></div>
                            <div class="flex-fill rounded" style="height:3px; background:#e9e9f5;" id="sb4"></div>
                        </div>
                        <p class="small text-secondary mb-3" id="strength-label" style="min-height:18px;"></p>

                        {{-- Confirmar contraseña --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label small fw-medium text-secondary">
                                Confirmar contraseña
                            </label>
                            <div class="auth-password">
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-control auth-input" placeholder="Repite tu contraseña"
                                    autocomplete="new-password" required>
                                <button type="button" class="auth-eye" data-toggle-password="password_confirmation"
                                    aria-label="Mostrar contraseña" title="Mostrar contraseña" aria-pressed="false">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Términos --}}
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label small text-secondary" for="terms">
                                Acepto los
                                <a href="#" style="color:#534AB7;">Términos de servicio</a>
                                y la
                                <a href="#" style="color:#534AB7;">Política de privacidad</a>
                            </label>
                        </div>

                        {{-- Botón submit --}}
                        <button type="submit" class="auth-submit btn w-100 text-white fw-medium mb-3"
                            style="background:#534AB7;">
                            Crear cuenta
                        </button>

                        {{-- Link a login --}}
                        @if (Route::has('login'))
                            <div class="text-center text-secondary small">
                                ¿Ya tienes cuenta?
                                <a href="{{ route('login') }}" style="color:#534AB7; font-weight:500;">
                                    Iniciar sesión
                                </a>
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- codigo para la validación de contraseña --}}
    <script src="{{ asset('js/password-toggle.js') }}?v={{ filemtime(public_path('js/password-toggle.js')) }}"></script>
    <script>
        function updateStrength(val) {
            const colors = ['#e24b4a', '#ef9f27', '#a855f7', '#1D9E75'];
            const labels = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            for (let i = 1; i <= 4; i++) {
                const bar = document.getElementById('sb' + i);
                bar.style.background = i <= score ? colors[score - 1] : '#e9e9f5';
                bar.style.transition = 'background 0.3s';
            }
            document.getElementById('strength-label').textContent = val.length ? labels[score] : '';
        }
    </script>

</body>

</html>
