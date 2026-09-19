@props(['label', 'value', 'color' => null, 'shadow' => true])

<div {{ $attributes->merge(['class' => 'card stat-card ' . ($shadow ? 'shadow-sm' : '')]) }}>
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <p class="text-muted mb-1">{{ $label }}</p>
            <h4 class="fw-bold mb-0 {{ $color ? 'text-' . $color : '' }}">${{ number_format($value, 2) }} mxn</h4>
        </div>
    </div>
</div>
