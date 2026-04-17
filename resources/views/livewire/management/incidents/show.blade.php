<div>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="ops-page__title">{{ __('Incident Detail') }}</h2>
                <p class="text-sm">
                    Review the report, supporting context, and current handling status.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6">
        @if (session()->has('message'))
            <div data-alert data-auto-dismiss="5000" role="status" aria-live="polite" class="ops-alert ops-alert--success">
                <div class="ops-alert__inner">
                    <div class="ops-alert__copy">{{ session('message') }}</div>
                    <button type="button" class="ops-alert__dismiss" data-dismiss-alert aria-label="{{ __('Dismiss message') }}">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>
        @endif

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-6">
                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-[var(--app-heading)]">{{ $incident->title }}</h3>
                        <p class="mt-2 text-sm text-[var(--app-text-muted)]">Reported by {{ $incident->creator?->name ?? 'Unknown' }} on {{ $incident->created_at->format('M d, Y H:i') }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                            <span class="ops-badge ops-badge--neutral">Open for {{ $this->ageInDays }} day{{ $this->ageInDays === 1 ? '' : 's' }}</span>
                            @if ($this->isStale)
                                <span class="ops-badge ops-badge--warning">Stale {{ $this->staleThresholdDays }}+ days</span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary">
                        Back to incident list
                    </a>
                </div>

                <dl class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Category</dt>
                        <dd class="mt-2 text-sm font-medium text-[var(--app-heading)]">{{ $incident->category }}</dd>
                    </div>
                    <div class="rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Severity</dt>
                        <dd class="mt-2 text-sm">
                            <x-incidents.severity-badge :severity="$incident->severity" />
                        </dd>
                    </div>
                    <div class="rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Current Status</dt>
                        <dd class="mt-2 text-sm">
                            <x-incidents.status-badge :status="$incident->status" />
                        </dd>
                    </div>
                    <div class="rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] px-4 py-3">
                        <dt class="text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Resolved At</dt>
                        <dd class="mt-2 text-sm font-medium text-[var(--app-heading)]">
                            {{ $incident->resolved_at?->format('M d, Y H:i') ?? 'Not resolved yet' }}
                        </dd>
                    </div>
                </dl>

                @if ($this->latestNextActionNote || $this->latestResolutionNote)
                    <div class="grid gap-4 xl:grid-cols-2">
                        @if ($this->latestNextActionNote)
                            <div class="rounded-xl border border-[var(--app-warning-border)] bg-[var(--app-warning-bg)] px-5 py-4">
                                <h4 class="text-sm font-semibold uppercase tracking-[0.08em] text-[var(--app-warning-text)]">Latest Follow-up Direction</h4>
                                <p class="mt-3 text-sm leading-6 text-[var(--app-heading)]">{{ $this->latestNextActionNote }}</p>
                            </div>
                        @endif

                        @if ($this->latestResolutionNote)
                            <div class="rounded-xl border border-[var(--app-success-border)] bg-[var(--app-success-bg)] px-5 py-4">
                                <h4 class="text-sm font-semibold uppercase tracking-[0.08em] text-[var(--app-success-text)]">Latest Resolution Summary</h4>
                                <p class="mt-3 text-sm leading-6 text-[var(--app-heading)]">{{ $this->latestResolutionNote }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(18rem,1fr)]">
                    <div class="rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] px-5 py-4">
                        <h4 class="text-sm font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Description</h4>
                        <p class="mt-3 whitespace-pre-line text-sm leading-6 text-[var(--app-heading)]">{{ $incident->description }}</p>
                    </div>

                    @if($incident->attachment_path)
                        <div class="rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] px-5 py-4">
                            <h4 class="text-sm font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Attachment</h4>
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $incident->attachment_path) }}" target="_blank" rel="noopener noreferrer" class="ops-button ops-button--secondary">
                                    View attachment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <form wire:submit="updateStatus" class="space-y-4">
                    <div>
                        <label for="status" class="ops-field-label">Update Status</label>
                        <select id="status" wire:model="status" class="ops-control">
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption }}">{{ $statusOption }}</option>
                            @endforeach
                        </select>
                        @error('status') <span class="ops-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="follow-up-note" class="ops-field-label">{{ $this->followUpNoteLabel }}</label>
                        <textarea
                            id="follow-up-note"
                            wire:model="followUpNote"
                            rows="3"
                            class="ops-control"
                            placeholder="{{ $status === 'Resolved' ? 'Summarize what resolved the issue...' : 'Add a short follow-up note for the next person reviewing this incident...' }}"
                        ></textarea>
                        <p class="ops-field-help">{{ $this->followUpNoteHelp }}</p>
                        @error('followUpNote') <span class="ops-field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end border-t border-[var(--app-border)] pt-5">
                        <button type="submit" class="ops-button ops-button--primary min-w-44">
                            <span wire:loading.remove wire:target="updateStatus">Update Status</span>
                            <span wire:loading wire:target="updateStatus">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <h3 class="text-base font-semibold text-[var(--app-heading)]">Activity Timeline</h3>

                <ul role="list" class="mt-4 space-y-4">
                    @foreach($incident->activities->sortByDesc('created_at') as $activity)
                        <li class="border-l-2 border-[var(--app-border)] pl-4">
                            <p class="text-sm font-medium text-[var(--app-heading)]">{{ $activity->summary }}</p>
                            <p class="mt-1 text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">
                                {{ $this->getActivityTypeLabel($activity->action_type) }}
                            </p>
                            <p class="mt-1 text-xs text-[var(--app-text-muted)]">
                                {{ $activity->actor?->name ?? 'Unknown' }} · {{ $activity->created_at?->format('M d, Y H:i') ?? 'Unknown time' }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    </div>
</div>
