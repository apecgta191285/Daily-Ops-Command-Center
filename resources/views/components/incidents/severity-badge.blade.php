@props(['severity'])

@php
    $badgeClass = match ($severity) {
        'High' => 'ops-badge--danger',
        'Medium' => 'ops-badge--warning',
        default => 'ops-badge--info',
    };
@endphp

<span {{ $attributes->class(['ops-badge', $badgeClass]) }}>
    {{ $severity }}
</span>
