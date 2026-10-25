{{--
    Campos compartidos por los modales de crear y editar transacción.
    $prefix:     'create' | 'edit' (prefijo de los ids)
    $showErrors: true si los errores de validación son de este formulario
    $values:     valores iniciales cuando no hay errores (los de edición los llena el JS)
    $onlyActive: true para listar solo cuentas / categorías / métodos activos
--}}
@php
    $val = fn($field) => $showErrors ? old($field) : ($values[$field] ?? '');
    $invalid = fn($field) => $showErrors && $errors->has($field) ? 'is-invalid' : '';
    $visible = fn($items) => $onlyActive ? $items->where('is_active', true) : $items;
    $suffix = fn($item) => $item->is_active ? '' : ' (inactiva)';
@endphp

<div class="gf-form-grid">
    <div class="gf-form-group">
        <label for="{{ $prefix }}_date">Fecha</label>
        <input type="date" name="date" id="{{ $prefix }}_date" class="form-control {{ $invalid('date') }}"
            value="{{ $val('date') }}">
        <x-form-error name="date" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_type">Tipo de transacción</label>
        <select name="type" id="{{ $prefix }}_type" class="form-select {{ $invalid('type') }}" data-category-target="{{ $prefix }}_category_id">
            <option value="income" @selected($val('type') === 'income')>Ingreso</option>
            <option value="expense" @selected($val('type') === 'expense')>Gasto</option>
        </select>
        <x-form-error name="type" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_amount">Monto (MXN)</label>
        <input type="number" step="0.01" min="0.01" name="amount" id="{{ $prefix }}_amount"
            class="form-control {{ $invalid('amount') }}" value="{{ $val('amount') }}" placeholder="0.00">
        <x-form-error name="amount" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_account_id">Cuenta</label>
        <select name="account_id" id="{{ $prefix }}_account_id" class="form-select {{ $invalid('account_id') }}">
            @foreach ($visible($accounts) as $account)
                <option value="{{ $account->id }}" @selected((string) $val('account_id') === (string) $account->id)>
                    {{ $account->name }}{{ $suffix($account) }}
                </option>
            @endforeach
        </select>
        <x-form-error name="account_id" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_category_id">Categoría</label>
        <select name="category_id" id="{{ $prefix }}_category_id" class="form-select {{ $invalid('category_id') }}">
            @foreach ($visible($categories) as $category)
                <option value="{{ $category->id }}" data-type="{{ $category->type }}"
                    @selected((string) $val('category_id') === (string) $category->id)>
                    {{ $category->name }}{{ $suffix($category) }}
                </option>
            @endforeach
        </select>
        <x-form-error name="category_id" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_payment_method_id">Método de pago</label>
        <select name="payment_method_id" id="{{ $prefix }}_payment_method_id"
            class="form-select {{ $invalid('payment_method_id') }}">
            @foreach ($visible($paymentMethods) as $paymentMethod)
                <option value="{{ $paymentMethod->id }}"
                    @selected((string) $val('payment_method_id') === (string) $paymentMethod->id)>
                    {{ $paymentMethod->name }}{{ $suffix($paymentMethod) }}
                </option>
            @endforeach
        </select>
        <x-form-error name="payment_method_id" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_is_historical">¿Es histórica?</label>
        <select name="is_historical" id="{{ $prefix }}_is_historical" class="form-select {{ $invalid('is_historical') }}">
            <option value="0" @selected((string) $val('is_historical') === '0')>No</option>
            <option value="1" @selected((string) $val('is_historical') === '1')>Sí</option>
        </select>
        <x-form-error name="is_historical" :show="$showErrors" />
    </div>

    <div class="gf-form-group gf-form-full">
        <label for="{{ $prefix }}_description">Descripción</label>
        <textarea name="description" id="{{ $prefix }}_description" rows="2"
            class="form-control {{ $invalid('description') }}" placeholder="Opcional">{{ $val('description') }}</textarea>
        <x-form-error name="description" :show="$showErrors" />
    </div>
</div>
