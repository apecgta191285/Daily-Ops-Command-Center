<x-layouts::auth :title="__('เข้าสู่ระบบ')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('เข้าสู่ระบบด้วยบัญชีของคุณ')" :description="__('ใช้บัญชีที่ได้รับมอบหมายเพื่อเข้าสู่งานประจำวันของทีมดูแลห้องคอม')" />

        <x-auth-session-status class="text-center" :status="session('status')" />

        @if (app()->environment(['local', 'testing']))
            <div class="auth-callout">
                <p class="auth-callout__eyebrow">{{ __('บัญชีเดโมสำหรับเครื่อง local') }}</p>
                <ul class="mt-3 space-y-2">
                    <li><strong class="ops-text-heading">ผู้ดูแลระบบ</strong>: <code>admin@example.com</code> / <code>password</code> — {{ __('อาจารย์ผู้รับผิดชอบหรือผู้ได้รับมอบหมายที่ดูแลแม่แบบ ผู้ใช้งาน แดชบอร์ดภาพรวม และการติดตามงาน') }}</li>
                    <li><strong class="ops-text-heading">ผู้ดูแลห้องแล็บ</strong>: <code>supervisor@example.com</code> / <code>password</code> — {{ __('เจ้าหน้าที่แล็บหรือผู้ดูแลห้องที่ตรวจแดชบอร์ด คิวปัญหา และการติดตามรายงานปัญหาแยกตามห้อง') }}</li>
                    <li><strong class="ops-text-heading">ผู้ตรวจห้อง A</strong>: <code>operatora@example.com</code> / <code>password</code> — {{ __('นักศึกษาที่เข้าเวรตรวจห้องและแจ้งรายงานปัญหาของห้อง') }}</li>
                    <li><strong class="ops-text-heading">ผู้ตรวจห้อง B</strong>: <code>operatorb@example.com</code> / <code>password</code> — {{ __('บัญชีนักศึกษาอีกหนึ่งคนสำหรับใช้เดโมกรณีหลายห้อง') }}</li>
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="auth-form">
            @csrf

            <div>
                <label for="email" class="ops-field-label">{{ __('อีเมล') }}</label>
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
                    <label for="password" class="ops-field-label !mb-0">{{ __('รหัสผ่าน') }}</label>

                    @if (Route::has('password.request'))
                        <a class="auth-link" href="{{ route('password.request') }}" wire:navigate>
                            {{ __('ลืมรหัสผ่านใช่หรือไม่') }}
                        </a>
                    @endif
                </div>

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

            <label class="auth-checkbox">
                <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="auth-checkbox__input">
                <span class="auth-checkbox__label">{{ __('จดจำการเข้าสู่ระบบ') }}</span>
            </label>

            <div class="flex items-center justify-end">
                <button type="submit" class="ops-button ops-button--primary w-full" data-test="login-button">
                    {{ __('เข้าสู่ระบบ') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts::auth>
