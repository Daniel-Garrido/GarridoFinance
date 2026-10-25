{{--
    Campos compartidos por los modales de crear y editar transferencia.
    $prefix:     'create' | 'edit' (prefijo de los ids)
    $showErrors: true si los errores de validación son de este formulario
    $values:     valores iniciales cuando no hay errores (los de edición los llena el JS)
    $onlyActive: true para listar solo cuentas activas
--}}
@php
    $val = fn($field) => $showErrors ? old($field) : ($values[$field] ?? '');
    $invalid = fn($field) => $showErrors && $errors->has($field) ? 'is-invalid' : '';
    $options = $onlyActive ? $accounts->where('is_active', true) : $accounts;
@endphp

<div class="gf-form-grid">
    <div class="gf-form-group">
        <label for="{{ $prefix }}_date">Fecha</label>
        <input type="date" name="date" id="{{ $prefix }}_date" class="form-control {{ $invalid('date') }}"
            value="{{ $val('date') }}">
        <x-form-error name="date" :show="$showErrors" />
    </div>

    <div class="gf-form-group">
        <label for="{{ $prefix }}_amount">Monto (MXN)</label>
        <input type="number" step="0.01" min="0.01" name="amount" id="{{ $prefix }}_amount"
            class="form-control {{ $invalid('amount') }}" value="{{ $val('amount') }}" placeholder="0.00">
        <x-form-error name="amount" :show="$showErrors" />
    </div>

    @foreach (['from_account_id' => 'Cuenta origen', 'to_account_id' => 'Cuenta destino'] as $field => $label)
        <div class="gf-form-group">
            <label for="{{ $prefix }}_{{ $field }}">{{ $label }}</label>
            <select name="{{ $field }}" id="{{ $prefix }}_{{ $field }}" class="form-select {{ $invalid($field) }}">
                <option value="" disabled @selected((string) $val($field) === '')>Selecciona una cuenta</option>
                @foreach ($options as $account)
                    <option value="{{ $account->id }}" @selected((string) $val($field) === (string) $account->id)>
                        {{ $account->name }}{{ $account->is_active ? '' : ' (inactiva)' }}
                    </option>
                @endforeach
            </select>
            <x-form-error :name="$field" :show="$showErrors" />
        </div>
    @endforeach

    <div class="gf-form-group gf-form-full">
        <label for="{{ $prefix }}_description">Descripción</label>
        <textarea name="description" id="{{ $prefix }}_description" rows="2"
            class="form-control {{ $invalid('description') }}" placeholder="Opcional">{{ $val('description') }}</textarea>
        <x-form-error name="description" :show="$showErrors" />
    </div>
</div>
