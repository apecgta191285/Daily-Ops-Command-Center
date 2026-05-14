@props([
    'eyebrow' => null,
    'title',
    'body' => null,
])

<div {{ $attributes->merge(['class' => 'ops-section-heading']) }}>
    <div>
        @if ($eyebrow)
            <p class="ops-section-heading__eyebrow">{{ $eyebrow }}</p>
        @endif
        
        <h2 class="ops-section-heading__title">{{ $title }}</h2>
        
        @if ($body)
            <p class="ops-section-heading__body">{{ $body }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex shrink-0 flex-wrap gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
