@props([
    'title',
    'copy',
    'count',
    'tone' => 'neutral',
])

<article {{ $attributes->merge(['class' => 'ops-bucket-card ops-bucket-card--' . $tone]) }}>
    <div class="ops-bucket-card__header">
        <div>
            <p class="ops-bucket-card__title">{{ $title }}</p>
            <p class="ops-bucket-card__copy">{{ $copy }}</p>
        </div>

        <strong class="ops-bucket-card__count">{{ $count }}</strong>
    </div>

    @if (isset($footer))
        <div class="ops-bucket-card__footer">
            {{ $footer }}
        </div>
    @endif
</article>
