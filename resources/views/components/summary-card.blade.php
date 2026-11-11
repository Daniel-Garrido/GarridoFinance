@props(['title', 'income', 'expense', 'balance', 'icon' => 'bi-calendar3'])

@php
    // Formato de dinero: signo menos tipográfico y valor absoluto
    $money = fn($value) => ($value < 0 ? '−' : '') . '$' . number_format(abs($value), 2);
@endphp

{{-- Tarjeta agrupada: ingresos y gastos lado a lado, balance abajo --}}
<div {{ $attributes->merge(['class' => 'gf-card gf-summary-card']) }}>
    <div class="gf-stat-top">
        <span class="gf-stat-label">{{ $title }}</span>
        <span class="gf-icon gf-tone-primary"><i class="bi {{ $icon }}"></i></span>
    </div>

    <div class="gf-summary-split">
        <div class="gf-summary-item">
            <span class="gf-summary-tag gf-tone-income">
                <span class="gf-icon"><i class="bi bi-arrow-up"></i></span> Ingresos
            </span>
            <p class="gf-summary-value gf-amount-income">{{ $money($income) }}</p>
        </div>

        <div class="gf-summary-item">
            <span class="gf-summary-tag gf-tone-expense">
                <span class="gf-icon"><i class="bi bi-arrow-down"></i></span> Gastos
            </span>
            <p class="gf-summary-value gf-amount-expense">{{ $money($expense) }}</p>
        </div>
    </div>

    <div class="gf-summary-foot">
        <span>Balance</span>
        <strong class="{{ $balance < 0 ? 'gf-amount-expense' : 'gf-summary-positive' }}">
            {{ $money($balance) }} <small>MXN</small>
        </strong>
    </div>
</div>
