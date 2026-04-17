@props([
    'title',
    'tone' => 'neutral',
])

@php
    $toneClass = match ($tone) {
        'info' => 'ops-callout--info',
        'success' => 'ops-callout--success',
        'warning' => 'ops-callout--warning',
        default => 'ops-callout--neutral',
    };
@endphp

<section {{ $attributes->merge(['class' => 'ops-callout '.$toneClass]) }}>
    <h4 class="ops-callout__title">{{ $title }}</h4>
    <div class="ops-callout__body">
        {{ $slot }}
    </div>
</section>
