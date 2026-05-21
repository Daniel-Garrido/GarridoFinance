@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        {{-- contenedor header principal seccion de categorias --}}
        <div class="d-flex justify-content-between align-items-center mb-4 p-4 ">
            <div>
                <h2 class="fw-bold mb-1">Lista de categorías</h2>
                <p class="text-muted mb-0">Administra tus categorías registradas en GarridoFinance</p>
            </div>

            {{-- btn crear categoria --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-circle me-1"></i>
                Crear nueva categoría
            </button>
        </div>

        {{-- Alerta cueando se crea una categoría --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- contenedor de la tabla de categorías --}}
        <div class="card border-0 shadow-sm rounded-4">

            <div class="card-header bg-white border-0 py-3">
                <h3 class="mb-0 fw-semibold">
                    <i class="bi bi-wallet2 me-1"></i>
                    Categorías registradas
                </h3>
            </div>

            {{-- cuerpo de la tabla de categorías --}}
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">
                        {{-- cabecera de las tablas --}}
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nombre de la categoría</th>
                                <th>Tipo de categoría</th>
                                <th>Estado</th>
                                <th class="text-end pe-4">Opciones</th>
                            </tr>
                        </thead>

                        {{-- cuerpo de la tabla de categorías --}}
                        <tbody>

                            @forelse($categories as $category)
                                <tr>
                                    {{-- nombre de la categoría --}}
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="account-icon me-3">
                                                <i class="bi bi-wallet2"></i>
                                            </div>

                                            <div>
                                                <h6 class="mb-0 fw-semibold">
                                                    {{ $category->name }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- tipo de categoría --}}
                                    <td>
                                        {{ $category->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                    </td>

                                    {{-- si esta activa la cuenta --}}
                                    <td>
                                        @if ($category->is_active)
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

                                        {{--  btn para editar la categoria --}}
                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editCategoryModal{{ $category->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        {{-- opcion para eliminar la categoria --}}
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#deleteCategoryModal"
                                            data-action="{{ route('categories.destroy', $category) }}"
                                            data-name="{{ $category->name }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>

                                </tr>

                                {{-- Modal para  editar categorias --}}
                                <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1"
                                    aria-hidden="true">

                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header">
                                                {{-- titulo modal --}}
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    Editar categoria
                                                </h5>
                                                {{-- btn cerrar modal --}}
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            {{-- Formulario para editar categoria --}}
                                            <form action="{{ route('categories.update', $category) }}" method="POST">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">

                                                    {{-- contenedor de nombre de la categoria --}}
                                                    <div class="mb-3">
                                                        <label for="name{{ $category->id }}" class="form-label">
                                                            Nombre de la categoria
                                                        </label>

                                                        <input type="text" name="name" id="name{{ $category->id }}"
                                                            class="form-control"
                                                            value="{{ old('name', $category->name) }}">
                                                    </div>

                                                    {{-- contenedor de tipo de categoria --}}
                                                    <div class="mb-3">

                                                        <label for="type{{ $category->id }}" class="form-label">
                                                            Tipo de categoria
                                                        </label>

                                                        <select name="type" id="type{{ $category->id }}"
                                                            class="form-select">

                                                            <option value="income"
                                                                {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>
                                                                Ingreso
                                                            </option>

                                                            <option value="expense"
                                                                {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>
                                                                Gasto
                                                            </option>

                                                        </select>
                                                    </div>

                                                    {{-- contenedor de estado de la categoria --}}
                                                    <div class="mb-3">

                                                        <label for="is_active{{ $category->id }} "class="form-label">
                                                            Estado de la categoria
                                                        </label>

                                                        <select name="is_active" id="is_active{{ $category->id }}"
                                                            class="form-select">

                                                            <option
                                                                value="1"{{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>
                                                                Activa
                                                            </option>

                                                            <option
                                                                value="0"{{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>
                                                                Inactiva
                                                            </option>

                                                        </select>
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

                                {{-- Modal para eliminar categorias --}}
                                <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content rounded-4 border-0 shadow">

                                            <div class="modal-header border-0 pb-0">

                                                {{-- btn cerrar modal --}}
                                                <button type="button" class="btn-close" data-bs-dismiss="modal">
                                                </button>
                                            </div>

                                            {{-- contenido del modal - eliminar categoria --}}
                                            <div class="modal-body text-center py-4">
                                                <i class="bi bi-trash fs-1 text-danger mb-3 d-block"></i>
                                                <p class="mb-1">¿Estás seguro que deseas eliminar la categoria:</p>
                                                <h6 class="fw-bold" id="deleteCategoryName"></h6>
                                                <p class="text-muted small mt-2 mb-0">Esta acción no se puede deshacer.</p>
                                            </div>

                                            {{-- contenedor botones del modal --}}
                                            <div class="modal-footer border-0 pt-0">
                                                {{-- btn cancelar --}}
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                                    Cancelar
                                                </button>

                                                {{-- btn eliminar --}}
                                                <form id="deleteCategoryForm" method="POST" class="d-inline">
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

                                {{-- cuando no hay categorias registradas --}}
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-folder-x fs-1 text-muted"></i>
                                        <h5 class="mt-3 mb-1">No hay categorias registradas</h5>
                                        <p class="text-muted mb-3">
                                            Agrega tu primera categoria para comenzar.
                                        </p>

                                        {{-- btn crear categorias --}}
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#createCategoryModal">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Crear nueva categoria
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

    {{-- Modal para crear categorias --}}
    <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createCategoryModalLabel">
                        <i class="bi bi-wallet2 me-1"></i>
                        Crear nueva categoría
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST">
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
                            <label for="name" class="form-label">Nombre de la categoría</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ old('name') }}" placeholder="Ej. Efectivo, BBVA, Santander">
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Tipo de categoría</label>
                
                            <select name="type" id="type{{ $category->id }}" class="form-select">

                                <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>
                                    Ingreso
                                </option>

                                <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>
                                    Gasto
                                </option>
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
            const deleteModal = document.getElementById('deleteCategoryModal') // Obtener referencia al modal de eliminación

            deleteModal.addEventListener('show.bs.modal', function(event) {
                // botón que disparó el modal
                const button = event.relatedTarget

                // leer los datos del botón
                const action = button.getAttribute('data-action')
                const name = button.getAttribute('data-name')

                // inyectarlos en el modal
                document.getElementById('deleteCategoryName').textContent = name
                document.getElementById('deleteCategoryForm').setAttribute('action', action)
            })
        </script>
    @endpush

@endsection
