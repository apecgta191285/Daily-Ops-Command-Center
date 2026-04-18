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
        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
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
                        <input type="checkbox" wire:model.live="unresolved" class="ops-choice__control">
                        <span>Only unresolved incidents</span>
                    </label>

                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="stale" class="ops-choice__control">
                        <span>Only stale incidents ({{ $this->staleThresholdDays }}+ days)</span>
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
            <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                <div class="ops-card__body ops-text-muted flex flex-wrap items-center gap-3 text-sm">
                    <span class="ops-text-heading font-medium">Active filter context:</span>
                    @if ($unresolved)
                        <span class="ops-chip ops-chip--info">Unresolved only</span>
                    @endif
                    @if ($stale)
                        <span class="ops-chip ops-chip--warning">Stale {{ $this->staleThresholdDays }}+ days</span>
                    @endif
                </div>
            </section>
        @endif

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="{{ ($unresolved || $stale) ? '120' : '80' }}">
            <div class="ops-card__body">
                @if($incidents->isEmpty())
                    <x-ops.empty-state
                        title="No incidents match the current filters."
                        body="Try clearing one or more filters, or wait for staff to report new incidents that match this view."
                    />
                @else
                    <div class="ops-table-wrap">
                        <table class="ops-table ops-table--responsive min-w-full">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Severity</th>
                                    <th>Status</th>
                                    <th>Attention</th>
                                    <th>Reported By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $index => $incident)
                                    <tr class="ops-table__row" data-motion="scale-soft" data-motion-delay="{{ 140 + ($index * 15) }}">
                                        <td data-label="Title" class="ops-text-heading px-4 py-4 text-sm font-medium">{{ $incident->title }}</td>
                                        <td data-label="Category" class="ops-text-muted px-4 py-4 text-sm">{{ $incident->category }}</td>
                                        <td data-label="Severity" class="px-4 py-4 text-sm">
                                            <x-incidents.severity-badge :severity="$incident->severity" />
                                        </td>
                                        <td data-label="Status" class="px-4 py-4 text-sm">
                                            <x-incidents.status-badge :status="$incident->status" />
                                        </td>
                                        <td data-label="Attention" class="ops-text-muted px-4 py-4 text-sm">
                                            @if ($incident->is_stale_for_attention)
                                                <span class="ops-badge ops-badge--warning">Stale</span>
                                            @else
                                                <span class="text-xs">-</span>
                                            @endif
                                        </td>
                                        <td data-label="Reported By" class="ops-text-muted px-4 py-4 text-sm">{{ $incident->creator?->name ?? 'Unknown' }}</td>
                                        <td data-label="Action" class="px-4 py-4 text-right text-sm">
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
