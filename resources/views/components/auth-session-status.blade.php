@props([
    'status',
])

@if ($status)
    <div
        data-alert
        data-auto-dismiss="6000"
        role="status"
        aria-live="polite"
        {{ $attributes->merge(['class' => 'ops-alert ops-alert--success']) }}
    >
        <div class="ops-alert__inner">
            <div class="ops-alert__copy font-medium">
                {{ $status }}
            </div>

            <button type="button" class="ops-alert__dismiss" data-dismiss-alert aria-label="{{ __('Dismiss message') }}">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    </div>
@endif
