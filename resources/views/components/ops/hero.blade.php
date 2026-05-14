@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
])

<section {{ $attributes->merge(['class' => 'ops-hero'])->merge(['data-motion' => 'glance-rise']) }}>
    <div class="ops-hero__inner">
        <div>
            @if ($eyebrow ?? false)
                <p class="ops-hero__eyebrow">{{ $eyebrow }}</p>
            @endif
            
            @if ($title)
                <h3 class="ops-hero__title">{{ $title }}</h3>
            @elseif (isset($titleFallback))
                {{ $titleFallback }}
            @endif
            
            @if ($lead ?? false)
                <p class="ops-hero__lead">{{ $lead }}</p>
            @endif

            @if (isset($meta))
                <div class="ops-hero__meta">
                    {{ $meta }}
                </div>
            @endif
        </div>

        @if (isset($aside))
            <aside class="ops-hero__aside">
                {{ $aside }}
            </aside>
        @endif
    </div>
</section>
