<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-shell antialiased">
        <a href="#main-content" class="ops-skip-link">{{ __('Skip to main content') }}</a>

        <main id="main-content" class="flex min-h-svh items-center justify-center">
            <div class="auth-panel" tabindex="-1">
                <div class="auth-panel__brand">
                    <p class="auth-panel__kicker">{{ __('Daily Ops Command Center') }}</p>
                    <a href="{{ route('home') }}" class="flex flex-col items-center gap-3" wire:navigate>
                        <span class="app-brand-mark size-11">
                            <x-app-logo-icon class="size-6 fill-current text-current" />
                        </span>
                        <div class="space-y-1">
                            <p class="text-base font-semibold text-[var(--app-heading)]">{{ config('app.name', 'Daily Ops Command Center') }}</p>
                            <p class="text-sm text-[var(--app-text-muted)]">{{ __('Checklist, incident, and supervisor workflows in one operations console.') }}</p>
                        </div>
                    </a>
                </div>

                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </main>
        @fluxScripts
    </body>
</html>
