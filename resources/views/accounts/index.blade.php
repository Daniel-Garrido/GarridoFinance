@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/accounts.css') }}?v={{ filemtime(public_path('css/accounts.css')) }}">
@endpush

@section('content')
    @php
        $types = \App\Models\Account::TYPES;

        // Si falló una validación, se vuelve a abrir el modal que la envió
        $reopen = $errors->any() ? old('_form') : null;
        $editId = old('_account_id');
    @endphp

    <div class="gf-page">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Lista de cuentas</h1>
                <p>Administra tus cuentas registradas en GarridoFinance</p>
            </div>
            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                <i class="bi bi-plus-circle"></i> Crear nueva cuenta
            </button>
        </header>

        {{-- Alerta de éxito --}}
        @if (session('success'))
            <div class="gf-alert alert alert-dismissible fade show mb-0" role="alert">
                <i class="bi bi-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        {{-- Tabla de cuentas --}}
        <section class="gf-panel">
            <div class="gf-panel-head">
                <div class="gf-panel-title">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-credit-card"></i></span>
                    <div>
                        <h2>Cuentas registradas</h2>
                        <p>{{ $accounts->total() }} {{ $accounts->total() === 1 ? 'cuenta encontrada' : 'cuentas encontradas' }}</p>
                    </div>
                </div>

                <form method="GET" action="{{ route('accounts.index') }}" id="accountSearchForm">
                    <label class="gf-search">
                        <i class="bi bi-search"></i>
                        <input type="search" name="q" value="{{ $search }}" placeholder="Buscar cuenta"
                            aria-label="Buscar cuenta">
                    </label>
                </form>
            </div>

            <div class="gf-table gf-accounts-table">
                <div class="gf-table-head">
                    <span>Nombre de la cuenta</span>
                    <span>Saldo total</span>
                    <span>Tipo de cuenta</span>
                    <span>Estado</span>
                    <span class="gf-cell-end">Opciones</span>
                </div>

                @forelse ($accounts as $account)
                    @php $info = $account->type_info; @endphp
                    <div class="gf-table-row">
                        {{-- Nombre --}}
                        <div class="gf-cell-main">
                            <span class="gf-icon gf-tone-{{ $info['tone'] }}"><i class="bi bi-credit-card"></i></span>
                            <div>
                                <p class="gf-cell-title">{{ $account->name }}</p>
                                <p class="gf-cell-meta">{{ $info['subtitle'] }}</p>
                            </div>
                        </div>

                        {{-- Saldo --}}
                        <span class="gf-cell-amount {{ $account->balance < 0 ? 'gf-amount-expense' : '' }}">
                            {{ $account->balance < 0 ? '−' : '' }}${{ number_format(abs($account->balance), 2) }} MXN
                        </span>

                        {{-- Tipo --}}
                        <span class="gf-pill gf-pill-{{ $info['tone'] }}">{{ $info['label'] }}</span>

                        {{-- Estado --}}
                        @if ($account->is_active)
                            <span class="gf-pill gf-pill-income"><span class="gf-pill-dot"></span>Activa</span>
                        @else
                            <span class="gf-pill gf-pill-muted"><span class="gf-pill-dot"></span>Inactiva</span>
                        @endif

                        {{-- Opciones --}}
                        <div class="gf-row-actions">
                            <button type="button" class="gf-action" title="Editar" aria-label="Editar {{ $account->name }}"
                                data-bs-toggle="modal" data-bs-target="#editAccountModal"
                                data-id="{{ $account->id }}"
                                data-action="{{ route('accounts.update', $account) }}"
                                data-name="{{ $account->name }}"
                                data-type="{{ $account->type }}"
                                data-active="{{ $account->is_active ? 1 : 0 }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="gf-action gf-action-danger" title="Eliminar"
                                aria-label="Eliminar {{ $account->name }}"
                                data-bs-toggle="modal" data-bs-target="#deleteAccountModal"
                                data-action="{{ route('accounts.destroy', $account) }}"
                                data-name="{{ $account->name }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="gf-table-empty">
                        @if ($search !== '')
                            <h3>Sin resultados</h3>
                            <p>No hay cuentas que coincidan con «{{ $search }}». Ajusta la búsqueda.</p>
                        @else
                            <h3>No hay cuentas registradas</h3>
                            <p>Agrega tu primera cuenta para comenzar.</p>
                            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createAccountModal">
                                <i class="bi bi-plus-circle"></i> Crear nueva cuenta
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <x-pagination :paginator="$accounts" label="cuentas" />
        </section>

    </div>

    {{-- Modal: crear cuenta --}}
    <div class="modal fade gf-modal" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-credit-card"></i></span>
                    <h5 class="modal-title" id="createAccountModalLabel">Crear nueva cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('accounts.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_form" value="create">

                    @php $showErrors = $reopen === 'create'; @endphp
                    <div class="modal-body">
                        <div class="gf-form-group">
                            <label for="create_name">Nombre de la cuenta</label>
                            <input type="text" name="name" id="create_name"
                                class="form-control {{ $showErrors && $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $showErrors ? old('name') : '' }}" placeholder="Ej. Efectivo, BBVA, Santander">
                            @if ($showErrors)
                                @error('name') <span class="gf-form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="gf-form-group">
                            <label for="create_type">Tipo de cuenta</label>
                            <select name="type" id="create_type"
                                class="form-select {{ $showErrors && $errors->has('type') ? 'is-invalid' : '' }}">
                                @foreach ($types as $value => $type)
                                    <option value="{{ $value }}" @selected($showErrors && old('type') === $value)>{{ $type['label'] }}</option>
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
                            <i class="bi bi-check-lg"></i> Guardar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar cuenta (uno solo, se llena con los datos del botón) --}}
    <div class="modal fade gf-modal" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-pencil"></i></span>
                    <h5 class="modal-title" id="editAccountModalLabel">Editar cuenta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                @php $showErrors = $reopen === 'edit'; @endphp
                <form id="editAccountForm" method="POST"
                    action="{{ $showErrors && $editId ? route('accounts.update', $editId) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="_account_id" id="edit_account_id" value="{{ $showErrors ? $editId : '' }}">

                    <div class="modal-body">
                        <div class="gf-form-group">
                            <label for="edit_name">Nombre de la cuenta</label>
                            <input type="text" name="name" id="edit_name"
                                class="form-control {{ $showErrors && $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $showErrors ? old('name') : '' }}">
                            @if ($showErrors)
                                @error('name') <span class="gf-form-error">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <div class="gf-form-group">
                            <label for="edit_type">Tipo de cuenta</label>
                            <select name="type" id="edit_type"
                                class="form-select {{ $showErrors && $errors->has('type') ? 'is-invalid' : '' }}">
                                @foreach ($types as $value => $type)
                                    <option value="{{ $value }}" @selected($showErrors && old('type') === $value)>{{ $type['label'] }}</option>
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
                            <i class="bi bi-check-lg"></i> Actualizar cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: confirmar eliminación --}}
    <div class="modal fade gf-modal" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body gf-confirm">
                    <span class="gf-icon gf-tone-expense"><i class="bi bi-trash3"></i></span>
                    <h3 id="deleteAccountModalLabel">¿Eliminar esta cuenta?</h3>
                    <p>Vas a eliminar <strong id="deleteAccountName"></strong>.</p>
                    <p>También se eliminarán sus transacciones y transferencias. Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteAccountForm" method="POST">
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
            // Buscador: al borrar con la "x" del campo se vuelve a la lista completa
            const searchForm = document.getElementById('accountSearchForm');
            searchForm.querySelector('input').addEventListener('search', function () {
                if (this.value === '') searchForm.submit();
            });

            // Editar: llena el modal con los datos del botón que lo abrió
            document.getElementById('editAccountModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return; // reabierto tras un error: ya trae los datos enviados

                document.getElementById('editAccountForm').setAttribute('action', button.dataset.action);
                document.getElementById('edit_account_id').value = button.dataset.id;
                document.getElementById('edit_name').value = button.dataset.name;
                document.getElementById('edit_type').value = button.dataset.type;
                document.getElementById('edit_is_active').value = button.dataset.active;

                // Limpia errores de un intento anterior
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.gf-form-error').forEach(el => el.remove());
            });

            // Eliminar: pasa la ruta y el nombre de la cuenta al modal
            document.getElementById('deleteAccountModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('deleteAccountName').textContent = button.dataset.name;
                document.getElementById('deleteAccountForm').setAttribute('action', button.dataset.action);
            });

            // Si el servidor devolvió errores, reabre el modal correspondiente
            @if ($reopen === 'create')
                new bootstrap.Modal(document.getElementById('createAccountModal')).show();
            @elseif ($reopen === 'edit')
                new bootstrap.Modal(document.getElementById('editAccountModal')).show();
            @endif
        })();
    </script>
@endpush
