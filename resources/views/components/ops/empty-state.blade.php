@props([
    'title',
    'body',
])

<div {{ $attributes->merge(['class' => 'ops-empty']) }}>
    <x-placeholder-pattern class="ops-empty__pattern" aria-hidden="true" />

    <div class="ops-empty__content">
        <p class="ops-empty__title">{{ $title }}</p>
        <p class="ops-empty__body">{{ $body }}</p>

        @if (! $slot->isEmpty())
            <div class="ops-empty__actions">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
