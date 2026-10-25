@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categories.css') }}?v={{ filemtime(public_path('css/categories.css')) }}">
@endpush

@section('content')
    @php
        $types = \App\Models\Category::TYPES;
        $filterLabel = $type ? $types[$type]['filter'] : 'Todas las categorías';

        // Si falló una validación, se vuelve a abrir el modal que la envió
        $reopen = $errors->any() ? old('_form') : null;
        $editId = old('_category_id');
    @endphp

    <div class="gf-page">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Lista de categorías</h1>
                <p>Administra tus categorías registradas en GarridoFinance</p>
            </div>

            <div class="gf-header-tools">
                {{-- Filtro por tipo: cada opción es un enlace, la paginación vuelve a la página 1 --}}
                <div class="dropdown">
                    <button type="button" class="gf-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <span>{{ $filterLabel }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <ul class="dropdown-menu gf-dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ $type ? '' : 'active' }}" href="{{ route('categories.index') }}">
                                Todas las categorías
                            </a>
                        </li>
                        @foreach ($types as $value => $info)
                            <li>
                                <a class="dropdown-item {{ $type === $value ? 'active' : '' }}"
                                    href="{{ route('categories.index', ['type' => $value]) }}">
                                    {{ $info['filter'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createCategoryModal">
                    <i class="bi bi-plus-circle"></i> Crear nueva categoría
                </button>
            </div>
        </header>

        {{-- Alerta de éxito --}}
        @if (session('success'))
            <div class="gf-alert alert alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Tabla de categorías --}}
        <section class="gf-panel">
            <div class="gf-panel-head">
                <div class="gf-panel-title">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-tag"></i></span>
                    <div>
                        <h2>Categorías registradas</h2>
                        <p>{{ $categories->total() }} {{ $categories->total() === 1 ? 'categoría encontrada' : 'categorías encontradas' }}</p>
                    </div>
                </div>
            </div>

            <div class="gf-table gf-categories-table">
                <div class="gf-table-head">
                    <span>Nombre de la categoría</span>
                    <span>Tipo de categoría</span>
                    <span>Estado</span>
                    <span class="gf-cell-end">Opciones</span>
                </div>

                @forelse ($categories as $category)
                    @php $info = $category->type_info; @endphp
                    <div class="gf-table-row">
                        {{-- Nombre --}}
                        <div class="gf-cell-main">
                            <span class="gf-icon {{ $category->type === 'income' ? 'gf-tone-income' : 'gf-tone-primary' }}">
                                <i class="bi bi-tag"></i>
                            </span>
                            <div>
                                <p class="gf-cell-title">{{ $category->name }}</p>
                                <p class="gf-cell-meta">{{ $info['subtitle'] }}</p>
                            </div>
                        </div>

                        {{-- Tipo --}}
                        <span class="gf-pill gf-pill-{{ $info['tone'] }}"><span class="gf-pill-dot"></span>{{ $info['label'] }}</span>

                        {{-- Estado --}}
                        @if ($category->is_active)
                            <span class="gf-pill gf-pill-income"><span class="gf-pill-dot"></span>Activa</span>
                        @else
                            <span class="gf-pill gf-pill-muted"><span class="gf-pill-dot"></span>Inactiva</span>
                        @endif

                        {{-- Opciones --}}
                        <div class="gf-row-actions">
                            <button type="button" class="gf-action" title="Editar" aria-label="Editar {{ $category->name }}"
                                data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                data-id="{{ $category->id }}"
                                data-action="{{ route('categories.update', $category) }}"
                                data-name="{{ $category->name }}"
                                data-type="{{ $category->type }}"
                                data-active="{{ $category->is_active ? 1 : 0 }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="gf-action gf-action-danger" title="Eliminar"
                                aria-label="Eliminar {{ $category->name }}"
                                data-bs-toggle="modal" data-bs-target="#deleteCategoryModal"
                                data-action="{{ route('categories.destroy', $category) }}"
                                data-name="{{ $category->name }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="gf-table-empty">
                        @if ($type)
                            <h3>Sin resultados</h3>
                            <p>No hay categorías de {{ mb_strtolower($types[$type]['filter']) }}. Ajusta el filtro de tipo de categoría.</p>
                        @else
                            <h3>No hay categorías registradas</h3>
                            <p>Agrega tu primera categoría para comenzar.</p>
                            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createCategoryModal">
                                <i class="bi bi-plus-circle"></i> Crear nueva categoría
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <x-pagination :paginator="$categories" label="categorías" />
        </section>

    </div>

    {{-- Modal: crear categoría --}}
    <div class="modal fade gf-modal" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-tag"></i></span>
                    <h5 class="modal-title" id="createCategoryModalLabel">Crear nueva categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_form" value="create">

                    @php $showErrors = $reopen === 'create'; @endphp
                    <div class="modal-body">
                        <div class="gf-form-group">
                            <label for="create_name">Nombre de la categoría</label>
                            <input type="text" name="name" id="create_name"
                                class="form-control {{ $showErrors && $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $showErrors ? old('name') : '' }}" placeholder="Ej. Salario, Comida, Transporte">
                            @if ($showErrors)
                                @error('name') <span class="gf-form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="gf-form-group">
                            <label for="create_type">Tipo de categoría</label>
                            <select name="type" id="create_type"
                                class="form-select {{ $showErrors && $errors->has('type') ? 'is-invalid' : '' }}">
                                @foreach ($types as $value => $info)
                                    {{-- Sin error, se preselecciona el tipo del filtro activo --}}
                                    <option value="{{ $value }}" @selected($showErrors ? old('type') === $value : $type === $value)>{{ $info['label'] }}</option>
                                @endforeach
                            </select>
                            @if ($showErrors)
                                @error('type') <span class="gf-form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="gf-form-group">
                            <label for="create_is_active">Estado</label>
                            <select name="is_active" id="create_is_active" class="form-select">
                                <option value="1" @selected(! $showErrors || old('is_active') === '1')>Activa</option>
                                <option value="0" @selected($showErrors && old('is_active') === '0')>Inactiva</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar categoría (uno solo, se llena con los datos del botón) --}}
    <div class="modal fade gf-modal" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-pencil"></i></span>
                    <h5 class="modal-title" id="editCategoryModalLabel">Editar categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                @php $showErrors = $reopen === 'edit'; @endphp
                <form id="editCategoryForm" method="POST"
                    action="{{ $showErrors && $editId ? route('categories.update', $editId) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="_category_id" id="edit_category_id" value="{{ $showErrors ? $editId : '' }}">

                    <div class="modal-body">
                        <div class="gf-form-group">
                            <label for="edit_name">Nombre de la categoría</label>
                            <input type="text" name="name" id="edit_name"
                                class="form-control {{ $showErrors && $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $showErrors ? old('name') : '' }}">
                            @if ($showErrors)
                                @error('name') <span class="gf-form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="gf-form-group">
                            <label for="edit_type">Tipo de categoría</label>
                            <select name="type" id="edit_type"
                                class="form-select {{ $showErrors && $errors->has('type') ? 'is-invalid' : '' }}">
                                @foreach ($types as $value => $info)
                                    <option value="{{ $value }}" @selected($showErrors && old('type') === $value)>{{ $info['label'] }}</option>
                                @endforeach
                            </select>
                            @if ($showErrors)
                                @error('type') <span class="gf-form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="gf-form-group">
                            <label for="edit_is_active">Estado</label>
                            <select name="is_active" id="edit_is_active" class="form-select">
                                <option value="1" @selected($showErrors && old('is_active') === '1')>Activa</option>
                                <option value="0" @selected($showErrors && old('is_active') === '0')>Inactiva</option>
                            </select>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-primary">
                            <i class="bi bi-check-lg"></i> Actualizar categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: confirmar eliminación --}}
    <div class="modal fade gf-modal" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body gf-confirm">
                    <span class="gf-icon gf-tone-expense"><i class="bi bi-trash3"></i></span>
                    <h3 id="deleteCategoryModalLabel">¿Eliminar esta categoría?</h3>
                    <p>Vas a eliminar <strong id="deleteCategoryName"></strong>.</p>
                    <p>También se eliminarán todas sus transacciones. Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteCategoryForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="gf-btn gf-btn-danger">
                            <i class="bi bi-trash3"></i> Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            // Editar: llena el modal con los datos del botón que lo abrió
            document.getElementById('editCategoryModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return; // reabierto tras un error: ya trae los datos enviados

                document.getElementById('editCategoryForm').setAttribute('action', button.dataset.action);
                document.getElementById('edit_category_id').value = button.dataset.id;
                document.getElementById('edit_name').value = button.dataset.name;
                document.getElementById('edit_type').value = button.dataset.type;
                document.getElementById('edit_is_active').value = button.dataset.active;

                // Limpia errores de un intento anterior
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.gf-form-error').forEach(el => el.remove());
            });

            // Eliminar: pasa la ruta y el nombre de la categoría al modal
            document.getElementById('deleteCategoryModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('deleteCategoryName').textContent = button.dataset.name;
                document.getElementById('deleteCategoryForm').setAttribute('action', button.dataset.action);
            });

            // Si el servidor devolvió errores, reabre el modal correspondiente
            @if ($reopen === 'create')
                new bootstrap.Modal(document.getElementById('createCategoryModal')).show();
            @elseif ($reopen === 'edit')
                new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
            @endif
        })();
    </script>
@endpush
