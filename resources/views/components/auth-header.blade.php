@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="text-2xl font-semibold tracking-tight text-[var(--app-heading)]">{{ $title }}</h1>
    <p class="mt-2 text-sm leading-6 text-[var(--app-text-muted)]">{{ $description }}</p>
</div>
