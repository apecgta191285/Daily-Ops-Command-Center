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
                            <p class="auth-stage__kicker">{{ __('University computer lab ops') }}</p>
                            <a href="{{ route('home') }}" class="auth-stage__brand-link" wire:navigate>
                                <span class="app-brand-mark size-12">
                                    <x-app-logo-icon class="size-7 fill-current text-current" />
                                </span>
                                <div class="space-y-1">
                                    <p class="auth-stage__brand-name">{{ config('app.name', 'Daily Ops Command Center') }}</p>
                                    <p class="auth-stage__brand-copy">{{ __('Checklist execution, incident follow-up, and admin workflow control for one university lab team.') }}</p>
                                </div>
                            </a>
                        </div>

                        <div class="auth-stage__story">
                            <div>
                                <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Lab team sign-in') }}</span>
                                <h1 class="auth-stage__title">{{ __('Enter the workspace where lab checks, room issues, and team follow-up stay visible.') }}</h1>
                                <p class="auth-stage__lead">{{ __('This is not a generic admin panel. It is the shared workspace for one university computer lab team to run routine checks, report problems, and keep the day on track.') }}</p>
                            </div>

                            <div class="auth-stage__signal-grid">
                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('Staff flow') }}</p>
                                    <p class="auth-signal-card__title">{{ __('Run today clearly') }}</p>
                                    <p class="auth-signal-card__body">{{ __('Open the right lab lane, submit the checklist once, and hand off issues without breaking the daily rhythm.') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('Management') }}</p>
                                    <p class="auth-signal-card__title">{{ __('See pressure fast') }}</p>
                                    <p class="auth-signal-card__body">{{ __('Track unfinished lanes, high-severity incidents, and follow-up pressure from one workboard.') }}</p>
                                </article>
                            </div>

                            <div class="auth-stage__meta">
                                <div class="auth-stage__meta-block">
                                    <p class="auth-stage__meta-label">{{ __('Shared discipline') }}</p>
                                    <p class="auth-stage__meta-value">{{ __('One lab workflow') }}</p>
                                </div>
                                <div class="auth-stage__meta-block">
                                    <p class="auth-stage__meta-label">{{ __('Product stance') }}</p>
                                    <p class="auth-stage__meta-value">{{ __('University Computer Lab Daily Ops') }}</p>
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
                                <p class="auth-panel__body">{{ __('Sign in with your assigned account to continue into the lab operations workspace.') }}</p>
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
