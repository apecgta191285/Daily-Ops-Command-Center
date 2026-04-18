<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-shell antialiased">
        <a href="#main-content" class="ops-skip-link">{{ __('Skip to main content') }}</a>

        <main id="main-content" class="auth-stage">
            <section class="auth-stage__frame">
                <div class="auth-stage__scene" data-motion="fade-right">
                    <div class="auth-stage__scene-inner">
                        <div class="auth-stage__brand-lockup">
                            <p class="auth-stage__kicker">{{ __('Operations command') }}</p>
                            <a href="{{ route('home') }}" class="auth-stage__brand-link" wire:navigate>
                                <span class="app-brand-mark size-12">
                                    <x-app-logo-icon class="size-7 fill-current text-current" />
                                </span>
                                <div class="space-y-1">
                                    <p class="auth-stage__brand-name">{{ config('app.name', 'Daily Ops Command Center') }}</p>
                                    <p class="auth-stage__brand-copy">{{ __('Checklist execution, incident follow-up, and admin workflow control in one operational system.') }}</p>
                                </div>
                            </a>
                        </div>

                        <div class="auth-stage__story">
                            <div>
                                <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Command entry') }}</span>
                                <h1 class="auth-stage__title">{{ __('Enter the workspace where daily operations stay visible, traceable, and in control.') }}</h1>
                                <p class="auth-stage__lead">{{ __('This is not a generic admin panel. It is the shared control room for recurring work, incident response, and checklist governance across one small operations team.') }}</p>
                            </div>

                            <div class="auth-stage__signal-grid">
                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('Staff flow') }}</p>
                                    <p class="auth-signal-card__title">{{ __('Run today clearly') }}</p>
                                    <p class="auth-signal-card__body">{{ __('Open today’s checklist, submit once, and hand off issues without breaking the daily rhythm.') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('Management') }}</p>
                                    <p class="auth-signal-card__title">{{ __('See pressure fast') }}</p>
                                    <p class="auth-signal-card__body">{{ __('Track stale work, high-severity incidents, and checklist completion from one command view.') }}</p>
                                </article>
                            </div>

                            <div class="auth-stage__meta">
                                <div class="auth-stage__meta-block">
                                    <p class="auth-stage__meta-label">{{ __('Shared discipline') }}</p>
                                    <p class="auth-stage__meta-value">{{ __('One live workflow') }}</p>
                                </div>
                                <div class="auth-stage__meta-block">
                                    <p class="auth-stage__meta-label">{{ __('Product stance') }}</p>
                                    <p class="auth-stage__meta-value">{{ __('Precision Ops Control Room') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="auth-stage__panel-wrap" data-motion="fade-left">
                    <div class="auth-panel" tabindex="-1">
                        <div class="auth-panel__brand">
                            <p class="auth-panel__kicker">{{ __('Secure sign-in') }}</p>
                            <div class="auth-panel__copy">
                                <p class="auth-panel__title">{{ __('Access your operations workspace') }}</p>
                                <p class="auth-panel__body">{{ __('Sign in with your assigned account to continue into the command workspace.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </section>
        </main>
        @fluxScripts
    </body>
</html>
