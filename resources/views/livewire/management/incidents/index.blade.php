<div>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="ops-page__title">{{ __('Incident List') }}</h2>
                <p class="text-sm">
                    Review, filter, and inspect reported incidents without changing workflow behavior.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label for="status" class="ops-field-label">Status</label>
                        <select id="status" wire:model.live="status" class="ops-control">
                            <option value="">All statuses</option>
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="category" class="ops-field-label">Category</label>
                        <select id="category" wire:model.live="category" class="ops-control">
                            <option value="">All categories</option>
                            @foreach($categories as $categoryOption)
                                <option value="{{ $categoryOption }}">{{ $categoryOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="severity" class="ops-field-label">Severity</label>
                        <select id="severity" wire:model.live="severity" class="ops-control">
                            <option value="">All severities</option>
                            @foreach($severities as $severityOption)
                                <option value="{{ $severityOption }}">{{ $severityOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="unresolved" class="h-4 w-4 border-[var(--app-border)] text-[var(--app-action-primary)] focus:ring-[var(--app-action-primary)]">
                        <span>Only unresolved incidents</span>
                    </label>

                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="stale" class="h-4 w-4 border-[var(--app-border)] text-[var(--app-action-primary)] focus:ring-[var(--app-action-primary)]">
                        <span>Only stale incidents (2+ days)</span>
                    </label>

                    @if ($status !== '' || $category !== '' || $severity !== '' || $unresolved || $stale)
                        <button type="button" wire:click="clearFilters" class="ops-button ops-button--secondary">
                            Clear filters
                        </button>
                    @endif
                </div>
            </div>
        </section>

        @if ($unresolved || $stale)
            <section class="ops-card overflow-hidden">
                <div class="ops-card__body flex flex-wrap items-center gap-3 text-sm text-[var(--app-text-muted)]">
                    <span class="font-medium text-[var(--app-heading)]">Active filter context:</span>
                    @if ($unresolved)
                        <span class="ops-badge ops-badge--info">Unresolved only</span>
                    @endif
                    @if ($stale)
                        <span class="ops-badge ops-badge--warning">Stale 2+ days</span>
                    @endif
                </div>
            </section>
        @endif

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                @if($incidents->isEmpty())
                    <div class="rounded-2xl border border-dashed border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-5">
                        <p class="text-sm font-medium text-[var(--app-heading)]">No incidents match the current filters.</p>
                        <p class="mt-1 text-sm text-[var(--app-text-muted)]">Try clearing one or more filters, or wait for staff to report new incidents that match this view.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="ops-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Severity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Attention</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Reported By</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $incident)
                                    <tr class="bg-white">
                                        <td class="px-4 py-4 text-sm font-medium text-[var(--app-heading)]">{{ $incident->title }}</td>
                                        <td class="px-4 py-4 text-sm text-[var(--app-text-muted)]">{{ $incident->category }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            <x-incidents.severity-badge :severity="$incident->severity" />
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <x-incidents.status-badge :status="$incident->status" />
                                        </td>
                                        <td class="px-4 py-4 text-sm text-[var(--app-text-muted)]">
                                            @if ($incident->status !== 'Resolved' && $incident->created_at->lte(now()->subDays(2)))
                                                <span class="ops-badge ops-badge--warning">Stale</span>
                                            @else
                                                <span class="text-xs">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm text-[var(--app-text-muted)]">{{ $incident->creator?->name ?? 'Unknown' }}</td>
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
</div>
