<x-layouts::auth :title="__('ยืนยันรหัสผ่าน')">
    <div class="flex flex-col gap-6">
        <x-auth-header
            :title="__('ยืนยันรหัสผ่าน')"
            :description="__('นี่คือพื้นที่ที่ต้องยืนยันตัวตนก่อน กรุณากรอกรหัสผ่านอีกครั้งเพื่อดำเนินการต่อ')"
        />

        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.confirm.store') }}" class="auth-form">
            @csrf

            <div>
                <label for="password" class="ops-field-label">{{ __('รหัสผ่าน') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('รหัสผ่าน') }}"
                    class="ops-control"
                >
                @error('password') <span class="ops-field-error">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="ops-button ops-button--primary w-full" data-test="confirm-password-button">
                {{ __('ยืนยัน') }}
            </button>
        </form>
    </div>
</x-layouts::auth>
