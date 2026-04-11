<x-layouts::app :title="__('Admin Templates')">
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="ops-page__title">{{ __('Admin Templates') }}</h2>
            <p class="text-sm">
                {{ __('Checklist template management lives in the admin CRUD surface, separate from daily operations workflows.') }}
            </p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-5">
                <div class="ops-alert ops-alert--warning">
                    <strong class="font-semibold">{{ __('Admin surface ahead.') }}</strong>
                    <span class="block sm:inline">
                        {{ __('You are leaving the operations workspace and opening the Filament-based administration area for checklist template maintenance.') }}
                    </span>
                </div>

                <div class="space-y-3 text-sm text-[var(--app-text-muted)]">
                    <p>{{ __('Use the admin area only when you need to create, edit, activate, or retire checklist templates.') }}</p>
                    <p>{{ __('Daily checklist execution, incident reporting, and incident review remain in the main operations workspace.') }}</p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-[var(--app-border)] pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('dashboard') }}" class="ops-button ops-button--secondary">
                        {{ __('Back to operations dashboard') }}
                    </a>
                    <a href="{{ $adminTemplatesUrl }}" class="ops-button ops-button--primary">
                        {{ __('Open admin template management') }}
                    </a>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
