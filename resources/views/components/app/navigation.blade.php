<flux:sidebar.group :heading="__('เมนูระบบ')" class="grid">
    @if (auth()->user()->isManagement())
        <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
            {{ __('แดชบอร์ดภาพรวม') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="clipboard-document-list" :href="route('incidents.index')" :current="request()->routeIs('incidents.index') || request()->routeIs('incidents.show')" wire:navigate>
            {{ __('คิวปัญหา') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="clock" :href="route('incidents.history.index')" :current="request()->routeIs('incidents.history.index')" wire:navigate>
            {{ __('ประวัติรายงานปัญหา') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="archive-box" :href="route('checklists.history.index')" :current="request()->routeIs('checklists.history.index') || request()->routeIs('checklists.history.show')" wire:navigate>
            {{ __('ประวัติรอบการตรวจเช็ก') }}
        </flux:sidebar.item>
        @if (auth()->user()->isAdmin())
            <flux:sidebar.item icon="rectangle-stack" :href="route('templates.index')" :current="request()->routeIs('templates.index') || request()->routeIs('templates.create') || request()->routeIs('templates.edit')" wire:navigate>
                {{ __('แม่แบบรายการตรวจ') }}
            </flux:sidebar.item>
            <flux:sidebar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.index') || request()->routeIs('users.create') || request()->routeIs('users.edit')" wire:navigate>
                {{ __('ผู้ใช้งาน') }}
            </flux:sidebar.item>
        @endif
    @endif

    @if (auth()->user()->isStaff())
        <flux:sidebar.item icon="clipboard-document-check" :href="route('checklists.runs.today')" :current="request()->routeIs('checklists.runs.today')" wire:navigate>
            {{ __('รายการตรวจเช็กวันนี้') }}
        </flux:sidebar.item>
        <flux:sidebar.item icon="exclamation-triangle" :href="route('incidents.create')" :current="request()->routeIs('incidents.create')" wire:navigate>
            {{ __('แจ้งรายงานปัญหา') }}
        </flux:sidebar.item>
    @endif
</flux:sidebar.group>
