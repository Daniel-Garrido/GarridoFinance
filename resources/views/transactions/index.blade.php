@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/transactions.css') }}?v={{ filemtime(public_path('css/transactions.css')) }}">
@endpush

@section('content')
    @php
        // Opciones de los desplegables de filtro
        $typeOptions = [
            ['value' => '', 'label' => 'Todas'],
            ['value' => 'income', 'label' => 'Ingreso'],
            ['value' => 'expense', 'label' => 'Gasto'],
        ];

        $categoryOptions = collect([['value' => '', 'label' => 'Todas']])
            ->merge($categories->map(fn($c) => ['value' => (string) $c->id, 'label' => $c->name, 'type' => $c->type]))
            ->all();

        $hasFilters = request()->filled('type') || request()->filled('category_id') || request()->filled('period') || $search !== '';

        // Si falló una validación, se vuelve a abrir el modal que la envió
        $reopen = $errors->any() ? old('_form') : null;
        $editId = old('_transaction_id');
    @endphp

    <div class="gf-page">

        {{-- Encabezado --}}
        <header class="gf-header">
            <div>
                <h1>Lista de transacciones</h1>
                <p>Administra tus transacciones registradas en GarridoFinance</p>
            </div>
            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                data-bs-target="#createTransactionModal">
                <i class="bi bi-plus-circle"></i> Crear una transacción
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
        <form method="GET" action="{{ route('transactions.index') }}" id="filterForm" class="gf-filter gf-card">
            <x-filter-dropdown name="type" label="Tipo" :options="$typeOptions" :value="request('type', '')" />
            <x-filter-dropdown name="category_id" label="Categoría" :options="$categoryOptions"
                :value="request('category_id', '')" />
            <x-period-filter />

            <div class="gf-filter-actions">
                <button type="submit" class="gf-btn gf-btn-primary">
                    <i class="bi bi-funnel"></i> Filtrar
                </button>
                <a href="{{ route('transactions.index') }}" class="gf-btn gf-btn-icon" title="Limpiar filtros"
                    aria-label="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>

        {{-- Tabla de transacciones --}}
        <section class="gf-panel">
            <div class="gf-panel-head">
                <div class="gf-panel-title">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-currency-dollar"></i></span>
                    <div>
                        <h2>Transacciones registradas</h2>
                        <p>{{ $transactions->total() }} {{ $transactions->total() === 1 ? 'transacción encontrada' : 'transacciones encontradas' }}</p>
                    </div>
                </div>

                {{-- El buscador pertenece al formulario de filtros: se envía junto con ellos --}}
                <label class="gf-search gf-search-wide">
                    <i class="bi bi-search"></i>
                    <input type="search" name="q" form="filterForm" value="{{ $search }}"
                        placeholder="Buscar por descripción o cuenta" aria-label="Buscar por descripción o cuenta">
                </label>
            </div>

            <div class="gf-table gf-table-compact gf-transactions-table">
                <div class="gf-table-head">
                    <span>Fecha</span>
                    <span>Tipo</span>
                    <span>Monto</span>
                    <span>Cuenta</span>
                    <span>Categoría</span>
                    <span>Método de pago</span>
                    <span>Histórica</span>
                    <span>Descripción</span>
                    <span class="gf-cell-end">Opciones</span>
                </div>

                @forelse ($transactions as $transaction)
                    @php
                        $isIncome = $transaction->type === 'income';
                        $amountLabel = ($isIncome ? '+' : '−') . '$' . number_format($transaction->amount, 2) . ' MXN';
                    @endphp
                    <div class="gf-table-row">
                        {{-- Fecha --}}
                        <div class="gf-date-cell" title="{{ $transaction->date->format('Y-m-d') }}">
                            <strong>{{ $transaction->date_label }}</strong>
                            <span>{{ $transaction->date_hint }}</span>
                        </div>

                        {{-- Tipo --}}
                        <span class="gf-pill gf-pill-sm {{ $isIncome ? 'gf-pill-income' : 'gf-pill-expense' }}">
                            <span class="gf-pill-dot"></span>{{ $isIncome ? 'Ingreso' : 'Gasto' }}
                        </span>

                        {{-- Monto --}}
                        <span class="gf-cell-amount {{ $isIncome ? 'gf-amount-income' : 'gf-amount-expense' }}">{{ $amountLabel }}</span>

                        {{-- Cuenta, categoría y método --}}
                        <span class="gf-cell-text gf-cell-strong" title="{{ $transaction->account?->name }}">{{ $transaction->account?->name }}</span>
                        <span class="gf-cell-text" title="{{ $transaction->category?->name }}">{{ $transaction->category?->name }}</span>
                        <span class="gf-cell-text" title="{{ $transaction->paymentMethod?->name }}">{{ $transaction->paymentMethod?->name }}</span>

                        {{-- Histórica --}}
                        <span class="gf-pill gf-pill-sm {{ $transaction->is_historical ? 'gf-pill-primary' : 'gf-pill-muted' }}">
                            {{ $transaction->is_historical ? 'Sí' : 'No' }}
                        </span>

                        {{-- Descripción --}}
                        <span class="gf-cell-text gf-cell-muted" title="{{ $transaction->description }}">{{ $transaction->description ?: '—' }}</span>

                        {{-- Opciones --}}
                        <div class="gf-row-actions">
                            <button type="button" class="gf-action" title="Editar" aria-label="Editar transacción"
                                data-bs-toggle="modal" data-bs-target="#editTransactionModal"
                                data-id="{{ $transaction->id }}"
                                data-action="{{ route('transactions.update', $transaction) }}"
                                data-date="{{ $transaction->date->format('Y-m-d') }}"
                                data-type="{{ $transaction->type }}"
                                data-amount="{{ $transaction->amount }}"
                                data-account="{{ $transaction->account_id }}"
                                data-category="{{ $transaction->category_id }}"
                                data-payment-method="{{ $transaction->payment_method_id }}"
                                data-historical="{{ $transaction->is_historical ? 1 : 0 }}"
                                data-description="{{ $transaction->description }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="gf-action gf-action-danger" title="Eliminar"
                                aria-label="Eliminar transacción"
                                data-bs-toggle="modal" data-bs-target="#deleteTransactionModal"
                                data-action="{{ route('transactions.destroy', $transaction) }}"
                                data-summary="{{ ($isIncome ? 'el ingreso de ' : 'el gasto de ') . $amountLabel . ' del ' . $transaction->date_label . ($transaction->category ? ' (' . $transaction->category->name . ')' : '') }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="gf-table-empty">
                        @if ($hasFilters)
                            <h3>Sin transacciones</h3>
                            <p>Ajusta los filtros de tipo, categoría o periodo.</p>
                        @else
                            <h3>No hay transacciones registradas</h3>
                            <p>Agrega tu primera transacción para comenzar.</p>
                            <button type="button" class="gf-btn gf-btn-primary" data-bs-toggle="modal"
                                data-bs-target="#createTransactionModal">
                                <i class="bi bi-plus-circle"></i> Crear una transacción
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <x-pagination :paginator="$transactions" label="transacciones" />
        </section>

    </div>

    {{-- Modal: crear transacción --}}
    <div class="modal fade gf-modal" id="createTransactionModal" tabindex="-1"
        aria-labelledby="createTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-currency-dollar"></i></span>
                    <h5 class="modal-title" id="createTransactionModalLabel">Crear una transacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_form" value="create">

                    <div class="modal-body">
                        @include('transactions.partials._form-fields', [
                            'prefix' => 'create',
                            'showErrors' => $reopen === 'create',
                            'onlyActive' => true,
                            'values' => [
                                'date' => now()->toDateString(),
                                'type' => request('type') === 'expense' ? 'expense' : 'income',
                                'is_historical' => '0',
                            ],
                        ])
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-primary">
                            <i class="bi bi-check-lg"></i> Guardar transacción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: editar transacción (uno solo, se llena con los datos del botón) --}}
    <div class="modal fade gf-modal" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="gf-icon gf-tone-primary"><i class="bi bi-pencil"></i></span>
                    <h5 class="modal-title" id="editTransactionModalLabel">Editar transacción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="editTransactionForm" method="POST"
                    action="{{ $reopen === 'edit' && $editId ? route('transactions.update', $editId) : '' }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="_transaction_id" id="edit_transaction_id"
                        value="{{ $reopen === 'edit' ? $editId : '' }}">

                    <div class="modal-body">
                        @include('transactions.partials._form-fields', [
                            'prefix' => 'edit',
                            'showErrors' => $reopen === 'edit',
                            'onlyActive' => false,
                            'values' => [],
                        ])
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="gf-btn gf-btn-primary">
                            <i class="bi bi-check-lg"></i> Actualizar transacción
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: confirmar eliminación --}}
    <div class="modal fade gf-modal" id="deleteTransactionModal" tabindex="-1"
        aria-labelledby="deleteTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body gf-confirm">
                    <span class="gf-icon gf-tone-expense"><i class="bi bi-trash3"></i></span>
                    <h3 id="deleteTransactionModalLabel">¿Eliminar esta transacción?</h3>
                    <p>Vas a eliminar <strong id="deleteTransactionSummary"></strong>.</p>
                    <p>El saldo de la cuenta se recalculará. Esta acción no se puede deshacer.</p>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="gf-btn gf-btn-ghost" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteTransactionForm" method="POST">
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
            // ===== Filtro de categoría: solo las del tipo elegido (todas si el tipo es "Todas") =====
            const filterType = document.getElementById('filter_type');
            const filterCategory = document.getElementById('filter_category_id');
            const categoryDropdown = filterCategory.closest('.gf-filter-dropdown');

            function syncFilterCategories() {
                const type = filterType.value;
                let selectedHidden = false;

                categoryDropdown.querySelectorAll('.dropdown-item[data-type]').forEach(function (item) {
                    const hide = type !== '' && item.dataset.type !== type;
                    item.parentElement.hidden = hide;
                    if (hide && item.dataset.value === filterCategory.value) selectedHidden = true;
                });

                // Si la categoría elegida ya no corresponde al tipo, vuelve a "Todas"
                if (selectedHidden) {
                    categoryDropdown.querySelector('.dropdown-item[data-value=""]').click();
                }
            }
            filterType.addEventListener('change', syncFilterCategories);
            syncFilterCategories();

            // ===== Modales: la categoría solo muestra las del tipo de transacción =====
            function filterCategoriesByType(typeSelect, categorySelect) {
                const selectedType = typeSelect.value;

                categorySelect.querySelectorAll('option').forEach(option => {
                    option.hidden = option.dataset.type !== selectedType;
                    if (option.hidden && option.selected) option.selected = false;
                });

                const current = categorySelect.querySelector('option:checked');
                if (!current || current.hidden) {
                    const firstVisible = categorySelect.querySelector('option:not([hidden])');
                    if (firstVisible) firstVisible.selected = true;
                }
            }

            ['create', 'edit'].forEach(function (prefix) {
                const typeSelect = document.getElementById(prefix + '_type');
                const categorySelect = document.getElementById(prefix + '_category_id');
                typeSelect.addEventListener('change', () => filterCategoriesByType(typeSelect, categorySelect));
                filterCategoriesByType(typeSelect, categorySelect);
            });

            // ===== Editar: llena el modal con los datos del botón que lo abrió =====
            document.getElementById('editTransactionModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return; // reabierto tras un error: ya trae los datos enviados

                const d = button.dataset;
                document.getElementById('editTransactionForm').setAttribute('action', d.action);
                document.getElementById('edit_transaction_id').value = d.id;
                document.getElementById('edit_date').value = d.date;
                document.getElementById('edit_type').value = d.type;
                document.getElementById('edit_amount').value = d.amount;
                document.getElementById('edit_account_id').value = d.account;
                document.getElementById('edit_payment_method_id').value = d.paymentMethod;
                document.getElementById('edit_is_historical').value = d.historical;
                document.getElementById('edit_description').value = d.description;

                // Primero se filtran las categorías por tipo y luego se elige la de la transacción
                filterCategoriesByType(document.getElementById('edit_type'), document.getElementById('edit_category_id'));
                document.getElementById('edit_category_id').value = d.category;

                // Limpia errores de un intento anterior
                this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                this.querySelectorAll('.gf-form-error').forEach(el => el.remove());
            });

            // ===== Eliminar: pasa la ruta y el resumen de la transacción al modal =====
            document.getElementById('deleteTransactionModal').addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                document.getElementById('deleteTransactionSummary').textContent = button.dataset.summary;
                document.getElementById('deleteTransactionForm').setAttribute('action', button.dataset.action);
            });

            // ===== Si el servidor devolvió errores, reabre el modal correspondiente =====
            @if ($reopen === 'create')
                new bootstrap.Modal(document.getElementById('createTransactionModal')).show();
            @elseif ($reopen === 'edit')
                new bootstrap.Modal(document.getElementById('editTransactionModal')).show();
            @endif
        })();
    </script>
@endpush
