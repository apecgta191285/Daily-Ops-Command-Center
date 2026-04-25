<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <title>{{ config('app.name', 'Daily Ops Command Center') }}</title>
    </head>
    <body class="auth-shell auth-shell--welcome-polish antialiased" data-no-progress-bar>
        <a href="#main-content" class="ops-skip-link">{{ __('ข้ามไปยังเนื้อหาหลัก') }}</a>

        <main id="main-content" class="auth-stage auth-stage--welcome auth-stage--welcome-polish">
            <section class="auth-stage__frame auth-stage__frame--welcome">
                <div class="auth-stage__scene auth-stage__scene--welcome" data-motion="fade-right">
                    <div class="auth-stage__scene-inner">
                        <div class="auth-stage__brand-lockup">
                            <p class="auth-stage__kicker">{{ __('งานประจำวันห้องปฏิบัติการคอมพิวเตอร์') }}</p>
                            <div class="auth-stage__brand-link">
                                <span class="app-brand-mark size-14">
                                    <x-app-logo-icon class="size-8 fill-current text-current" />
                                </span>
                                <div class="space-y-1">
                                    <p class="auth-stage__brand-name">{{ config('app.name', 'Daily Ops Command Center') }}</p>
                                    <p class="auth-stage__brand-copy">{{ __('ระบบกลางสำหรับตรวจห้อง แจ้งปัญหา และติดตามงานของผู้ดูแลห้องแล็บ') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="welcome-stage__hero">
                            <div class="space-y-4">
                                <span class="ops-shell-chip ops-shell-chip--accent">{{ __('ระบบงานประจำวันห้องปฏิบัติการคอมพิวเตอร์') }}</span>
                                <h1 class="welcome-stage__title">{{ __('ตรวจห้อง แจ้งปัญหา และติดตามงานของทีมจากพื้นที่ทำงานร่วมกันเพียงจุดเดียว') }}</h1>
                                <p class="welcome-stage__lead">{{ __('ออกแบบมาสำหรับทีมดูแลห้องคอมในมหาวิทยาลัยที่ต้องการระบบกลางที่เชื่อถือได้สำหรับรายการตรวจเช็ก การส่งต่องานปัญหา และการมองเห็นภาพรวมของผู้ดูแลห้องแล็บ โดยไม่ต้องกระจายงานไปอยู่ในกระดาษ แชต หรือสเปรดชีต') }}</p>
                            </div>

                            <div class="welcome-stage__grid">
                                <article class="auth-signal-card auth-signal-card--wide">
                                    <p class="auth-signal-card__eyebrow">{{ __('ฝั่งผู้ตรวจห้อง') }}</p>
                                    <p class="auth-signal-card__title">{{ __('งานประจำวันชัดเจนในเส้นทางเดียว') }}</p>
                                    <p class="auth-signal-card__body">{{ __('ความคืบหน้าของรายการตรวจเช็ก ความพร้อมของจุดใช้งาน และการส่งต่อรายงานปัญหาอยู่ในเส้นทางเดียว ไม่กระจายเป็นโน้ตหรือแชตย่อย') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('ฝั่งผู้ดูแลห้องแล็บ') }}</p>
                                    <p class="auth-signal-card__title">{{ __('คัดแยกปัญหาได้โดยไม่สับสน') }}</p>
                                    <p class="auth-signal-card__body">{{ __('เห็นปัญหาที่เลยกำหนดติดตาม ทิศทางการติดตาม และช่วงตรวจที่ยังไม่เสร็จก่อนงานจะค้างสะสม') }}</p>
                                </article>

                                <article class="auth-signal-card">
                                    <p class="auth-signal-card__eyebrow">{{ __('ฝั่งผู้ดูแลระบบ') }}</p>
                                    <p class="auth-signal-card__title">{{ __('ควบคุมแม่แบบรายการตรวจได้ตรงกับงานจริง') }}</p>
                                    <p class="auth-signal-card__body">{{ __('ดูแลแม่แบบช่วงเปิดห้อง ระหว่างวัน และปิดห้องให้ตรงกับงานจริง โดยไม่รบกวน workflow ของผู้ตรวจห้อง') }}</p>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="welcome-stage__aside" data-motion="fade-left">
                    <div class="welcome-stage__aside-card">
                        <div class="welcome-stage__aside-header">
                            <p class="welcome-stage__aside-eyebrow">{{ __('ลำดับเดโมที่แนะนำ') }}</p>
                            <h2 class="welcome-stage__aside-title">{{ __('ดูภาพรวมระบบในลำดับสั้น ๆ') }}</h2>
                            <p class="welcome-stage__aside-copy">{{ __('ระบบนี้ออกแบบให้กระชับ ใช้กับทีมเดียวในมหาวิทยาลัยเดียว และเชื่อมงานตั้งแต่รายการตรวจเช็กไปจนถึงการติดตามรายงานปัญหา') }}</p>
                        </div>

                        <ol class="welcome-stage__steps">
                            <li class="welcome-stage__step">
                                <span class="ops-step-index">1</span>
                                <div>
                                    <p class="welcome-stage__step-title">{{ __('ทำรายการตรวจเช็กในบทบาทผู้ตรวจห้อง') }}</p>
                                    <p class="welcome-stage__step-copy">{{ __('เลือกช่วงตรวจที่ถูกต้อง ทำรายการตรวจเช็กของวันนี้ให้ครบ และส่งผลพร้อมดูความคืบหน้าได้ชัดเจน') }}</p>
                                </div>
                            </li>
                            <li class="welcome-stage__step">
                                <span class="ops-step-index">2</span>
                                <div>
                                    <p class="welcome-stage__step-title">{{ __('แจ้งรายงานปัญหา') }}</p>
                                    <p class="welcome-stage__step-copy">{{ __('สร้างรายงานปัญหาสำหรับเครื่อง อุปกรณ์ เครือข่าย หรือห้อง แล้วไปดูต่อในมุมของผู้ดูแลห้องแล็บ') }}</p>
                                </div>
                            </li>
                            <li class="welcome-stage__step">
                                <span class="ops-step-index">3</span>
                                <div>
                                    <p class="welcome-stage__step-title">{{ __('ตรวจการตั้งค่าแม่แบบรายการตรวจ') }}</p>
                                    <p class="welcome-stage__step-copy">{{ __('เข้าสู่หน้าจัดการแม่แบบเพื่อดูว่าแต่ละรอบเวลามีแม่แบบที่ใช้งานจริงของตัวเองอย่างไร') }}</p>
                                </div>
                            </li>
                        </ol>

                        <div class="welcome-stage__aside-footer">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ops-button ops-button--primary w-full" wire:navigate>
                                    {{ __('เข้าสู่ระบบ') }}
                                </a>
                            @endif

                            <p class="ops-inline-note">{{ __('ใช้งานภายในสำหรับผู้ตรวจห้อง ผู้ดูแลห้องแล็บ และผู้ดูแลระบบ') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
