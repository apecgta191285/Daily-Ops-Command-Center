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
                    <p class="text-sm text-[var(--app-text-muted)]">No incidents available yet.</p>
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
