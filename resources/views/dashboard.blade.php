<x-layouts::app :title="__('Dashboard')">
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="ops-page__title">{{ __('Dashboard') }}</h2>
                <p class="text-sm">
                    Track checklist completion and monitor live incident workload at a glance.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <section class="ops-hero">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Management Visibility</p>
                    <h3 class="ops-hero__title">Operational command view for today&apos;s workload.</h3>
                    <p class="ops-hero__lead">
                        Use this surface to spot unresolved risk, compare today with yesterday, and jump straight into the queue that needs attention first.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">Needs Attention Today</span>
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

                    <div class="ops-hero__aside-stack">
                        <div class="ops-shell-chip">
                            <span>Open incidents</span>
                            <strong class="font-semibold text-white">{{ $incidentCounts['Open'] }}</strong>
                        </div>
                        <div class="ops-shell-chip">
                            <span>In Progress</span>
                            <strong class="font-semibold text-white">{{ $incidentCounts['In Progress'] }}</strong>
                        </div>
                        <div class="ops-shell-chip">
                            <span>Resolved</span>
                            <strong class="font-semibold text-white">{{ $incidentCounts['Resolved'] }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="ops-command-grid ops-command-grid--dashboard">
            <div class="ops-stack">
                <section class="ops-card overflow-hidden">
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
                                                <p class="text-xs uppercase tracking-[0.12em] text-[var(--app-text-muted)]">items</p>
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

                <div class="ops-stat-grid">
                    <x-ops.stat-card
                        kicker="Checklist Completion Today"
                        :value="$completionRate.'%'"
                        :meta="$submittedTodayRuns.' of '.$todayRuns.' checklist runs submitted'"
                    />

                    <x-ops.stat-card
                        kicker="Open Incidents"
                        :value="$incidentCounts['Open']"
                        meta="Incidents still waiting for active handling"
                    />

                    <x-ops.stat-card
                        kicker="In Progress"
                        :value="$incidentCounts['In Progress']"
                        meta="Incidents currently being worked on"
                    />

                    <x-ops.stat-card
                        kicker="Resolved"
                        :value="$incidentCounts['Resolved']"
                        meta="Incidents closed in the current dataset"
                    />
                </div>

                <section class="ops-card overflow-hidden">
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
                            <div class="overflow-x-auto">
                                <table class="ops-table min-w-full">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Title</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Severity</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentIncidents as $incident)
                                            <tr class="bg-white">
                                                <td class="px-4 py-4 text-sm font-medium text-[var(--app-heading)]">{{ $incident->title }}</td>
                                                <td class="px-4 py-4 text-sm">
                                                    <x-incidents.status-badge :status="$incident->status" />
                                                </td>
                                                <td class="px-4 py-4 text-sm">
                                                    <x-incidents.severity-badge :severity="$incident->severity" />
                                                </td>
                                                <td class="px-4 py-4 text-right text-sm">
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
                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Momentum</p>
                            <h2 class="ops-section-heading__title">Checklist Trend</h2>
                            <p class="ops-section-heading__body">Compare daily completion against yesterday&apos;s baseline.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-progress-panel">
                            <p class="text-3xl font-semibold text-[var(--app-heading)]">{{ $checklistTrend['todayRate'] }}%</p>
                            <p class="mt-2 text-sm text-[var(--app-text-muted)]">Yesterday: {{ $checklistTrend['yesterdayRate'] }}%</p>
                            <p class="mt-4 text-sm font-medium text-[var(--app-heading)]">
                                @if ($checklistTrend['direction'] === 'up')
                                    Up {{ $checklistTrend['difference'] }} points from yesterday
                                @elseif ($checklistTrend['direction'] === 'down')
                                    Down {{ $checklistTrend['difference'] }} points from yesterday
                                @else
                                    Flat versus yesterday
                                @endif
                            </p>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Intake pressure</p>
                            <h2 class="ops-section-heading__title">Incident Intake Trend</h2>
                            <p class="ops-section-heading__body">Track how many incidents were reported today compared with yesterday.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-progress-panel">
                            <p class="text-3xl font-semibold text-[var(--app-heading)]">{{ $incidentIntakeTrend['todayCount'] }}</p>
                            <p class="mt-2 text-sm text-[var(--app-text-muted)]">Yesterday: {{ $incidentIntakeTrend['yesterdayCount'] }} reported</p>
                            <p class="mt-4 text-sm font-medium text-[var(--app-heading)]">
                                @if ($incidentIntakeTrend['direction'] === 'up')
                                    Up {{ $incidentIntakeTrend['difference'] }} incidents from yesterday
                                @elseif ($incidentIntakeTrend['direction'] === 'down')
                                    Down {{ $incidentIntakeTrend['difference'] }} incidents from yesterday
                                @else
                                    Intake is flat versus yesterday
                                @endif
                            </p>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
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
                            <ul class="ops-detail-list">
                                @foreach ($hotspotCategories as $hotspot)
                                    <li class="ops-detail-list__item">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="ops-detail-list__title">{{ $hotspot['category'] }}</p>
                                                <p class="ops-detail-list__body">
                                                    {{ $hotspot['unresolvedCount'] }} unresolved
                                                    @if ($hotspot['staleCount'] > 0)
                                                        · {{ $hotspot['staleCount'] }} stale
                                                    @endif
                                                </p>
                                            </div>

                                            @if ($hotspot['url'])
                                                <a href="{{ $hotspot['url'] }}" class="ops-button ops-button--secondary">
                                                    Review
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts::app>
