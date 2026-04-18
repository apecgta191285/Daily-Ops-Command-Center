@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h1 class="ops-text-heading text-2xl font-semibold tracking-tight">{{ $title }}</h1>
    <p class="ops-text-muted mt-2 text-sm leading-6">{{ $description }}</p>
</div>
