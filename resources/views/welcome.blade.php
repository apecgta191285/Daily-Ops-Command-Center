<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <title>{{ config('app.name', 'Daily Ops Command Center') }}</title>
    </head>
    <body class="auth-shell antialiased">
        <a href="#main-content" class="ops-skip-link">{{ __('Skip to main content') }}</a>

        <main id="main-content" class="mx-auto flex min-h-svh max-w-5xl items-center justify-center px-6 py-10">
            <section class="ops-surface-panel w-full max-w-3xl rounded-[2rem] p-8 shadow-lg sm:p-10">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-xl space-y-5">
                        <div class="flex items-center gap-4">
                            <span class="app-brand-mark size-14">
                                <x-app-logo-icon class="size-8 fill-current text-current" />
                            </span>
                            <div class="space-y-1">
                                <p class="ops-text-muted text-xs font-semibold uppercase tracking-[0.18em]">{{ __('Internal operations') }}</p>
                                <h1 class="ops-text-heading text-2xl font-semibold tracking-tight sm:text-3xl">{{ config('app.name', 'Daily Ops Command Center') }}</h1>
                            </div>
                        </div>

                        <p class="ops-text-muted text-base leading-7 sm:text-lg">
                            {{ __('Run daily checklists, capture incidents with evidence, and help supervisors see what still needs attention from one shared operations workspace.') }}
                        </p>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="ops-surface-soft p-4">
                                <p class="ops-text-muted text-xs font-semibold uppercase tracking-[0.12em]">{{ __('Staff') }}</p>
                                <p class="ops-text-heading mt-2 text-sm font-medium">{{ __('Complete daily work clearly') }}</p>
                                <p class="ops-text-muted mt-1 text-sm">{{ __('Open today’s checklist, submit it once, and report issues immediately.') }}</p>
                            </div>

                            <div class="ops-surface-soft p-4">
                                <p class="ops-text-muted text-xs font-semibold uppercase tracking-[0.12em]">{{ __('Supervisor') }}</p>
                                <p class="ops-text-heading mt-2 text-sm font-medium">{{ __('See what needs follow-up') }}</p>
                                <p class="ops-text-muted mt-1 text-sm">{{ __('Track unresolved incidents, review updates, and watch daily completion from one dashboard.') }}</p>
                            </div>

                            <div class="ops-surface-soft p-4">
                                <p class="ops-text-muted text-xs font-semibold uppercase tracking-[0.12em]">{{ __('Admin') }}</p>
                                <p class="ops-text-heading mt-2 text-sm font-medium">{{ __('Keep the workflow ready') }}</p>
                                <p class="ops-text-muted mt-1 text-sm">{{ __('Maintain the active checklist template and keep the daily workflow aligned with real operations.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ops-button ops-button--primary" wire:navigate>
                                    {{ __('Log in') }}
                                </a>
                            @endif

                            <span class="ops-inline-note">
                                {{ __('Role-based access for staff, supervisors, and admins') }}
                            </span>
                        </div>

                        <div class="ops-surface-panel ops-surface-panel--subtle p-5">
                            <p class="ops-text-muted text-xs font-semibold uppercase tracking-[0.12em]">{{ __('Suggested demo walkthrough') }}</p>
                            <ol class="ops-text-muted mt-3 space-y-3 text-sm">
                                <li class="flex gap-3">
                                    <span class="ops-step-index">1</span>
                                    <span>{{ __('Log in as staff, open today’s checklist, and see how the system tracks completion before submission.') }}</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="ops-step-index">2</span>
                                    <span>{{ __('Create an incident from a checklist issue, then switch to the management dashboard to review stale or high-severity follow-up.') }}</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="ops-step-index">3</span>
                                    <span>{{ __('Use the admin template screen to show how one active checklist template keeps daily operations aligned.') }}</span>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="ops-card max-w-sm lg:w-[22rem]">
                        <div class="ops-card__header">
                            <p class="ops-text-heading text-sm font-semibold">{{ __('What this system already covers') }}</p>
                            <p class="ops-text-muted mt-1 text-sm">{{ __('This A-lite scope is intentionally small: one team, one daily workflow, and one clear place to follow incidents.') }}</p>
                        </div>
                        <div class="ops-card__body">
                            <ul class="ops-text-muted space-y-3 text-sm">
                                <li class="flex items-start gap-3">
                                    <span class="ops-badge ops-badge--info">{{ __('Staff') }}</span>
                                    <span>{{ __('Checklist today and incident reporting') }}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="ops-badge ops-badge--warning">{{ __('Supervisor') }}</span>
                                    <span>{{ __('Incident review, status updates, and dashboard oversight') }}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="ops-badge ops-badge--success">{{ __('Admin') }}</span>
                                    <span>{{ __('Dashboard access plus checklist template setup in the same operations workspace') }}</span>
                                </li>
                            </ul>

                            <div class="ops-surface-panel ops-surface-panel--subtle mt-5 p-4">
                                <p class="ops-text-muted text-xs font-semibold uppercase tracking-[0.12em]">{{ __('Why it matters') }}</p>
                                <p class="ops-text-muted mt-2 text-sm">
                                    {{ __('Instead of scattered paper notes or chat messages, the team gets one traceable path for routine work and incident follow-up.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
