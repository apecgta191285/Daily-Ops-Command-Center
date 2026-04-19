<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Operational history') }}</p>
                <h2 class="ops-page__title">{{ __('Checklist Run Archive') }}</h2>
                <p class="ops-page-intro__body">
                    Review submitted checklist runs by date, scope, and operator so the team can revisit what actually happened without reading raw tables or relying on memory.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Archive review') }}</span>
                    <span class="ops-shell-chip">{{ __('Submitted runs only') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope-aware') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('dashboard') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-card__body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label for="run_date" class="ops-field-label">{{ __('Run date') }}</label>
                        <input id="run_date" type="date" wire:model.live="runDate" class="ops-control">
                    </div>

                    <div>
                        <label for="scope" class="ops-field-label">{{ __('Scope lane') }}</label>
                        <select id="scope" wire:model.live="scope" class="ops-control">
                            <option value="">{{ __('All scope lanes') }}</option>
                            @foreach ($scopeOptions as $scopeOption)
                                <option value="{{ $scopeOption['route_key'] }}">{{ $scopeOption['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="operator" class="ops-field-label">{{ __('Operator') }}</label>
                        <select id="operator" wire:model.live="operator" class="ops-control">
                            <option value="">{{ __('All operators') }}</option>
                            @foreach ($operators as $operatorOption)
                                <option value="{{ $operatorOption->id }}">{{ $operatorOption->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($runDate !== '' || $scope !== '' || $operator !== '')
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <span class="ops-text-heading text-sm font-medium">{{ __('Active archive filters:') }}</span>
                        @if ($runDate !== '')
                            <span class="ops-chip ops-chip--info">{{ $runDate }}</span>
                        @endif
                        @if ($scope !== '')
                            <span class="ops-chip ops-chip--neutral">{{ \App\Domain\Checklists\Enums\ChecklistScope::fromRouteKey($scope)?->value }}</span>
                        @endif
                        @if ($operator !== '')
                            <span class="ops-chip ops-chip--neutral">{{ $operators->firstWhere('id', (int) $operator)?->name }}</span>
                        @endif
                        <button type="button" wire:click="clearFilters" class="ops-button ops-button--secondary">
                            {{ __('Clear filters') }}
                        </button>
                    </div>
                @endif
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
            <div class="ops-card__body">
                @if ($runs->isEmpty())
                    <x-ops.empty-state
                        title="No archived checklist runs match the current filters."
                        body="Try another date, scope lane, or operator. This archive only shows runs that were actually submitted."
                    />
                @else
                    <div class="ops-table-wrap">
                        <table class="ops-table ops-table--responsive min-w-full">
                            <thead>
                                <tr>
                                    <th>{{ __('Run date') }}</th>
                                    <th>{{ __('Template') }}</th>
                                    <th>{{ __('Scope') }}</th>
                                    <th>{{ __('Operator') }}</th>
                                    <th>{{ __('Submitted') }}</th>
                                    <th>{{ __('Signals') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($runs as $index => $run)
                                    <tr class="ops-table__row" data-motion="scale-soft" data-motion-delay="{{ 120 + ($index * 12) }}">
                                        <td data-label="Run date" class="ops-text-heading px-4 py-4 text-sm font-medium">
                                            {{ $run->run_date->format('M d, Y') }}
                                        </td>
                                        <td data-label="Template" class="ops-text-muted px-4 py-4 text-sm">
                                            {{ $run->template?->title ?? __('Unknown template') }}
                                        </td>
                                        <td data-label="Scope" class="px-4 py-4 text-sm">
                                            <span class="ops-chip ops-chip--neutral">{{ $run->assigned_team_or_scope ?: __('Unknown scope') }}</span>
                                        </td>
                                        <td data-label="Operator" class="ops-text-muted px-4 py-4 text-sm">
                                            {{ $run->creator?->name ?? __('Unknown') }}
                                        </td>
                                        <td data-label="Submitted" class="ops-text-muted px-4 py-4 text-sm">
                                            {{ $run->submitted_at?->format('M d, Y H:i') ?? __('Unknown') }}
                                        </td>
                                        <td data-label="Signals" class="px-4 py-4 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($run->not_done_items_count > 0)
                                                    <span class="ops-badge ops-badge--warning">{{ $run->not_done_items_count }} {{ __('Not Done') }}</span>
                                                @else
                                                    <span class="ops-badge ops-badge--success">{{ __('All Done') }}</span>
                                                @endif

                                                @if ($run->noted_items_count > 0)
                                                    <span class="ops-badge ops-badge--neutral">{{ $run->noted_items_count }} {{ __('note(s)') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Action" class="px-4 py-4 text-right text-sm">
                                            <a href="{{ route('checklists.history.show', $run) }}" class="ops-button ops-button--secondary" wire:navigate>
                                                {{ __('View recap') }}
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
