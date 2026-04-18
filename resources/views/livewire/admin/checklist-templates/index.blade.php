<div>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="ops-page__title">{{ __('Checklist Templates') }}</h2>
                <p class="text-sm">
                    Admin-only template setup for the daily checklist workflow used by staff.
                </p>
            </div>

            <a href="{{ route('templates.create') }}" class="ops-button ops-button--primary" wire:navigate>
                {{ __('Create template') }}
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
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

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-3">
                <x-ops.callout title="Template administration context" tone="neutral">
                    <p>
                        This screen now lives inside the main application shell so template administration uses the same navigation, authentication, and visual language as the rest of the product.
                    </p>
                    <p class="mt-3">
                        Scope is currently a classification label for template organization and reporting. The runtime still executes exactly one active daily checklist template at a time, so saving an active template will automatically retire the others.
                    </p>
                </x-ops.callout>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                @if ($templates->isEmpty())
                    <x-ops.empty-state
                        title="No checklist templates exist yet."
                        body="Create the first active template to define what staff should complete during the daily checklist flow."
                    >
                        <a href="{{ route('templates.create') }}" class="ops-button ops-button--primary" wire:navigate>
                            {{ __('Create first template') }}
                        </a>
                    </x-ops.empty-state>
                @else
                    <div class="ops-table-wrap">
                        <table class="ops-table ops-table--responsive min-w-full">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Scope</th>
                                    <th>Items</th>
                                    <th>State</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($templates as $template)
                                    <tr class="ops-table__row" data-template-active="{{ $template->is_active ? 'true' : 'false' }}">
                                        <td data-label="Title" class="ops-text-heading px-4 py-4 text-sm font-medium">
                                            <div class="space-y-1">
                                                <p>{{ $template->title }}</p>
                                                @if (filled($template->description))
                                                    <p class="ops-text-muted text-xs">{{ $template->description }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Scope" class="ops-text-muted px-4 py-4 text-sm">{{ $template->scope }}</td>
                                        <td data-label="Items" class="ops-text-muted px-4 py-4 text-sm">{{ $template->items_count }}</td>
                                        <td data-label="State" class="px-4 py-4 text-sm">
                                            <span class="ops-badge {{ $template->is_active ? 'ops-badge--success' : 'ops-badge--neutral' }}">
                                                {{ $template->is_active ? __('Active') : __('Retired') }}
                                            </span>
                                        </td>
                                        <td data-label="Action" class="px-4 py-4 text-right text-sm">
                                            <div class="flex flex-wrap justify-end gap-2">
                                                <form method="POST" action="{{ route('templates.duplicate', $template) }}">
                                                    @csrf
                                                    <button type="submit" class="ops-button ops-button--secondary">
                                                        {{ __('Duplicate') }}
                                                    </button>
                                                </form>

                                                <a href="{{ route('templates.edit', $template) }}" class="ops-button ops-button--secondary" wire:navigate>
                                                    {{ __('Edit template') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
