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
                <span class="block sm:inline">Multiple active checklist templates are currently active. This application only supports a single daily checklist flow per user. Please contact an administrator to resolve this template ambiguity.</span>
            </div>
        @else
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
