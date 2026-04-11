@props(['status'])

@php
    $badgeClass = match ($status) {
        'Resolved' => 'ops-badge--success',
        'In Progress' => 'ops-badge--warning',
        default => 'ops-badge--info',
    };
@endphp

<span {{ $attributes->class(['ops-badge', $badgeClass]) }}>
    {{ $status }}
</span>
