{{-- Selector Año/Mes, independiente del filtro de arriba --}}
<form method="GET" action="{{ url()->current() }}" id="chartFilterForm" class="row g-2 align-items-end mb-4 rounded">

    {{-- Conservamos el filtro principal si ya viene aplicado --}}
    <input type="hidden" name="period" value="{{ request('period') }}">
    <input type="hidden" name="week" value="{{ request('week') }}">
    <input type="hidden" name="month" value="{{ request('month') }}">
    <input type="hidden" name="year" value="{{ request('year') }}">

    <div class="col-md-2">
        <label for="chart_mode" class="form-label">Ver gráficas por</label>
        <select name="chart_mode" id="chart_mode" class="form-select">
            <option value="year" {{ $chartMode === 'year' ? 'selected' : '' }}>Año</option>
            <option value="month" {{ $chartMode === 'month' ? 'selected' : '' }}>Mes</option>
        </select>
    </div>

    <div class="col-md-2">
        <label for="chart_year" class="form-label">Año</label>
        <select name="chart_year" id="chart_year" class="form-select">
            @foreach ($availableChartYears as $year)
                <option value="{{ $year }}" {{ $chartYear === $year ? 'selected' : '' }}>
                    {{ $year }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2" id="chart_month_wrapper" style="{{ $chartMode === 'month' ? '' : 'display:none;' }}">
        <label for="chart_month" class="form-label">Mes</label>
        <select name="chart_month" id="chart_month" class="form-select">
            @foreach ($monthLabels as $i => $label)
                <option value="{{ $i + 1 }}" {{ $chartMonth === $i + 1 ? 'selected' : '' }}>
                    {{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Aplicar</button>
    </div>
</form>
