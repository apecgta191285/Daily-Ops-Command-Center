<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <title>{{ config('app.name', 'Daily Ops Command Center') }}</title>
    </head>
    <body class="auth-shell antialiased" data-no-progress-bar>
        <a href="#main-content" class="ops-skip-link">{{ __('Skip to main content') }}</a>

        <main id="main-content" class="auth-stage auth-stage--welcome">
            <section class="auth-stage__frame auth-stage__frame--welcome">
                <div class="auth-stage__scene auth-stage__scene--welcome" data-motion="fade-right">
                    <div class="auth-stage__scene-inner">
                        <div class="auth-stage__brand-lockup">
                            <p class="auth-stage__kicker">{{ __('University computer lab ops') }}</p>
                            <div class="auth-stage__brand-link">
                                <span class="app-brand-mark size-14">
                                    <x-app-logo-icon class="size-8 fill-current text-current" />
                                </span>
                                <div class="space-y-1">
                                    <p class="auth-stage__brand-name">{{ config('app.name', 'Daily Ops Command Center') }}</p>
                                    <p class="auth-stage__brand-copy">{{ __('One shared system for lab opening checks, issue reporting, and supervisor follow-up.') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="welcome-stage__hero">
                            <div class="space-y-4">
                                <span class="ops-shell-chip ops-shell-chip--accent">{{ __('University Computer Lab Daily Ops') }}</span>
                                <h1 class="welcome-stage__title">{{ __('Run lab opening checks, catch room issues early, and keep the team aligned from one shared workspace.') }}</h1>
                                <p class="welcome-stage__lead">{{ __('Designed for university computer lab teams that need one trustworthy place for routine checks, issue handoff, and supervisor visibility without drifting into paper notes, scattered chat, or spreadsheet theatre.') }}</p>
                            </div>

                            <div class="welcome-stage__grid">
                                <article class="auth-signal-card auth-signal-card--wide">
                                    <p class="auth-signal-card__eyebrow">{{ __('Staff lane') }}</p>
                                    <p class="auth-signal-card__title">{{ __('Daily work stays explicit') }}</p>
                                    <p class="auth-signal-card__body">{{ __('Checklist progress, workstation readiness, and issue handoff stay visible in one path instead of scattered notes or chat fragments.') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('Supervisor lane') }}</p>
                                    <p class="auth-signal-card__title">{{ __('Triage without noise') }}</p>
                                    <p class="auth-signal-card__body">{{ __('See overdue issues, follow-up direction, and unfinished lanes before the day slips.') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('Admin lane') }}</p>
                                    <p class="auth-signal-card__title">{{ __('Govern live lab checks') }}</p>
                                    <p class="auth-signal-card__body">{{ __('Keep the opening, during-day, and closing checklist lanes accurate without fracturing the live staff workflow.') }}</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="welcome-stage__aside" data-motion="fade-left">
                    <div class="welcome-stage__aside-card">
                        <div class="welcome-stage__aside-header">
                            <p class="welcome-stage__aside-eyebrow">{{ __('Suggested demo walkthrough') }}</p>
                            <h2 class="welcome-stage__aside-title">{{ __('See the system in one short sequence') }}</h2>
                            <p class="welcome-stage__aside-copy">{{ __('This product is intentionally compact: one lab team, one daily workflow, and one operational thread from checklist to issue follow-up.') }}</p>
                        </div>

                        <ol class="welcome-stage__steps">
                            <li class="welcome-stage__step">
                                <span class="ops-step-index">1</span>
                                <div>
                                    <p class="welcome-stage__step-title">{{ __('Run the checklist as staff') }}</p>
                                    <p class="welcome-stage__step-copy">{{ __('Open the correct lab lane, complete today’s checklist, and submit with clear progress feedback.') }}</p>
                                </div>
                            </li>
                            <li class="welcome-stage__step">
                                <span class="ops-step-index">2</span>
                                <div>
                                    <p class="welcome-stage__step-title">{{ __('Escalate an incident') }}</p>
                                    <p class="welcome-stage__step-copy">{{ __('Create an incident for a workstation, network, printer, or room problem, then review it from the supervisor side.') }}</p>
                                </div>
                            </li>
                            <li class="welcome-stage__step">
                                <span class="ops-step-index">3</span>
                                <div>
                                    <p class="welcome-stage__step-title">{{ __('Check the live checklist setup') }}</p>
                                    <p class="welcome-stage__step-copy">{{ __('Move into template administration to see how each scope owns its own live lab checklist lane.') }}</p>
                                </div>
                            </li>
                        </ol>

                        <div class="welcome-stage__aside-footer">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ops-button ops-button--primary w-full" wire:navigate>
                                    {{ __('Log in') }}
                                </a>
                            @endif

                            <p class="ops-inline-note">{{ __('Internal access for duty staff, lab supervisors, and admins') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
