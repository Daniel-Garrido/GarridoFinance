@extends('layouts.app')

@section('content')

    {{-- contenedor principal del metodo de pago --}}
    <div class="container-fluid">

        {{-- contenedor header principal seccion de metodos de pago --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-4 ">
            <div>
                <h2 class="fw-bold mb-1">Lista de métodos de pago</h2>
                <p class="text-muted mb-0">Administra tus métodos de pago registrados en GarridoFinance</p>
            </div>

            {{-- btn crear metodo de pago --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPaymentMethodModal">
                <i class="bi bi-plus-circle me-1"></i>
                Crear nuevo método de pago
            </button>
        </div>

        {{-- Alerta cueando se crea un metodo de pago --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- contenedor de la tabla de métodos de pago --}}
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i>
                    Métodos de pago registrados
                </h3>
            </div>

            {{-- cuerpo de la tabla de métodos de pago --}}
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">
                        {{-- cabecera de las tablas --}}
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tipo de método de pago</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Opciones</th>
                            </tr>
                        </thead>

                        {{-- cuerpo de la tabla de métodos de pago --}}
                        <tbody>

                            @forelse($paymentMethods as $method)
                                <tr>
                                    {{-- nombre del método de pago --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="account-icon me-3">
                                                <i class="bi bi-wallet2"></i>
                                            </div>

                                            <div>
                                                <h6 class="mb-0 fw-semibold">
                                                    {{ $method->name }}
                                                </h6>
                                                {{-- <small class="text-muted">
                                                    {{ $method->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                                </small> --}}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- si esta activa la cuenta --}}
                                    <td>
                                        @if ($method->is_active)
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

                                    {{-- opciones del metodo de pago --}}
                                    <td class="text-end pe-4">

                                        {{--  btn para editar el método de pago --}}
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editPaymentMethodModal{{ $method->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- opcion para eliminar el método de pago --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deletePaymentMethodModal"
                                            data-action="{{ route('payment-methods.destroy', $method) }}"
                                            data-name="{{ $method->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                                {{-- modal para editar metodo de pago --}}
                                <div class="modal fade" id="editPaymentMethodModal{{ $method->id }}" tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header">
                                                {{-- titulo modal --}}
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Editar metodo de pago
                                                </h5>
                                                {{-- btn cerrar modal --}}
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            {{-- Formulario para editar metodo de pago --}}
                                            <form action="{{ route('payment-methods.update', $method) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">

                                                    {{-- contenedor de nombre del metodo de pago --}}
                                                    <div class="mb-3">
                                                        <label for="name{{ $method->id }}" class="form-label">
                                                            Nombre del metodo de pago
                                                        </label>

                                                        <input type="text" name="name" id="name{{ $method->id }}"
                                                            class="form-control" value="{{ old('name', $method->name) }}">
                                                    </div>

                                                    {{-- contenedor del estado del metodo de pago --}}
                                                    <div class="mb-3">
                                                        <label for="is_active{{ $method->id }}" class="form-label">
                                                            Estado
                                                        </label>

                                                        <select name="is_active" id="is_active{{ $method->id }}"
                                                            class="form-select">
                                                            <option value="1"
                                                                {{ old('is_active', $method->is_active) == 1 ? 'selected' : '' }}>
                                                                Activa
                                                            </option>
                                                            <option value="0"
                                                                {{ old('is_active', $method->is_active) == 0 ? 'selected' : '' }}>
                                                                Inactiva
                                                            </option>
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de opciones  --}}
                                                    <div class="modal-footer">
                                                        {{-- btn cancelar --}}
                                                        <button type="button" class="btn btn-light"
                                                            data-bs-dismiss="modal">
                                                            Cancelar
                                                        </button>
                                                        {{-- btn actualizar --}}
                                                        <button type="submit" class="btn btn-warning text-white">
                                                            <i class="bi bi-save me-1"></i>
                                                            Actualizar método de pago
                                                        </button>
                                                    </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            @empty
                                {{-- cuando no hay metodos de pago registrados --}}
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                                        <h5 class="mt-3 mb-1">No hay metodos de pago registrados</h5>
                                        <p class="text-muted mb-3">
                                            Agrega tu primer método de pago para comenzar.
                                        </p>

                                        {{-- btn crear categorias --}}
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#createPaymentMethodModal">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Crear nuevo método de pago
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

    {{-- modal para crear metodo de pago --}}
    <div class="modal fade" id="createPaymentMethodModal" tabindex="-1" aria-labelledby="createPaymentMethodModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content rounded-4 border-0 shadow">

                {{-- encabezado del modal de metodo de pago --}}
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createPaymentMethodModalLabel">
                        <i class="bi bi-wallet2 me-1"></i>
                        Crear nuevo método de pago
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- formulario para crear método de pago --}}
                <form action="{{ route('payment-methods.store') }}" method="POST">
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

                        {{-- tipo de método de pago --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Tipo de método de pago</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Ej: Efectivo, Tarjeta">
                        </div>

                        {{-- estado del método de pago --}}
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Estado</label>
                            <select name="is_active" id="is_active" class="form-select">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Activa
                                </option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                        </div>

                    </div>

                    {{-- Opciones --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar método de pago
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- modal para eliminar metodo de pago --}}
    <div class="modal fade" id="deletePaymentMethodModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header border-0 pb-0">
                    {{-- btn cerrar modal --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- contenido del modal - eliminar metodo de pago --}}
                <div class="modal-body text-center py-4">
                    <i class="bi bi-trash fs-1 text-danger mb-3 d-block"></i>
                    <p class="mb-1">¿Estás seguro que deseas eliminar el Método de pago:</p>
                    <h6 class="fw-bold" id="deletePaymentMethodName"></h6>
                    <p class="text-muted small mt-2 mb-0">Esta acción no se puede deshacer.</p>
                </div>

                {{-- contenedor botones del modal --}}
                <div class="modal-footer border-0 pt-0">
                    {{-- btn cancelar --}}
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    {{-- btn eliminar --}}
                    <form id="deletePaymentMethodForm" method="POST" class="d-inline">
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


    @push('scripts')
        <script>
            const deleteModal = document.getElementById('deletePaymentMethodModal') // Obtener referencia al modal de eliminación

            deleteModal.addEventListener('show.bs.modal', function(event) {
                // botón que disparó el modal
                const button = event.relatedTarget;

                // leer los datos del botón
                const action = button.getAttribute('data-action');
                const name = button.getAttribute('data-name');

                // inyectarlos en el modal
                document.getElementById('deletePaymentMethodName').textContent = name;
                document.getElementById('deletePaymentMethodForm').setAttribute('action', action);
            })
        </script>
    @endpush

@endsection
