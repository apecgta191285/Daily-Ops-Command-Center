<x-layouts.print :title="__('Incident summary print')">
    <x-slot name="toolbar">
        <div class="ops-print-toolbar__inner">
            <div>
                <p class="ops-print-toolbar__eyebrow">{{ __('Printable evidence surface') }}</p>
                <h1 class="ops-print-toolbar__title">{{ __('Incident summary print view') }}</h1>
                <p class="ops-print-toolbar__copy">
                    {{ __('Use this version when you need one clean incident record for review, handoff discussion, or capstone evidence.') }}
                </p>
            </div>

            <div class="ops-print-toolbar__actions">
                <a href="{{ route('incidents.show', $incident) }}" class="ops-button ops-button--secondary">
                    {{ __('Back to incident detail') }}
                </a>
                <button type="button" class="ops-button" onclick="window.print()">
                    {{ __('Print summary') }}
                </button>
            </div>
        </div>
    </x-slot>

    <section class="ops-print-header">
        <div>
            <p class="ops-print-header__eyebrow">{{ __('Lab issue evidence pack') }}</p>
            <h1 class="ops-print-header__title">{{ $incident->title }}</h1>
            <p class="ops-print-header__body">
                {{ __('This print-friendly summary captures the reported lab issue, current accountability state, and the most relevant handling context without turning the incident into a report theater document.') }}
            </p>
        </div>

        <div class="ops-print-chip-row">
            <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Issue detail') }}</span>
            <span class="ops-shell-chip">{{ __($incident->status->value) }}</span>
            <span class="ops-shell-chip">{{ __($incident->severity->value) }}</span>
            <span class="ops-shell-chip">{{ $incident->category->value }}</span>
        </div>
    </section>

    <section class="ops-print-grid ops-print-grid--summary">
        <article class="ops-recap-panel">
            <p class="ops-recap-panel__title">{{ __('Incident summary') }}</p>
            <dl class="ops-detail-stack">
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Reported by') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $incident->creator?->name ?? __('Unknown') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Reported at') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $incident->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Owner') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $incident->owner?->name ?? __('Unowned') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Follow-up target') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $incident->follow_up_due_at?->format('M d, Y') ?? __('Not set') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Resolved at') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $incident->resolved_at?->format('M d, Y H:i') ?? __('Still active') }}</dd>
                </div>
            </dl>
        </article>

        <article class="ops-recap-panel ops-recap-panel--subtle">
            <p class="ops-recap-panel__title">{{ __('Queue pressure snapshot') }}</p>
            <div class="ops-history-summary-grid">
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Age') }}</p>
                    <p class="ops-glance-card__value">{{ $ageInDays }}</p>
                </div>
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Stale') }}</p>
                    <p class="ops-glance-card__value">{{ $isStale ? __('Yes') : __('No') }}</p>
                </div>
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Needs owner') }}</p>
                    <p class="ops-glance-card__value">{{ $needsOwner ? __('Yes') : __('No') }}</p>
                </div>
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Follow-up overdue') }}</p>
                    <p class="ops-glance-card__value">{{ $isFollowUpOverdue ? __('Yes') : __('No') }}</p>
                </div>
            </div>
        </article>
    </section>

    @if ($latestNextActionNote || $latestResolutionNote)
        <section class="ops-print-section ops-print-grid">
            @if ($latestNextActionNote)
                <x-ops.callout title="Latest follow-up direction" tone="warning">
                    {{ $latestNextActionNote }}
                </x-ops.callout>
            @endif

            @if ($latestResolutionNote)
                <x-ops.callout title="Latest resolution summary" tone="success">
                    {{ $latestResolutionNote }}
                </x-ops.callout>
            @endif
        </section>
    @endif

    <section class="ops-print-section ops-print-grid ops-print-grid--summary">
        <article class="ops-incident-panel" data-severity="{{ $incident->severity->value }}">
            <p class="ops-incident-panel__eyebrow">{{ __('Description') }}</p>
            <h2 class="ops-incident-panel__title">{{ __('What was reported') }}</h2>
            <p class="ops-incident-panel__body whitespace-pre-line">{{ $incident->description }}</p>
        </article>

        <article class="ops-incident-panel" data-severity="{{ $incident->severity->value }}">
            <p class="ops-incident-panel__eyebrow">{{ __('Attachment') }}</p>
            <h2 class="ops-incident-panel__title">{{ $incident->attachment_path ? __('Supporting file available') : __('No attachment provided') }}</h2>
            <p class="ops-incident-panel__body">
                {{ $incident->attachment_path
                    ? __('The uploaded file is available in the live product detail view when the supervisor needs supporting proof.')
                    : __('This issue was reported without an attachment.') }}
            </p>
            @if ($incident->attachment_path)
                <p class="ops-text-muted text-sm">{{ asset('storage/' . $incident->attachment_path) }}</p>
            @endif
        </article>
    </section>

    <section class="ops-print-section">
        <div class="ops-section-heading">
            <div>
                <p class="ops-section-heading__eyebrow">{{ __('Activity trail') }}</p>
                <h2 class="ops-section-heading__title">{{ __('Handling history') }}</h2>
                <p class="ops-section-heading__body">{{ __('Use the trail below when you need the compact sequence of what happened after the incident was reported.') }}</p>
            </div>
        </div>

        <ul role="list" class="ops-timeline mt-6">
            @foreach ($incident->activities->sortByDesc('created_at') as $activity)
                <li class="ops-timeline__item">
                    <span class="ops-timeline__dot" aria-hidden="true"></span>
                    <div class="ops-timeline__card">
                        <div class="ops-incident-sequence__item">
                            <div class="ops-incident-sequence__header">
                                <div>
                                    <p class="ops-incident-sequence__title">{{ str_replace('_', ' ', ucfirst($activity->action_type)) }}</p>
                                    <p class="ops-incident-sequence__meta">
                                        {{ $activity->actor?->name ?? __('Unknown') }} · {{ $activity->created_at?->format('M d, Y H:i') ?? __('Unknown time') }}
                                    </p>
                                </div>
                            </div>
                            <p class="ops-incident-sequence__body">{{ $activity->summary }}</p>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
</x-layouts.print>
