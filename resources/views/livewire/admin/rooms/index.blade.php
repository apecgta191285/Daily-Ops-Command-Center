<div class="ops-screen ops-screen--room-index">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Room administration') }}</p>
                <h2 class="ops-page__title">{{ __('Room Master Data') }}</h2>
                <p class="ops-page-intro__body">
                    จัดการห้องที่ใช้เป็นแกนกลางของรายการตรวจ รายงานปัญหา และรายงานสรุป เพื่อให้ข้อมูลไม่แตกเป็นคนละชุดและตรวจสอบย้อนหลังได้จริง
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Master data') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin only') }}</span>
                    <span class="ops-shell-chip">{{ __('Audit-safe lifecycle') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('rooms.create') }}" class="ops-button ops-button--primary" wire:navigate>
                    {{ __('Create room') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
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
                    <p class="ops-hero__eyebrow">ฐานข้อมูลห้องสำหรับทั้งระบบ</p>
                    <h3 class="ops-hero__title">ทำให้ทุก workflow อ้างอิงห้องชุดเดียวกัน</h3>
                    <p class="ops-hero__lead">
                        ห้องเป็นแกนสำคัญของ checklist, incident, dashboard และ report ถ้าจัดการจากฐานข้อมูลด้วยมือ ระบบจะเสี่ยงผิดพลาดและขาด audit trail หน้านี้ทำให้ผู้ดูแลระบบควบคุมได้อย่างเป็นระบบ
                    </p>
                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room registry') }}</span>
                        <span class="ops-shell-chip">{{ __('Checklist context') }}</span>
                        <span class="ops-shell-chip">{{ __('Incident context') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">จำนวนห้องทั้งหมด</p>
                        <p class="ops-hero__aside-value">{{ $roomSummary['total_count'] }}</p>
                        <p class="ops-hero__aside-copy">ห้องที่อยู่ใน master data ของระบบตอนนี้</p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Active') }}</p>
                            <p class="ops-authoring-metric__value">{{ $roomSummary['active_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Inactive') }}</p>
                            <p class="ops-authoring-metric__value">{{ $roomSummary['inactive_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Protected') }}</p>
                            <p class="ops-authoring-metric__value">{{ $roomSummary['protected_count'] }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">นโยบายวงจรชีวิตห้อง</p>
                    <h3 class="ops-section-heading__title">ลบเฉพาะห้องที่ยังไม่เคยถูกใช้งาน</h3>
                    <p class="ops-section-heading__body">ห้องที่มี checklist หรือ incident แล้วต้องเก็บไว้เพื่อประวัติระบบ หากไม่ใช้งานต่อให้ปิดใช้งานแทนการลบ</p>
                </div>
            </div>

            <div class="ops-card__body">
                <div class="ops-governance-grid">
                    <article class="ops-governance-card ops-governance-card--covered">
                        <div class="ops-governance-card__header">
                            <div>
                                <p class="ops-admin-item__eyebrow">{{ __('Operational') }}</p>
                                <h4 class="ops-admin-item__title">ห้องที่เปิดใช้งาน</h4>
                            </div>
                            <span class="ops-chip ops-chip--success">{{ $roomSummary['active_count'] }}</span>
                        </div>
                        <p class="ops-governance-card__meta mt-4">ห้องที่ผู้ตรวจสามารถเลือกใช้ใน workflow ปัจจุบัน</p>
                    </article>

                    <article class="ops-governance-card {{ $roomSummary['protected_count'] > 0 ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }}">
                        <div class="ops-governance-card__header">
                            <div>
                                <p class="ops-admin-item__eyebrow">{{ __('Audit locked') }}</p>
                                <h4 class="ops-admin-item__title">ห้องที่มีประวัติแล้ว</h4>
                            </div>
                            <span class="ops-chip ops-chip--warning">{{ $roomSummary['protected_count'] }}</span>
                        </div>
                        <p class="ops-governance-card__meta mt-4">ลบไม่ได้โดยตั้งใจ เพื่อไม่ให้ checklist, incident และ report ย้อนหลังเสียความหมาย</p>
                    </article>

                    <article class="ops-governance-card ops-governance-card--covered">
                        <div class="ops-governance-card__header">
                            <div>
                                <p class="ops-admin-item__eyebrow">{{ __('Draft-safe') }}</p>
                                <h4 class="ops-admin-item__title">ห้องที่ยังไม่มีประวัติ</h4>
                            </div>
                            <span class="ops-chip ops-chip--info">{{ $roomSummary['unused_count'] }}</span>
                        </div>
                        <p class="ops-governance-card__meta mt-4">แก้ไขหรือลบได้หากสร้างผิดก่อนถูกใช้งานจริง</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <div class="ops-table-wrap">
                    <table class="ops-table ops-table--responsive min-w-full">
                        <thead>
                            <tr>
                                <th>{{ __('Room') }}</th>
                                <th>{{ __('State') }}</th>
                                <th>{{ __('Checklist runs') }}</th>
                                <th>{{ __('Incidents') }}</th>
                                <th>{{ __('Lifecycle') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rooms as $room)
                                @php($historyCount = $room->checklist_runs_count + $room->incidents_count)
                                <tr class="ops-table__row">
                                    <td data-label="Room" class="px-4 py-4 text-sm">
                                        <p class="ops-text-heading text-sm font-semibold">{{ $room->name }}</p>
                                        <p class="ops-text-muted text-xs">{{ $room->code }}</p>
                                        @if ($room->description)
                                            <p class="ops-text-muted mt-1 text-xs">{{ $room->description }}</p>
                                        @endif
                                    </td>
                                    <td data-label="State" class="px-4 py-4 text-sm">
                                        <span class="ops-badge {{ $room->is_active ? 'ops-badge--success' : 'ops-badge--danger' }}">
                                            {{ $room->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="Checklist runs" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $room->checklist_runs_count }}
                                    </td>
                                    <td data-label="Incidents" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $room->incidents_count }}
                                    </td>
                                    <td data-label="Lifecycle" class="px-4 py-4 text-sm">
                                        <span class="ops-badge {{ $historyCount > 0 ? 'ops-badge--warning' : 'ops-badge--info' }}">
                                            {{ $historyCount > 0 ? __('Protected history') : __('Can delete if needed') }}
                                        </span>
                                    </td>
                                    <td data-label="Action" class="px-4 py-4 text-right text-sm">
                                        <a href="{{ route('rooms.edit', $room) }}" class="ops-button ops-button--secondary" wire:navigate>
                                            {{ __('Edit room') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
