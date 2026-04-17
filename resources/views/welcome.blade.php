<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <title>{{ config('app.name', 'Daily Ops Command Center') }}</title>
    </head>
    <body class="auth-shell antialiased">
        <main class="mx-auto flex min-h-svh max-w-5xl items-center justify-center px-6 py-10">
            <section class="w-full max-w-3xl rounded-[2rem] border border-[var(--app-border)] bg-[var(--app-surface-elevated)] p-8 text-[var(--app-text)] shadow-lg sm:p-10">
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-xl space-y-5">
                        <div class="flex items-center gap-4">
                            <span class="app-brand-mark size-14">
                                <x-app-logo-icon class="size-8 fill-current text-current" />
                            </span>
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">{{ __('Internal operations') }}</p>
                                <h1 class="text-2xl font-semibold tracking-tight text-[var(--app-heading)] sm:text-3xl">{{ config('app.name', 'Daily Ops Command Center') }}</h1>
                            </div>
                        </div>

                        <p class="text-base leading-7 text-[var(--app-text-muted)] sm:text-lg">
                            {{ __('Run daily checklists, capture incidents with evidence, and help supervisors see what still needs attention from one shared operations workspace.') }}
                        </p>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div class="rounded-2xl border border-[var(--app-border)] bg-white/80 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--app-text-muted)]">{{ __('Staff') }}</p>
                                <p class="mt-2 text-sm font-medium text-[var(--app-heading)]">{{ __('Complete daily work clearly') }}</p>
                                <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ __('Open today’s checklist, submit it once, and report issues immediately.') }}</p>
                            </div>

                            <div class="rounded-2xl border border-[var(--app-border)] bg-white/80 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--app-text-muted)]">{{ __('Supervisor') }}</p>
                                <p class="mt-2 text-sm font-medium text-[var(--app-heading)]">{{ __('See what needs follow-up') }}</p>
                                <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ __('Track unresolved incidents, review updates, and watch daily completion from one dashboard.') }}</p>
                            </div>

                            <div class="rounded-2xl border border-[var(--app-border)] bg-white/80 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--app-text-muted)]">{{ __('Admin') }}</p>
                                <p class="mt-2 text-sm font-medium text-[var(--app-heading)]">{{ __('Keep the workflow ready') }}</p>
                                <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ __('Maintain the active checklist template and keep the daily workflow aligned with real operations.') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ops-button ops-button--primary" wire:navigate>
                                    {{ __('Log in') }}
                                </a>
                            @endif

                            <span class="inline-flex items-center rounded-xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] px-4 py-2.5 text-sm text-[var(--app-text-muted)]">
                                {{ __('Role-based access for staff, supervisors, and admins') }}
                            </span>
                        </div>

                        <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--app-text-muted)]">{{ __('Suggested demo walkthrough') }}</p>
                            <ol class="mt-3 space-y-3 text-sm text-[var(--app-text-muted)]">
                                <li class="flex gap-3">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-white text-xs font-semibold text-[var(--app-heading)]">1</span>
                                    <span>{{ __('Log in as staff, open today’s checklist, and see how the system tracks completion before submission.') }}</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-white text-xs font-semibold text-[var(--app-heading)]">2</span>
                                    <span>{{ __('Create an incident from a checklist issue, then switch to the management dashboard to review stale or high-severity follow-up.') }}</span>
                                </li>
                                <li class="flex gap-3">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-white text-xs font-semibold text-[var(--app-heading)]">3</span>
                                    <span>{{ __('Use the admin template screen to show how one active checklist template keeps daily operations aligned.') }}</span>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div class="ops-card max-w-sm lg:w-[22rem]">
                        <div class="ops-card__header">
                            <p class="text-sm font-semibold text-[var(--app-heading)]">{{ __('What this system already covers') }}</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ __('This A-lite scope is intentionally small: one team, one daily workflow, and one clear place to follow incidents.') }}</p>
                        </div>
                        <div class="ops-card__body">
                            <ul class="space-y-3 text-sm text-[var(--app-text-muted)]">
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

                            <div class="mt-5 rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--app-text-muted)]">{{ __('Why it matters') }}</p>
                                <p class="mt-2 text-sm text-[var(--app-text-muted)]">
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
