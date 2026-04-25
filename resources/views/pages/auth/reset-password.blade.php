<x-layouts::auth :title="__('ตั้งรหัสผ่านใหม่')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('ตั้งรหัสผ่านใหม่')" :description="__('กรุณากรอกรหัสผ่านใหม่ด้านล่าง')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.update') }}" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div>
                <label for="email" class="ops-field-label">{{ __('อีเมล') }}</label>
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
                <label for="password" class="ops-field-label">{{ __('รหัสผ่าน') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('รหัสผ่าน') }}"
                    class="ops-control"
                >
                @error('password') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="ops-field-label">{{ __('ยืนยันรหัสผ่าน') }}</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="{{ __('ยืนยันรหัสผ่าน') }}"
                    class="ops-control"
                >
                @error('password_confirmation') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="ops-button ops-button--primary w-full" data-test="reset-password-button">
                    {{ __('ตั้งรหัสผ่านใหม่') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>
