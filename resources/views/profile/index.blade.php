@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}?v={{ filemtime(public_path('css/profile.css')) }}">
@endpush

@section('content')
    @php
        // Mensajes de éxito: los de Fortify llegan como clave en session('status')
        $statusMessages = [
            'profile-information-updated' => 'Los cambios de tu perfil se guardaron correctamente.',
            'password-updated' => 'Tu contraseña se actualizó correctamente.',
        ];
        $success = session('success') ?? ($statusMessages[session('status')] ?? null);

        // Bolsas de errores de cada formulario
        $profileErrors = $errors->updateProfileInformation;
        $passwordErrors = $errors->updatePassword;
        $photoErrors = $errors->updatePhoto;
        $deleteErrors = $errors->deleteAccount;

        // Al fallar la validación del perfil se muestran los datos enviados
        $profileValue = fn($field) => $profileErrors->any() ? old($field) : $user->{$field};
    @endphp

    <div class="gf-page">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Mi perfil</h1>
                <p>Consulta y actualiza los datos de tu cuenta en GarridoFinance</p>
            </div>
            <button type="submit" form="profileForm" class="gf-btn gf-btn-primary">
                <i class="bi bi-check-lg"></i> Guardar cambios
            </button>
        </header>

        {{-- Alerta de éxito --}}
        @if ($success)
            <div class="gf-alert alert alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-check-circle"></i>
                {{ $success }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <section class="gf-profile-grid">

            {{-- Tarjeta del usuario --}}
            <div class="gf-card gf-profile-card">
                <div class="gf-profile-user">
                    <x-avatar :user="$user" size="lg" />
                    <div>
                        <p class="gf-profile-name">{{ $user->full_name }}</p>
                        <p class="gf-profile-email">{{ $user->email }}</p>
                    </div>
                </div>

                {{-- Foto de perfil: al elegir un archivo el formulario se envía solo --}}
                <div class="gf-profile-actions">
                    <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data"
                        id="photoForm">
                        @csrf
                        <label class="gf-btn gf-btn-ghost gf-btn-sm mb-0" for="photoInput">
                            <i class="bi bi-camera"></i> Cambiar foto
                        </label>
                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" hidden>
                    </form>

                    @if ($user->profile_photo_path)
                        <form method="POST" action="{{ route('profile.photo.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="gf-btn gf-btn-outline-danger gf-btn-sm">Quitar foto</button>
                        </form>
                    @endif
                </div>
                <x-form-error name="photo" bag="updatePhoto" />

                <div class="gf-divider"></div>

                {{-- Resumen de la cuenta --}}
                <dl class="gf-profile-stats">
                    <div>
                        <dt>Miembro desde</dt>
                        <dd>{{ $memberSince }}</dd>
                    </div>
                    <div>
                        <dt>Transacciones registradas</dt>
                        <dd>{{ number_format($transactionsCount) }}</dd>
                    </div>
                    <div>
                        <dt>Cuentas activas</dt>
                        <dd>{{ $activeAccounts }}</dd>
                    </div>
                    <div>
                        <dt>Último acceso</dt>
                        <dd>{{ $lastAccess }}</dd>
                    </div>
                </dl>

                <div class="gf-divider"></div>

                {{-- Cerrar sesión --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="gf-btn gf-btn-ghost gf-btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                    </button>
                </form>

                <div class="gf-divider"></div>

                {{-- Eliminar cuenta --}}
                <div class="gf-profile-section">
                    <div>
                        <p class="gf-section-title gf-danger-title">Eliminar cuenta</p>
                        <p class="gf-section-hint">Se borrarán tus cuentas, categorías, métodos de pago, transacciones y
                            transferencias de forma permanente.</p>
                    </div>
                    <div>
                        <button type="button" class="gf-btn gf-btn-soft-danger gf-btn-sm" data-bs-toggle="modal"
                            data-bs-target="#deleteAccountModal">
                            Eliminar mi cuenta
                        </button>
                    </div>
                </div>
            </div>

            {{-- Datos personales y seguridad --}}
            <div class="gf-card gf-profile-card">

                {{-- Datos personales (Fortify: user-profile-information.update) --}}
                <form method="POST" action="{{ route('user-profile-information.update') }}" id="profileForm"
                    class="gf-profile-section">
                    @csrf
                    @method('PUT')

                    <div>
                        <h2 class="gf-section-title">Datos personales</h2>
                        <p class="gf-section-hint">Esta información aparece en tus reportes y exportaciones.</p>
                    </div>

                    <div class="gf-form-grid">
                        <div class="gf-form-group">
                            <label for="profile_name">Nombre</label>
                            <input type="text" name="name" id="profile_name" autocomplete="given-name"
                                class="form-control {{ $profileErrors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $profileValue('name') }}">
                            <x-form-error name="name" bag="updateProfileInformation" />
                        </div>

                        <div class="gf-form-group">
                            <label for="profile_last_name">Apellidos</label>
                            <input type="text" name="last_name" id="profile_last_name" autocomplete="family-name"
                                class="form-control {{ $profileErrors->has('last_name') ? 'is-invalid' : '' }}"
                                value="{{ $profileValue('last_name') }}">
                            <x-form-error name="last_name" bag="updateProfileInformation" />
                        </div>

                        <div class="gf-form-group">
                            <label for="profile_email">Correo electrónico</label>
                            <input type="email" name="email" id="profile_email" autocomplete="email"
                                class="form-control {{ $profileErrors->has('email') ? 'is-invalid' : '' }}"
                                value="{{ $profileValue('email') }}">
                            <x-form-error name="email" bag="updateProfileInformation" />
                        </div>

                        <div class="gf-form-group">
                            <label for="profile_phone">Teléfono</label>
                            <input type="tel" name="phone" id="profile_phone" autocomplete="tel"
                                placeholder="+52 81 2345 6789"
                                class="form-control {{ $profileErrors->has('phone') ? 'is-invalid' : '' }}"
                                value="{{ $profileValue('phone') }}">
                            <x-form-error name="phone" bag="updateProfileInformation" />
                        </div>
                    </div>
                </form>

                <div class="gf-divider"></div>

                {{-- Seguridad (Fortify: user-password.update) --}}
                <form method="POST" action="{{ route('user-password.update') }}" class="gf-profile-section">
                    @csrf
                    @method('PUT')

                    <div>
                        <h2 class="gf-section-title">Seguridad</h2>
                        <p class="gf-section-hint">Actualiza tu contraseña de acceso.</p>
                    </div>

                    <div class="gf-form-grid">
                        <div class="gf-form-group">
                            <label for="current_password">Contraseña actual</label>
                            <input type="password" name="current_password" id="current_password"
                                autocomplete="current-password" placeholder="••••••••"
                                class="form-control {{ $passwordErrors->has('current_password') ? 'is-invalid' : '' }}">
                            <x-form-error name="current_password" bag="updatePassword" />
                        </div>

                        <div class="gf-form-group">
                            <label for="password">Nueva contraseña</label>
                            <input type="password" name="password" id="password" autocomplete="new-password"
                                placeholder="Mínimo 8 caracteres"
                                class="form-control {{ $passwordErrors->has('password') ? 'is-invalid' : '' }}">
                            <x-form-error name="password" bag="updatePassword" />
                        </div>

                        <div class="gf-form-group">
                            <label for="password_confirmation">Confirmar contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                autocomplete="new-password" placeholder="Repite la nueva contraseña" class="form-control">
                        </div>
                    </div>

                    <div class="gf-profile-section-foot">
                        <button type="submit" class="gf-btn gf-btn-soft">
                            <i class="bi bi-shield-lock"></i> Actualizar contraseña
                        </button>
                    </div>
                </form>
            </div>
        </section>

    </div>

    {{-- Modal: eliminar cuenta (pide la contraseña) --}}
    <div class="modal fade gf-modal" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-body gf-confirm">
                        <span class="gf-icon gf-tone-expense"><i class="bi bi-exclamation-triangle"></i></span>
                        <h3 id="deleteAccountModalLabel">¿Eliminar tu cuenta?</h3>
                        <p>Se borrarán <strong>{{ number_format($transactionsCount) }} transacciones</strong>, tus
                            cuentas, categorías, métodos de pago y transferencias. Esta acción no se puede deshacer.</p>

                        <div class="gf-form-group">
                            <label for="delete_password">Escribe tu contraseña para confirmar</label>
                            <input type="password" name="password" id="delete_password" autocomplete="current-password"
                                class="form-control {{ $deleteErrors->has('password') ? 'is-invalid' : '' }}">
                            <x-form-error name="password" bag="deleteAccount" />
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-danger">
                            <i class="bi bi-trash3"></i> Sí, eliminar mi cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            // Foto de perfil: se sube en cuanto se elige el archivo
            document.getElementById('photoInput').addEventListener('change', function () {
                if (this.files.length) document.getElementById('photoForm').submit();
            });

            // Si la contraseña para eliminar la cuenta fue incorrecta, reabre el modal
            @if ($deleteErrors->any())
                new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
            @endif
        })();
    </script>
@endpush
