<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Issue review') }}</p>
                <h2 class="ops-page__title">{{ __('Incident Detail') }}</h2>
                <p class="ops-page-intro__body">
                    Review the room issue, confirm where it happened, and move the queue forward with a clear next decision.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Issue detail') }}</span>
                    <span class="ops-shell-chip">{{ $incident->room?->name ?? __('No room') }}</span>
                    <span class="ops-shell-chip">{{ __($incident->status->value) }}</span>
                    <span class="ops-shell-chip">{{ __($incident->severity->value) }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('incidents.print', $incident) }}" class="ops-button" target="_blank" rel="noopener noreferrer">
                    {{ __('Printable summary') }}
                </a>
                <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary">
                    {{ __('Back to incident list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="ops-incident-shell">
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

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Lab issue record</p>
                    <h3 class="ops-hero__title">{{ $incident->title }}</h3>
                    <p class="ops-hero__lead">
                        Use this screen to review what was reported in this room, see the latest handling note, and decide the next follow-up for the room or equipment involved.
                    </p>
                    <p class="ops-text-shell-muted mt-3 text-sm">
                        Reported by {{ $incident->creator?->name ?? 'Unknown' }} on {{ $incident->created_at->format('M d, Y H:i') }}
                    </p>
                    <p class="ops-text-shell-muted mt-2 text-sm">
                        {{ __('Room: :room', ['room' => $incident->room?->name ?? __('Not recorded')]) }}
                        @if ($incident->equipment_reference)
                            {{ __(' • Equipment: :equipment', ['equipment' => $incident->equipment_reference]) }}
                        @endif
                    </p>

                    <div class="ops-incident-meta mt-4">
                        <span class="ops-badge ops-badge--neutral">Open for {{ $this->ageInDays }} day{{ $this->ageInDays === 1 ? '' : 's' }}</span>
                        @if ($this->isStale)
                            <span class="ops-badge ops-badge--warning">Stale {{ $this->staleThresholdDays }}+ days</span>
                        @endif
                        @if ($this->needsOwner)
                            <span class="ops-badge ops-badge--warning">Needs owner</span>
                        @endif
                        @if ($this->isFollowUpOverdue)
                            <span class="ops-badge ops-badge--danger">Follow-up overdue</span>
                        @endif
                        @if ($incident->owner)
                            <span class="ops-badge ops-badge--neutral">Owner: {{ $incident->owner->name }}</span>
                        @endif
                        @if ($incident->follow_up_due_at)
                            <span class="ops-badge ops-badge--warning">Follow-up {{ $incident->follow_up_due_at->format('M d, Y') }}</span>
                        @endif
                        <x-incidents.status-badge :status="$incident->status" />
                        <x-incidents.severity-badge :severity="$incident->severity" />
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Current state</p>
                        <p class="ops-hero__aside-value">{{ $incident->category->value }}</p>
                        <p class="ops-hero__aside-copy">
                            {{ $incident->resolved_at ? 'Resolved at '.$incident->resolved_at->format('M d, Y H:i') : 'Still active in the team queue.' }}
                        </p>
                    </div>

                    <div class="ops-glance-grid--hero">
                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Severity</p>
                            <div class="mt-3"><x-incidents.severity-badge :severity="$incident->severity" /></div>
                            <p class="ops-glance-card__meta">Risk signal for how urgently the issue should be handled.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Status</p>
                            <div class="mt-3"><x-incidents.status-badge :status="$incident->status" /></div>
                            <p class="ops-glance-card__meta">Live handling state as of the most recent management action.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Age</p>
                            <p class="ops-glance-card__value">{{ $this->ageInDays }}</p>
                            <p class="ops-glance-card__meta">Days since the incident first entered the queue.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Room</p>
                            <p class="ops-glance-card__value">{{ $incident->room?->name ?? 'Not set' }}</p>
                            <p class="ops-glance-card__meta">The lab room currently tied to this issue record.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Equipment reference</p>
                            <p class="ops-glance-card__value">{{ $incident->equipment_reference ?? 'Not provided' }}</p>
                            <p class="ops-glance-card__meta">Optional workstation or equipment context reported by staff.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Owner</p>
                            <p class="ops-glance-card__value">{{ $incident->owner?->name ?? 'Unowned' }}</p>
                            <p class="ops-glance-card__meta">The management person currently accountable for moving this issue forward.</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">Follow-up target</p>
                            <p class="ops-glance-card__value">{{ $incident->follow_up_due_at?->format('M d, Y') ?? 'Not set' }}</p>
                            <p class="ops-glance-card__meta">Target date for the next meaningful review or action.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="ops-incident-grid">
            <div class="ops-incident-story">
                @if ($this->latestNextActionNote || $this->latestResolutionNote)
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-card__body ops-incident-summary-stack">
                            <div class="ops-section-heading">
                                <div>
                                    <p class="ops-section-heading__eyebrow">Latest handling context</p>
                                    <h2 class="ops-section-heading__title">What the next reviewer should know first</h2>
                                    <p class="ops-section-heading__body">The newest direction and the newest resolution summary are surfaced here before the full timeline.</p>
                                </div>
                            </div>

                            <div class="grid gap-4 xl:grid-cols-2">
                                @if ($this->latestNextActionNote)
                                    <x-ops.callout title="Latest Follow-up Direction" tone="warning">
                                        {{ $this->latestNextActionNote }}
                                    </x-ops.callout>
                                @endif

                                @if ($this->latestResolutionNote)
                                    <x-ops.callout title="Latest Resolution Summary" tone="success">
                                        {{ $this->latestResolutionNote }}
                                    </x-ops.callout>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                @if ($this->needsOwner || $this->isFollowUpOverdue)
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="65">
                        <div class="ops-card__body">
                            @if ($this->isFollowUpOverdue)
                                <x-ops.callout title="Follow-up target overdue" tone="danger">
                                    {{ $incident->follow_up_due_at?->format('M d, Y') }} has passed and this incident is still unresolved. Review ownership and the next move before this record turns into passive queue debt.
                                </x-ops.callout>
                            @elseif ($this->needsOwner)
                                <x-ops.callout title="Ownership still missing" tone="warning">
                                    This unresolved incident does not currently have a management owner. Assign one in the accountability lane so the next move is clearly carried by someone.
                                </x-ops.callout>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="90">
                    <div class="ops-card__body">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Reported context</p>
                                <h2 class="ops-section-heading__title">Description and evidence</h2>
                                <p class="ops-section-heading__body">This is the original operational signal the team is responding to, plus any supporting file attached during reporting.</p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(18rem,1fr)]">
                            <article class="ops-incident-panel" data-severity="{{ $incident->severity->value }}">
                                <p class="ops-incident-panel__eyebrow">Description</p>
                                <h3 class="ops-incident-panel__title">What was reported</h3>
                                <p class="ops-incident-panel__body whitespace-pre-line">{{ $incident->description }}</p>
                            </article>

                            <article class="ops-incident-panel" data-severity="{{ $incident->severity->value }}">
                                <p class="ops-incident-panel__eyebrow">Room and evidence</p>
                                <h3 class="ops-incident-panel__title">{{ $incident->room?->name ?? 'Room not recorded' }}</h3>
                                <p class="ops-incident-panel__body">
                                    {{ $incident->equipment_reference
                                        ? __('Equipment reference: :equipment', ['equipment' => $incident->equipment_reference])
                                        : __('No equipment reference was attached to this room issue record.') }}
                                </p>

                                <p class="ops-incident-panel__body">
                                    {{ $incident->attachment_path
                                        ? 'Open the uploaded file when you need more proof or supporting visual context before changing status.'
                                        : 'This incident was reported without a file. Review the description and timeline before deciding the next step.' }}
                                </p>

                                @if($incident->attachment_path)
                                    <div class="ops-incident-panel__actions">
                                        <a href="{{ asset('storage/' . $incident->attachment_path) }}" target="_blank" rel="noopener noreferrer" class="ops-button ops-button--secondary">
                                            View attachment
                                        </a>
                                    </div>
                                @endif
                            </article>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="140">
                    <div class="ops-card__body">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Operational history</p>
                                <h2 class="ops-section-heading__title">Activity timeline</h2>
                                <p class="ops-section-heading__body">Read the sequence below to understand how the incident moved from report to the current state.</p>
                            </div>
                        </div>

                        <ul role="list" class="ops-timeline mt-6">
                            @foreach($incident->activities->sortByDesc('created_at') as $activity)
                                <li class="ops-timeline__item">
                                    <span class="ops-timeline__dot" aria-hidden="true"></span>
                                    <div class="ops-timeline__card">
                                        <div class="ops-incident-sequence__item">
                                            <div class="ops-incident-sequence__header">
                                                <div>
                                                    <p class="ops-incident-sequence__title">{{ $this->getActivityTypeLabel($activity->action_type) }}</p>
                                                    <p class="ops-incident-sequence__meta">
                                                        {{ $activity->actor?->name ?? 'Unknown' }} · {{ $activity->created_at?->format('M d, Y H:i') ?? 'Unknown time' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="ops-incident-sequence__body">{{ $activity->summary }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            </div>

            <div class="ops-stack">
                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="40">
                    <div class="ops-card__body ops-incident-lane">
                        <div>
                            <p class="ops-section-heading__eyebrow">Accountability lane</p>
                            <h2 class="ops-incident-lane__title">Set owner and follow-up target</h2>
                            <p class="ops-incident-lane__body">Use this lane to make accountability explicit without changing incident status. Keep ownership light, visible, and honest.</p>
                        </div>

                        <form wire:submit="updateAccountability" class="space-y-4">
                            <div>
                                <label for="owner-id" class="ops-field-label">Incident Owner</label>
                                <select id="owner-id" wire:model="ownerId" class="ops-control">
                                    <option value="">{{ __('Unowned') }}</option>
                                    @foreach($managementOwners as $owner)
                                        <option value="{{ $owner['id'] }}">{{ $owner['name'] }} ({{ ucfirst($owner['role']) }})</option>
                                    @endforeach
                                </select>
                                <p class="ops-field-help">Only administrators and supervisors can own active incident follow-up.</p>
                                @error('ownerId') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="follow-up-due-at" class="ops-field-label">Follow-up Target Date</label>
                                <input
                                    id="follow-up-due-at"
                                    type="date"
                                    wire:model="followUpDueAt"
                                    class="ops-control"
                                >
                                <p class="ops-field-help">This is an internal target date for the next review or operational action, not a formal SLA.</p>
                                @error('followUpDueAt') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="ops-divider-top flex justify-end pt-5">
                                <button type="submit" class="ops-button ops-button--secondary min-w-44">
                                    <span wire:loading.remove wire:target="updateAccountability">Save accountability</span>
                                    <span wire:loading wire:target="updateAccountability">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="80">
                    <div class="ops-card__body ops-incident-lane">
                        <div>
                            <p class="ops-section-heading__eyebrow">Action lane</p>
                            <h2 class="ops-incident-lane__title">Update status with intent</h2>
                            <p class="ops-incident-lane__body">Use this form when you want the timeline to show a real handoff, progress update, or resolution summary.</p>
                        </div>

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

                            <div class="ops-divider-top flex justify-end pt-5">
                                <button type="submit" class="ops-button ops-button--primary min-w-44">
                                    <span wire:loading.remove wire:target="updateStatus">Update Status</span>
                                    <span wire:loading wire:target="updateStatus">Updating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="140">
                    <div class="ops-card__body ops-incident-lane">
                        <div>
                            <p class="ops-section-heading__eyebrow">Reference snapshot</p>
                            <h2 class="ops-incident-lane__title">Quick facts</h2>
                            <p class="ops-incident-lane__body">Keep these details visible while deciding the next move so the update stays grounded in the reported context.</p>
                        </div>

                        <div class="ops-stat-grid">
                            <x-ops.stat-card kicker="Category" :value="$incident->category->value" />
                            <x-ops.stat-card kicker="Room" :value="$incident->room?->name ?? 'Not set'" />
                            <x-ops.stat-card kicker="Equipment" :value="$incident->equipment_reference ?? 'Not provided'" />
                            <x-ops.stat-card kicker="Owner" :value="$incident->owner?->name ?? 'Unowned'" />
                            <x-ops.stat-card kicker="Follow-up Target" :value="$incident->follow_up_due_at?->format('M d, Y') ?? 'Not set'" />
                            <x-ops.stat-card kicker="Resolved At" :value="$incident->resolved_at?->format('M d, Y H:i') ?? 'Not resolved yet'" />
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
