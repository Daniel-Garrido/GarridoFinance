{{--
    Modal para crear una transacción, compartido por el dashboard y el listado.
    $reopen:         'create' si el formulario volvió con errores de validación
    $accounts, $categories, $paymentMethods: catálogos del usuario
--}}
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
