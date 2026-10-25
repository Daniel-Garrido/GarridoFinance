@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/transfers.css') }}?v={{ filemtime(public_path('css/transfers.css')) }}">
@endpush

@section('content')
    @php
        $hasFilters = request()->filled('period') || $search !== '';

        // Si falló una validación, se vuelve a abrir el modal que la envió
        $reopen = $errors->any() ? old('_form') : null;
        $editId = old('_transfer_id');
    @endphp

    <div class="gf-page">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Lista de transferencias</h1>
                <p>Administra tus transferencias registradas en GarridoFinance</p>
            </div>
            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                data-bs-target="#createTransferModal">
                <i class="bi bi-plus-circle"></i> Crear una transferencia
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

        {{-- Filtros --}}
        <form method="GET" action="{{ route('transfers.index') }}" id="filterForm" class="gf-filter gf-card">
            <x-period-filter />

            <div class="gf-filter-actions">
                <button type="submit" class="gf-btn gf-btn-primary">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('transfers.index') }}" class="gf-btn gf-btn-icon" title="Limpiar filtros"
                    aria-label="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>

        {{-- Tabla de transferencias --}}
        <section class="gf-panel">
            <div class="gf-panel-head">
                <div class="gf-panel-title">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-arrow-left-right"></i></span>
                    <div>
                        <h2>Transferencias registradas</h2>
                        <p>{{ $transfers->total() }} {{ $transfers->total() === 1 ? 'transferencia encontrada' : 'transferencias encontradas' }}</p>
                    </div>
                </div>

                {{-- El buscador pertenece al formulario de filtros: se envía junto con el periodo --}}
                <label class="gf-search gf-search-wide">
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" form="filterForm" value="{{ $search }}"
                        placeholder="Buscar por cuenta o descripción" aria-label="Buscar por cuenta o descripción">
                </label>
            </div>

            <div class="gf-table gf-table-compact gf-transfers-table">
                <div class="gf-table-head">
                    <span>Fecha</span>
                    <span>Monto</span>
                    <span>Cuenta origen</span>
                    <span>Cuenta destino</span>
                    <span>Descripción</span>
                    <span class="gf-cell-end">Opciones</span>
                </div>

                @forelse ($transfers as $transfer)
                    @php $amountLabel = '$' . number_format($transfer->amount, 2) . ' MXN'; @endphp
                    <div class="gf-table-row">
                        {{-- Fecha --}}
                        <div class="gf-date-cell" title="{{ $transfer->date->format('Y-m-d') }}">
                            <strong>{{ $transfer->date_label }}</strong>
                            <span>{{ $transfer->date_hint }}</span>
                        </div>

                        {{-- Monto --}}
                        <span class="gf-cell-amount">{{ $amountLabel }}</span>

                        {{-- Cuenta origen (sale el dinero) --}}
                        <div class="gf-cell-account">
                            <span class="gf-icon gf-tone-expense"><i class="bi bi-arrow-down"></i></span>
                            <span class="gf-cell-text gf-cell-strong" title="{{ $transfer->fromAccount?->name }}">{{ $transfer->fromAccount?->name }}</span>
                        </div>

                        {{-- Cuenta destino (entra el dinero) --}}
                        <div class="gf-cell-account">
                            <span class="gf-icon gf-tone-income"><i class="bi bi-arrow-up"></i></span>
                            <span class="gf-cell-text gf-cell-strong" title="{{ $transfer->toAccount?->name }}">{{ $transfer->toAccount?->name }}</span>
                        </div>

                        {{-- Descripción --}}
                        <span class="gf-cell-text gf-cell-muted" title="{{ $transfer->description }}">{{ $transfer->description ?: '—' }}</span>

                        {{-- Opciones --}}
                        <div class="gf-row-actions">
                            <button type="button" class="gf-action" title="Editar" aria-label="Editar transferencia"
                                data-bs-toggle="modal" data-bs-target="#editTransferModal"
                                data-id="{{ $transfer->id }}"
                                data-action="{{ route('transfers.update', $transfer) }}"
                                data-date="{{ $transfer->date->format('Y-m-d') }}"
                                data-amount="{{ $transfer->amount }}"
                                data-from="{{ $transfer->from_account_id }}"
                                data-to="{{ $transfer->to_account_id }}"
                                data-description="{{ $transfer->description }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="gf-action gf-action-danger" title="Eliminar"
                                aria-label="Eliminar transferencia"
                                data-bs-toggle="modal" data-bs-target="#deleteTransferModal"
                                data-action="{{ route('transfers.destroy', $transfer) }}"
                                data-summary="{{ 'la transferencia de ' . $amountLabel . ' de ' . ($transfer->fromAccount?->name ?? '—') . ' a ' . ($transfer->toAccount?->name ?? '—') . ' del ' . $transfer->date_label }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="gf-table-empty">
                        @if ($hasFilters)
                            <h3>Sin transferencias</h3>
                            <p>Ajusta el periodo o la búsqueda.</p>
                        @else
                            <h3>No hay transferencias registradas</h3>
                            <p>Agrega tu primera transferencia para comenzar.</p>
                            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createTransferModal">
                                <i class="bi bi-plus-circle"></i> Crear una transferencia
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <x-pagination :paginator="$transfers" label="transferencias" />
        </section>

    </div>

    {{-- Modal: crear transferencia --}}
    <div class="modal fade gf-modal" id="createTransferModal" tabindex="-1" aria-labelledby="createTransferModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-arrow-left-right"></i></span>
                    <h5 class="modal-title" id="createTransferModalLabel">Crear una transferencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('transfers.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_form" value="create">

                    <div class="modal-body">
                        @include('transfers.partials._form-fields', [
                            'prefix' => 'create',
                            'showErrors' => $reopen === 'create',
                            'onlyActive' => true,
                            'values' => ['date' => now()->toDateString()],
                        ])
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar transferencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar transferencia (uno solo, se llena con los datos del botón) --}}
    <div class="modal fade gf-modal" id="editTransferModal" tabindex="-1" aria-labelledby="editTransferModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-pencil"></i></span>
                    <h5 class="modal-title" id="editTransferModalLabel">Editar transferencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="editTransferForm" method="POST"
                    action="{{ $reopen === 'edit' && $editId ? route('transfers.update', $editId) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="_transfer_id" id="edit_transfer_id"
                        value="{{ $reopen === 'edit' ? $editId : '' }}">

                    <div class="modal-body">
                        @include('transfers.partials._form-fields', [
                            'prefix' => 'edit',
                            'showErrors' => $reopen === 'edit',
                            'onlyActive' => false,
                            'values' => [],
                        ])
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-primary">
                            <i class="bi bi-check-lg"></i> Actualizar transferencia
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: confirmar eliminación --}}
    <div class="modal fade gf-modal" id="deleteTransferModal" tabindex="-1" aria-labelledby="deleteTransferModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body gf-confirm">
                    <span class="gf-icon gf-tone-expense"><i class="bi bi-trash3"></i></span>
                    <h3 id="deleteTransferModalLabel">¿Eliminar esta transferencia?</h3>
                    <p>Vas a eliminar <strong id="deleteTransferSummary"></strong>.</p>
                    <p>El saldo de ambas cuentas se recalculará. Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteTransferForm" method="POST">
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
    <script src="{{ asset('js/filters.js') }}?v={{ filemtime(public_path('js/filters.js')) }}"></script>
    <script>
        (function () {
            // Editar: llena el modal con los datos del botón que lo abrió
            document.getElementById('editTransferModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return; // reabierto tras un error: ya trae los datos enviados

                const d = button.dataset;
                document.getElementById('editTransferForm').setAttribute('action', d.action);
                document.getElementById('edit_transfer_id').value = d.id;
                document.getElementById('edit_date').value = d.date;
                document.getElementById('edit_amount').value = d.amount;
                document.getElementById('edit_from_account_id').value = d.from;
                document.getElementById('edit_to_account_id').value = d.to;
                document.getElementById('edit_description').value = d.description;

                // Limpia errores de un intento anterior
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.gf-form-error').forEach(el => el.remove());
            });

            // Eliminar: pasa la ruta y el resumen de la transferencia al modal
            document.getElementById('deleteTransferModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('deleteTransferSummary').textContent = button.dataset.summary;
                document.getElementById('deleteTransferForm').setAttribute('action', button.dataset.action);
            });

            // Si el servidor devolvió errores, reabre el modal correspondiente
            @if ($reopen === 'create')
                new bootstrap.Modal(document.getElementById('createTransferModal')).show();
            @elseif ($reopen === 'edit')
                new bootstrap.Modal(document.getElementById('editTransferModal')).show();
            @endif
        })();
    </script>
@endpush
