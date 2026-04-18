<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="ops-content-main">
        <div class="ops-page">
            @isset($header)
                <div class="ops-page__header">
                    <div class="ops-page__header-inner">
                        {{ $header }}
                    </div>
                </div>
            @endisset

            <div class="ops-page__body">
                {{ $slot }}
            </div>
        </div>
    </flux:main>
</x-layouts::app.sidebar>
