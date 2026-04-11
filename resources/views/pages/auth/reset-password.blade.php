<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div>
                <label for="email" class="ops-field-label">{{ __('Email') }}</label>
                <input
                    id="email"
                    name="email"
                    value="{{ request('email') }}"
                    type="email"
                    required
                    autocomplete="email"
                    class="ops-control"
                >
                @error('email') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password" class="ops-field-label">{{ __('Password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Password') }}"
                    class="ops-control"
                >
                @error('password') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="ops-field-label">{{ __('Confirm password') }}</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('Confirm password') }}"
                    class="ops-control"
                >
                @error('password_confirmation') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="ops-button ops-button--primary w-full" data-test="reset-password-button">
                    {{ __('Reset password') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>
