@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- Alerta cueando se crea una transacción --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        {{-- contenedor header principal seccion de transacciones --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-4 ">
            <div>
                <h2 class="fw-bold mb-1">Lista de transacciones</h2>
                <p class="text-muted mb-0">Administra tus transacciones registradas en GarridoFinance</p>
            </div>

            {{-- btn crear transacción --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTransactionModal">
                <i class="bi bi-plus-circle me-1"></i>
                Crear una transacción
            </button>
        </div>

        {{-- contenedor de la tabla  transacciones --}}
        <div class="card border-0 shadow-sm rounded-4">

            {{-- cabecera de la tabla transacciones --}}
            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i>
                    Transacciones registradas
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
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Cuenta</th>
                                <th>Categoría</th>
                                <th>Método de pago</th>
                                <th>¿Es histórica?</th>
                                <th>Descripción</th>
                                <th class="text-end pe-4">Opciones</th>
                            </tr>
                        </thead>

                        {{-- cuerpo de la tabla de transacciones --}}
                        <tbody>

                            @forelse($transactions as $transaction)
                                <tr>
                                    {{-- fecha de la transacción --}}
                                    <td>
                                        {{ $transaction->date }}
                                    </td>

                                    {{-- tipo de la transaccion --}}
                                    <td>
                                        {{ $transaction->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                    </td>

                                    {{-- monto de la transacción --}}
                                    <td>
                                        ${{ number_format($transaction->amount, 2) }} MXN
                                    </td>

                                    {{-- cuenta de la transacción --}}
                                    <td>
                                        {{ $transaction->account?->name }}
                                    </td>
                                    {{-- categoría de la transacción --}}
                                    <td>
                                        {{ $transaction->category?->name }}
                                    </td>
                                    {{-- método de pago --}}
                                    <td>
                                        {{ $transaction->paymentMethod?->name }}
                                    </td>
                                    {{-- es histórica --}}
                                    <td>
                                        {{ $transaction->is_historical ? 'Sí' : 'No' }}
                                    </td>
                                    {{-- descripción de la transacción --}}
                                    <td>
                                        {{ $transaction->description }}
                                    </td>

                                    {{-- opciones  --}}
                                    <td class="text-end pe-4">

                                        {{--  btn para editar la transaccion --}}
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editTransactionModal{{ $transaction->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- opcion para eliminar la transaccion --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteTransactionModal"
                                            data-action="{{ route('transactions.destroy', $transaction) }}"
                                            data-name="{{ $transaction->type }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                                {{-- Modal para  editar transacciones --}}
                                <div class="modal fade" id="editTransactionModal{{ $transaction->id }}" tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header">

                                                {{-- titulo modal --}}
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Editar transacción
                                                </h5>

                                                {{-- btn cerrar modal --}}
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            {{-- Formulario para editar transaccion --}}
                                            <form action="{{ route('transactions.update', $transaction) }}" method="POST">

                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">

                                                    {{-- contenedor de fecha --}}
                                                    <div class="mb-3">
                                                        <label for="date">Fecha:</label><br>
                                                        <input type="date" name="date" id="date"
                                                            value="{{ old('date', $transaction->date) }}">
                                                    </div>

                                                    {{-- contenedor de tipo de transacción --}}
                                                    <div class="mb-3">
                                                        <label for="type">Tipo de transacción:</label>
                                                        <select name="type" id="type" class="form-select">

                                                            <option
                                                                value="income"{{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>
                                                                Ingreso
                                                            </option>

                                                            <option
                                                                value="expense"{{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>
                                                                Gasto
                                                            </option>

                                                        </select>
                                                    </div>

                                                    {{-- contenedor de monto --}}
                                                    <div class="mb-3">
                                                        <label for="amount">Monto:</label><br>
                                                        <input type="number" step="0.01" name="amount"
                                                            id="amount"value="{{ old('amount', $transaction->amount) }}">
                                                    </div>

                                                    {{-- contenedor de cuenta --}}
                                                    <div class="mb-3">
                                                        <label for="account_id">Cuenta:</label><br>
                                                        <select name="account_id" id="account_id">
                                                            @foreach ($accounts as $account)
                                                                <option
                                                                    value="{{ $account->id }}"{{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>
                                                                    {{ $account->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de categoría --}}
                                                    <div class="mb-3">
                                                        <label for="category_id">Categoría:</label>
                                                        <select name="category_id" id="category_id">
                                                            @foreach ($categories as $category)
                                                                <option
                                                                    value="{{ $category->id }}"{{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                                                    {{ $category->name }}
                                                                    ({{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de método de pago --}}
                                                    <div class="mb-3">
                                                        <label for="payment_method_id">Método de pago:</label>
                                                        <select name="payment_method_id" id="payment_method_id">
                                                            @foreach ($paymentMethods as $paymentMethod)
                                                                <option
                                                                    value="{{ $paymentMethod->id }}"{{ old('payment_method_id', $transaction->payment_method_id) == $paymentMethod->id ? 'selected' : '' }}>
                                                                    {{ $paymentMethod->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de es histórica --}}
                                                    <div class="mb-3">
                                                        <label for="is_historical">¿Es histórica?</label>
                                                        <select name="is_historical" id="is_historical" class="form-select">
                                                            <option value="0"
                                                                {{ old('is_historical', $transaction->is_historical) == 0 ? 'selected' : '' }}>
                                                                No</option>
                                                            <option value="1"
                                                                {{ old('is_historical', $transaction->is_historical) == 1 ? 'selected' : '' }}>
                                                                Sí</option>
                                                        </select>
                                                    </div>

                                                    {{-- contenedor de descripción --}}
                                                    <div class="mb-3">
                                                        <label for="description">Descripción:</label><br>
                                                        <textarea name="description" id="description" rows="3">{{ old('description', $transaction->description) }}</textarea>
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
                                                        Actualizar categoria
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                            @empty

                                {{-- cuando no hay categorias registradas --}}
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                                        <h5 class="mt-3 mb-1">No hay transacciones registradas</h5>
                                        <p class="text-muted mb-3">
                                            Agrega tu primera transacción para comenzar.
                                        </p>

                                        {{-- btn crear transacciones --}}
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#createTransactionModal">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Crear nueva transacción
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

    {{-- Modal para eliminar las transaccioness --}}
    <div class="modal fade" id="deleteTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header border-0 pb-0">

                    {{-- btn cerrar modal --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>

                {{-- contenido del modal - eliminar transacción --}}
                <div class="modal-body text-center py-4">
                    <i class="bi bi-trash fs-1 text-danger mb-3 d-block"></i>
                    <p class="mb-1">¿Estás seguro que deseas eliminar la transacción:</p>
                    <h6 class="fw-bold" id="deleteTransactionName"></h6>
                    <p class="text-muted small mt-2 mb-0">Esta acción no se puede deshacer.</p>
                </div>

                {{-- contenedor botones del modal --}}
                <div class="modal-footer border-0 pt-0">
                    {{-- btn cancelar --}}
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    {{-- btn eliminar --}}
                    <form id="deleteTransactionForm" method="POST" class="d-inline">
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

    {{-- Modal para crear transacción --}}
    <div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nueva transacción
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf

                    <div class="modal-body">

                        {{-- fecha --}}
                        <div class="mb-3">
                            <label for="create_date">Fecha:</label>
                            <input type="date" name="date" id="create_date" class="form-control"
                                value="{{ old('date', date('Y-m-d')) }}">
                        </div>

                        {{-- tipo --}}
                        <div class="mb-3">
                            <label for="create_type">Tipo de transacción:</label>
                            <select name="type" id="create_type" class="form-select">
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Ingreso</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Gasto</option>
                            </select>
                        </div>

                        {{-- monto --}}
                        <div class="mb-3">
                            <label for="create_amount">Monto:</label>
                            <input type="number" step="0.01" name="amount" id="create_amount" class="form-control"
                                value="{{ old('amount') }}">
                        </div>

                        {{-- cuenta --}}
                        <div class="mb-3">
                            <label for="create_account_id">Cuenta:</label>
                            <select name="account_id" id="create_account_id" class="form-select">
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}"
                                        {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- categoría --}}
                        <div class="mb-3">
                            <label for="create_category_id">Categoría:</label>
                            <select name="category_id" id="create_category_id" class="form-select">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                        ({{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- método de pago --}}
                        <div class="mb-3">
                            <label for="create_payment_method_id">Método de pago:</label>
                            <select name="payment_method_id" id="create_payment_method_id" class="form-select">
                                @foreach ($paymentMethods as $paymentMethod)
                                    <option value="{{ $paymentMethod->id }}"
                                        {{ old('payment_method_id') == $paymentMethod->id ? 'selected' : '' }}>
                                        {{ $paymentMethod->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- is_historical --}}
                        <div class="mb-3">
                            <label for="create_is_historical">¿Es histórica?</label>
                            <select name="is_historical" id="create_is_historical" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>

                        {{-- descripción --}}
                        <div class="mb-3">
                            <label for="create_description">Descripción:</label>
                            <textarea name="description" id="create_description" rows="3" class="form-control">{{ old('description') }}</textarea>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar transacción
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <script>
            const deleteModal = document.getElementById('deleteTransactionModal');

            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // botón que disparó el modal

                const action = button.getAttribute('data-action');
                const name = button.getAttribute('data-name');

                // Asignar la URL correcta al formulario
                document.getElementById('deleteTransactionForm').action = action;

                // Mostrar el nombre de la transacción
                document.getElementById('deleteTransactionName').textContent = name;
            });
        </script>
    @endpush

@endsection
