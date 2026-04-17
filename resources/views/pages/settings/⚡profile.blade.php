<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <div class="settings-stack">
            <section class="settings-surface" data-motion="fade-up" data-motion-delay="40">
                <div class="settings-surface__header">
                    <div>
                        <p class="settings-surface__eyebrow">{{ __('Identity') }}</p>
                        <h3 class="settings-surface__title">{{ __('Profile details') }}</h3>
                        <p class="settings-surface__body">{{ __('Keep your displayed name and sign-in email current so the product can present the right ownership and contact context across incidents and checklist history.') }}</p>
                    </div>

                    <div class="settings-status-strip">
                        <span class="settings-status-chip settings-status-chip--info">{{ __('Account owner') }}</span>
                        <span class="settings-status-chip {{ $this->hasUnverifiedEmail ? 'settings-status-chip--warning' : 'settings-status-chip--success' }}">
                            {{ $this->hasUnverifiedEmail ? __('Email unverified') : __('Email verified') }}
                        </span>
                    </div>
                </div>

                <form wire:submit="updateProfileInformation" class="settings-form settings-form--stacked">
                    <div class="settings-grid settings-grid--single">
                        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />
                        <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                    </div>

                    @if ($this->hasUnverifiedEmail)
                        <div class="settings-note-block">
                            <flux:text class="settings-note">
                                {{ __('Your email address is unverified.') }}

                                <flux:link class="settings-link cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                    {{ __('Click here to re-send the verification email.') }}
                                </flux:link>
                            </flux:text>

                            @if (session('status') === 'verification-link-sent')
                                <flux:text class="settings-success-note">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </flux:text>
                            @endif
                        </div>
                    @endif

                    <div class="settings-action-row">
                        <div class="flex items-center justify-end">
                            <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                                {{ __('Save') }}
                            </flux:button>
                        </div>

                        <x-action-message class="settings-action-message" on="profile-updated">
                            {{ __('Saved.') }}
                        </x-action-message>
                    </div>
                </form>
            </section>

            <section class="settings-surface" data-motion="fade-up" data-motion-delay="90">
                <div class="settings-surface__header">
                    <div>
                        <p class="settings-surface__eyebrow">{{ __('Profile context') }}</p>
                        <h3 class="settings-surface__title">{{ __('What this account controls') }}</h3>
                        <p class="settings-surface__body">{{ __('These details affect how your name appears in operational history and who the rest of the team sees as the actor behind checklist and incident activity.') }}</p>
                    </div>
                </div>

                <div class="settings-grid">
                    <div class="settings-fact-card">
                        <p class="settings-fact-card__label">{{ __('Display name') }}</p>
                        <p class="settings-fact-card__value">{{ $name ?: __('Not set') }}</p>
                    </div>

                    <div class="settings-fact-card">
                        <p class="settings-fact-card__label">{{ __('Verification state') }}</p>
                        <p class="settings-fact-card__value">{{ $this->hasUnverifiedEmail ? __('Pending verification') : __('Verified and active') }}</p>
                    </div>
                </div>
            </section>
        </div>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
