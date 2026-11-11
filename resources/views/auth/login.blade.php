<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — FinanceApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}" rel="stylesheet">
</head>

<body class="auth-body">

    {{-- contenedor principal --}}
    <div class="auth-wrapper min-vh-100 container-fluid d-flex align-items-center justify-content-center">

        {{-- contenedor del login de usuario --}}
        <div class="row g-0 justify-content-center w-100">
            <div
                class="auth-card col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4 bg-white d-flex align-items-center justify-content-center rounded-3">

                <div class="w-100">

                    {{-- LOGO PRINCIPAL --}}
                    <div class="text-center">
                        <img class="login-img" src="{{ asset('Images/Logo.png') }}" alt="Logo de FinanceApp"
                            width="150">
                    </div>

                    {{-- TITULO Y SUBTITULOS --}}
                    <h2 class="auth-title text-center fw-medium mb-1">Bienvenido de nuevo</h2>
                    <p class="text-center text-secondary mb-4">La mejor plataforma de gestión financiera</p>

                    {{-- Mensaje de estado, ej. al eliminar la cuenta --}}
                    @if (session('status'))
                        <div class="alert alert-success py-2 small rounded-3">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2 small rounded-3">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- FORMULARIO DE INICIO DE SESIÓN --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label small fw-medium text-secondary">
                                Ingresa tu correo electrónico
                            </label>

                            <input type="email" id="email" name="email"
                                class="form-control auth-input @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="tu@correo.com" autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-1 mb-1">
                                <label for="password" class="form-label small fw-medium text-secondary mb-0">
                                    Ingresa tu contraseña
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small" style="color:#534AB7;">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif

                            </div>

                            <div class="auth-password">
                                <input type="password" id="password" name="password"
                                    class="form-control auth-input @error('password') is-invalid @enderror"
                                    placeholder="Tu contraseña" autocomplete="current-password" required>
                                <button type="button" class="auth-eye" data-toggle-password="password"
                                    aria-label="Mostrar contraseña" title="Mostrar contraseña" aria-pressed="false">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label small text-secondary" for="remember">
                                Recordar sesión
                            </label>
                        </div>

                        {{-- BOTON DE ENVIO --}}
                        <button type="submit" class="auth-submit btn w-100 text-white fw-medium mb-3"
                            style="background:#534AB7;">
                            Iniciar sesión
                        </button>

                        @if (Route::has('register'))
                            <div class="text-center text-secondary small mb-3">¿No tienes cuenta? <span><a
                                        href="{{ route('register') }}" class="" style="">
                                        Crear cuenta nueva
                                    </a></span></div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/password-toggle.js') }}?v={{ filemtime(public_path('js/password-toggle.js')) }}"></script>

</body>

</html>
