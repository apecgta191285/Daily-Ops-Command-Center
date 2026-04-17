<div>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="ops-page__title">{{ __('Daily Checklist') }}</h2>
                <p class="text-sm">
                    Complete today&apos;s assigned checklist and report any issues immediately.
                </p>
            </div>

            @if (! $errorState)
                <div class="text-sm">
                    {{ \Carbon\Carbon::parse($run->run_date)->format('M d, Y') }}
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errorState === 'zero')
            <div class="ops-alert ops-alert--danger">
                <strong class="font-semibold">Configuration Error:</strong>
                <span class="block sm:inline">No active checklist template exists. Please contact an administrator.</span>
            </div>
        @elseif ($errorState === 'multiple')
            <div class="ops-alert ops-alert--danger">
                <strong class="font-semibold">Configuration Error:</strong>
                <span class="block sm:inline">Multiple active checklist templates are currently active. The current baseline supports exactly one active daily checklist template for the whole system, so an administrator must retire the extras before staff can continue.</span>
            </div>
        @else
            <section class="ops-hero">
                <div class="ops-hero__inner">
                    <div>
                        <p class="ops-hero__eyebrow">Daily Checklist</p>
                        <h3 class="ops-hero__title">{{ $template->title }}</h3>
                        <p class="ops-hero__lead">
                            Complete the live run for {{ \Carbon\Carbon::parse($run->run_date)->format('M d, Y') }}, keep note quality tight, and hand off any real issue into the incident flow without losing context.
                        </p>

                        <div class="ops-hero__meta">
                            <span class="ops-shell-chip ops-shell-chip--accent">Scope: {{ $template->scope }}</span>
                            <span class="ops-shell-chip">{{ $this->answeredItems }}/{{ $this->totalItems }} answered</span>
                            @if ($isSubmitted)
                                <span class="ops-shell-chip">Submitted</span>
                            @else
                                <span class="ops-shell-chip">Pending</span>
                            @endif
                        </div>
                    </div>

                    <aside class="ops-hero__aside">
                        <div>
                            <p class="ops-hero__aside-title">Completion</p>
                            <p class="ops-hero__aside-value">{{ $this->completionPercentage }}%</p>
                            <p class="ops-hero__aside-copy">
                                {{ $this->remainingItems }} item(s) remain before the checklist can be treated as fully complete.
                            </p>
                        </div>

                        <div class="ops-hero__aside-stack">
                            <div class="ops-shell-chip">
                                <span>Marked Not Done</span>
                                <strong class="font-semibold text-white">{{ $this->notDoneItems }}</strong>
                            </div>
                            <div class="ops-shell-chip">
                                <span>Recent reference runs</span>
                                <strong class="font-semibold text-white">{{ count($recentRuns) }}</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <div class="ops-command-grid ops-command-grid--checklist">
                <div class="ops-stack">
                    <section class="ops-card overflow-hidden">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Run state</p>
                                <h3 class="ops-section-heading__title">Today&apos;s Progress</h3>
                                <p class="ops-section-heading__body">Keep the checklist complete before handing over the shift.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="ops-progress-panel">
                                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Answered</div>
                                    <div class="mt-2 text-2xl font-semibold text-[var(--app-heading)]">
                                        {{ $this->answeredItems }}/{{ $this->totalItems }}
                                    </div>
                                </div>
                                <div class="ops-progress-panel">
                                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Remaining</div>
                                    <div class="mt-2 text-2xl font-semibold text-[var(--app-heading)]">
                                        {{ $this->remainingItems }}
                                    </div>
                                </div>
                                <div class="ops-progress-panel">
                                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Marked Not Done</div>
                                    <div class="mt-2 text-2xl font-semibold text-[var(--app-heading)]">
                                        {{ $this->notDoneItems }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm text-[var(--app-text-muted)]">
                                    <span>Completion</span>
                                    <span>{{ $this->completionPercentage }}%</span>
                                </div>
                                <div class="ops-progress-bar">
                                    <div
                                        class="ops-progress-bar__value"
                                        style="width: {{ $this->completionPercentage }}%;"
                                    ></div>
                                </div>
                            </div>

                            <x-ops.callout title="Run guidance" tone="neutral">
                                @if ($isSubmitted)
                                    Checklist submitted for today. You can still review your responses below, but no further edits are allowed.
                                @elseif ($this->remainingItems === 0)
                                    All required responses are filled in. Review any notes, then submit the checklist.
                                @else
                                    {{ $this->remainingItems }} item(s) still need a result before submission.
                                @endif
                            </x-ops.callout>

                            @if ($this->notDoneItems > 0 && ! $isSubmitted)
                                <x-ops.callout title="Follow-up warning" tone="warning">
                                    {{ $this->notDoneItems }} item(s) are currently marked Not Done. If this reflects a real issue in the workspace, prepare to file an incident after submission so management can follow up.
                                </x-ops.callout>
                            @endif
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Execution surface</p>
                                <h3 class="ops-section-heading__title">{{ $template->title }}</h3>
                                <p class="ops-section-heading__body">Work through the checklist in order, attach notes where needed, and keep the output ready for incident handoff when something fails.</p>
                            </div>

                            <div class="flex shrink-0">
                                <a href="{{ route('incidents.create') }}" class="ops-button ops-button--danger">
                                    Report Incident
                                </a>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            @if (session()->has('message'))
                                <div data-alert data-auto-dismiss="5000" role="status" aria-live="polite" class="ops-alert ops-alert--success mb-5">
                                    <div class="ops-alert__inner">
                                        <div class="ops-alert__copy">{{ session('message') }}</div>
                                        <button type="button" class="ops-alert__dismiss" data-dismiss-alert aria-label="{{ __('Dismiss message') }}">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if ($isSubmitted)
                                <div class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                                    <div class="ops-progress-panel">
                                        <h4 class="text-sm font-semibold text-[var(--app-heading)]">Submission Recap</h4>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            <span class="ops-badge border-[var(--app-border)] bg-white text-[var(--app-text-muted)]">
                                                {{ $this->answeredItems }} answered
                                            </span>
                                            <span class="ops-badge border-[var(--app-border)] bg-white text-[var(--app-text-muted)]">
                                                {{ $this->notDoneItems }} not done
                                            </span>
                                            <span class="ops-badge border-[var(--app-border)] bg-white text-[var(--app-text-muted)]">
                                                {{ $this->notedItems }} note(s)
                                            </span>
                                        </div>
                                        <p class="mt-3 text-sm text-[var(--app-text-muted)]">
                                            @if ($this->notDoneItems > 0)
                                                This run includes items marked Not Done. If they reflect a real operational problem, file an incident so management can track follow-up.
                                            @else
                                                This run was completed without any items marked Not Done. Use the note history below if you need to review what changed.
                                            @endif
                                        </p>
                                        @if ($this->repeatedNotDoneTitles !== [])
                                            <div class="mt-3 rounded-2xl border border-[var(--app-warning-border)] bg-[var(--app-warning-bg)] px-3 py-3 text-sm text-[var(--app-warning-text)]">
                                                <span class="font-semibold">Repeated issue memory:</span>
                                                {{ collect($this->repeatedNotDoneTitles)->join(', ') }}
                                                {{ count($this->repeatedNotDoneTitles) > 1 ? 'have' : 'has' }}
                                                been marked Not Done before. Consider filing a follow-up incident with this context.
                                            </div>
                                        @endif
                                    </div>

                                    @if ($this->notDoneItems > 0)
                                        <a href="{{ $this->incidentPrefillUrl }}" class="ops-button ops-button--primary min-w-56">
                                            Report follow-up incident
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <form wire:submit="submit" class="space-y-5">
                                @php($previousGroupLabel = null)
                                <ul role="list" class="ops-item-stack">
                                    @foreach($run->items as $runItem)
                                        @php($groupLabel = $runItem->checklistItem->group_label ?: 'General checks')
                                        @php($showGroupHeader = $groupLabel !== $previousGroupLabel)

                                        @if ($showGroupHeader)
                                            <li class="ops-item-group">
                                                <div class="ops-item-group__label">{{ $groupLabel }}</div>
                                                @php($previousGroupLabel = $groupLabel)
                                            </li>
                                        @endif

                                        <li class="ops-item-card">
                                            <div class="ops-item-card__content">
                                                <div class="min-w-0">
                                                    <h4 class="ops-item-card__title">
                                                        {{ $runItem->checklistItem->title }}
                                                        @if($runItem->checklistItem->is_required)
                                                            <span class="text-[var(--app-danger-text)]">*</span>
                                                        @endif
                                                    </h4>
                                                    @if($runItem->checklistItem->description)
                                                        <p class="ops-item-card__description">{{ $runItem->checklistItem->description }}</p>
                                                    @endif
                                                    @php($anomalyMemory = $itemAnomalyMemory[$runItem->checklist_item_id] ?? null)
                                                    @if (($anomalyMemory['recent_not_done_count'] ?? 0) > 0)
                                                        <div class="mt-3 rounded-2xl border border-[var(--app-warning-border)] bg-[var(--app-warning-bg)] px-3 py-2 text-xs text-[var(--app-warning-text)]">
                                                            <span class="font-semibold">Recent issue memory:</span>
                                                            marked Not Done {{ $anomalyMemory['recent_not_done_count'] }} time(s)
                                                            in the last {{ $anomalyMemory['sample_run_count'] }} submitted run(s).
                                                            @if (filled($anomalyMemory['last_not_done_at']))
                                                                Last seen on {{ $anomalyMemory['last_not_done_at'] }}.
                                                            @endif
                                                            @if (filled($anomalyMemory['last_note']))
                                                                Last note: {{ $anomalyMemory['last_note'] }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex flex-col gap-3">
                                                    <div class="flex flex-wrap gap-3">
                                                        <label class="ops-choice {{ $isSubmitted ? 'opacity-70' : '' }}">
                                                            <input type="radio" wire:model="runItems.{{ $runItem->id }}.result" value="Done" class="h-4 w-4 border-[var(--app-border)] text-[var(--app-action-primary)] focus:ring-[var(--app-action-primary)]" {{ $isSubmitted ? 'disabled' : '' }}>
                                                            <span>Done</span>
                                                        </label>
                                                        <label class="ops-choice {{ $isSubmitted ? 'opacity-70' : '' }}">
                                                            <input type="radio" wire:model="runItems.{{ $runItem->id }}.result" value="Not Done" class="h-4 w-4 border-[var(--app-border)] text-[var(--app-danger-text)] focus:ring-[var(--app-danger-text)]" {{ $isSubmitted ? 'disabled' : '' }}>
                                                            <span>Not Done</span>
                                                        </label>
                                                    </div>
                                                    @error("runItems.{$runItem->id}.result")
                                                        <span class="ops-field-error">{{ $message }}</span>
                                                    @enderror

                                                    <input type="text" wire:model="runItems.{{ $runItem->id }}.note" placeholder="Optional note..." class="ops-control {{ $isSubmitted ? 'cursor-not-allowed bg-slate-100 text-[var(--app-text-muted)]' : '' }}" {{ $isSubmitted ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                @if(!$isSubmitted)
                                    <div class="flex justify-end border-t border-[var(--app-border)] pt-5">
                                        <button type="submit" class="ops-button ops-button--primary min-w-44 disabled:opacity-50">
                                            <span wire:loading.remove wire:target="submit">Submit Checklist</span>
                                            <span wire:loading wire:target="submit">Submitting...</span>
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </section>
                </div>

                <div class="ops-stack">
                    <section class="ops-card overflow-hidden">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Reference memory</p>
                                <h3 class="ops-section-heading__title">Recent Submission Context</h3>
                                <p class="ops-section-heading__body">Use your last few runs as a quick reference before submitting today&apos;s checklist.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            @if ($recentRuns !== [])
                                <ul role="list" class="ops-detail-list">
                                    @foreach ($recentRuns as $recentRun)
                                        <li class="ops-detail-list__item">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="text-sm font-semibold text-[var(--app-heading)]">
                                                    {{ \Carbon\Carbon::parse($recentRun['run_date'])->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs text-[var(--app-text-muted)]">
                                                    Submitted {{ \Carbon\Carbon::parse($recentRun['submitted_at'])->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                                <span class="ops-badge border-[var(--app-border)] bg-white text-[var(--app-text-muted)]">
                                                    {{ $recentRun['not_done_count'] }} not done
                                                </span>
                                                <span class="ops-badge border-[var(--app-border)] bg-white text-[var(--app-text-muted)]">
                                                    {{ $recentRun['noted_items_count'] }} note(s)
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <x-ops.empty-state
                                    title="No submitted checklist history yet."
                                    body="Once you complete a few runs, this panel will show your recent pattern for faster review."
                                />
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        @endif
    </div>
</div>
