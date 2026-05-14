@props([
    'title',
    'body' => null,
    'tone' => 'neutral',
])

@php
    $toneClass = match ($tone) {
        'danger' => 'ops-signal-card--danger',
        'warning' => 'ops-signal-card--warning',
        default => 'ops-signal-card--neutral',
    };
@endphp

<article {{ $attributes->merge(['class' => 'ops-signal-card ' . $toneClass]) }}>
    <div class="ops-signal-card__header">
        <div>
            @if(isset($titleFallback) && $titleFallback)
                {{ $titleFallback }}
            @else
                <h3 class="ops-signal-card__title">{{ $title }}</h3>
            @endif
            
            @if ($body)
                <p class="ops-signal-card__body">{{ $body }}</p>
            @endif
        </div>

        @if (isset($headerRight))
            <div class="text-right">
                {{ $headerRight }}
            </div>
        @endif
        @if (isset($headerCount))
            <div class="ops-signal-card__count">
                {{ $headerCount }}
            </div>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="ops-signal-card__body">
            {{ $slot }}
        </div>
    @endif

    @if (isset($footer))
        <div class="ops-signal-card__footer">
            {{ $footer }}
        </div>
    @endif
</article>
