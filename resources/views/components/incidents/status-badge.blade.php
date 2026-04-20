@props(['status'])

@php
    $statusValue = $status instanceof \App\Domain\Incidents\Enums\IncidentStatus ? $status->value : $status;

    $badgeClass = match ($statusValue) {
        'Resolved' => 'ops-badge--success',
        'In Progress' => 'ops-badge--warning',
        default => 'ops-badge--info',
    };
@endphp

<span {{ $attributes->class(['ops-badge', $badgeClass]) }}>
    {{ $statusValue }}
</span>
