<x-layouts::app :title="__('Dashboard')">
    @php
        $unresolvedCount = ($incidentCounts['Open'] ?? 0) + ($incidentCounts['In Progress'] ?? 0);
        $totalVisibleIncidents = max(array_sum($incidentCounts), 1);
        $openIncidentShare = (int) round((($incidentCounts['Open'] ?? 0) / $totalVisibleIncidents) * 100);
        $inProgressIncidentShare = (int) round((($incidentCounts['In Progress'] ?? 0) / $totalVisibleIncidents) * 100);
        $resolvedIncidentShare = (int) round((($incidentCounts['Resolved'] ?? 0) / $totalVisibleIncidents) * 100);
        $hotspotMaxCount = max(array_map(static fn (array $hotspot): int => $hotspot['unresolvedCount'], $hotspotCategories ?: [['unresolvedCount' => 1]]));
        $checklistTrendTone = match ($checklistTrend['direction']) {
            'up' => 'ops-trend-pill--up',
            'down' => 'ops-trend-pill--down',
            default => 'ops-trend-pill--flat',
        };
        $incidentTrendTone = match ($incidentIntakeTrend['direction']) {
            'up' => 'ops-trend-pill--down',
            'down' => 'ops-trend-pill--up',
            default => 'ops-trend-pill--flat',
        };
        $checklistTrendLabel = match ($checklistTrend['direction']) {
            'up' => 'Improving',
            'down' => 'Below yesterday',
            default => 'Holding steady',
        };
        $incidentTrendLabel = match ($incidentIntakeTrend['direction']) {
            'up' => 'Higher intake',
            'down' => 'Lower intake',
            default => 'Intake steady',
        };
        $scopeLaneIncompleteCount = collect($scopeChecklistLanes)->filter(fn (array $lane) => in_array($lane['state'], ['not_started', 'in_progress'], true))->count();
    @endphp

    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Management surface') }}</p>
                <h2 class="ops-page__title">{{ __('Dashboard') }}</h2>
                <p class="ops-page-intro__body">
                    Track checklist completion, scope-lane coverage, unresolved pressure, and operational drift from one command frame.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Live command view') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope runtime truth') }}</span>
                    <span class="ops-shell-chip">{{ __('Checklist momentum') }}</span>
                    <span class="ops-shell-chip">{{ __('Incident hotspots') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary">
                    {{ __('Review incidents') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Management Visibility</p>
                    <h3 class="ops-hero__title">Operational command view for today&apos;s workload.</h3>
                    <p class="ops-hero__lead">
                        Use this surface to spot unresolved risk, compare today with yesterday, and confirm whether opening, midday, and closing work is actually live and complete.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">Needs Attention Today</span>
                        <span class="ops-shell-chip">Scope lane status</span>
                        <span class="ops-shell-chip">Checklist Trend</span>
                        <span class="ops-shell-chip">Operational Hotspots</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Today at a glance</p>
                        <p class="ops-hero__aside-value">{{ $completionRate }}%</p>
                        <p class="ops-hero__aside-copy">
                            Checklist completion is based on {{ $submittedTodayRuns }} submitted run(s) out of {{ $todayRuns }} expected run(s) today.
                        </p>
                    </div>

                    <div class="ops-glance-grid--hero">
                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Unresolved queue</p>
                            <p class="ops-glance-card__value">{{ $unresolvedCount }}</p>
                            <p class="ops-glance-card__meta">Open and in-progress incidents still waiting for management closure.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Incomplete lanes</p>
                            <p class="ops-glance-card__value">{{ $scopeLaneIncompleteCount }}</p>
                            <p class="ops-glance-card__meta">Opening, midday, or closing lanes that still need checklist progress today.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Open now</p>
                            <p class="ops-glance-card__value">{{ $incidentCounts['Open'] }}</p>
                            <p class="ops-glance-card__meta">New incidents that still need first response.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="ops-command-grid ops-command-grid--dashboard">
            <div class="ops-stack">
                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Priority queue</p>
                            <h2 class="ops-section-heading__title">Needs Attention Today</h2>
                            <p class="ops-section-heading__body">Fast signals for the items management should review first.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        @if ($attentionItems === [])
                            <x-ops.empty-state
                                title="No urgent operational alerts right now."
                                body="Current dashboard signals do not show overdue high-risk issues or checklist pressure beyond the expected flow."
                            />
                        @else
                            <div class="ops-signal-grid">
                                @foreach ($attentionItems as $item)
                                    @php
                                        $toneClass = match ($item['tone']) {
                                            'danger' => 'ops-signal-card--danger',
                                            'warning' => 'ops-signal-card--warning',
                                            default => 'ops-signal-card--neutral',
                                        };
                                    @endphp

                                    <article class="ops-signal-card {{ $toneClass }}">
                                        <div class="ops-signal-card__header">
                                            <div>
                                                <h3 class="ops-signal-card__title">{{ $item['title'] }}</h3>
                                                <p class="ops-signal-card__body">{{ $item['description'] }}</p>
                                            </div>

                                            <div class="text-right">
                                                <p class="ops-signal-card__count">{{ $item['count'] }}</p>
                                                <p class="ops-eyebrow-label">items</p>
                                            </div>
                                        </div>

                                        @if ($item['url'] && $item['actionLabel'])
                                            <div class="ops-signal-card__footer">
                                                <a href="{{ $item['url'] }}" class="ops-button ops-button--secondary">
                                                    {{ $item['actionLabel'] }}
                                                </a>
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Runtime coverage</p>
                            <h2 class="ops-section-heading__title">Checklist by Scope</h2>
                            <p class="ops-section-heading__body">Check whether opening, midday, and closing lanes are configured and actually moving today.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-signal-grid">
                            @foreach ($scopeChecklistLanes as $lane)
                                @php
                                    $toneClass = match ($lane['state']) {
                                        'unavailable' => 'ops-signal-card--danger',
                                        'not_started', 'in_progress' => 'ops-signal-card--warning',
                                        default => 'ops-signal-card--neutral',
                                    };
                                    $stateLabel = match ($lane['state']) {
                                        'unavailable' => 'Missing live template',
                                        'not_started' => 'Not started',
                                        'in_progress' => 'In progress',
                                        default => 'Submitted',
                                    };
                                @endphp

                                <article class="ops-signal-card {{ $toneClass }}">
                                    <div class="ops-signal-card__header">
                                        <div>
                                            <h3 class="ops-signal-card__title">{{ $lane['scope'] }}</h3>
                                            <p class="ops-signal-card__body">
                                                {{ $lane['template_title'] ?? __('No active template') }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="ops-signal-card__count">{{ $lane['completion_percentage'] }}%</p>
                                            <p class="ops-eyebrow-label">submitted</p>
                                        </div>
                                    </div>

                                    <div class="ops-signal-card__body">
                                        @if ($lane['state'] === 'unavailable')
                                            Management cannot verify this operating lane yet because no live template is active for the scope.
                                        @elseif ($lane['state'] === 'not_started')
                                            A live template exists, but staff have not opened this lane today.
                                        @elseif ($lane['state'] === 'in_progress')
                                            Staff have entered this lane, but not every run has been submitted yet.
                                        @else
                                            All runs created for this lane today are already submitted.
                                        @endif
                                    </div>

                                    <div class="ops-signal-card__footer flex items-center justify-between gap-3">
                                        <span class="ops-chip {{ $lane['state'] === 'submitted' ? 'ops-chip--success' : ($lane['state'] === 'unavailable' ? '' : 'ops-chip--warning') }}">
                                            {{ $stateLabel }}
                                        </span>
                                        <span class="ops-text-muted text-xs">
                                            {{ $lane['submitted_runs'] }}/{{ $lane['total_runs'] }} submitted
                                        </span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>

                <div class="ops-stat-grid" data-motion-group data-stagger-base="70" data-stagger-unit="40" data-stagger-max="220">
                    <x-ops.stat-card
                        kicker="Checklist Completion Today"
                        :value="$completionRate.'%'"
                        :meta="$submittedTodayRuns.' of '.$todayRuns.' checklist runs submitted'"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$completionRate" :size="58" />
                                <span class="ops-arc-wrapper__label">{{ $completionRate }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>

                    <x-ops.stat-card
                        kicker="Open Incidents"
                        :value="$incidentCounts['Open']"
                        meta="Incidents still waiting for active handling"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$openIncidentShare" :size="58" tone="danger" />
                                <span class="ops-arc-wrapper__label">{{ $openIncidentShare }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>

                    <x-ops.stat-card
                        kicker="In Progress"
                        :value="$incidentCounts['In Progress']"
                        meta="Incidents currently being worked on"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$inProgressIncidentShare" :size="58" tone="warning" />
                                <span class="ops-arc-wrapper__label">{{ $inProgressIncidentShare }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>

                    <x-ops.stat-card
                        kicker="Resolved"
                        :value="$incidentCounts['Resolved']"
                        meta="Incidents closed in the current dataset"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$resolvedIncidentShare" :size="58" tone="success" />
                                <span class="ops-arc-wrapper__label">{{ $resolvedIncidentShare }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>
                </div>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Live queue</p>
                            <h2 class="ops-section-heading__title">Recent Incidents</h2>
                            <p class="ops-section-heading__body">Latest 5 incidents from the live database.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        @if ($recentIncidents->isEmpty())
                            <x-ops.empty-state
                                title="No incidents available yet."
                                body="Once staff report an issue, the latest incidents will appear here so supervisors can review and track follow-up from the dashboard."
                            />
                        @else
                            <div class="ops-table-wrap">
                                <table class="ops-table ops-table--responsive min-w-full">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Severity</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentIncidents as $incident)
                                            <tr class="ops-table__row">
                                                <td data-label="Title" class="ops-text-heading px-4 py-4 text-sm font-medium">{{ $incident->title }}</td>
                                                <td data-label="Status" class="px-4 py-4 text-sm">
                                                    <x-incidents.status-badge :status="$incident->status" />
                                                </td>
                                                <td data-label="Severity" class="px-4 py-4 text-sm">
                                                    <x-incidents.severity-badge :severity="$incident->severity" />
                                                </td>
                                                <td data-label="Detail" class="px-4 py-4 text-right text-sm">
                                                    <a href="{{ route('incidents.show', $incident) }}" class="ops-button ops-button--secondary">
                                                        View details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="ops-stack">
                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Momentum</p>
                            <h2 class="ops-section-heading__title">Checklist Trend</h2>
                            <p class="ops-section-heading__body">Compare daily completion against yesterday&apos;s baseline.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-trend-card">
                            <div class="ops-trend-card__header">
                                <div>
                                    <p class="ops-trend-card__eyebrow">Completion momentum</p>
                                    <p class="ops-trend-card__value">{{ $checklistTrend['todayRate'] }}%</p>
                                </div>

                                @if (($checklistTrend['series'] ?? []) !== [])
                                    <div class="ops-trend-card__visual">
                                        <x-ops.sparkline :points="$checklistTrend['series']" :width="88" :height="30" />
                                    </div>
                                @endif

                                <span class="ops-trend-pill {{ $checklistTrendTone }}">
                                    {{ $checklistTrendLabel }}
                                </span>
                            </div>

                            <p class="ops-trend-card__meta">Yesterday: {{ $checklistTrend['yesterdayRate'] }}%</p>
                            <p class="ops-trend-card__copy">
                                @if ($checklistTrend['direction'] === 'up')
                                    Up {{ $checklistTrend['difference'] }} points from yesterday
                                @elseif ($checklistTrend['direction'] === 'down')
                                    Down {{ $checklistTrend['difference'] }} points from yesterday
                                @else
                                    Flat versus yesterday
                                @endif
                            </p>

                            <div class="ops-compare-list">
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">Today</span>
                                    <strong class="ops-compare-list__value">{{ $submittedTodayRuns }} / {{ $todayRuns ?: 0 }} submitted</strong>
                                </div>
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">Yesterday</span>
                                    <strong class="ops-compare-list__value">{{ $checklistTrend['yesterdayRate'] }}% completion baseline</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="120">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Intake pressure</p>
                            <h2 class="ops-section-heading__title">Incident Intake Trend</h2>
                            <p class="ops-section-heading__body">Track how many incidents were reported today compared with yesterday.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-trend-card">
                            <div class="ops-trend-card__header">
                                <div>
                                    <p class="ops-trend-card__eyebrow">Daily intake</p>
                                    <p class="ops-trend-card__value">{{ $incidentIntakeTrend['todayCount'] }}</p>
                                </div>

                                @if (($incidentIntakeTrend['series'] ?? []) !== [])
                                    <div class="ops-trend-card__visual">
                                        <x-ops.sparkline
                                            :points="$incidentIntakeTrend['series']"
                                            :width="88"
                                            :height="30"
                                            :tone="$incidentIntakeTrend['direction'] === 'up' ? 'warning' : ($incidentIntakeTrend['direction'] === 'down' ? 'success' : 'primary')"
                                        />
                                    </div>
                                @endif

                                <span class="ops-trend-pill {{ $incidentTrendTone }}">
                                    {{ $incidentTrendLabel }}
                                </span>
                            </div>

                            <p class="ops-trend-card__meta">Yesterday: {{ $incidentIntakeTrend['yesterdayCount'] }} reported</p>
                            <p class="ops-trend-card__copy">
                                @if ($incidentIntakeTrend['direction'] === 'up')
                                    Up {{ $incidentIntakeTrend['difference'] }} incidents from yesterday
                                @elseif ($incidentIntakeTrend['direction'] === 'down')
                                    Down {{ $incidentIntakeTrend['difference'] }} incidents from yesterday
                                @else
                                    Intake is flat versus yesterday
                                @endif
                            </p>

                            <div class="ops-compare-list">
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">Open now</span>
                                    <strong class="ops-compare-list__value">{{ $incidentCounts['Open'] }} waiting for first handling</strong>
                                </div>
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">In progress</span>
                                    <strong class="ops-compare-list__value">{{ $incidentCounts['In Progress'] }} currently active</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="160">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Hotspot scan</p>
                            <h2 class="ops-section-heading__title">Operational Hotspots</h2>
                            <p class="ops-section-heading__body">The categories carrying the heaviest unresolved workload right now.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        @if ($hotspotCategories === [])
                            <x-ops.empty-state
                                title="No unresolved category hotspots right now."
                                body="Once unresolved incidents accumulate in one category, this summary will highlight the pressure point."
                            />
                        @else
                            <ol class="ops-hotspot-list">
                                @foreach ($hotspotCategories as $index => $hotspot)
                                    @php($intensity = max(18, (int) round(($hotspot['unresolvedCount'] / max($hotspotMaxCount, 1)) * 100)))
                                    <li class="ops-hotspot-list__item" data-hotspot-rank="{{ $index + 1 }}">
                                        <div class="ops-hotspot-list__row">
                                            <div class="ops-hotspot-list__identity">
                                                <span class="ops-hotspot-list__rank">{{ $index + 1 }}</span>
                                                <div class="min-w-0">
                                                    <p class="ops-hotspot-list__title">{{ $hotspot['category'] }}</p>
                                                    <p class="ops-hotspot-list__meta">
                                                        {{ $hotspot['unresolvedCount'] }} unresolved
                                                        @if ($hotspot['staleCount'] > 0)
                                                            · {{ $hotspot['staleCount'] }} stale
                                                        @else
                                                            · no stale incidents right now
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($hotspot['url'])
                                                <a href="{{ $hotspot['url'] }}" class="ops-button ops-button--secondary">
                                                    Review
                                                </a>
                                            @endif
                                        </div>

                                        <div class="ops-hotspot-list__meter" aria-hidden="true">
                                            <div class="ops-hotspot-list__meter-fill" data-meter-target="{{ $intensity }}"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts::app>
