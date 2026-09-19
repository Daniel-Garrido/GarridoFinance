@props(['title', 'canvasId', 'categories' => null, 'textClass' => null])

<div class="card shadow-sm">
    <div class="card-body">
        <h6 class="card-title">{{ $title }}</h6>
        <canvas id="{{ $canvasId }}"></canvas>

        @if ($categories)
            <div class="mt-3">
                @foreach ($categories as $cat)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>
                            <span class="d-inline-block rounded-circle me-2"
                                style="width:10px;height:10px;background:{{ $cat['color'] }}"></span>
                            {{ $cat['name'] }}
                        </span>
                        <span class="{{ $textClass }} fw-semibold">${{ number_format($cat['total'], 2) }}
                            ({{ $cat['percent'] }}%)</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
