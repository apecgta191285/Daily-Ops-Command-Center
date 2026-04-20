<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Duty staff issue handoff') }}</p>
                <h2 class="ops-page__title">{{ __('Report Incident') }}</h2>
                <p class="ops-page-intro__body">
                    Capture the lab issue clearly so management can triage it quickly without losing the operating context behind the report.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Structured handoff') }}</span>
                    <span class="ops-shell-chip">{{ __('Evidence-ready') }}</span>
                    <span class="ops-shell-chip">{{ __('Management visible') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('checklists.runs.today', $checklistReturnScope ? ['scope' => $checklistReturnScope] : []) }}" class="ops-button ops-button--secondary">
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
                            Incident reported successfully. Management can now see it in the dashboard and issue follow-up views.
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
                                        <dt class="ops-detail-stack__label">Created At</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['created_at'] }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="ops-recap-panel ops-recap-panel--subtle">
                                <h3 class="ops-recap-panel__title">What Happens Next</h3>
                                <ul role="list" class="ops-next-steps">
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Management users will see the incident in the dashboard attention and incident follow-up screens.</span></li>
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>The incident starts as <span class="ops-text-heading font-medium">{{ $submissionRecap['status'] }}</span> until someone updates it.</span></li>
                                    @if ($submissionRecap['has_attachment'])
                                        <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Your attachment was included with the report.</span></li>
                                    @endif
                                    @if ($submissionRecap['from_checklist'])
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>This report is linked to a checklist follow-up flow, so you can return and continue the daily lab workflow.</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            @if ($submissionRecap['from_checklist'])
                                <a href="{{ route('checklists.runs.today', $checklistReturnScope ? ['scope' => $checklistReturnScope] : []) }}" class="ops-button ops-button--secondary">
                                    Back to today&apos;s checklist
                                </a>
                            @endif

                            <button type="button" wire:click="startAnother" class="ops-button ops-button--primary min-w-52">
                                Report another incident
                            </button>
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
                            <a href="{{ route('checklists.runs.today', $checklistReturnScope ? ['scope' => $checklistReturnScope] : []) }}" class="ops-button ops-button--secondary">
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
