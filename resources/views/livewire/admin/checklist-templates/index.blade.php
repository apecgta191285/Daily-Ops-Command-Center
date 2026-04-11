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
            <div class="ops-alert ops-alert--success">
                {{ session('message') }}
            </div>
        @endif

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-3">
                <p class="text-sm text-[var(--app-text-muted)]">
                    This screen now lives inside the main application shell so template administration uses the same navigation, authentication, and visual language as the rest of the product.
                </p>
                <p class="text-sm text-[var(--app-text-muted)]">
                    Scope is currently a classification label for template organization and reporting. The runtime still executes exactly one active daily checklist template at a time, so saving an active template will automatically retire the others.
                </p>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                @if ($templates->isEmpty())
                    <p class="text-sm text-[var(--app-text-muted)]">No checklist templates exist yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="ops-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Scope</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Items</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">State</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-[0.08em] text-[var(--app-text-muted)]">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($templates as $template)
                                    <tr class="bg-white">
                                        <td class="px-4 py-4 text-sm font-medium text-[var(--app-heading)]">
                                            <div class="space-y-1">
                                                <p>{{ $template->title }}</p>
                                                @if (filled($template->description))
                                                    <p class="text-xs text-[var(--app-text-muted)]">{{ $template->description }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-[var(--app-text-muted)]">{{ $template->scope }}</td>
                                        <td class="px-4 py-4 text-sm text-[var(--app-text-muted)]">{{ $template->items_count }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            <span class="ops-badge {{ $template->is_active ? 'ops-badge--success' : 'ops-badge--neutral' }}">
                                                {{ $template->is_active ? __('Active') : __('Retired') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right text-sm">
                                            <a href="{{ route('templates.edit', $template) }}" class="ops-button ops-button--secondary" wire:navigate>
                                                {{ __('Edit template') }}
                                            </a>
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
