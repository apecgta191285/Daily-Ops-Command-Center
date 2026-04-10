<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[var(--app-shell-bg)]">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[var(--app-shell-border)] bg-[var(--app-shell-bg)]">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route(auth()->user()->landingRouteName()) }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    @if (auth()->user()->isManagement())
                        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                            {{ __('Dashboard') }}
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

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="border-b border-[var(--app-shell-border)] bg-[var(--app-shell-bg)] lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
