@props(['name', 'label', 'options' => [], 'value' => ''])

{{--
    Desplegable de filtro (diseño del mockup) que guarda su valor en un input oculto,
    así el formulario GET se envía igual que con un <select>.
    options: [['value' => '', 'label' => 'Todas', 'type' => null], ...]
    Al elegir una opción, el input oculto dispara el evento "change".
--}}
@php
    $value = (string) $value;
    $current = collect($options)->firstWhere('value', $value) ?? ($options[0] ?? ['label' => '']);
@endphp

<div {{ $attributes->merge(['class' => 'gf-field gf-filter-dropdown dropdown']) }}>
    <label id="{{ $name }}_label">{{ $label }}</label>
    <input type="hidden" name="{{ $name }}" id="filter_{{ $name }}" value="{{ $value }}">

    <button type="button" class="gf-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
        aria-labelledby="{{ $name }}_label">
        <span class="gf-dropdown-value">{{ $current['label'] }}</span>
        <i class="bi bi-chevron-down"></i>
    </button>

    <ul class="dropdown-menu gf-dropdown-menu">
        @foreach ($options as $option)
            <li>
                <button type="button" class="dropdown-item {{ (string) $option['value'] === $value ? 'active' : '' }}"
                    data-value="{{ $option['value'] }}"
                    @isset($option['type']) data-type="{{ $option['type'] }}" @endisset>
                    {{ $option['label'] }}
                </button>
            </li>
        @endforeach
    </ul>
</div>
