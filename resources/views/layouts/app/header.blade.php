<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[var(--app-shell-bg)] text-[var(--app-shell-text)]">
        <flux:header container class="border-b border-[var(--app-shell-border)] bg-[var(--app-shell-bg)] text-[var(--app-shell-text)]">
            <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route(auth()->user()->landingRouteName()) }}" wire:navigate />

            <flux:navbar class="-mb-px max-lg:hidden">
                @if (auth()->user()->isManagement())
                    <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:navbar.item>

                    <flux:navbar.item icon="clipboard-document-list" :href="route('incidents.index')" :current="request()->routeIs('incidents.index') || request()->routeIs('incidents.show')" wire:navigate>
                        {{ __('Incidents') }}
                    </flux:navbar.item>

                    @if (auth()->user()->isAdmin())
                        <flux:navbar.item icon="rectangle-stack" :href="route('templates.index')" :current="request()->routeIs('templates.index')" wire:navigate>
                            {{ __('Templates') }}
                        </flux:navbar.item>
                    @endif
                @endif

                @if (auth()->user()->isStaff())
                    <flux:navbar.item icon="clipboard-document-check" :href="route('checklists.runs.today')" :current="request()->routeIs('checklists.runs.today')" wire:navigate>
                        {{ __('Checklist Today') }}
                    </flux:navbar.item>

                    <flux:navbar.item icon="exclamation-triangle" :href="route('incidents.create')" :current="request()->routeIs('incidents.create')" wire:navigate>
                        {{ __('Report Incident') }}
                    </flux:navbar.item>
                @endif
            </flux:navbar>

            <flux:spacer />

            <x-desktop-user-menu />
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar collapsible="mobile" sticky class="border-e border-[var(--app-shell-border)] bg-[var(--app-shell-bg)] text-[var(--app-shell-text)] lg:hidden">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route(auth()->user()->landingRouteName()) }}" wire:navigate />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')">
                    @if (auth()->user()->isManagement())
                        <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard')  }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="clipboard-document-list" :href="route('incidents.index')" :current="request()->routeIs('incidents.index') || request()->routeIs('incidents.show')" wire:navigate>
                            {{ __('Incidents') }}
                        </flux:sidebar.item>
                        @if (auth()->user()->isAdmin())
                            <flux:sidebar.item icon="rectangle-stack" :href="route('templates.index')" :current="request()->routeIs('templates.index')" wire:navigate>
                                {{ __('Templates') }}
                            </flux:sidebar.item>
                        @endif
                    @endif

                    @if (auth()->user()->isStaff())
                        <flux:sidebar.item icon="clipboard-document-check" :href="route('checklists.runs.today')" :current="request()->routeIs('checklists.runs.today')" wire:navigate>
                            {{ __('Checklist Today') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="exclamation-triangle" :href="route('incidents.create')" :current="request()->routeIs('incidents.create')" wire:navigate>
                            {{ __('Report Incident') }}
                        </flux:sidebar.item>
                    @endif
                </flux:sidebar.group>
            </flux:sidebar.nav>
        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
