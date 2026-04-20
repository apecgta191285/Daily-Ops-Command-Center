@props(['severity'])

@php
    $severityValue = $severity instanceof \App\Domain\Incidents\Enums\IncidentSeverity ? $severity->value : $severity;

    $badgeClass = match ($severityValue) {
        'High' => 'ops-badge--danger',
        'Medium' => 'ops-badge--warning',
        default => 'ops-badge--info',
    };
@endphp

<span {{ $attributes->class(['ops-badge', $badgeClass]) }}>
    {{ $severityValue }}
</span>
