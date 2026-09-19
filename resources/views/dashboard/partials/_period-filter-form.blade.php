{{-- Formulario de filtro de periodo --}}
<form method="GET" action="{{ url()->current() }}" id="periodFilterForm" class="row g-2 align-items-end mb-4 rounded">

    {{-- Selector de tipo de periodo --}}
    <div class="col-md-2">
        <label for="period" class="form-label">Ver por</label>
        <select name="period" id="period" class="form-select">
            <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Semana</option>
            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Mes</option>
            <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Año</option>
        </select>
    </div>

    {{-- input dinámico: semana --}}
    <div class="col-md-2" id="period_week_wrapper" style="display:none;">
        <label for="filter_week">Semana</label>
        <input type="week" name="week" id="filter_week" class="form-control"
            value="{{ request('week', $period === 'week' ? $currentWeekValue : '') }}">
    </div>

    {{-- input dinámico: mes --}}
    <div class="col-md-2" id="period_month_wrapper" style="display:none;">
        <label for="filter_month">Mes</label>
        <input type="month" name="month" id="filter_month" class="form-control"
            value="{{ request('month', $period === 'month' ? $currentMonthValue : '') }}">
    </div>

    {{-- input dinámico: año --}}
    <div class="col-md-2" id="period_year_wrapper" style="display:none;">
        <label for="filter_year">Año</label>
        <input type="number" name="year" id="filter_year" class="form-control" min="2000"
            max="2100" placeholder="2026"
            value="{{ request('year', $period === 'year' ? $currentYearValue : '') }}">
    </div>

    {{-- Botón de filtrar --}}
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>

   {{-- Botón para limpiar filtros --}}
    <div class="col-md-2">
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i>
        </a>
    </div>

</form>
