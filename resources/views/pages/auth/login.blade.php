<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        @if (app()->environment(['local', 'testing']))
            <div class="rounded-2xl border border-[var(--app-border)] bg-[var(--app-surface-subtle)] p-4 text-sm text-[var(--app-text-muted)]">
                <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--app-text-muted)]">{{ __('Local demo accounts') }}</p>
                <ul class="mt-3 space-y-2">
                    <li><strong class="text-[var(--app-heading)]">Admin</strong>: <code>admin@example.com</code> / <code>password</code> — {{ __('dashboard, triage, and checklist template administration') }}</li>
                    <li><strong class="text-[var(--app-heading)]">Supervisor</strong>: <code>supervisor@example.com</code> / <code>password</code> — {{ __('dashboard and incident follow-up without template administration') }}</li>
                    <li><strong class="text-[var(--app-heading)]">Staff</strong>: <code>operatora@example.com</code> / <code>password</code> — {{ __('daily checklist execution and incident reporting') }}</li>
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="auth-form">
            @csrf

            <div>
                <label for="email" class="ops-field-label">{{ __('Email address') }}</label>
                <input
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                    class="ops-control"
                >
                @error('email') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <div>
                <div class="flex items-center justify-between gap-3">
                    <label for="password" class="ops-field-label !mb-0">{{ __('Password') }}</label>

                    @if (Route::has('password.request'))
                        <a class="auth-link" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('Password') }}"
                    class="ops-control"
                >
                @error('password') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <label class="auth-checkbox">
                <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="auth-checkbox__input">
                <span class="auth-checkbox__label">{{ __('Remember me') }}</span>
            </label>

            <div class="flex items-center justify-end">
                <button type="submit" class="ops-button ops-button--primary w-full" data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>
