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
    {{ match ($severityValue) {
        'Low' => 'ต่ำ',
        'Medium' => 'กลาง',
        'High' => 'สูง',
        default => $severityValue,
    } }}
</span>
