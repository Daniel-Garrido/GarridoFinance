@props(['title', 'canvasId', 'subtitle' => null, 'categories' => null, 'type' => 'income'])

{{-- Si recibe $categories se dibuja como dona con su lista; si no, como gráfica de barras --}}
<div {{ $attributes->merge(['class' => 'gf-card']) }}>
    <div class="gf-card-head">
        <div>
            <h2 class="gf-card-title">{{ $title }}</h2>
            @if ($subtitle)
                <p class="gf-card-subtitle mb-0">{{ $subtitle }}</p>
            @endif
        </div>
        {{ $actions ?? '' }}
    </div>

    @if (is_null($categories))
        <div class="gf-chart-box">
            <canvas id="{{ $canvasId }}"></canvas>
        </div>
    @elseif (count($categories) === 0)
        <p class="gf-empty">Sin movimientos en este periodo.</p>
    @else
        @php $total = collect($categories)->sum('total'); @endphp

        <div class="gf-donut-box">
            <canvas id="{{ $canvasId }}"></canvas>
            <div class="gf-donut-center">
                <span>{{ $type === 'income' ? 'Ingresos' : 'Gastos' }}</span>
                <strong>${{ $total >= 1000 ? number_format($total / 1000, 1) . 'k' : number_format($total, 0) }}</strong>
            </div>
        </div>

        <div class="gf-legend">
            @foreach ($categories as $cat)
                <div class="gf-legend-row">
                    <span class="gf-dot" style="background: {{ $cat['color'] }}"></span>
                    <span class="gf-legend-name">{{ $cat['name'] }}</span>
                    <span class="gf-legend-amount {{ $type === 'income' ? 'gf-amount-income' : 'gf-amount-expense' }}">
                        ${{ number_format($cat['total'], 2) }} ({{ $cat['percent'] }}%)
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>
