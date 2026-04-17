<div class="settings-screen" data-motion="fade-up">
    <div class="settings-shell">
        <div class="settings-shell__nav ops-card" data-motion="fade-right" data-motion-delay="40">
            <div class="ops-card__body">
                <div class="settings-nav-card">
                    <div class="settings-nav-card__intro">
                        <p class="settings-nav-card__eyebrow">{{ __('Navigation rail') }}</p>
                        <h3 class="settings-nav-card__title">{{ __('Account workspace') }}</h3>
                        <p class="settings-nav-card__body">{{ __('Use this rail to switch between your personal identity settings and security controls without leaving the main application shell.') }}</p>
                    </div>

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
        </div>

        <div class="settings-shell__content ops-card" data-motion="fade-left" data-motion-delay="90">
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
</div>
