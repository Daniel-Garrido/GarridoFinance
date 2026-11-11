@props(['label', 'value', 'tone' => 'primary', 'icon' => 'bi-bar-chart'])

{{-- tone: primary | income | expense --}}
<div {{ $attributes->merge(['class' => 'gf-card gf-tone-' . $tone]) }}>
    <div class="gf-stat-top">
        <span class="gf-stat-label">{{ $label }}</span>
        <span class="gf-icon"><i class="bi {{ $icon }}"></i></span>
    </div>
    <p class="gf-stat-value {{ $value < 0 ? 'gf-stat-negative' : '' }}">{{ $value < 0 ? '−' : '' }}${{ number_format(abs($value), 2) }} <small>MXN</small></p>
</div>
