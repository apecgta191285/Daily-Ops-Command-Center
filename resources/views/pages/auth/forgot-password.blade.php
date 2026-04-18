<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf

            <div>
                <label for="email" class="ops-field-label">{{ __('Email address') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    placeholder="email@example.com"
                    class="ops-control"
                >
                @error('email') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="ops-button ops-button--primary w-full" data-test="email-password-reset-link-button">
                {{ __('Email password reset link') }}
            </button>
        </form>

        <div class="ops-text-muted space-x-1 text-center text-sm rtl:space-x-reverse">
            <span>{{ __('Or, return to') }}</span>
            <a href="{{ route('login') }}" class="auth-link" wire:navigate>{{ __('log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
