@props([
    'eyebrow',
    'value',
])

<div {{ $attributes->merge(['class' => 'ops-trend-card']) }}>
    <div class="ops-trend-card__header">
        <div>
            <p class="ops-trend-card__eyebrow">{{ $eyebrow }}</p>
            <p class="ops-trend-card__value">{{ $value }}</p>
        </div>

        @if (isset($visual))
            <div class="ops-trend-card__visual">
                {{ $visual }}
            </div>
        @endif

        @if (isset($pill))
            {{ $pill }}
        @endif
    </div>

    {{ $slot }}
</div>
