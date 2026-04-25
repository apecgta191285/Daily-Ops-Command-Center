<x-layouts::auth :title="__('ยืนยันอีเมล')">
    <div class="mt-4 flex flex-col gap-6">
        <p class="auth-helper-text">
            {{ __('กรุณายืนยันอีเมลของคุณโดยกดลิงก์ที่เราเพิ่งส่งไปให้') }}
        </p>

        @if (session('status') == 'verification-link-sent')
            <p class="ops-text-success text-center text-sm font-medium">
                {{ __('เราได้ส่งลิงก์ยืนยันอีเมลฉบับใหม่ไปยังอีเมลที่คุณใช้ลงทะเบียนแล้ว') }}
            </p>
        @endif

        <div class="flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}" class="w-full">
                @csrf
                <button type="submit" class="ops-button ops-button--primary w-full">
                    {{ __('ส่งอีเมลยืนยันอีกครั้ง') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="ops-button ops-button--secondary w-full cursor-pointer" data-test="logout-button">
                    {{ __('ออกจากระบบ') }}
                </button>
            </form>
        </div>
    </div>
</x-layouts::auth>
