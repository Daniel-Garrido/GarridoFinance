@props(['label', 'value', 'tone' => 'primary', 'icon' => 'bi-bar-chart'])

{{-- tone: primary | income | expense --}}
<div {{ $attributes->merge(['class' => 'gf-card gf-tone-' . $tone]) }}>
    <div class="gf-stat-top">
        <span class="gf-stat-label">{{ $label }}</span>
        <span class="gf-icon"><i class="bi {{ $icon }}"></i></span>
    </div>
    <p class="gf-stat-value">${{ number_format($value, 2) }} <small>MXN</small></p>
</div>
