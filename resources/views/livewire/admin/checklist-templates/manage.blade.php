<div>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
            <p class="text-sm">
                {{ __($this->pageDescription) }}
            </p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
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

        <section class="ops-hero">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Template Administration</p>
                    <h3 class="ops-hero__title">{{ __($this->pageTitle) }}</h3>
                    <p class="ops-hero__lead">
                        Build the live daily checklist with safer revision cues, lightweight grouping, and explicit activation impact before anything replaces the current operational standard.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $template ? __('Template revision mode') : __('New template draft') }}</span>
                        <span class="ops-shell-chip">{{ __('Admin-only surface') }}</span>
                        <span class="ops-shell-chip">{{ __('Single active daily template') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Control note</p>
                        <p class="ops-hero__aside-value">{{ count($items) }}</p>
                        <p class="ops-hero__aside-copy">
                            Checklist item(s) currently defined in this template draft.
                        </p>
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
                    <section class="ops-card overflow-hidden">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Template identity</p>
                                <h3 class="ops-section-heading__title">Core definition</h3>
                                <p class="ops-section-heading__body">Set the operating name and description that explain when this template should be used.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="title" class="ops-field-label">Title <span class="text-[var(--app-danger-text)]">*</span></label>
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

                    <section class="ops-card overflow-hidden">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Checklist structure</p>
                                <h3 class="ops-section-heading__title">Checklist Items</h3>
                                <p class="ops-section-heading__body">Define the ordered steps staff will see during the daily checklist flow.</p>
                            </div>

                            <button type="button" wire:click="addItem" class="ops-button ops-button--secondary">
                                {{ __('Add item') }}
                            </button>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @error('items') <span class="ops-field-error">{{ $message }}</span> @enderror

                            <div class="space-y-4">
                                @foreach ($items as $index => $item)
                                    <section class="ops-admin-item">
                                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_170px]">
                                            <div class="space-y-5">
                                                <div>
                                                    <p class="ops-admin-item__eyebrow">{{ __('Checklist item') }} {{ $index + 1 }}</p>
                                                </div>

                                                <div>
                                                    <label for="item-title-{{ $index }}" class="ops-field-label">Item title <span class="text-[var(--app-danger-text)]">*</span></label>
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
                                                <div>
                                                    <label for="item-order-{{ $index }}" class="ops-field-label">Order</label>
                                                    <input id="item-order-{{ $index }}" type="number" min="1" wire:model="items.{{ $index }}.sort_order" class="ops-control">
                                                    @error('items.'.$index.'.sort_order') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <label class="ops-choice w-full justify-between">
                                                    <span class="text-sm font-medium text-[var(--app-heading)]">{{ __('Required') }}</span>
                                                    <input type="checkbox" wire:model="items.{{ $index }}.is_required" class="size-4 rounded border-[var(--app-border)] text-[var(--app-action-primary)]">
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
                    @if ($template)
                        <section class="ops-card overflow-hidden">
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
                                        {{ __('This template is currently active. Duplicate it if you want to prepare a revised version without changing the live daily checklist immediately.') }}
                                    @else
                                        {{ __('Duplicate this template when you want to branch a new revision instead of overwriting the current draft.') }}
                                    @endif
                                </x-ops.callout>
                                @if ($currentLiveTemplateTitle)
                                    <p class="mt-3 text-sm text-[var(--app-text-muted)]">
                                        {{ __('Current live template: :title', ['title' => $currentLiveTemplateTitle]) }}
                                    </p>
                                @endif
                                <button type="submit" form="duplicate-template-form" class="ops-button ops-button--secondary mt-4 w-full">
                                    {{ __('Duplicate template instead') }}
                                </button>
                            </div>
                        </section>
                    @endif

                    <section class="ops-card overflow-hidden">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Governance</p>
                                <h3 class="ops-section-heading__title">Activation impact</h3>
                                <p class="ops-section-heading__body">Scope currently classifies the template for administration and reporting. Runtime still executes exactly one active daily checklist template at a time.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="scope" class="ops-field-label">Scope <span class="text-[var(--app-danger-text)]">*</span></label>
                                <select id="scope" wire:model="scope" class="ops-control">
                                    @foreach ($scopes as $scopeOption)
                                        <option value="{{ $scopeOption }}">{{ $scopeOption }}</option>
                                    @endforeach
                                </select>
                                <p class="ops-field-help">Scope currently classifies the template for administration and reporting. It does not create a parallel daily execution flow yet.</p>
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
                                    <span class="block font-medium text-[var(--app-heading)]">{{ __('Active template') }}</span>
                                    <span class="mt-1 block text-xs text-[var(--app-text-muted)]">{{ __('Saving as active will automatically retire every other template.') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="size-4 rounded border-[var(--app-border)] text-[var(--app-action-primary)]">
                            </label>
                        </div>
                    </section>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[var(--app-border)] pt-6 sm:flex-row sm:justify-end">
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
