<div class="settings-shell">
    <div class="settings-shell__nav ops-card">
        <div class="ops-card__body">
            <flux:navlist aria-label="{{ __('Settings') }}">
                <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
                <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
                <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            </flux:navlist>
        </div>
    </div>

    <flux:separator class="md:hidden" />

    <div class="settings-shell__content ops-card">
        <div class="ops-card__body">
            <flux:heading class="settings-section-title">{{ $heading ?? '' }}</flux:heading>
            <flux:subheading class="settings-section-subheading">{{ $subheading ?? '' }}</flux:subheading>

            <div class="settings-shell__body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
