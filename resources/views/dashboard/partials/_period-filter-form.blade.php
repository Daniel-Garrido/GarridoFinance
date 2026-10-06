{{-- Formulario de filtro de periodo --}}
<form method="GET" action="{{ url()->current() }}" id="periodFilterForm" class="gf-card gf-filter">

    {{-- Selector de tipo de periodo --}}
    <div class="gf-field">
        <label for="period">Ver por</label>
        <select name="period" id="period" class="form-select">
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Semana</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Mes</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Año</option>
        </select>
    </div>

    {{-- input dinámico: semana --}}
    <div class="gf-field" id="period_week_wrapper" style="display:none;">
        <label for="filter_week">Semana</label>
        <input type="week" name="week" id="filter_week" class="form-control"
            value="{{ request('week', $period === 'week' ? $currentWeekValue : '') }}">
    </div>

    {{-- input dinámico: mes --}}
    <div class="gf-field" id="period_month_wrapper" style="display:none;">
        <label for="filter_month">Mes</label>
        <input type="month" name="month" id="filter_month" class="form-control"
            value="{{ request('month', $period === 'month' ? $currentMonthValue : '') }}">
    </div>

    {{-- input dinámico: año --}}
    <div class="gf-field" id="period_year_wrapper" style="display:none;">
        <label for="filter_year">Año</label>
        <input type="number" name="year" id="filter_year" class="form-control" min="2000"
            max="2100" placeholder="2026"
            value="{{ request('year', $period === 'year' ? $currentYearValue : '') }}">
    </div>

    <div class="gf-filter-actions">
        {{-- Botón de filtrar --}}
        <button type="submit" class="gf-btn gf-btn-primary">Filtrar</button>

        {{-- Botón para limpiar filtros --}}
        <a href="{{ url()->current() }}" class="gf-btn gf-btn-icon" title="Limpiar filtros">
            <i class="bi bi-x-circle"></i>
        </a>
    </div>

</form>
