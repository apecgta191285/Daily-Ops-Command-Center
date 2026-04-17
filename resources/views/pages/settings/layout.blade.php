<div class="settings-shell">
    <div class="settings-shell__nav ops-card">
        <div class="ops-card__body">
            <nav class="settings-nav" aria-label="{{ __('Settings') }}">
                <a
                    href="{{ route('profile.edit') }}"
                    class="settings-nav__item {{ request()->routeIs('profile.edit') ? 'settings-nav__item--current' : '' }}"
                    wire:navigate
                >
                    {{ __('Profile') }}
                </a>
                <a
                    href="{{ route('security.edit') }}"
                    class="settings-nav__item {{ request()->routeIs('security.edit') ? 'settings-nav__item--current' : '' }}"
                    wire:navigate
                >
                    {{ __('Security') }}
                </a>
            </nav>
        </div>
    </div>

    <div class="settings-shell__content ops-card">
        <div class="ops-card__body">
            <div class="settings-section-heading">
                <h2 class="settings-section-title">{{ $heading ?? '' }}</h2>
                <p class="settings-section-subheading">{{ $subheading ?? '' }}</p>
            </div>

            <div class="settings-shell__body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
