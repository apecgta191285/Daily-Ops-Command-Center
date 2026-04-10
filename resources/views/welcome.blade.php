<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
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
                            <span class="flex aspect-square size-14 items-center justify-center rounded-2xl border border-blue-100 bg-blue-50 text-blue-700 shadow-sm">
                                <x-app-logo-icon class="size-8 fill-current text-blue-700" />
                            </span>
                            <div class="space-y-1">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--app-text-muted)]">{{ __('Internal operations') }}</p>
                                <h1 class="text-2xl font-semibold tracking-tight text-[var(--app-heading)] sm:text-3xl">{{ config('app.name', 'Daily Ops Command Center') }}</h1>
                            </div>
                        </div>

                        <p class="text-base leading-7 text-[var(--app-text-muted)] sm:text-lg">
                            {{ __('Run daily checklists, report incidents, and manage supervisor workflows from one controlled operations console.') }}
                        </p>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ops-button ops-button--primary" wire:navigate>
                                    {{ __('Log in') }}
                                </a>
                            @endif

                            <span class="inline-flex items-center rounded-xl border border-[var(--app-border)] bg-[#f8fafc] px-4 py-2.5 text-sm text-[var(--app-text-muted)]">
                                {{ __('Role-based access for staff, supervisors, and admins') }}
                            </span>
                        </div>
                    </div>

                    <div class="ops-card max-w-sm lg:w-[22rem]">
                        <div class="ops-card__header">
                            <p class="text-sm font-semibold text-[var(--app-heading)]">{{ __('Daily workflow coverage') }}</p>
                            <p class="mt-1 text-sm text-[var(--app-text-muted)]">{{ __('The current MVP already supports the core internal demo path.') }}</p>
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
                                    <span>{{ __('Dashboard access plus checklist template administration') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
