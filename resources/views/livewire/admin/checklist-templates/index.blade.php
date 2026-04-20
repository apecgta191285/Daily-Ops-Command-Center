<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Admin checklist control') }}</p>
                <h2 class="ops-page__title">{{ __('Checklist Templates') }}</h2>
                <p class="ops-page-intro__body">
                    Govern the live checklist lanes by scope, shape draft revisions safely, and keep the lab team aligned with the real daily routine.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Live template control') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope-aware lanes') }}</span>
                    <span class="ops-shell-chip">{{ __('Draft-safe editing') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin only') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('templates.create') }}" class="ops-button ops-button--primary" wire:navigate>
                    {{ __('Create template') }}
                </a>
            </div>
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
                        Each checklist scope now owns its own live lab lane. Saving a template as active only retires the currently active template inside the same scope.
                    </p>
                </x-ops.callout>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">Scope governance</p>
                    <h3 class="ops-section-heading__title">Live checklist ownership by scope</h3>
                    <p class="ops-section-heading__body">Use this board to confirm which template currently owns each live checklist lane before you duplicate, revise, or replace anything.</p>
                </div>
            </div>

            <div class="ops-card__body">
                <div class="ops-governance-grid">
                    @foreach ($scopeGovernance as $lane)
                        <article class="ops-governance-card {{ $lane['state'] === 'missing' ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }}">
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
                                    @if ($lane['state'] === 'missing')
                                        {{ __('This operating lane has drafts only or no template at all, so staff cannot receive a live checklist here yet.') }}
                                    @else
                                        {{ __('This template currently owns the live :scope checklist lane.', ['scope' => strtolower($lane['scope'])]) }}
                                    @endif
                                </p>
                            </div>

                            <div class="ops-governance-card__stats">
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Templates') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['template_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Drafts') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['draft_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Live runs') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['live_run_count'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
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
                                        <td data-label="Scope" class="ops-text-muted px-4 py-4 text-sm">{{ $template->scope->value }}</td>
                                        <td data-label="Items" class="ops-text-muted px-4 py-4 text-sm">{{ $template->items_count }}</td>
                                        <td data-label="State" class="px-4 py-4 text-sm">
                                            <span class="ops-badge {{ $template->is_active ? 'ops-badge--success' : 'ops-badge--neutral' }}">
                                                {{ $template->is_active ? __('Live in scope') : __('Draft') }}
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
