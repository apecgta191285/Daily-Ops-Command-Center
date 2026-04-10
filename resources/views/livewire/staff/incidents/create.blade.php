<div>
    <x-slot name="header">
        <div>
            <h2 class="ops-page__title">{{ __('Report Incident') }}</h2>
            <p class="text-sm">
                Capture the issue clearly so management can triage it quickly.
            </p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                @if (session()->has('message'))
                    <div class="ops-alert ops-alert--success mb-5">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="submit" class="space-y-6">
                    <div class="grid gap-6">
                        <div>
                            <label for="title" class="ops-field-label">Title <span class="text-[var(--app-danger-text)]">*</span></label>
                            <input type="text" id="title" wire:model="title" class="ops-control" placeholder="e.g. เครื่อง PC-03 เปิดไม่ติด">
                            @error('title') <span class="ops-field-error">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="category" class="ops-field-label">Category <span class="text-[var(--app-danger-text)]">*</span></label>
                                <select id="category" wire:model="category" class="ops-control">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="severity" class="ops-field-label">Severity <span class="text-[var(--app-danger-text)]">*</span></label>
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
                            <label for="description" class="ops-field-label">Description <span class="text-[var(--app-danger-text)]">*</span></label>
                            <textarea id="description" wire:model="description" rows="5" class="ops-control"></textarea>
                            @error('description') <span class="ops-field-error">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="attachment" class="ops-field-label">Attachment (Optional)</label>
                            <input type="file" id="attachment" wire:model="attachment" class="ops-control file:mr-3 file:rounded-lg file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-sm file:font-medium file:text-[var(--app-text)]">
                            @error('attachment') <span class="ops-field-error">{{ $message }}</span> @enderror
                            <p class="ops-field-help">Max file size: 10MB.</p>
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-[var(--app-border)] pt-6 sm:flex-row sm:justify-end">
                        <a href="{{ route('checklists.runs.today') }}" class="ops-button ops-button--secondary">
                            Cancel
                        </a>
                        <button type="submit" class="ops-button ops-button--primary min-w-44">
                            <span wire:loading.remove wire:target="submit">Create Incident</span>
                            <span wire:loading wire:target="submit">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
