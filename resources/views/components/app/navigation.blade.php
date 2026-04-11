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
                {{ __('Admin Templates') }}
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
