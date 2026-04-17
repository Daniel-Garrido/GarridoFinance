<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — FinanceApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>

<body class="bg-light">

    {{-- contenedor principal --}}
    <div class="w-100 min-vh-100 container-fluid d-flex align-items-center justify-content-center">

        {{-- contenedor del login de usuario --}}
        <div class="row justify-content-center w-100">
            <div
                class="col-lg-5 col-12 bg-white d-flex align-items-center justify-content-center p-4 rounded-3 shadow-sm">

                <div class="w-100">

                    {{-- LOGO PRINCIPAL --}}
                    <div class="text-center">
                        <img class="login-img" src="{{ asset('images/Logo.png') }}" alt="Logo de FinanceApp"
                            width="150">
                    </div>

                    {{-- TITULO Y SUBTITULOS --}}
                    <h2 class="text-center fw-medium mb-1">Bienvenido de nuevo</h2>
                    <p class="text-center text-secondary mb-4">La mejor plataforma de gestión financiera</p>

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
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="tu@correo.com" autocomplete="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label small fw-medium text-secondary mb-0">
                                    Ingresa tu contraseña
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small" style="color:#534AB7;">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif

                            </div>

                            <input type="password" id="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder=""
                                autocomplete="current-password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label small text-secondary" for="remember">
                                Recordar sesión
                            </label>
                        </div>

                        {{-- BOTON DE ENVIO --}}
                        <button type="submit" class="btn w-100 text-white fw-medium mb-3"
                            style="background:#534AB7; height:42px;">
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

</body>

</html>
