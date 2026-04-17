@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name', 'Daily Ops Command Center')" {{ $attributes }}>
        <x-slot name="logo" class="app-brand-mark size-8">
            <x-app-logo-icon class="size-5 fill-current text-current" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name', 'Daily Ops Command Center')" {{ $attributes }}>
        <x-slot name="logo" class="app-brand-mark size-8">
            <x-app-logo-icon class="size-5 fill-current text-current" />
        </x-slot>
    </flux:brand>
@endif
