<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('User administration') }}</p>
                <h2 class="ops-page__title">{{ __('Team Access Roster') }}</h2>
                <p class="ops-page-intro__body">
                    Govern internal accounts, keep role ownership visible, and make active versus inactive access read as an intentional operating decision.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Internal roster') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin only') }}</span>
                    <span class="ops-shell-chip">{{ __('Lifecycle control') }}</span>
                    <span class="ops-shell-chip">{{ __('No public sign-up') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('users.create') }}" class="ops-button ops-button--primary" wire:navigate>
                    {{ __('Create user') }}
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

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Administration</p>
                    <h3 class="ops-hero__title">Operate access like part of the product, not a hidden database task.</h3>
                    <p class="ops-hero__lead">
                        WF3 exposes account lifecycle inside the real app shell so admins can provision staff, adjust roles, and disable access intentionally without inventing enterprise IAM.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('WF3-B live') }}</span>
                        <span class="ops-shell-chip">{{ __('Admin-governed lifecycle') }}</span>
                        <span class="ops-shell-chip">{{ __('Explicit initial password') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Roster size</p>
                        <p class="ops-hero__aside-value">{{ $rosterSummary['total_count'] }}</p>
                        <p class="ops-hero__aside-copy">
                            Internal account(s) currently governed from inside the product shell.
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Active') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['active_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Inactive') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['inactive_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Management') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['management_count'] }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">Role governance</p>
                    <h3 class="ops-section-heading__title">Coverage by role lane</h3>
                    <p class="ops-section-heading__body">Use this board to understand how administrative, supervisory, and staff access is currently distributed before you add or revise accounts.</p>
                </div>
            </div>

            <div class="ops-card__body">
                <div class="ops-governance-grid">
                    @foreach ($rosterSummary['role_lanes'] as $lane)
                        <article class="ops-governance-card {{ $lane['state'] === 'warning' ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }}">
                            <div class="ops-governance-card__header">
                                <div>
                                    <p class="ops-admin-item__eyebrow">{{ __('Role lane') }}</p>
                                    <h4 class="ops-admin-item__title">{{ $lane['title'] }}</h4>
                                </div>

                                <span class="ops-chip {{ $lane['state'] === 'warning' ? 'ops-chip--warning' : 'ops-chip--success' }}">
                                    {{ $lane['state'] === 'warning' ? __('No active accounts') : __('Active coverage') }}
                                </span>
                            </div>

                            <div class="ops-governance-card__body">
                                <p class="ops-governance-card__meta">{{ __($lane['description']) }}</p>
                            </div>

                            <div class="ops-governance-card__stats">
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Total') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['total_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Active') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['active_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Inactive') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['inactive_count'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-3">
                <x-ops.callout title="Lifecycle meaning" tone="neutral">
                    <p>
                        Active accounts can sign in and continue through protected routes. Inactive accounts are intentionally blocked at authentication time and should read as disabled access, not a broken login.
                    </p>
                    <p class="mt-3">
                        WF3 keeps lifecycle small on purpose: one internal roster, one create/edit path, and one clear active access switch without invitations, approvals, or enterprise IAM drift.
                    </p>
                </x-ops.callout>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <div class="ops-table-wrap">
                    <table class="ops-table ops-table--responsive min-w-full">
                        <thead>
                            <tr>
                                <th>{{ __('Account') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('State') }}</th>
                                <th>{{ __('Owned incidents') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $managedUser)
                                <tr class="ops-table__row">
                                    <td data-label="Account" class="px-4 py-4 text-sm">
                                        <div class="ops-admin-item__identity">
                                            <span class="ops-user-avatar" aria-hidden="true">{{ $managedUser->initials() }}</span>
                                            <div>
                                                <p class="ops-text-heading text-sm font-semibold">
                                                    {{ $managedUser->name }}
                                                    @if (auth()->id() === $managedUser->id)
                                                        <span class="ops-chip ml-2">{{ __('You') }}</span>
                                                    @endif
                                                </p>
                                                <p class="ops-text-muted text-xs">{{ $managedUser->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Role" class="px-4 py-4 text-sm">
                                        <span class="ops-badge {{ $managedUser->role === 'admin' ? 'ops-badge--info' : ($managedUser->role === 'supervisor' ? 'ops-badge--warning' : 'ops-badge--neutral') }}">
                                            {{ \Illuminate\Support\Str::headline($managedUser->role) }}
                                        </span>
                                    </td>
                                    <td data-label="State" class="px-4 py-4 text-sm">
                                        <span class="ops-badge {{ $managedUser->is_active ? 'ops-badge--success' : 'ops-badge--danger' }}">
                                            {{ $managedUser->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="Owned incidents" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $managedUser->owned_incidents_count }}
                                    </td>
                                    <td data-label="Action" class="px-4 py-4 text-right text-sm">
                                        <a href="{{ route('users.edit', $managedUser) }}" class="ops-button ops-button--secondary" wire:navigate>
                                            {{ __('Edit account') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
