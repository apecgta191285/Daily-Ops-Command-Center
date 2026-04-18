@props([
    'kicker',
    'value' => null,
    'meta' => null,
    'emphasis' => null,
])

<section {{ $attributes->merge(['class' => 'ops-stat']) }}>
    <div class="ops-stat__body">
        <div class="ops-stat__header">
            <p class="ops-stat__kicker">{{ $kicker }}</p>

            @if (isset($visual) && ! $visual->isEmpty())
                <div class="ops-stat__visual">
                    {{ $visual }}
                </div>
            @endif
        </div>

        @if (filled($value))
            <p class="ops-stat__value">{{ $value }}</p>
        @endif

        @if (filled($meta))
            <p class="ops-stat__meta">{{ $meta }}</p>
        @endif

        @if (filled($emphasis))
            <p class="ops-stat__emphasis">{{ $emphasis }}</p>
        @endif

        @if (! $slot->isEmpty())
            <div class="mt-4">
                {{ $slot }}
            </div>
        @endif
    </div>
</section>
