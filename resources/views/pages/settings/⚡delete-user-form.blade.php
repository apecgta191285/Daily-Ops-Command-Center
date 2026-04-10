<?php

use Livewire\Component;

new class extends Component {}; ?>

<section class="settings-danger-zone">
    <div class="relative mb-5">
        <flux:heading class="settings-danger-zone__title">{{ __('Delete account') }}</flux:heading>
        <flux:subheading class="settings-danger-zone__subheading">{{ __('Delete your account and all of its resources') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" data-test="delete-user-button">
            {{ __('Delete account') }}
        </flux:button>
    </flux:modal.trigger>

    <livewire:pages::settings.delete-user-modal />
</section>
