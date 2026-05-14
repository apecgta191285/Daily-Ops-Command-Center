<div class="ops-screen ops-screen--room-manage">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Room administration') }}</p>
                <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
                <p class="ops-page-intro__body">{{ __($this->pageDescription) }}</p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ $room ? __('Lifecycle update') : __('New room') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin-owned') }}</span>
                    <span class="ops-shell-chip">{{ __('Audit-safe') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('rooms.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to rooms') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @php($lifecycleSignals = $this->lifecycleSignals)

        @if (session()->has('message'))
            <div data-alert data-auto-dismiss="5000" role="status" aria-live="polite" class="ops-alert ops-alert--success">
                <div class="ops-alert__inner">
                    <div class="ops-alert__copy">{{ session('message') }}</div>
                    <button type="button" class="ops-alert__dismiss" data-dismiss-alert aria-label="{{ __('Dismiss message') }}">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
            </div>
        @endif

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">ห้องในระบบกลาง</p>
                    <h3 class="ops-hero__title">{{ __($this->pageTitle) }}</h3>
                    <p class="ops-hero__lead">
                        ตั้งค่าห้องให้ใช้งานตรงกันทั้ง checklist, incident และ report โดยใช้รหัสห้องที่สั้น อ่านง่าย และไม่ซ้ำกัน
                    </p>
                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $is_active ? __('Active room') : __('Inactive room') }}</span>
                        <span class="ops-shell-chip">{{ $room ? __('Existing record') : __('New master data') }}</span>
                        <span class="ops-shell-chip">{{ $this->hasOperationalHistory ? __('History protected') : __('No history yet') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">รหัสห้อง</p>
                        <p class="ops-hero__aside-value">{{ filled($code) ? \Illuminate\Support\Str::upper($code) : 'NEW' }}</p>
                        <p class="ops-hero__aside-copy">
                            {{ $is_active ? 'ห้องนี้จะอยู่ใน workflow ใหม่หลังบันทึก' : 'ห้องนี้จะไม่ถูกเสนอใน workflow ใหม่หลังบันทึก' }}
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Rooms') }}</p>
                            <p class="ops-authoring-metric__value">{{ $roomSummary['total_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Active') }}</p>
                            <p class="ops-authoring-metric__value">{{ $roomSummary['active_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Locked') }}</p>
                            <p class="ops-authoring-metric__value">{{ $roomSummary['protected_count'] }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
                    <section class="ops-card overflow-hidden xl:col-span-8" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ข้อมูลห้อง</p>
                                <h3 class="ops-section-heading__title">ชื่อ รหัส และคำอธิบาย</h3>
                                <p class="ops-section-heading__body">ใช้รหัสที่สั้นและมั่นคง เพราะจะถูกนำไปใช้ในตาราง รายงาน และการอ้างอิงย้อนหลัง</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="name" class="ops-field-label">ชื่อห้อง <span class="ops-required-mark">*</span></label>
                                <input id="name" type="text" wire:model="name" class="ops-control" placeholder="เช่น Lab 1">
                                @error('name') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="code" class="ops-field-label">รหัสห้อง <span class="ops-required-mark">*</span></label>
                                <input id="code" type="text" wire:model="code" class="ops-control" placeholder="เช่น LAB-01">
                                <p class="ops-field-help">ใช้ A-Z, 0-9, จุด, ขีดกลาง หรือขีดล่าง เช่น LAB-01 หรือ ROOM_A</p>
                                @error('code') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="description" class="ops-field-label">คำอธิบาย</label>
                                <textarea id="description" wire:model="description" rows="4" class="ops-control" placeholder="เช่น ห้องปฏิบัติการคอมพิวเตอร์สำหรับวิชาเครือข่าย"></textarea>
                                @error('description') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <label class="ops-choice w-full justify-between">
                                <span>
                                    <span class="ops-text-heading text-sm font-medium">{{ __('Active room') }}</span>
                                    <span class="ops-text-muted mt-1 block text-sm">{{ __('When off, the room stays in history but is not offered for new operational work.') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="ops-choice__control">
                            </label>
                            @error('is_active') <span class="ops-field-error">{{ $message }}</span> @enderror
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden xl:col-span-8" data-motion="fade-up" data-motion-delay="90">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ผลหลังบันทึก</p>
                                <h3 class="ops-section-heading__title">บันทึกการเปลี่ยนแปลง</h3>
                                <p class="ops-section-heading__body">การแก้ไขนี้จะมีผลกับ workflow ใหม่ทันที แต่ประวัติเดิมยังอ้างอิงห้องเดิมได้ตามปกติ</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <button type="submit" class="ops-button ops-button--primary w-full">
                                {{ $room ? __('Save room changes') : __('Create room') }}
                            </button>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden xl:col-span-4" data-motion="fade-left" data-motion-delay="70">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ตรวจสอบวงจรชีวิต</p>
                                <h3 class="ops-section-heading__title">สัญญาณก่อนบันทึก</h3>
                                <p class="ops-section-heading__body">ระบบแยกการปิดใช้งานออกจากการลบ เพื่อรักษาประวัติการปฏิบัติงานและรายงานย้อนหลัง</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @foreach ($lifecycleSignals as $signal)
                                <x-ops.callout :title="$signal['title']" :tone="$signal['tone']">
                                    {{ __($signal['body']) }}
                                </x-ops.callout>
                            @endforeach
                        </div>
                    </section>

                    @if ($room)
                        <section class="ops-card overflow-hidden xl:col-span-4" data-motion="fade-left" data-motion-delay="120">
                            <div class="ops-section-heading">
                                <div>
                                    <p class="ops-section-heading__eyebrow">การลบข้อมูล</p>
                                    <h3 class="ops-section-heading__title">ลบเฉพาะห้องที่ยังไม่มีประวัติ</h3>
                                    <p class="ops-section-heading__body">ถ้าห้องนี้ถูกใช้แล้ว ระบบจะปฏิเสธการลบและให้ปิดใช้งานแทน</p>
                                </div>
                            </div>

                            <div class="ops-card__body space-y-4">
                                @error('room') <span class="ops-field-error">{{ $message }}</span> @enderror

                                <button
                                    type="button"
                                    wire:click="delete"
                                    wire:confirm="ยืนยันลบห้องนี้หรือไม่? ใช้เฉพาะห้องที่สร้างผิดและยังไม่มีประวัติเท่านั้น"
                                    class="ops-button ops-button--secondary w-full"
                                    @disabled($this->hasOperationalHistory)
                                >
                                    {{ __('Delete unused room') }}
                                </button>

                                @if ($this->hasOperationalHistory)
                                    <p class="ops-field-help">ปุ่มลบถูกปิดไว้เพราะห้องนี้มีข้อมูล operational history แล้ว</p>
                                @else
                                    <p class="ops-field-help">ใช้เฉพาะกรณีสร้างห้องผิดและยังไม่ถูกใช้ใน checklist หรือ incident</p>
                                @endif
                            </div>
                        </section>
                    @endif
            </div>
        </form>
    </div>
</div>
