@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- contenedor header principal seccion cuentas --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-4 ">
            <div>
                <h2 class="fw-bold mb-1">Lista de cuentas</h2>
                <p class="text-muted mb-0">Administra tus cuentas registradas en GarridoFinance</p>
            </div>

            {{-- btn crear cuenta --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                <i class="bi bi-plus-circle me-1"></i>
                Crear nueva cuenta
            </button>
        </div>

        {{-- Alerta cueando se crea una cuenta --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- contenedor de la tabla de cuentas --}}
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i>
                    Cuentas registradas
                </h3>
            </div>


            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">
                        {{-- cabecera de las tablas --}}
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nombre de la cuenta</th>
                                <th>Tipo de cuenta</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Opciones</th>
                            </tr>
                        </thead>

                        {{-- cuerpo de la tabla de cuentas --}}
                        <tbody>

                            @forelse($accounts as $account)
                                <tr>

                                    {{-- nombre de la cuenta --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="account-icon me-3">
                                                <i class="bi bi-wallet2"></i>
                                            </div>

                                            <div>
                                                <h6 class="mb-0 fw-semibold">
                                                    {{ $account->name }}
                                                </h6>
                                                <small class="text-muted">
                                                    Cuenta financiera
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- tipo de cuenta --}}
                                    <td>
                                        <span class="badge text-bg-light border">
                                            {{ ucfirst($account->type) }}
                                        </span>
                                    </td>

                                    {{-- si esta activa la cuenta --}}
                                    <td>
                                        @if ($account->is_active)
                                            <span class="badge rounded-pill text-bg-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Activa
                                            </span>
                                        @else
                                            <span class="badge rounded-pill text-bg-secondary">
                                                <i class="bi bi-x-circle me-1"></i>
                                                Inactiva
                                            </span>
                                        @endif
                                    </td>

                                    {{-- opciones  --}}
                                    <td class="text-end pe-4">

                                        {{--  btn para editar cuenta --}}
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editAccountModal{{ $account->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- opcion para eliminar cuenta --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteAccountModal"
                                            data-action="{{ route('accounts.destroy', $account) }}"
                                            data-name="{{ $account->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                                {{-- Modal para  editar cuentas --}}
                                <div class="modal fade" id="editAccountModal{{ $account->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Editar cuenta
                                                </h5>

                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form action="{{ route('accounts.update', $account) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">

                                                    <div class="mb-3">
                                                        <label for="name{{ $account->id }}" class="form-label">Nombre de la
                                                            cuenta</label>
                                                        <input type="text" name="name" id="name{{ $account->id }}"
                                                            class="form-control" value="{{ old('name', $account->name) }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="type{{ $account->id }}" class="form-label">Tipo de
                                                            cuenta</label>
                                                        <select name="type" id="type{{ $account->id }}"
                                                            class="form-select">
                                                            <option value="cash"
                                                                {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>
                                                                Efectivo
                                                            </option>
                                                            <option value="bank"
                                                                {{ old('type', $account->type) == 'bank' ? 'selected' : '' }}>
                                                                Banco
                                                            </option>
                                                            <option value="card"
                                                                {{ old('type', $account->type) == 'card' ? 'selected' : '' }}>
                                                                Tarjeta
                                                            </option>
                                                            <option value="saving"
                                                                {{ old('type', $account->type) == 'saving' ? 'selected' : '' }}>
                                                                Ahorro
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="is_active{{ $account->id }}"
                                                            class="form-label">Estado</label>
                                                        <select name="is_active" id="is_active{{ $account->id }}"
                                                            class="form-select">
                                                            <option value="1"
                                                                {{ old('is_active', $account->is_active) == 1 ? 'selected' : '' }}>
                                                                Activa
                                                            </option>
                                                            <option value="0"
                                                                {{ old('is_active', $account->is_active) == 0 ? 'selected' : '' }}>
                                                                Inactiva
                                                            </option>
                                                        </select>
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>

                                                    <button type="submit" class="btn btn-warning text-white">
                                                        <i class="bi bi-save me-1"></i>
                                                        Actualizar cuenta
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                                {{-- Modal para eliminar cuentas --}}
                                <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header border-0 pb-0">

                                                {{-- btn cerrar modal --}}
                                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                </button>
                                            </div>

                                            {{-- contenido del modal - eliminar cuenta --}}
                                            <div class="modal-body text-center py-4">
                                                <i class="bi bi-trash fs-1 text-danger mb-3 d-block"></i>
                                                <p class="mb-1">¿Estás seguro que deseas eliminar la cuenta:</p>
                                                <h6 class="fw-bold" id="deleteAccountName"></h6>
                                                <p class="text-muted small mt-2 mb-0">Esta acción no se puede deshacer.</p>
                                            </div>

                                            <div class="modal-footer border-0 pt-0">
                                                {{-- btn cancelar --}}
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>

                                                {{-- btn eliminar --}}
                                                <form id="deleteAccountForm" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-trash me-1"></i>
                                                        Sí, eliminar
                                                    </button>
                                                </form>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @empty

                                {{-- cuando no hay cuentas registradas --}}
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                                        <h5 class="mt-3 mb-1">No hay cuentas registradas</h5>
                                        <p class="text-muted mb-3">
                                            Agrega tu primera cuenta para comenzar.
                                        </p>

                                        {{-- btn crear cuentas --}}
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#createAccountModal">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Crear nueva cuenta
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal para crear cuentas --}}
    <div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createAccountModalLabel">
                        <i class="bi bi-wallet2 me-1"></i>
                        Crear nueva cuenta
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre de la cuenta</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Ej. Efectivo, BBVA, Santander">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de cuenta</label>
                            <select name="type" id="type" class="form-select">
                                <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                                <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Banco</option>
                                <option value="card" {{ old('type') == 'card' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="saving" {{ old('type') == 'saving' ? 'selected' : '' }}>Ahorro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="is_active" class="form-label">Estado</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Activa
                                </option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar cuenta
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const deleteModal = document.getElementById('deleteAccountModal') // Obtener referencia al modal de eliminación

            deleteModal.addEventListener('show.bs.modal', function(event) {
                // botón que disparó el modal
                const button = event.relatedTarget

                // leer los datos del botón
                const action = button.getAttribute('data-action')
                const name = button.getAttribute('data-name')

                // inyectarlos en el modal
                document.getElementById('deleteAccountName').textContent = name
                document.getElementById('deleteAccountForm').setAttribute('action', action)
            })
        </script>
    @endpush

@endsection
