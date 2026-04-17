<?php

use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Security settings')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public bool $canManageTwoFactor;

    public bool $twoFactorEnabled;

    public bool $requiresConfirmation;

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $this->canManageTwoFactor = Features::canManageTwoFactorAuthentication();

        if ($this->canManageTwoFactor) {
            if (Fortify::confirmsTwoFactorAuthentication() && is_null(auth()->user()->two_factor_confirmed_at)) {
                $disableTwoFactorAuthentication(auth()->user());
            }

            $this->twoFactorEnabled = auth()->user()->hasEnabledTwoFactorAuthentication();
            $this->requiresConfirmation = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
        }
    }

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }

    /**
     * Handle the two-factor authentication enabled event.
     */
    #[On('two-factor-enabled')]
    public function onTwoFactorEnabled(): void
    {
        $this->twoFactorEnabled = true;
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void
    {
        $disableTwoFactorAuthentication(auth()->user());

        $this->twoFactorEnabled = false;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Security settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <div class="settings-stack">
            <section class="settings-surface" data-motion="fade-up" data-motion-delay="40">
                <div class="settings-surface__header">
                    <div>
                        <p class="settings-surface__eyebrow">{{ __('Credentials') }}</p>
                        <h3 class="settings-surface__title">{{ __('Password hygiene') }}</h3>
                        <p class="settings-surface__body">{{ __('Keep this account protected with a strong password so incident and checklist actions can be trusted as real operational history.') }}</p>
                    </div>

                    <div class="settings-status-strip">
                        <span class="settings-status-chip settings-status-chip--info">{{ __('Primary sign-in') }}</span>
                    </div>
                </div>

                <form method="POST" wire:submit="updatePassword" class="settings-form settings-form--stacked">
                    <flux:input
                        wire:model="current_password"
                        :label="__('Current password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        viewable
                    />
                    <flux:input
                        wire:model="password"
                        :label="__('New password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        viewable
                    />
                    <flux:input
                        wire:model="password_confirmation"
                        :label="__('Confirm password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        viewable
                    />

                    <div class="settings-action-row">
                        <div class="flex items-center justify-end">
                            <flux:button variant="primary" type="submit" class="w-full" data-test="update-password-button">
                                {{ __('Save') }}
                            </flux:button>
                        </div>

                        <x-action-message class="settings-action-message" on="password-updated">
                            {{ __('Saved.') }}
                        </x-action-message>
                    </div>
                </form>
            </section>

            @if ($canManageTwoFactor)
                <section class="settings-surface" data-motion="fade-up" data-motion-delay="90">
                    <div class="settings-surface__header">
                        <div>
                            <p class="settings-surface__eyebrow">{{ __('Verification layer') }}</p>
                            <h3 class="settings-surface__title">{{ __('Two-factor authentication') }}</h3>
                            <p class="settings-surface__body">{{ __('Add a second verification step so privileged actions remain tied to the real account owner, even when a password is exposed.') }}</p>
                        </div>

                        <div class="settings-status-strip">
                            <span class="settings-status-chip {{ $twoFactorEnabled ? 'settings-status-chip--success' : 'settings-status-chip--warning' }}">
                                {{ $twoFactorEnabled ? __('2FA enabled') : __('2FA disabled') }}
                            </span>
                            @if ($twoFactorEnabled && $requiresConfirmation)
                                <span class="settings-status-chip">{{ __('Confirmation required at setup') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="settings-inline-stack" wire:cloak>
                        <div class="settings-grid">
                            <div class="settings-fact-card">
                                <p class="settings-fact-card__label">{{ __('Current state') }}</p>
                                <p class="settings-fact-card__value">{{ $twoFactorEnabled ? __('This account requires an authenticator code during sign-in.') : __('This account still relies on password-only access.') }}</p>
                            </div>

                            <div class="settings-fact-card">
                                <p class="settings-fact-card__label">{{ __('Operational note') }}</p>
                                <p class="settings-fact-card__value">{{ __('Use 2FA on shared or high-privilege devices to reduce the chance of silent account misuse.') }}</p>
                            </div>
                        </div>

                        @if ($twoFactorEnabled)
                            <div class="settings-inline-stack">
                                <flux:text class="settings-supporting-copy">
                                    {{ __('You will be prompted for a secure, random pin during login, which you can retrieve from the TOTP-supported application on your phone.') }}
                                </flux:text>

                                <div class="flex justify-start">
                                    <flux:button
                                        variant="danger"
                                        wire:click="disable"
                                    >
                                        {{ __('Disable 2FA') }}
                                    </flux:button>
                                </div>

                                <livewire:pages::settings.two-factor.recovery-codes :$requiresConfirmation />
                            </div>
                        @else
                            <div class="settings-inline-stack">
                                <flux:text class="settings-supporting-copy">
                                    {{ __('When you enable two-factor authentication, you will be prompted for a secure pin during login. This pin can be retrieved from a TOTP-supported application on your phone.') }}
                                </flux:text>

                                <flux:modal.trigger name="two-factor-setup-modal">
                                    <flux:button
                                        variant="primary"
                                        wire:click="$dispatch('start-two-factor-setup')"
                                    >
                                        {{ __('Enable 2FA') }}
                                    </flux:button>
                                </flux:modal.trigger>

                                <livewire:pages::settings.two-factor-setup-modal :requires-confirmation="$requiresConfirmation" />
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </x-pages::settings.layout>
</section>
