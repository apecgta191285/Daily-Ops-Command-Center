<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="settings-danger-zone">
    <div class="relative mb-5">
        <flux:heading class="settings-danger-zone__title settings-inline-heading">{{ __('Delete account') }}</flux:heading>
        <flux:subheading class="settings-danger-zone__subheading">{{ __('Delete your account and all of its resources') }}</flux:subheading>
    </div>

    <div class="settings-inline-stack">
        <div class="settings-fact-card">
            <p class="settings-fact-card__label">{{ __('Danger zone') }}</p>
            <p class="settings-fact-card__value">{{ __('This action permanently removes your account, authentication state, and the personal ownership link attached to your activity history.') }}</p>
        </div>

        <flux:modal.trigger name="confirm-user-deletion">
            <flux:button variant="danger" data-test="delete-user-button">
                {{ __('Delete account') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <livewire:pages::settings.delete-user-modal />
</section>
