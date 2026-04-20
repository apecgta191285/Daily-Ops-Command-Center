<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Template authoring') }}</p>
                <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
                <p class="ops-page-intro__body">
                    {{ __($this->pageDescription) }}
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ $template ? __('Revision workspace') : __('New draft') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope-aware activation') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin-owned') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('templates.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to templates') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @php
            $summary = $this->templateSummary;
            $authoringSignals = $this->authoringSignals;
            $previewGroups = $this->previewGroups;
            $scopeGovernance = $this->scopeGovernance;
        @endphp

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

        @if ($template)
            <form id="duplicate-template-form" method="POST" action="{{ route('templates.duplicate', $template) }}" class="hidden">
                @csrf
            </form>
        @endif

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Template administration</p>
                    <h3 class="ops-hero__title">{{ __($this->pageTitle) }}</h3>
                    <p class="ops-hero__lead">
                        Build the live lab checklist with safer revision cues, lightweight grouping, and explicit activation impact before anything replaces the current operating standard.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $template ? __('Template revision mode') : __('New template draft') }}</span>
                        <span class="ops-shell-chip">{{ __('Admin-only surface') }}</span>
                        <span class="ops-shell-chip">{{ __('One live template per scope') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Control note</p>
                        <p class="ops-hero__aside-value">{{ $summary['item_count'] }}</p>
                        <p class="ops-hero__aside-copy">
                            Checklist item(s) currently defined in this template draft.
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Sections') }}</p>
                            <p class="ops-authoring-metric__value">{{ max($summary['grouped_section_count'], 1) }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Required') }}</p>
                            <p class="ops-authoring-metric__value">{{ $summary['required_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Optional') }}</p>
                            <p class="ops-authoring-metric__value">{{ $summary['optional_count'] }}</p>
                        </div>
                    </div>

                    <div class="ops-hero__aside-stack">
                        <div class="ops-shell-chip">
                            <span>{{ __('Scope') }}</span>
                            <strong class="font-semibold text-white">{{ $scope }}</strong>
                        </div>
                        <div class="ops-shell-chip">
                            <span>{{ __('State') }}</span>
                            <strong class="font-semibold text-white">{{ $is_active ? __('Active') : __('Draft') }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <form wire:submit="save" class="space-y-6">
            <div class="ops-command-grid ops-command-grid--template">
                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Authoring rhythm</p>
                                <h3 class="ops-section-heading__title">Build the live checklist in three passes</h3>
                                <p class="ops-section-heading__body">Define the identity, shape the staff-facing sequence, then confirm the activation impact before this draft becomes the daily operational standard.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-authoring-rhythm">
                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">1</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">{{ __('Frame the template') }}</p>
                                        <p class="ops-authoring-rhythm__body">{{ __('Choose a clear title, explain the operating moment, and keep the description focused on why the checklist exists.') }}</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">2</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">{{ __('Shape execution order') }}</p>
                                        <p class="ops-authoring-rhythm__body">{{ __('Use group labels and item order to make the run read like a real shift routine instead of a flat collection of tasks.') }}</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">3</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">{{ __('Review live impact') }}</p>
                                        <p class="ops-authoring-rhythm__body">{{ __('Pause on the governance lane before save so you understand whether this draft stays private or replaces the current production checklist.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Template identity</p>
                                <h3 class="ops-section-heading__title">Core definition</h3>
                                <p class="ops-section-heading__body">Set the operating name and description that explain when this template should be used.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="title" class="ops-field-label">Title <span class="ops-required-mark">*</span></label>
                                <input id="title" type="text" wire:model="title" class="ops-control" placeholder="เช่น เปิดห้องปฏิบัติการ">
                                @error('title') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="description" class="ops-field-label">Description</label>
                                <textarea id="description" wire:model="description" rows="4" class="ops-control" placeholder="อธิบายว่าทำไม template นี้จึงมีอยู่"></textarea>
                                @error('description') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Checklist structure</p>
                                <h3 class="ops-section-heading__title">Checklist Items</h3>
                                <p class="ops-section-heading__body">Define the ordered steps duty staff will see during the daily checklist flow.</p>
                            </div>

                            <button type="button" wire:click="addItem" class="ops-button ops-button--secondary">
                                {{ __('Add item') }}
                            </button>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @error('items') <span class="ops-field-error">{{ $message }}</span> @enderror

                            <div class="space-y-4">
                                @foreach ($items as $index => $item)
                                    <section class="ops-admin-item ops-admin-item--authoring" data-motion="scale-soft" data-motion-delay="{{ 140 + ($index * 25) }}">
                                        <div class="ops-admin-item__header">
                                            <div class="ops-admin-item__identity">
                                                <span class="ops-step-index">{{ $index + 1 }}</span>
                                                <div>
                                                    <p class="ops-admin-item__eyebrow">{{ __('Checklist item') }}</p>
                                                    <h4 class="ops-admin-item__title">
                                                        {{ trim($item['title'] ?? '') !== '' ? $item['title'] : __('Untitled checklist item') }}
                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="ops-admin-item__chips">
                                                <span class="ops-chip {{ ($item['is_required'] ?? false) ? 'ops-chip--info' : '' }}">
                                                    {{ ($item['is_required'] ?? false) ? __('Required') : __('Optional') }}
                                                </span>
                                                <span class="ops-chip">{{ __('Order') }} {{ $item['sort_order'] }}</span>
                                                @if (trim($item['group_label'] ?? '') !== '')
                                                    <span class="ops-chip ops-chip--success">{{ $item['group_label'] }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_190px]">
                                            <div class="space-y-5">
                                                
                                                <div>
                                                    <label for="item-title-{{ $index }}" class="ops-field-label">Item title <span class="ops-required-mark">*</span></label>
                                                    <input id="item-title-{{ $index }}" type="text" wire:model="items.{{ $index }}.title" class="ops-control" placeholder="เช่น ตรวจการเชื่อมต่ออินเทอร์เน็ต">
                                                    @error('items.'.$index.'.title') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label for="item-description-{{ $index }}" class="ops-field-label">Item description</label>
                                                    <textarea id="item-description-{{ $index }}" wire:model="items.{{ $index }}.description" rows="3" class="ops-control" placeholder="อธิบายความหมายหรือเหตุผลของรายการนี้"></textarea>
                                                    @error('items.'.$index.'.description') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label for="item-group-{{ $index }}" class="ops-field-label">Group label</label>
                                                    <input id="item-group-{{ $index }}" type="text" wire:model="items.{{ $index }}.group_label" class="ops-control" placeholder="เช่น Safety checks">
                                                    <p class="ops-field-help">Optional. Use the same label on related items to create lightweight sections in the daily checklist.</p>
                                                    @error('items.'.$index.'.group_label') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="space-y-5">
                                                <div class="ops-surface-soft px-4 py-4">
                                                    <p class="ops-admin-item__meta-label">{{ __('Execution cue') }}</p>
                                                    <p class="ops-admin-item__meta-value">
                                                        {{ trim($item['group_label'] ?? '') !== '' ? __('Appears under :group', ['group' => $item['group_label']]) : __('Appears in the unlabelled sequence') }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <label for="item-order-{{ $index }}" class="ops-field-label">Order</label>
                                                    <input id="item-order-{{ $index }}" type="number" min="1" wire:model="items.{{ $index }}.sort_order" class="ops-control">
                                                    @error('items.'.$index.'.sort_order') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <label class="ops-choice w-full justify-between">
                                                    <span class="ops-text-heading text-sm font-medium">{{ __('Required') }}</span>
                                                    <input type="checkbox" wire:model="items.{{ $index }}.is_required" class="ops-choice__control">
                                                </label>

                                                <button
                                                    type="button"
                                                    wire:click="removeItem({{ $index }})"
                                                    class="ops-button ops-button--danger w-full"
                                                    @disabled(count($items) === 1)
                                                >
                                                    {{ __('Remove item') }}
                                                </button>
                                            </div>
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>

                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Authoring pulse</p>
                                <h3 class="ops-section-heading__title">Checkpoint summary</h3>
                                <p class="ops-section-heading__body">A quick scan of what is ready, what is still thin, and which decisions will affect staff most once this draft goes live.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @foreach ($authoringSignals as $signal)
                                <x-ops.callout :title="$signal['title']" :tone="$signal['tone']">
                                    {{ __($signal['body']) }}
                                </x-ops.callout>
                            @endforeach
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Live execution preview</p>
                                <h3 class="ops-section-heading__title">How staff will scan this checklist</h3>
                                <p class="ops-section-heading__body">This compact preview mirrors the way the checklist will read during execution once the draft is active.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-authoring-preview">
                                @foreach ($previewGroups as $group)
                                    <section class="ops-authoring-preview__group">
                                        <div class="ops-authoring-preview__label">{{ $group['label'] }}</div>

                                        <div class="ops-authoring-preview__items">
                                            @foreach ($group['items'] as $previewItem)
                                                <div class="ops-authoring-preview__item">
                                                    <p class="ops-authoring-preview__title">{{ $previewItem['title'] }}</p>
                                                    <span class="ops-chip {{ $previewItem['is_required'] ? 'ops-chip--info' : '' }}">
                                                        {{ $previewItem['is_required'] ? __('Required') : __('Optional') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    @if ($template)
                        <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="160">
                            <div class="ops-section-heading">
                                <div>
                                    <p class="ops-section-heading__eyebrow">Revision safety</p>
                                    <h3 class="ops-section-heading__title">Safer iteration path</h3>
                                    <p class="ops-section-heading__body">Duplicate before major structural edits when you need a cleaner revision trail.</p>
                                </div>
                            </div>

                            <div class="ops-card__body">
                                <x-ops.callout title="Safer iteration path" tone="neutral">
                                    @if ($hasRunHistory)
                                        {{ __('This template already has :count recorded run(s). Duplicate it before major structural edits so historical runs stay easy to interpret.', ['count' => $runCount]) }}
                                    @elseif ($is_active)
                                        {{ __('This template is currently active for its scope. Duplicate it if you want to prepare a revised version without changing that live checklist lane immediately.') }}
                                    @else
                                        {{ __('Duplicate this template when you want to branch a new revision instead of overwriting the current draft.') }}
                                    @endif
                                </x-ops.callout>
                                @if ($currentLiveTemplateTitle)
                                    <p class="ops-text-muted mt-3 text-sm">
                                        {{ __('Current live template for this scope: :title', ['title' => $currentLiveTemplateTitle]) }}
                                    </p>
                                @endif
                                <button type="submit" form="duplicate-template-form" class="ops-button ops-button--secondary mt-4 w-full">
                                    {{ __('Duplicate template instead') }}
                                </button>
                            </div>
                        </section>
                    @endif

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="210">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Governance</p>
                                <h3 class="ops-section-heading__title">Activation impact</h3>
                                <p class="ops-section-heading__body">Each scope owns its own live template. Activation only replaces the currently active template inside the selected operating lane.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="scope" class="ops-field-label">Scope <span class="ops-required-mark">*</span></label>
                                <select id="scope" wire:model="scope" class="ops-control">
                                    @foreach ($scopes as $scopeOption)
                                        <option value="{{ $scopeOption }}">{{ $scopeOption }}</option>
                                    @endforeach
                                </select>
                                <p class="ops-field-help">Scope determines which live checklist lane this template can own. Only one active template may exist within each scope.</p>
                                @error('scope') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            @php
                                $activationImpact = $this->activationImpact;
                                $activationTone = $activationImpact['tone'] === 'warning' ? 'warning' : 'neutral';
                            @endphp

                            <x-ops.callout title="Activation impact" :tone="$activationTone">
                                <p>{{ __($activationImpact['title']) }}</p>
                                <p class="mt-3">{{ __($activationImpact['description']) }}</p>
                            </x-ops.callout>

                            <label class="ops-choice w-full justify-between">
                                <span>
                                    <span class="ops-text-heading block font-medium">{{ __('Active template') }}</span>
                                    <span class="ops-text-muted mt-1 block text-xs">{{ __('Saving as active will automatically retire the current live template in this scope only.') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="ops-choice__control">
                            </label>

                            <div class="ops-governance-grid ops-governance-grid--compact">
                                @foreach ($scopeGovernance as $lane)
                                    <article class="ops-governance-card {{ $lane['state'] === 'missing' ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }} {{ $lane['is_selected_scope'] ? 'ops-governance-card--selected' : '' }}">
                                        <div class="ops-governance-card__header">
                                            <div>
                                                <p class="ops-admin-item__eyebrow">{{ __('Scope lane') }}</p>
                                                <h4 class="ops-admin-item__title">{{ $lane['scope'] }}</h4>
                                            </div>

                                            <span class="ops-chip {{ $lane['state'] === 'missing' ? 'ops-chip--warning' : 'ops-chip--success' }}">
                                                {{ $lane['state'] === 'missing' ? __('Missing live template') : __('Live covered') }}
                                            </span>
                                        </div>

                                        <div class="ops-governance-card__body">
                                            <p class="ops-governance-card__title">
                                                {{ $lane['live_template_title'] ?? __('No active template') }}
                                            </p>
                                            <p class="ops-governance-card__meta">
                                                @if ($lane['is_selected_scope'])
                                                    {{ __('This is the scope lane currently selected in the governance form.') }}
                                                @elseif ($lane['state'] === 'missing')
                                                    {{ __('No live checklist owner exists in this scope yet.') }}
                                                @else
                                                    {{ __('This scope already has a live checklist owner.') }}
                                                @endif
                                            </p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('templates.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Cancel') }}
                </a>

                <button type="submit" class="ops-button ops-button--primary min-w-52">
                    <span wire:loading.remove wire:target="save">{{ $template ? __('Save template changes') : __('Create template') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
