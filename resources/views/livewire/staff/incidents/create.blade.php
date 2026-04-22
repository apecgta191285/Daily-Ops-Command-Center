<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Duty staff issue handoff') }}</p>
                <h2 class="ops-page__title">{{ __('Report Incident') }}</h2>
                <p class="ops-page-intro__body">
                    Capture the room issue clearly so management can see where it happened, what it affected, and what the duty student actually found.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room-tied handoff') }}</span>
                    @if ($roomId !== '')
                        <span class="ops-shell-chip">{{ collect($rooms)->firstWhere('id', $roomId)['name'] ?? __('Selected room') }}</span>
                    @endif
                    <span class="ops-shell-chip">{{ __('Optional equipment reference') }}</span>
                    <span class="ops-shell-chip">{{ __('Management visible') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                    {{ __('Back to checklist') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-card__body">
                @if ($submissionRecap)
                    <div class="space-y-6">
                        <div class="ops-alert ops-alert--success">
                            Incident reported successfully. Management can now review it with the room context intact in the dashboard and follow-up views.
                        </div>

                        <div class="ops-recap-grid">
                            <div class="ops-recap-panel">
                                <h3 class="ops-recap-panel__title">Submission Recap</h3>
                                <dl class="ops-detail-stack">
                                    <div>
                                        <dt class="ops-detail-stack__label">Title</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['title'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">Category</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['category'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">Severity</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['severity'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">Room</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['room_name'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">Equipment Reference</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['equipment_reference'] ?? __('Not provided') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">Created At</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['created_at'] }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="ops-recap-panel ops-recap-panel--subtle">
                                <h3 class="ops-recap-panel__title">What Happens Next</h3>
                                <ul role="list" class="ops-next-steps">
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Management users will see the incident in the dashboard attention and incident follow-up screens.</span></li>
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>The room and any equipment reference stay attached to the issue so the next reviewer knows exactly where to look.</span></li>
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>The incident starts as <span class="ops-text-heading font-medium">{{ $submissionRecap['status'] }}</span> until someone updates it.</span></li>
                                    @if ($submissionRecap['has_attachment'])
                                        <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Your attachment was included with the report.</span></li>
                                    @endif
                                    @if ($submissionRecap['from_checklist'])
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>This report is linked to the checklist follow-up path, so you can return and continue the daily lab routine.</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            @if ($submissionRecap['from_checklist'])
                                <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                                    Back to today&apos;s checklist
                                </a>
                            @endif

                            <button type="button" wire:click="startAnother" class="ops-button ops-button--primary min-w-52">
                                Report another incident
                            </button>
                        </div>
                    </div>
                @elseif (count($rooms) === 0)
                    <div class="space-y-6">
                        <div class="ops-alert ops-alert--warning">
                            No active lab room is available right now, so this incident cannot be filed yet. Ask an administrator or supervisor to restore at least one active room first.
                        </div>

                        <div class="ops-recap-panel ops-recap-panel--subtle">
                            <h3 class="ops-recap-panel__title">Why reporting is paused</h3>
                            <ul role="list" class="ops-next-steps">
                                <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Every incident in this workflow must stay tied to one room.</span></li>
                                <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>No active room is currently available to attach to this report.</span></li>
                                <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Once a room is active again, the same incident form can be used normally.</span></li>
                            </ul>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                                Back to checklist
                            </a>
                        </div>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-6">
                        <div class="grid gap-6">
                            <div>
                                <label for="title" class="ops-field-label">Title <span class="ops-required-mark">*</span></label>
                                <input type="text" id="title" wire:model="title" class="ops-control" placeholder="e.g. เครื่อง PC-03 เปิดไม่ติด">
                                @error('title') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label for="category" class="ops-field-label">Category <span class="ops-required-mark">*</span></label>
                                    <select id="category" wire:model="category" class="ops-control">
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                    @error('category') <span class="ops-field-error">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="room_id" class="ops-field-label">Room <span class="ops-required-mark">*</span></label>
                                    <select id="room_id" wire:model="roomId" class="ops-control">
                                        <option value="">-- Select Room --</option>
                                        @foreach($rooms as $roomOption)
                                            <option value="{{ $roomOption['id'] }}">{{ $roomOption['name'] }} ({{ $roomOption['code'] }})</option>
                                        @endforeach
                                    </select>
                                    @error('roomId') <span class="ops-field-error">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="severity" class="ops-field-label">Severity <span class="ops-required-mark">*</span></label>
                                    <select id="severity" wire:model="severity" class="ops-control">
                                        <option value="">-- Select Severity --</option>
                                        @foreach($severities as $sev)
                                            <option value="{{ $sev }}">{{ $sev }}</option>
                                        @endforeach
                                    </select>
                                    @error('severity') <span class="ops-field-error">{{ $message }}</span> @enderror
                                    <p class="ops-field-help">Low = รบกวนเล็กน้อย, Medium = กระทบการใช้งานบางส่วน, High = กระทบหลัก/ความปลอดภัย</p>
                                </div>
                            </div>

                            <div>
                                <label for="equipment_reference" class="ops-field-label">Equipment Reference (Optional)</label>
                                <input type="text" id="equipment_reference" wire:model="equipmentReference" class="ops-control" placeholder="e.g. PC-12, Printer Lab 2, Plug A3">
                                @error('equipmentReference') <span class="ops-field-error">{{ $message }}</span> @enderror
                                <p class="ops-field-help">Use a short room-level equipment reference when the issue affects one workstation, printer, projector, or power point.</p>
                            </div>

                            <div>
                                <label for="description" class="ops-field-label">Description <span class="ops-required-mark">*</span></label>
                                <textarea id="description" wire:model="description" rows="5" class="ops-control"></textarea>
                                @error('description') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="attachment" class="ops-field-label">Attachment (Optional)</label>
                                <input type="file" id="attachment" wire:model="attachment" class="ops-control ops-control--file">
                                @error('attachment') <span class="ops-field-error">{{ $message }}</span> @enderror
                                <p class="ops-field-help">Max file size: 10MB.</p>
                            </div>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                                Cancel
                            </a>
                            <button type="submit" class="ops-button ops-button--primary min-w-44">
                                <span wire:loading.remove wire:target="submit">Create incident</span>
                                <span wire:loading wire:target="submit">Saving...</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </div>
</div>
