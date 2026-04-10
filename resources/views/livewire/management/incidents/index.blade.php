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

    @php
        $severityBadge = fn (string $severity) => match ($severity) {
            'High' => 'ops-badge--danger',
            'Medium' => 'ops-badge--warning',
            default => 'ops-badge--info',
        };

        $statusBadge = fn (string $status) => match ($status) {
            'Resolved' => 'ops-badge--success',
            'In Progress' => 'ops-badge--warning',
            default => 'ops-badge--info',
        };
    @endphp

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
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                @if($incidents->isEmpty())
                    <p class="text-sm text-[var(--app-text-muted)]">No incidents match the current filters.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="ops-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Category</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Severity</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Status</th>
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
                                            <span class="ops-badge {{ $severityBadge($incident->severity) }}">
                                                {{ $incident->severity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            <span class="ops-badge {{ $statusBadge($incident->status) }}">
                                                {{ $incident->status }}
                                            </span>
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
