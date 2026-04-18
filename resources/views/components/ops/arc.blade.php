@props([
    'value' => 0,
    'size' => 64,
    'tone' => 'primary',
])

@php
    $normalizedValue = max(0, min(100, (int) $value));
    $stroke = match ($tone) {
        'success' => 'var(--app-success-text)',
        'warning' => 'var(--app-warning-text)',
        'danger' => 'var(--app-danger-text)',
        default => 'var(--app-action-primary)',
    };
@endphp

<svg
    {{ $attributes->merge(['class' => 'ops-arc']) }}
    width="{{ $size }}"
    height="{{ $size }}"
    viewBox="0 0 36 36"
    role="presentation"
    aria-hidden="true"
>
    <circle class="ops-arc__track" cx="18" cy="18" r="15.9155" pathLength="100" />
    <circle
        class="ops-arc__fill"
        cx="18"
        cy="18"
        r="15.9155"
        pathLength="100"
        stroke="{{ $stroke }}"
        stroke-dasharray="{{ $normalizedValue }} 100"
        stroke-dashoffset="25"
    />
</svg>
