@props([
    'eyebrow',
    'title',
    'count',
    'copy',
])

<article {{ $attributes->merge(['class' => 'ops-context-card']) }}>
    <div class="ops-context-card__header">
        <div>
            <p class="ops-context-card__eyebrow">{{ $eyebrow }}</p>
            <h4 class="ops-context-card__title">{{ $title }}</h4>
        </div>

        <strong class="ops-context-card__count">{{ $count }}</strong>
    </div>

    <p class="ops-context-card__copy">{{ $copy }}</p>

    @if (isset($meta))
        <div class="ops-context-card__meta">
            {{ $meta }}
        </div>
    @endif

    @if (isset($footer))
        <div class="ops-context-card__footer">
            {{ $footer }}
        </div>
    @endif
</article>
