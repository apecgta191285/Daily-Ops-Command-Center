<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('User administration') }}</p>
                <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
                <p class="ops-page-intro__body">
                    {{ __($this->pageDescription) }}
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ $user ? __('Lifecycle update') : __('Provisioning flow') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin-owned') }}</span>
                    <span class="ops-shell-chip">{{ __('Internal account only') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('users.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to roster') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @php
            $roleGovernance = $this->roleGovernance;
            $lifecycleSignals = $this->lifecycleSignals;
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

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Lifecycle control</p>
                    <h3 class="ops-hero__title">{{ __($this->pageTitle) }}</h3>
                    <p class="ops-hero__lead">
                        Keep user lifecycle explicit: one role lane, one active access switch, and one controlled password path without inventing invitation infrastructure or hidden admin-only database rituals.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $user ? __('Editing existing account') : __('Creating internal account') }}</span>
                        <span class="ops-shell-chip">{{ __('Role-aware routing') }}</span>
                        <span class="ops-shell-chip">{{ __('Active gate enforced') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Selected role</p>
                        <p class="ops-hero__aside-value">{{ \Illuminate\Support\Str::headline($role ?: 'staff') }}</p>
                        <p class="ops-hero__aside-copy">
                            {{ $is_active ? __('This account will be able to authenticate after save.') : __('This account will remain blocked from authentication after save.') }}
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('State') }}</p>
                            <p class="ops-authoring-metric__value">{{ $is_active ? __('Live') : __('Off') }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Mode') }}</p>
                            <p class="ops-authoring-metric__value">{{ $user ? __('Edit') : __('New') }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Accounts') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['total_count'] }}</p>
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
                                <p class="ops-section-heading__eyebrow">Lifecycle rhythm</p>
                                <h3 class="ops-section-heading__title">Provision or revise access in three passes</h3>
                                <p class="ops-section-heading__body">Set identity first, choose the operating lane second, then decide whether the account should be active right now.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-authoring-rhythm">
                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">1</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">{{ __('Frame the account') }}</p>
                                        <p class="ops-authoring-rhythm__body">{{ __('Use a real name and durable email so the roster stays believable and useful as a real operating tool.') }}</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">2</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">{{ __('Assign the role lane') }}</p>
                                        <p class="ops-authoring-rhythm__body">{{ __('Pick the smallest role that matches the person’s real responsibility in the product.') }}</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">3</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">{{ __('Confirm access state') }}</p>
                                        <p class="ops-authoring-rhythm__body">{{ __('Active accounts can sign in immediately. Inactive accounts remain intentionally blocked until re-enabled.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Identity</p>
                                <h3 class="ops-section-heading__title">Core account details</h3>
                                <p class="ops-section-heading__body">Keep the roster trustworthy by maintaining the same name and email people will actually use to access the product.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="name" class="ops-field-label">Name <span class="ops-required-mark">*</span></label>
                                <input id="name" type="text" wire:model="name" class="ops-control" placeholder="เช่น Somchai Ops Lead">
                                @error('name') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="ops-field-label">Email <span class="ops-required-mark">*</span></label>
                                <input id="email" type="email" wire:model="email" class="ops-control" placeholder="name@example.com">
                                @error('email') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Access policy</p>
                                <h3 class="ops-section-heading__title">Role and access state</h3>
                                <p class="ops-section-heading__body">Role determines what lane the user can operate in. Active state determines whether authentication is allowed at all.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="role" class="ops-field-label">Role <span class="ops-required-mark">*</span></label>
                                <select id="role" wire:model.live="role" class="ops-control" @disabled($this->blocksSelfAdminRoleChange)>
                                    @foreach ($roles as $roleOption)
                                        <option value="{{ $roleOption }}">{{ \Illuminate\Support\Str::headline($roleOption) }}</option>
                                    @endforeach
                                </select>
                                @if ($this->blocksSelfAdminRoleChange)
                                    <p class="ops-field-help">{{ __('Your own administrator role cannot be changed from this workflow.') }}</p>
                                @endif
                                @error('role') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <label class="ops-choice w-full justify-between">
                                <span>
                                    <span class="ops-text-heading text-sm font-medium">{{ __('Active account') }}</span>
                                    <span class="ops-text-muted mt-1 block text-sm">{{ __('When off, the user cannot authenticate even if the password is correct.') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="ops-choice__control" @disabled($this->blocksSelfAdminRoleChange)>
                            </label>
                            @if ($this->blocksSelfAdminRoleChange)
                                <p class="ops-field-help">{{ __('Your own administrator access cannot be deactivated from this workflow.') }}</p>
                            @endif

                            <div class="ops-surface-soft px-4 py-4">
                                <p class="ops-admin-item__meta-label">{{ __('Access meaning') }}</p>
                                <p class="ops-admin-item__meta-value">
                                    {{ $is_active
                                        ? __('Saving active access keeps this account inside the live authentication pool.')
                                        : __('Saving inactive access intentionally removes this account from the sign-in path until an admin reactivates it.') }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="160">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Password control</p>
                                <h3 class="ops-section-heading__title">{{ $user ? __('Optional password reset') : __('Initial password') }}</h3>
                                <p class="ops-section-heading__body">
                                    {{ __($this->passwordHandoffNote) }}
                                </p>
                            </div>
                        </div>

                        <div class="ops-card__body grid gap-6 lg:grid-cols-2">
                            <div>
                                <label for="password" class="ops-field-label">
                                    {{ $user ? __('New password') : __('Password') }}
                                    @unless($user) <span class="ops-required-mark">*</span> @endunless
                                </label>
                                <input id="password" type="password" wire:model="password" class="ops-control">
                                @error('password') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="ops-field-label">
                                    {{ $user ? __('Confirm new password') : __('Confirm password') }}
                                    @unless($user) <span class="ops-required-mark">*</span> @endunless
                                </label>
                                <input id="password_confirmation" type="password" wire:model="password_confirmation" class="ops-control">
                            </div>
                        </div>
                    </section>
                </div>

                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Lifecycle pulse</p>
                                <h3 class="ops-section-heading__title">Checkpoint summary</h3>
                                <p class="ops-section-heading__body">Use these signals to confirm whether the account state you are about to save matches the operational intent.</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @foreach ($lifecycleSignals as $signal)
                                <x-ops.callout :title="$signal['title']" :tone="$signal['tone']">
                                    {{ __($signal['body']) }}
                                </x-ops.callout>
                            @endforeach
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Role governance</p>
                                <h3 class="ops-section-heading__title">Where this account will sit</h3>
                                <p class="ops-section-heading__body">Keep the roster balanced by seeing the selected role against the rest of the current operating lanes.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-governance-grid ops-governance-grid--compact">
                                @foreach ($roleGovernance as $lane)
                                    <article class="ops-governance-card {{ $lane['is_selected_role'] ? 'ops-governance-card--selected' : ($lane['state'] === 'warning' ? 'ops-governance-card--warning' : 'ops-governance-card--covered') }}">
                                        <div class="ops-governance-card__header">
                                            <div>
                                                <p class="ops-admin-item__eyebrow">{{ __('Role lane') }}</p>
                                                <h4 class="ops-admin-item__title">{{ $lane['title'] }}</h4>
                                            </div>
                                            <span class="ops-chip {{ $lane['is_selected_role'] ? 'ops-chip--info' : ($lane['state'] === 'warning' ? 'ops-chip--warning' : 'ops-chip--success') }}">
                                                {{ $lane['is_selected_role'] ? __('Selected role') : ($lane['state'] === 'warning' ? __('No active accounts') : __('Live coverage')) }}
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

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="170">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">Save intent</p>
                                <h3 class="ops-section-heading__title">What happens after save</h3>
                                <p class="ops-section-heading__body">This workflow is intentionally small and internal. It updates real lifecycle state immediately inside the product.</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <x-ops.callout title="No invitation pipeline here" tone="neutral">
                                {{ __('WF3 does not introduce invitation emails, approval workflow, or external identity sync. Saving here applies the account change directly against the app-owned lifecycle contract.') }}
                            </x-ops.callout>

                            <button type="submit" class="ops-button ops-button--primary mt-4 w-full">
                                {{ $user ? __('Save account changes') : __('Create user account') }}
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </div>
</div>
