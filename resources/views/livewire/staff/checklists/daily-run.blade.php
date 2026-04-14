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
            <section class="grid gap-4 xl:grid-cols-[minmax(0,1.5fr)_minmax(20rem,1fr)]">
                <div class="ops-card">
                    <div class="ops-card__header">
                        <h3 class="text-base font-semibold text-[var(--app-heading)]">Today&apos;s Progress</h3>
                        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                            Keep the checklist complete before handing over the shift.
                        </p>
                    </div>
                    <div class="ops-card__body space-y-4">
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Answered</div>
                                <div class="mt-2 text-2xl font-semibold text-[var(--app-heading)]">
                                    {{ $this->answeredItems }}/{{ $this->totalItems }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">Remaining</div>
                                <div class="mt-2 text-2xl font-semibold text-[var(--app-heading)]">
                                    {{ $this->remainingItems }}
                                </div>
                            </div>
                            <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-3">
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
                            <div class="h-2 overflow-hidden rounded-full bg-[var(--app-border)]">
                                <div
                                    class="h-full rounded-full bg-[var(--app-action-primary)] transition-all duration-300"
                                    style="width: {{ $this->completionPercentage }}%;"
                                ></div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-[var(--app-border)] bg-white px-4 py-3 text-sm text-[var(--app-text-muted)]">
                            @if ($isSubmitted)
                                Checklist submitted for today. You can still review your responses below, but no further edits are allowed.
                            @elseif ($this->remainingItems === 0)
                                All required responses are filled in. Review any notes, then submit the checklist.
                            @else
                                {{ $this->remainingItems }} item(s) still need a result before submission.
                            @endif
                        </div>

                        @if ($this->notDoneItems > 0 && ! $isSubmitted)
                            <div class="rounded-2xl border border-[var(--app-warning-border)] bg-[var(--app-warning-bg)] px-4 py-3 text-sm text-[var(--app-warning-text)]">
                                {{ $this->notDoneItems }} item(s) are currently marked Not Done. If this reflects a real issue in the workspace, prepare to file an incident after submission so management can follow up.
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ops-card">
                    <div class="ops-card__header">
                        <h3 class="text-base font-semibold text-[var(--app-heading)]">Recent Submission Context</h3>
                        <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                            Use your last few runs as a quick reference before submitting today&apos;s checklist.
                        </p>
                    </div>
                    <div class="ops-card__body">
                        @if ($recentRuns !== [])
                            <ul role="list" class="space-y-3">
                                @foreach ($recentRuns as $recentRun)
                                    <li class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-3">
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
                            <div class="rounded-2xl border border-dashed border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-4 text-sm text-[var(--app-text-muted)]">
                                No submitted checklist history yet. Once you complete a few runs, this panel will show your recent pattern for faster review.
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="ops-card overflow-hidden">
                <div class="ops-card__header flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <h3 class="text-lg font-semibold text-[var(--app-heading)]">
                            {{ $template->title }}
                        </h3>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-sm text-[var(--app-text-muted)]">
                            <span>Date: {{ \Carbon\Carbon::parse($run->run_date)->format('M d, Y') }}</span>
                            <span class="hidden sm:inline">|</span>
                            <span>Scope: {{ $template->scope }}</span>
                            @if($isSubmitted)
                                <span class="ops-badge border-[var(--app-success-border)] bg-[var(--app-success-bg)] text-[var(--app-success-text)]">
                                    Submitted
                                </span>
                            @else
                                <span class="ops-badge border-[var(--app-warning-border)] bg-[var(--app-warning-bg)] text-[var(--app-warning-text)]">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex shrink-0">
                        <a href="{{ route('incidents.create') }}" class="ops-button border-[var(--app-danger-border)] bg-[var(--app-danger-bg)] text-[var(--app-danger-text)] hover:bg-[#fbd7d9]">
                            Report Incident
                        </a>
                    </div>
                </div>

                <div class="ops-card__body">
                    @if (session()->has('message'))
                        <div class="ops-alert ops-alert--success mb-5">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if ($isSubmitted)
                        <div class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                            <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-4">
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
                            </div>

                            @if ($this->notDoneItems > 0)
                                <a href="{{ $this->incidentPrefillUrl }}" class="ops-button ops-button--primary min-w-56">
                                    Report follow-up incident
                                </a>
                            @endif
                        </div>
                    @endif

                    <form wire:submit="submit" class="space-y-5">
                        <ul role="list" class="divide-y divide-[var(--app-border)]">
                            @foreach($run->items as $runItem)
                                <li class="py-5 first:pt-0 last:pb-0">
                                    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-start lg:gap-6">
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-semibold text-[var(--app-heading)]">
                                                {{ $runItem->checklistItem->title }}
                                                @if($runItem->checklistItem->is_required)
                                                    <span class="text-[var(--app-danger-text)]">*</span>
                                                @endif
                                            </h4>
                                            @if($runItem->checklistItem->description)
                                                <p class="mt-2 text-sm text-[var(--app-text-muted)]">{{ $runItem->checklistItem->description }}</p>
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
        @endif
    </div>
</div>
