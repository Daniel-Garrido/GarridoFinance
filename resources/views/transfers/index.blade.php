@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        {{-- Alerta cueando se crea una transferencia --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- contenedor header principal seccion de transferencias --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-4 ">
            <div>
                <h2 class="fw-bold mb-1">Lista de transferencias</h2>
                <p class="text-muted mb-0">Administra tus transferencias registradas en GarridoFinance</p>
            </div>

            {{-- btn crear transferencia --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransferModal">
                <i class="bi bi-plus-circle me-1"></i>
                Crear una transferencia
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4">

            {{-- cabecera de la tabla transacciones --}}
            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i>
                    Transferencias registradas
                </h3>
            </div>

            {{-- cuerpo de la tabla de categorías --}}
            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">
                        {{-- cabecera de las tablas --}}
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Fecha</th>
                                <th>Monto</th>
                                <th>Cuenta origen</th>
                                <th>Cuenta destino</th>
                                <th>Descripción</th>
                                <th class="text-end pe-4">Opciones</th>
                            </tr>
                        </thead>

                        {{-- cuerpo de la tabla de transacciones --}}
                        <tbody>

                            @forelse($transfers as $transfer)
                                <tr>
                                    {{-- fecha de la transacción --}}
                                    <td>
                                        {{ $transfer->date }}
                                    </td>

                                    {{-- monto de la transacción --}}
                                    <td>
                                        ${{ number_format($transfer->amount, 2) }} MXN
                                    </td>
                                    {{-- cuenta origen --}}
                                    <td>
                                        {{ $transfer->fromAccount?->name }}
                                    </td>
                                    {{-- cuenta destino --}}
                                    <td>
                                        {{ $transfer->toAccount?->name }}
                                    </td>
                                    {{-- descripción --}}
                                    <td>
                                        {{ $transfer->description }}
                                    </td>


                                    {{-- opciones  --}}
                                    <td class="text-end pe-4">

                                        {{--  btn para editar la transaccion --}}
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editTransferModal{{ $transfer->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- opcion para eliminar la transaccion --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteTransferModal"
                                            data-action="{{ route('transfers.destroy', $transfer) }}"
                                            data-name="{{ $transfer->description }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                                {{-- Modal para editar transferencia --}}
                                <div class="modal fade" id="editTransferModal{{ $transfer->id }}" tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header">

                                                {{-- titulo modal --}}
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Editar transferencia
                                                </h5>

                                                {{-- btn cerrar modal --}}
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            {{-- Formulario para editar transferencia --}}
                                            <form action="{{ route('transfers.update', $transfer) }}" method="POST">

                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">

                                                    {{-- contenedor de fecha --}}
                                                    <div class="mb-3">
                                                        <label for="date{{ $transfer->id }}">Fecha:</label><br>
                                                        <input type="date" name="date" id="date{{ $transfer->id }}"
                                                            class="form-control"
                                                            value="{{ old('date', $transfer->date) }}">
                                                    </div>

                                                    {{-- contenedor de monto --}}
                                                    <div class="mb-3">
                                                        <label for="amount{{ $transfer->id }}">Monto:</label><br>
                                                        <input type="number" step="0.01" name="amount"
                                                            id="amount{{ $transfer->id }}" class="form-control"
                                                            value="{{ old('amount', $transfer->amount) }}">
                                                    </div>

                                                    {{-- contenedor de cuenta origen --}}
                                                    <div class="mb-3">
                                                        <label for="from_account_id{{ $transfer->id }}">Cuenta origen:</label><br>
                                                        <select name="from_account_id"
                                                            id="from_account_id{{ $transfer->id }}"
                                                            class="form-select">
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}"
                                                                    {{ old('from_account_id', $transfer->from_account_id) == $account->id ? 'selected' : '' }}>
                                                                    {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de cuenta destino --}}
                                                    <div class="mb-3">
                                                        <label for="to_account_id{{ $transfer->id }}">Cuenta destino:</label><br>
                                                        <select name="to_account_id"
                                                            id="to_account_id{{ $transfer->id }}"
                                                            class="form-select">
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}"
                                                                    {{ old('to_account_id', $transfer->to_account_id) == $account->id ? 'selected' : '' }}>
                                                                    {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de descripción --}}
                                                    <div class="mb-3">
                                                        <label for="description{{ $transfer->id }}">Descripción:</label><br>
                                                        <textarea name="description"
                                                            id="description{{ $transfer->id }}" class="form-control"
                                                            rows="3">{{ old('description', $transfer->description) }}</textarea>
                                                    </div>

                                                </div>

                                                {{-- contenedor de opciones  --}}
                                                <div class="modal-footer">
                                                    {{-- btn cancelar --}}
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                        Cancelar
                                                    </button>
                                                    {{-- btn actualizar --}}
                                                    <button type="submit" class="btn btn-warning text-white">
                                                        <i class="bi bi-save me-1"></i>
                                                        Actualizar transferencia
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                            @empty

                                {{-- cuando no hay transferencias registradas --}}
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                                        <h5 class="mt-3 mb-1">No hay transferencias registradas</h5>
                                        <p class="text-muted mb-3">
                                            Agrega tu primera transferencia para comenzar.
                                        </p>

                                        {{-- btn crear transferencia --}}
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#createTransferModal">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Crear nueva transferencia
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

    {{-- Modal para crear transferencia --}}
    <div class="modal fade" id="createTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nueva transferencia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('transfers.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        {{-- contenedor de fecha --}}
                        <div class="mb-3">
                            <label for="date">Fecha:</label><br>
                            <input type="date" name="date" id="date" class="form-control"
                                value="{{ old('date') }}" required>
                        </div>

                        {{-- contenedor de monto --}}
                        <div class="mb-3">
                            <label for="amount">Monto:</label><br>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control"
                                value="{{ old('amount') }}" required>
                        </div>

                        {{-- contenedor de cuenta origen --}}
                        <div class="mb-3">
                            <label for="from_account_id">Cuenta origen:</label><br>
                            <select name="from_account_id" id="from_account_id" class="form-select" required>
                                <option value="" disabled selected>Selecciona una cuenta</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- contenedor de cuenta destino --}}
                        <div class="mb-3">
                            <label for="to_account_id">Cuenta destino:</label><br>
                            <select name="to_account_id" id="to_account_id" class="form-select" required>
                                <option value="" disabled selected>Selecciona una cuenta</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- contenedor de descripción --}}
                        <div class="mb-3">
                            <label for="description">Descripción:</label><br>
                            <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar transferencia
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- Modal para eliminar transferencia --}}
    <div class="modal fade" id="deleteTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Eliminar transferencia
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar la transferencia
                    "<span id="deleteTransferName"></span>"? Esta acción no se puede deshacer.
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <form id="deleteTransferForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>
                            Eliminar
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Llena el modal de eliminar con la acción y el nombre correctos según el botón que lo abrió
    document.addEventListener('DOMContentLoaded', function () {
        const deleteModal = document.getElementById('deleteTransferModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                const name = button.getAttribute('data-name');

                document.getElementById('deleteTransferForm').setAttribute('action', action);
                document.getElementById('deleteTransferName').textContent = name;
            });
        }
    });
</script>
@endpush
