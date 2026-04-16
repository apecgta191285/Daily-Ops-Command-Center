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
        <section class="ops-card overflow-hidden">
            <div class="ops-card__header">
                <h2 class="text-base font-semibold text-[var(--app-heading)]">Needs Attention Today</h2>
                <p class="mt-1 text-sm text-[var(--app-text-muted)]">Fast signals for the items management should review first.</p>
            </div>

            <div class="ops-card__body">
                @if ($attentionItems === [])
                    <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-4">
                        <p class="text-sm font-medium text-[var(--app-heading)]">No urgent operational alerts right now.</p>
                        <p class="mt-1 text-sm text-[var(--app-text-muted)]">Current dashboard signals do not show overdue high-risk issues or checklist pressure beyond the expected flow.</p>
                    </div>
                @else
                    <div class="grid gap-4 xl:grid-cols-3">
                        @foreach ($attentionItems as $item)
                            @php
                                $toneClasses = match ($item['tone']) {
                                    'danger' => 'border-[var(--app-danger-border)] bg-[var(--app-danger-bg)]',
                                    'warning' => 'border-[var(--app-warning-border)] bg-[var(--app-warning-bg)]',
                                    default => 'border-[var(--app-border)] bg-[var(--app-surface-elevated)]',
                                };
                            @endphp

                            <article class="rounded-2xl border p-4 {{ $toneClasses }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-2">
                                        <h3 class="text-sm font-semibold text-[var(--app-heading)]">{{ $item['title'] }}</h3>
                                        <p class="text-sm text-[var(--app-text-muted)]">{{ $item['description'] }}</p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <p class="text-2xl font-semibold text-[var(--app-heading)]">{{ $item['count'] }}</p>
                                        <p class="text-xs uppercase tracking-[0.08em] text-[var(--app-text-muted)]">items</p>
                                    </div>
                                </div>

                                @if ($item['url'] && $item['actionLabel'])
                                    <div class="mt-4">
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

        <div class="grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,1.1fr)_minmax(0,1.3fr)]">
            <section class="ops-card overflow-hidden">
                <div class="ops-card__header">
                    <h2 class="text-base font-semibold text-[var(--app-heading)]">Checklist Trend</h2>
                    <p class="mt-1 text-sm text-[var(--app-text-muted)]">Compare daily completion against yesterday&apos;s baseline.</p>
                </div>
                <div class="ops-card__body">
                    <p class="text-3xl font-semibold text-[var(--app-heading)]">{{ $checklistTrend['todayRate'] }}%</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">Yesterday: {{ $checklistTrend['yesterdayRate'] }}%</p>
                    <p class="mt-3 text-sm font-medium text-[var(--app-heading)]">
                        @if ($checklistTrend['direction'] === 'up')
                            Up {{ $checklistTrend['difference'] }} points from yesterday
                        @elseif ($checklistTrend['direction'] === 'down')
                            Down {{ $checklistTrend['difference'] }} points from yesterday
                        @else
                            Flat versus yesterday
                        @endif
                    </p>
                </div>
            </section>

            <section class="ops-card overflow-hidden">
                <div class="ops-card__header">
                    <h2 class="text-base font-semibold text-[var(--app-heading)]">Incident Intake Trend</h2>
                    <p class="mt-1 text-sm text-[var(--app-text-muted)]">Track how many incidents were reported today compared with yesterday.</p>
                </div>
                <div class="ops-card__body">
                    <p class="text-3xl font-semibold text-[var(--app-heading)]">{{ $incidentIntakeTrend['todayCount'] }}</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">Yesterday: {{ $incidentIntakeTrend['yesterdayCount'] }} reported</p>
                    <p class="mt-3 text-sm font-medium text-[var(--app-heading)]">
                        @if ($incidentIntakeTrend['direction'] === 'up')
                            Up {{ $incidentIntakeTrend['difference'] }} incidents from yesterday
                        @elseif ($incidentIntakeTrend['direction'] === 'down')
                            Down {{ $incidentIntakeTrend['difference'] }} incidents from yesterday
                        @else
                            Intake is flat versus yesterday
                        @endif
                    </p>
                </div>
            </section>

            <section class="ops-card overflow-hidden">
                <div class="ops-card__header">
                    <h2 class="text-base font-semibold text-[var(--app-heading)]">Operational Hotspots</h2>
                    <p class="mt-1 text-sm text-[var(--app-text-muted)]">The categories carrying the heaviest unresolved workload right now.</p>
                </div>
                <div class="ops-card__body">
                    @if ($hotspotCategories === [])
                        <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-4">
                            <p class="text-sm font-medium text-[var(--app-heading)]">No unresolved category hotspots right now.</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">Once unresolved incidents accumulate in one category, this summary will highlight the pressure point.</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach ($hotspotCategories as $hotspot)
                                <li class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-sm font-semibold text-[var(--app-heading)]">{{ $hotspot['category'] }}</p>
                                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">
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

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <section class="ops-card">
                <div class="ops-card__body">
                    <p class="text-sm font-medium text-[var(--app-text-muted)]">Checklist Completion Today</p>
                    <p class="mt-3 text-3xl font-semibold text-[var(--app-heading)]">{{ $completionRate }}%</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">
                        {{ $submittedTodayRuns }} of {{ $todayRuns }} checklist runs submitted
                    </p>
                </div>
            </section>

            <section class="ops-card">
                <div class="ops-card__body">
                    <p class="text-sm font-medium text-[var(--app-text-muted)]">Open Incidents</p>
                    <p class="mt-3 text-3xl font-semibold text-[var(--app-heading)]">{{ $incidentCounts['Open'] }}</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">Incidents still waiting for active handling</p>
                </div>
            </section>

            <section class="ops-card">
                <div class="ops-card__body">
                    <p class="text-sm font-medium text-[var(--app-text-muted)]">In Progress</p>
                    <p class="mt-3 text-3xl font-semibold text-[var(--app-heading)]">{{ $incidentCounts['In Progress'] }}</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">Incidents currently being worked on</p>
                </div>
            </section>

            <section class="ops-card">
                <div class="ops-card__body">
                    <p class="text-sm font-medium text-[var(--app-text-muted)]">Resolved</p>
                    <p class="mt-3 text-3xl font-semibold text-[var(--app-heading)]">{{ $incidentCounts['Resolved'] }}</p>
                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">Incidents closed in the current dataset</p>
                </div>
            </section>
        </div>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__header">
                <h2 class="text-base font-semibold text-[var(--app-heading)]">Recent Incidents</h2>
                <p class="mt-1 text-sm text-[var(--app-text-muted)]">Latest 5 incidents from the live database</p>
            </div>

            <div class="ops-card__body">
                @if ($recentIncidents->isEmpty())
                    <div class="rounded-2xl border border-dashed border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-5">
                        <p class="text-sm font-medium text-[var(--app-heading)]">No incidents available yet.</p>
                        <p class="mt-1 text-sm text-[var(--app-text-muted)]">Once staff report an issue, the latest incidents will appear here so supervisors can review and track follow-up from the dashboard.</p>
                    </div>
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
</x-layouts::app>
