<div>
    @php
        $activeScopeCount = collect($scopeBoard)->where('state', '!=', 'unavailable')->count();
    @endphp
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Staff runtime') }}</p>
                <h2 class="ops-page__title">{{ __('Daily Checklist') }}</h2>
                <p class="ops-page-intro__body">
                    @if ($errorState === 'scope_required')
                        Choose the live checklist lane for this operating day, then continue with the right runtime instead of forcing every shift into one generic flow.
                    @elseif ($errorState === 'scope_missing' && $this->scopeLabel)
                        The {{ $this->scopeLabel }} lane is not configured yet. Pick another live lane or ask an administrator to activate a template for that operating moment.
                    @else
                        Complete the live checklist, keep evidence quality tight, and escalate real issues without losing operational context.
                    @endif
                </p>
                @if (! $errorState)
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Live daily run') }}</span>
                        <span class="ops-shell-chip">{{ $this->answeredItems }}/{{ $this->totalItems }} {{ __('answered') }}</span>
                        <span class="ops-shell-chip">{{ $isSubmitted ? __('Submitted') : __('Pending') }}</span>
                    </div>
                @elseif ($errorState === 'scope_required')
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Scope-aware runtime') }}</span>
                        <span class="ops-shell-chip">{{ $activeScopeCount }} {{ __('live lane(s) today') }}</span>
                    </div>
                @elseif ($errorState === 'scope_missing' && $this->scopeLabel)
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Missing scope runtime') }}</span>
                        <span class="ops-shell-chip">{{ $this->scopeLabel }}</span>
                    </div>
                @endif
            </div>

            @if (! $errorState)
                <div class="ops-page-intro__actions">
                    <span class="ops-shell-chip">
                        {{ \Carbon\Carbon::parse($run->run_date)->format('M d, Y') }}
                    </span>
                </div>
            @elseif ($errorState !== 'zero')
                <div class="ops-page-intro__actions">
                    <span class="ops-shell-chip">
                        {{ now()->format('M d, Y') }}
                    </span>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errorState === 'zero')
            <div data-motion="fade-up" class="ops-alert ops-alert--danger">
                <strong class="font-semibold">Configuration Error:</strong>
                <span class="block sm:inline">No active checklist template exists. Please contact an administrator.</span>
            </div>
        @elseif ($errorState === 'scope_required' || $errorState === 'scope_missing')
            <section class="ops-hero" data-motion="glance-rise">
                <div class="ops-hero__inner">
                    <div>
                        <p class="ops-hero__eyebrow">Daily Operations Runtime</p>
                        <h3 class="ops-hero__title">
                            @if ($errorState === 'scope_required')
                                Choose today&apos;s checklist lane
                            @else
                                {{ $this->scopeLabel }} lane is not live yet
                            @endif
                        </h3>
                        <p class="ops-hero__lead">
                            @if ($errorState === 'scope_required')
                                Staff runtime now follows the real operating moment. Pick the checklist scope you are working in so today&apos;s run, history, and incident follow-up stay aligned.
                            @else
                                There is no active template for the {{ $this->scopeLabel }} operating lane right now. You can move into another live lane, or ask an administrator to activate the correct template first.
                            @endif
                        </p>

                        <div class="ops-hero__meta">
                            <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Scope-aware runtime') }}</span>
                            <span class="ops-shell-chip">{{ $activeScopeCount }} {{ __('live lane(s)') }}</span>
                            <span class="ops-shell-chip">{{ __('Today') }} {{ now()->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <aside class="ops-hero__aside">
                        <div>
                            <p class="ops-hero__aside-title">Runtime board</p>
                            <p class="ops-hero__aside-value">{{ $activeScopeCount }}</p>
                            <p class="ops-hero__aside-copy">
                                Active checklist lane(s) are currently available for staff runtime today.
                            </p>
                        </div>

                        <div class="ops-hero__aside-stack">
                            <div class="ops-shell-chip">
                                <span>Not started</span>
                                <strong class="font-semibold text-white">{{ collect($scopeBoard)->where('state', 'not_started')->count() }}</strong>
                            </div>
                            <div class="ops-shell-chip">
                                <span>In progress</span>
                                <strong class="font-semibold text-white">{{ collect($scopeBoard)->where('state', 'in_progress')->count() }}</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            @if ($errorState === 'scope_missing')
                <div data-motion="fade-up" class="ops-alert ops-alert--warning">
                    <strong class="font-semibold">Selected lane unavailable:</strong>
                    <span class="block sm:inline">The {{ $this->scopeLabel }} runtime does not have an active checklist template yet. Choose another live lane below or ask an administrator to activate one.</span>
                </div>
            @endif

            <div class="ops-command-grid">
                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Today&apos;s operating lanes</p>
                                <h3 class="ops-section-heading__title">Choose the right checklist scope</h3>
                                <p class="ops-section-heading__body">Each live lane keeps its own checklist template, progress state, and history for today&apos;s work.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="grid gap-4 lg:grid-cols-3" data-motion-group data-stagger-base="50" data-stagger-unit="35" data-stagger-max="160">
                                @foreach ($scopeBoard as $lane)
                                    @php
                                        $laneTone = match ($lane['state']) {
                                            'submitted' => 'neutral',
                                            'in_progress' => 'warning',
                                            default => 'neutral',
                                        };
                                        $laneHref = route('checklists.runs.today', ['scope' => $lane['scope_key']]);
                                    @endphp

                                    <article data-motion="scale-soft" class="ops-signal-card {{ $laneTone === 'warning' ? 'ops-signal-card--warning' : 'ops-signal-card--neutral' }}">
                                        <div class="ops-signal-card__header">
                                            <div>
                                                <p class="ops-signal-card__title">{{ $lane['scope'] }}</p>
                                                <p class="ops-signal-card__body">
                                                    {{ $lane['template_title'] ?? __('No active template') }}
                                                </p>
                                            </div>
                                            <div class="ops-signal-card__count">
                                                {{ $lane['completion_percentage'] }}%
                                            </div>
                                        </div>

                                        <div class="ops-signal-card__body">
                                            @if ($lane['state'] === 'unavailable')
                                                No active template is configured for this operating lane yet.
                                            @elseif ($lane['state'] === 'submitted')
                                                Today&apos;s run is already complete for this lane.
                                            @elseif ($lane['state'] === 'in_progress')
                                                Resume the live run already started for today.
                                            @else
                                                Start the live checklist for this operating lane.
                                            @endif
                                        </div>

                                        <div class="ops-signal-card__footer">
                                            <span class="ops-chip {{ $lane['state'] === 'submitted' ? 'ops-chip--success' : ($lane['state'] === 'in_progress' ? 'ops-chip--warning' : '') }}">
                                                {{ str($lane['state'])->replace('_', ' ')->title() }}
                                            </span>
                                            <span class="ops-text-muted text-xs">
                                                {{ $lane['answered_items'] }}/{{ $lane['total_items'] }} {{ __('answered') }}
                                            </span>
                                        </div>

                                        <div class="mt-4">
                                            @if ($lane['state'] === 'unavailable')
                                                <button type="button" class="ops-button ops-button--secondary w-full" disabled>
                                                    {{ __('Unavailable') }}
                                                </button>
                                            @else
                                                <a href="{{ $laneHref }}" class="ops-button {{ $lane['state'] === 'submitted' ? 'ops-button--secondary' : 'ops-button--primary' }} w-full">
                                                    {{ $lane['state'] === 'submitted' ? __('Review lane') : __('Enter lane') }}
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        @else
            <section class="ops-hero" data-motion="glance-rise">
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
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
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
                                    <div class="ops-eyebrow-label">Answered</div>
                                    <div class="ops-metric-value mt-2 text-2xl font-semibold">
                                        {{ $this->answeredItems }}/{{ $this->totalItems }}
                                    </div>
                                </div>
                                <div class="ops-progress-panel">
                                    <div class="ops-eyebrow-label">Remaining</div>
                                    <div class="ops-metric-value mt-2 text-2xl font-semibold">
                                        {{ $this->remainingItems }}
                                    </div>
                                </div>
                                <div class="ops-progress-panel">
                                    <div class="ops-eyebrow-label">Marked Not Done</div>
                                    <div class="ops-metric-value mt-2 text-2xl font-semibold">
                                        {{ $this->notDoneItems }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="ops-text-muted flex items-center justify-between text-sm">
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

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="90">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Execution surface</p>
                                <h3 class="ops-section-heading__title">{{ $template->title }}</h3>
                                <p class="ops-section-heading__body">Work through the checklist in order, attach notes where needed, and keep the output ready for incident handoff when something fails.</p>
                            </div>

                            <div class="flex shrink-0 flex-wrap gap-3">
                                @if ($activeScopeCount > 1)
                                    <a href="{{ route('checklists.runs.today') }}" class="ops-button ops-button--secondary">
                                        {{ __('Back to runtime board') }}
                                    </a>
                                @endif
                                <a href="{{ $this->incidentPrefillUrl }}" class="ops-button ops-button--danger">
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
                                        <h4 class="ops-text-heading text-sm font-semibold">Submission Recap</h4>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            <span class="ops-badge ops-badge--neutral">
                                                {{ $this->answeredItems }} answered
                                            </span>
                                            <span class="ops-badge ops-badge--neutral">
                                                {{ $this->notDoneItems }} not done
                                            </span>
                                            <span class="ops-badge ops-badge--neutral">
                                                {{ $this->notedItems }} note(s)
                                            </span>
                                        </div>
                                        <p class="ops-text-muted mt-3 text-sm">
                                            @if ($this->notDoneItems > 0)
                                                This run includes items marked Not Done. If they reflect a real operational problem, file an incident so management can track follow-up.
                                            @else
                                                This run was completed without any items marked Not Done. Use the note history below if you need to review what changed.
                                            @endif
                                        </p>
                                        @if ($this->repeatedNotDoneTitles !== [])
                                            <div class="ops-tone-warning mt-3 rounded-2xl border px-3 py-3 text-sm">
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
                                    @foreach($run->items as $index => $runItem)
                                        @php($groupLabel = $runItem->checklistItem->group_label ?: 'General checks')
                                        @php($showGroupHeader = $groupLabel !== $previousGroupLabel)

                                        @if ($showGroupHeader)
                                            <li class="ops-item-group" data-motion="fade-up" data-motion-delay="{{ 110 + ($index * 15) }}">
                                                <div class="ops-item-group__label">{{ $groupLabel }}</div>
                                                @php($previousGroupLabel = $groupLabel)
                                            </li>
                                        @endif

                                        <li class="ops-item-card" data-motion="scale-soft" data-motion-delay="{{ 130 + ($index * 20) }}">
                                            <div class="ops-item-card__content">
                                                <div class="min-w-0">
                                                    <h4 class="ops-item-card__title">
                                                        {{ $runItem->checklistItem->title }}
                                                        @if($runItem->checklistItem->is_required)
                                                            <span class="ops-required-mark">*</span>
                                                        @endif
                                                    </h4>
                                                    @if($runItem->checklistItem->description)
                                                        <p class="ops-item-card__description">{{ $runItem->checklistItem->description }}</p>
                                                    @endif
                                                    @php($anomalyMemory = $itemAnomalyMemory[$runItem->checklist_item_id] ?? null)
                                                    @if (($anomalyMemory['recent_not_done_count'] ?? 0) > 0)
                                                        <div class="ops-tone-warning mt-3 rounded-2xl border px-3 py-2 text-xs">
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
                                                            <input type="radio" wire:model="runItems.{{ $runItem->id }}.result" value="Done" class="ops-choice__control" {{ $isSubmitted ? 'disabled' : '' }}>
                                                            <span>Done</span>
                                                        </label>
                                                        <label class="ops-choice {{ $isSubmitted ? 'opacity-70' : '' }}">
                                                            <input type="radio" wire:model="runItems.{{ $runItem->id }}.result" value="Not Done" class="ops-choice__control ops-choice__control--danger" {{ $isSubmitted ? 'disabled' : '' }}>
                                                            <span>Not Done</span>
                                                        </label>
                                                    </div>
                                                    @error("runItems.{$runItem->id}.result")
                                                        <span class="ops-field-error">{{ $message }}</span>
                                                    @enderror

                                                    <input type="text" wire:model="runItems.{{ $runItem->id }}.note" placeholder="Optional note..." class="ops-control" {{ $isSubmitted ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                @if(!$isSubmitted)
                                    <div class="ops-divider-top flex justify-end pt-5">
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
                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
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
                                                <div class="ops-text-heading text-sm font-semibold">
                                                    {{ \Carbon\Carbon::parse($recentRun['run_date'])->format('M d, Y') }}
                                                </div>
                                                <div class="ops-text-muted text-xs">
                                                    Submitted {{ \Carbon\Carbon::parse($recentRun['submitted_at'])->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                                <span class="ops-badge ops-badge--neutral">
                                                    {{ $recentRun['not_done_count'] }} not done
                                                </span>
                                                <span class="ops-badge ops-badge--neutral">
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
