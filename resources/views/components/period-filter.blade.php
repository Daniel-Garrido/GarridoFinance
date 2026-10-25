{{--
    Filtro de periodo: desplegable (Todos / Día / Semana / Mes / Año) + el campo que corresponde.
    Se usa dentro de un formulario GET; el backend lo aplica con el scope inPeriod().
    El JS de public/js/filters.js muestra el campo según la opción elegida.
--}}
@php
    $period = (string) request('period', '');
    $options = [
        ['value' => '', 'label' => 'Todos'],
        ['value' => 'day', 'label' => 'Día'],
        ['value' => 'week', 'label' => 'Semana'],
        ['value' => 'month', 'label' => 'Mes'],
        ['value' => 'year', 'label' => 'Año'],
    ];
@endphp

<x-filter-dropdown name="period" label="Periodo" :options="$options" :value="$period" />

<div class="gf-field gf-period-input" data-period="day" @if ($period !== 'day') hidden @endif>
    <label for="filter_date">Fecha</label>
    <input type="date" name="date" id="filter_date" class="form-control" value="{{ request('date') }}">
</div>
<div class="gf-field gf-period-input" data-period="week" @if ($period !== 'week') hidden @endif>
    <label for="filter_week">Semana</label>
    <input type="week" name="week" id="filter_week" class="form-control" value="{{ request('week') }}">
</div>
<div class="gf-field gf-period-input" data-period="month" @if ($period !== 'month') hidden @endif>
    <label for="filter_month">Mes</label>
    <input type="month" name="month" id="filter_month" class="form-control" value="{{ request('month') }}">
</div>
<div class="gf-field gf-period-input" data-period="year" @if ($period !== 'year') hidden @endif>
    <label for="filter_year">Año</label>
    <input type="number" name="year" id="filter_year" class="form-control" min="2000" max="2100"
        placeholder="{{ now()->year }}" value="{{ request('year') }}">
</div>
