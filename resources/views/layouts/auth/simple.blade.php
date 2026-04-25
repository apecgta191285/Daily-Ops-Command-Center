<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-shell antialiased" data-no-progress-bar>
        <a href="#main-content" class="ops-skip-link">{{ __('ข้ามไปยังเนื้อหาหลัก') }}</a>

        <main id="main-content" class="auth-stage">
            <section class="auth-stage__frame">
                <div class="auth-stage__scene" data-motion="fade-right">
                    <div class="auth-stage__scene-inner">
                        <div class="auth-stage__brand-lockup">
                            <p class="auth-stage__kicker">{{ __('งานประจำวันห้องปฏิบัติการคอมพิวเตอร์') }}</p>
                            <a href="{{ route('home') }}" class="auth-stage__brand-link" wire:navigate>
                                <span class="app-brand-mark size-12">
                                    <x-app-logo-icon class="size-7 fill-current text-current" />
                                </span>
                                <div class="space-y-1">
                                    <p class="auth-stage__brand-name">{{ config('app.name', 'Daily Ops Command Center') }}</p>
                                    <p class="auth-stage__brand-copy">{{ __('รายการตรวจเช็ก การติดตามรายงานปัญหา และการดูแลระบบสำหรับทีมดูแลห้องคอมภายในมหาวิทยาลัย') }}</p>
                                </div>
                            </a>
                        </div>

                        <div class="auth-stage__story">
                            <div>
                                <span class="ops-shell-chip ops-shell-chip--accent">{{ __('พื้นที่เข้าสู่ระบบของทีมดูแลห้องคอม') }}</span>
                                <h1 class="auth-stage__title">{{ __('เข้าสู่พื้นที่ที่ใช้ตรวจห้อง ติดตามปัญหา และประสานงานรายวันของทีมได้จากหน้าจอเดียว') }}</h1>
                                <p class="auth-stage__lead">{{ __('นี่ไม่ใช่แผงผู้ดูแลระบบทั่วไป แต่เป็นพื้นที่ทำงานร่วมกันของทีมดูแลห้องปฏิบัติการคอมพิวเตอร์ในมหาวิทยาลัย สำหรับตรวจห้อง แจ้งปัญหา และติดตามงานประจำวันให้ต่อเนื่อง') }}</p>
                            </div>

                            <div class="auth-stage__signal-grid">
                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('ฝั่งผู้ตรวจห้อง') }}</p>
                                    <p class="auth-signal-card__title">{{ __('ทำงานประจำวันได้ชัดเจน') }}</p>
                                    <p class="auth-signal-card__body">{{ __('เลือกช่วงตรวจที่ถูกต้อง ส่งรายการตรวจเช็กให้ครบ และส่งต่อรายงานปัญหาได้โดยไม่หลุดจากงานประจำวัน') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('ฝั่งผู้ดูแลห้องแล็บ') }}</p>
                                    <p class="auth-signal-card__title">{{ __('เห็นจุดที่ต้องติดตามได้เร็ว') }}</p>
                                    <p class="auth-signal-card__body">{{ __('ติดตามช่วงตรวจที่ยังไม่เสร็จ ปัญหาที่มีความรุนแรงสูง และงานติดตามจากแดชบอร์ดภาพรวมเดียว') }}</p>
                                </article>
                            </div>

                            <div class="auth-stage__meta">
                                <div class="auth-stage__meta-block">
                                    <p class="auth-stage__meta-label">{{ __('แนวทางการทำงานร่วมกัน') }}</p>
                                    <p class="auth-stage__meta-value">{{ __('งานประจำวันของห้องคอมเดียวกัน') }}</p>
                                </div>
                                <div class="auth-stage__meta-block">
                                    <p class="auth-stage__meta-label">{{ __('แนวทางของระบบ') }}</p>
                                    <p class="auth-stage__meta-value">{{ __('ระบบงานประจำวันห้องปฏิบัติการคอมพิวเตอร์') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="auth-stage__panel-wrap" data-motion="fade-left">
                    <div class="auth-panel" tabindex="-1">
                        <div class="auth-panel__brand">
                            <p class="auth-panel__kicker">{{ __('เข้าสู่ระบบอย่างปลอดภัย') }}</p>
                            <div class="auth-panel__copy">
                                <p class="auth-panel__title">{{ __('เข้าสู่พื้นที่งานประจำวันของห้องคอม') }}</p>
                                <p class="auth-panel__body">{{ __('ใช้บัญชีที่ได้รับมอบหมายเพื่อเข้าสู่งานประจำวันของทีมในวันนี้') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-6">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </section>
        </main>
        @fluxScripts
    </body>
</html>
