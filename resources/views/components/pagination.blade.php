@props(['paginator', 'label' => 'registros'])

{{-- Pie de tabla: rango mostrado + botones de página (diseño del mockup) --}}
@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();

    // Con muchas páginas se muestran la primera, la última y dos alrededor de la actual
    $pages = $last <= 7
        ? range(1, $last)
        : collect([1, $last])
            ->merge(range(max(1, $current - 2), min($last, $current + 2)))
            ->unique()
            ->sort()
            ->values()
            ->all();
@endphp

<div {{ $attributes->merge(['class' => 'gf-pagination']) }}>
    <p class="gf-pagination-range">
        @if ($paginator->total() > 0)
            Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }} {{ $label }}
        @else
            Sin {{ $label }} para mostrar
        @endif
    </p>

    <nav class="gf-pagination-pages" aria-label="Paginación">
        @if ($paginator->onFirstPage())
            <span class="gf-page-btn is-disabled" aria-disabled="true"><i class="bi bi-chevron-left"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="gf-page-btn" aria-label="Página anterior">
                <i class="bi bi-chevron-left"></i>
            </a>
        @endif

        @foreach ($pages as $i => $page)
            @if ($i > 0 && $page - $pages[$i - 1] > 1)
                <span class="gf-page-gap">…</span>
            @endif

            @if ($page === $current)
                <span class="gf-page-btn is-active" aria-current="page">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="gf-page-btn">{{ $page }}</a>
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="gf-page-btn" aria-label="Página siguiente">
                <i class="bi bi-chevron-right"></i>
            </a>
        @else
            <span class="gf-page-btn is-disabled" aria-disabled="true"><i class="bi bi-chevron-right"></i></span>
        @endif
    </nav>
</div>
