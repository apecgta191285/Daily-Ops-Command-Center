<div>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
            <p class="text-sm">
                {{ __($this->pageDescription) }}
            </p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl">
        @if (session()->has('message'))
            <div class="mb-6 ops-alert ops-alert--success">
                {{ session('message') }}
            </div>
        @endif

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <form wire:submit="save" class="space-y-8">
                    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_240px]">
                        <div class="space-y-6">
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

                        <div class="space-y-6">
                            @if ($template)
                                <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-4">
                                    <p class="text-sm font-semibold text-[var(--app-heading)]">{{ __('Safer iteration path') }}</p>
                                    <p class="mt-2 text-sm text-[var(--app-text-muted)]">
                                        @if ($hasRunHistory)
                                            {{ __('This template already has :count recorded run(s). Duplicate it before major structural edits so historical runs stay easy to interpret.', ['count' => $runCount]) }}
                                        @elseif ($is_active)
                                            {{ __('This template is currently active. Duplicate it if you want to prepare a revised version without changing the live daily checklist immediately.') }}
                                        @else
                                            {{ __('Duplicate this template when you want to branch a new revision instead of overwriting the current draft.') }}
                                        @endif
                                    </p>
                                    <form method="POST" action="{{ route('templates.duplicate', $template) }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="ops-button ops-button--secondary w-full">
                                            {{ __('Duplicate template instead') }}
                                        </button>
                                    </form>
                                </div>
                            @endif

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

                            <label class="ops-choice w-full justify-between">
                                <span>
                                    <span class="block font-medium text-[var(--app-heading)]">{{ __('Active template') }}</span>
                                    <span class="mt-1 block text-xs text-[var(--app-text-muted)]">{{ __('Saving as active will automatically retire every other template.') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="size-4 rounded border-[var(--app-border)] text-[var(--app-action-primary)]">
                            </label>
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-[var(--app-border)] pt-8">
                        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-[var(--app-heading)]">{{ __('Checklist Items') }}</h3>
                                <p class="mt-1 text-sm text-[var(--app-text-muted)]">
                                    Define the ordered steps staff will see during the daily checklist flow.
                                </p>
                            </div>

                            <button type="button" wire:click="addItem" class="ops-button ops-button--secondary">
                                {{ __('Add item') }}
                            </button>
                        </div>

                        @error('items') <span class="ops-field-error">{{ $message }}</span> @enderror

                        <div class="space-y-4">
                            @foreach ($items as $index => $item)
                                <section class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-5 shadow-sm">
                                    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_160px]">
                                        <div class="space-y-5">
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
                                                class="ops-button w-full border-[var(--app-danger-border)] bg-[var(--app-danger-bg)] text-[var(--app-danger-text)] hover:bg-[#fbd7d9]"
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
        </section>
    </div>
</div>
