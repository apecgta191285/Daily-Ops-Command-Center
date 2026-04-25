<x-layouts::auth :title="__('ลืมรหัสผ่าน')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('ลืมรหัสผ่าน')" :description="__('กรอกอีเมลเพื่อรับลิงก์สำหรับตั้งรหัสผ่านใหม่')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="auth-form">
            @csrf

            <div>
                <label for="email" class="ops-field-label">{{ __('อีเมล') }}</label>
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
                {{ __('ส่งลิงก์ตั้งรหัสผ่านใหม่') }}
            </button>
        </form>

        <div class="ops-text-muted space-x-1 text-center text-sm rtl:space-x-reverse">
            <span>{{ __('หรือกลับไป') }}</span>
            <a href="{{ route('login') }}" class="auth-link" wire:navigate>{{ __('เข้าสู่ระบบ') }}</a>
        </div>
    </div>
</x-layouts::auth>
