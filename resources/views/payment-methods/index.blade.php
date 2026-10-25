@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/payment-methods.css') }}?v={{ filemtime(public_path('css/payment-methods.css')) }}">
@endpush

@section('content')
    @php
        // Si falló una validación, se vuelve a abrir el modal que la envió
        $reopen = $errors->any() ? old('_form') : null;
        $editId = old('_payment_method_id');
    @endphp

    <div class="gf-page">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Lista de métodos de pago</h1>
                <p>Administra tus métodos de pago registrados en GarridoFinance</p>
            </div>
            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                data-bs-target="#createPaymentMethodModal">
                <i class="bi bi-plus-circle"></i> Crear nuevo método de pago
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

        {{-- Tabla de métodos de pago --}}
        <section class="gf-panel">
            <div class="gf-panel-head">
                <div class="gf-panel-title">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-credit-card"></i></span>
                    <div>
                        <h2>Métodos de pago registrados</h2>
                        <p>{{ $paymentMethods->total() }} {{ $paymentMethods->total() === 1 ? 'método registrado' : 'métodos registrados' }}</p>
                    </div>
                </div>
            </div>

            <div class="gf-table gf-payment-methods-table">
                <div class="gf-table-head">
                    <span>Tipo de método de pago</span>
                    <span>Estado</span>
                    <span class="gf-cell-end">Opciones</span>
                </div>

                @forelse ($paymentMethods as $method)
                    @php $kind = $method->kind_info; @endphp
                    <div class="gf-table-row">
                        {{-- Nombre --}}
                        <div class="gf-cell-main">
                            <span class="gf-icon gf-tone-{{ $kind['tone'] }}"><i class="bi bi-credit-card"></i></span>
                            <div>
                                <p class="gf-cell-title">{{ $method->name }}</p>
                                <p class="gf-cell-meta">{{ $kind['subtitle'] }}</p>
                            </div>
                        </div>

                        {{-- Estado --}}
                        @if ($method->is_active)
                            <span class="gf-pill gf-pill-income"><span class="gf-pill-dot"></span>Activa</span>
                        @else
                            <span class="gf-pill gf-pill-muted"><span class="gf-pill-dot"></span>Inactiva</span>
                        @endif

                        {{-- Opciones --}}
                        <div class="gf-row-actions">
                            <button type="button" class="gf-action" title="Editar" aria-label="Editar {{ $method->name }}"
                                data-bs-toggle="modal" data-bs-target="#editPaymentMethodModal"
                                data-id="{{ $method->id }}"
                                data-action="{{ route('payment-methods.update', $method) }}"
                                data-name="{{ $method->name }}"
                                data-active="{{ $method->is_active ? 1 : 0 }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="gf-action gf-action-danger" title="Eliminar"
                                aria-label="Eliminar {{ $method->name }}"
                                data-bs-toggle="modal" data-bs-target="#deletePaymentMethodModal"
                                data-action="{{ route('payment-methods.destroy', $method) }}"
                                data-name="{{ $method->name }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="gf-table-empty">
                        <h3>No hay métodos de pago registrados</h3>
                        <p>Agrega tu primer método de pago para comenzar.</p>
                        <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createPaymentMethodModal">
                            <i class="bi bi-plus-circle"></i> Crear nuevo método de pago
                        </button>
                    </div>
                @endforelse
            </div>

            <x-pagination :paginator="$paymentMethods" label="métodos de pago" />
        </section>

    </div>

    {{-- Modal: crear método de pago --}}
    <div class="modal fade gf-modal" id="createPaymentMethodModal" tabindex="-1"
        aria-labelledby="createPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-credit-card"></i></span>
                    <h5 class="modal-title" id="createPaymentMethodModalLabel">Crear nuevo método de pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('payment-methods.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_form" value="create">

                    @php $showErrors = $reopen === 'create'; @endphp
                    <div class="modal-body">
                        <div class="gf-form-group">
                            <label for="create_name">Nombre del método de pago</label>
                            <input type="text" name="name" id="create_name"
                                class="form-control {{ $showErrors && $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $showErrors ? old('name') : '' }}" placeholder="Ej. Efectivo, Tarjeta de débito BBVA">
                            @if ($showErrors)
                                @error('name') <span class="gf-form-error">{{ $message }}</span> @enderror
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
                            <i class="bi bi-check-lg"></i> Guardar método de pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar método de pago (uno solo, se llena con los datos del botón) --}}
    <div class="modal fade gf-modal" id="editPaymentMethodModal" tabindex="-1"
        aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-pencil"></i></span>
                    <h5 class="modal-title" id="editPaymentMethodModalLabel">Editar método de pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                @php $showErrors = $reopen === 'edit'; @endphp
                <form id="editPaymentMethodForm" method="POST"
                    action="{{ $showErrors && $editId ? route('payment-methods.update', $editId) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="_payment_method_id" id="edit_payment_method_id"
                        value="{{ $showErrors ? $editId : '' }}">

                    <div class="modal-body">
                        <div class="gf-form-group">
                            <label for="edit_name">Nombre del método de pago</label>
                            <input type="text" name="name" id="edit_name"
                                class="form-control {{ $showErrors && $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ $showErrors ? old('name') : '' }}">
                            @if ($showErrors)
                                @error('name') <span class="gf-form-error">{{ $message }}</span> @enderror
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
                            <i class="bi bi-check-lg"></i> Actualizar método de pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: confirmar eliminación --}}
    <div class="modal fade gf-modal" id="deletePaymentMethodModal" tabindex="-1"
        aria-labelledby="deletePaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body gf-confirm">
                    <span class="gf-icon gf-tone-expense"><i class="bi bi-trash3"></i></span>
                    <h3 id="deletePaymentMethodModalLabel">¿Eliminar este método de pago?</h3>
                    <p>Vas a eliminar <strong id="deletePaymentMethodName"></strong>.</p>
                    <p>También se eliminarán todas las transacciones pagadas con él. Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deletePaymentMethodForm" method="POST">
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
            document.getElementById('editPaymentMethodModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return; // reabierto tras un error: ya trae los datos enviados

                document.getElementById('editPaymentMethodForm').setAttribute('action', button.dataset.action);
                document.getElementById('edit_payment_method_id').value = button.dataset.id;
                document.getElementById('edit_name').value = button.dataset.name;
                document.getElementById('edit_is_active').value = button.dataset.active;

                // Limpia errores de un intento anterior
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.gf-form-error').forEach(el => el.remove());
            });

            // Eliminar: pasa la ruta y el nombre del método de pago al modal
            document.getElementById('deletePaymentMethodModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('deletePaymentMethodName').textContent = button.dataset.name;
                document.getElementById('deletePaymentMethodForm').setAttribute('action', button.dataset.action);
            });

            // Si el servidor devolvió errores, reabre el modal correspondiente
            @if ($reopen === 'create')
                new bootstrap.Modal(document.getElementById('createPaymentMethodModal')).show();
            @elseif ($reopen === 'edit')
                new bootstrap.Modal(document.getElementById('editPaymentMethodModal')).show();
            @endif
        })();
    </script>
@endpush
