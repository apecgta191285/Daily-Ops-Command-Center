@props([
    'points' => [],
    'width' => 88,
    'height' => 32,
    'tone' => 'primary',
])

@php
    $values = collect($points)
        ->map(static fn ($point): int => (int) $point)
        ->values();

    $lineColor = match ($tone) {
        'success' => 'var(--app-success-text)',
        'warning' => 'var(--app-warning-text)',
        'danger' => 'var(--app-danger-text)',
        default => 'var(--app-action-primary)',
    };

    $coordinates = [];

    if ($values->count() >= 2) {
        $minimum = $values->min();
        $maximum = $values->max();
        $range = max($maximum - $minimum, 1);
        $step = $values->count() > 1 ? $width / ($values->count() - 1) : $width;

        foreach ($values as $index => $point) {
            $x = round($index * $step, 1);
            $y = round($height - 4 - ((($point - $minimum) / $range) * ($height - 8)), 1);
            $coordinates[] = $x.','.$y;
        }
    }

    $polyline = implode(' ', $coordinates);
    $lastPoint = $coordinates !== [] ? explode(',', $coordinates[array_key_last($coordinates)]) : null;
@endphp

@if ($coordinates !== [])
    <svg
        {{ $attributes->merge(['class' => 'ops-sparkline']) }}
        width="{{ $width }}"
        height="{{ $height }}"
        viewBox="0 0 {{ $width }} {{ $height }}"
        role="presentation"
        aria-hidden="true"
    >
        <polyline class="ops-sparkline__line" points="{{ $polyline }}" stroke="{{ $lineColor }}" />

        @if ($lastPoint !== null)
            <circle
                class="ops-sparkline__dot"
                cx="{{ $lastPoint[0] }}"
                cy="{{ $lastPoint[1] }}"
                r="2.75"
                fill="{{ $lineColor }}"
            />
        @endif
    </svg>
@endif
