<div class="ops-screen ops-screen--notification-deliveries">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Notification audit') }}</p>
                <h2 class="ops-page__title">{{ __('ประวัติการส่งแจ้งเตือน') }}</h2>
                <p class="ops-page-intro__body">
                    ตรวจสอบหลักฐานการส่ง LINE notification ของระบบ ว่าส่งสำเร็จ ถูกข้ามเพราะปิดใช้งาน หรือล้มเหลวจากการตั้งค่า/API
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">LINE Messaging API</span>
                    <span class="ops-shell-chip">Audit trail</span>
                    <span class="ops-shell-chip">ไม่เปิดเผย token</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('กลับไปคิวปัญหา') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">ช่วงตรวจสอบ</p>
                    <h3 class="ops-hero__title">{{ $this->dateRangeLabel }}</h3>
                    <p class="ops-hero__lead">
                        ใช้หน้านี้ตอบคำถามตอน demo ได้ตรง ๆ ว่าระบบมีการพยายามส่งแจ้งเตือนเมื่อไร ส่งไปยัง recipient ประเภทใด และผลลัพธ์เป็นอย่างไร
                    </p>
                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $summary['total'] }} รายการ</span>
                        <span class="ops-shell-chip">{{ $summary['sent'] }} ส่งสำเร็จ</span>
                        <span class="ops-shell-chip">{{ $summary['failed'] }} ล้มเหลว</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Delivery status</p>
                        <p class="ops-hero__aside-value">{{ $summary['sent'] }}/{{ $summary['total'] }}</p>
                        <p class="ops-hero__aside-copy">จำนวนรายการที่ส่งสำเร็จจากตัวกรองปัจจุบัน</p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">Sent</p>
                            <p class="ops-authoring-metric__value">{{ $summary['sent'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">Failed</p>
                            <p class="ops-authoring-metric__value">{{ $summary['failed'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">Skipped</p>
                            <p class="ops-authoring-metric__value">{{ $summary['skipped'] }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">ตัวกรอง</p>
                    <h3 class="ops-section-heading__title">คัดกรอง delivery log</h3>
                    <p class="ops-section-heading__body">เลือกช่วงเวลา เหตุการณ์ และสถานะเพื่อไล่ตรวจปัญหา notification แบบไม่ต้องเปิดฐานข้อมูลเอง</p>
                </div>
            </div>

            <div class="ops-card__body space-y-5">
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="applyPreset('24h')" class="ops-button ops-button--secondary">วันนี้</button>
                    <button type="button" wire:click="applyPreset('7d')" class="ops-button ops-button--secondary">7 วันล่าสุด</button>
                    <button type="button" wire:click="applyPreset('30d')" class="ops-button ops-button--secondary">30 วันล่าสุด</button>
                    <button type="button" wire:click="clearFilters" class="ops-button ops-button--secondary">ล้างตัวกรอง</button>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <label for="start_date" class="ops-field-label">วันที่เริ่มต้น</label>
                        <input id="start_date" type="date" wire:model.live="startDate" class="ops-control">
                    </div>

                    <div>
                        <label for="end_date" class="ops-field-label">วันที่สิ้นสุด</label>
                        <input id="end_date" type="date" wire:model.live="endDate" class="ops-control">
                    </div>

                    <div>
                        <label for="event_type" class="ops-field-label">เหตุการณ์</label>
                        <select id="event_type" wire:model.live="eventType" class="ops-control">
                            <option value="">ทุกเหตุการณ์</option>
                            @foreach($eventTypes as $eventTypeOption)
                                <option value="{{ $eventTypeOption }}">{{ $this->eventLabel($eventTypeOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="ops-field-label">สถานะการส่ง</label>
                        <select id="status" wire:model.live="status" class="ops-control">
                            <option value="">ทุกสถานะ</option>
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption }}">{{ $this->statusLabel($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">Delivery log</p>
                    <h3 class="ops-section-heading__title">หลักฐานการส่งล่าสุด</h3>
                    <p class="ops-section-heading__body">ระบบเก็บเฉพาะ fingerprint ของผู้รับ ไม่เก็บ token และไม่แสดง recipient ID เต็มบนหน้าเว็บ</p>
                </div>
            </div>

            <div class="ops-card__body">
                @if (session('notification_delivery_status'))
                    <x-ops.callout title="ส่งซ้ำสำเร็จ" tone="success" class="mb-4">
                        {{ session('notification_delivery_status') }}
                    </x-ops.callout>
                @endif

                @if (session('notification_delivery_error'))
                    <x-ops.callout title="ส่งซ้ำไม่สำเร็จ" tone="warning" class="mb-4">
                        {{ session('notification_delivery_error') }}
                    </x-ops.callout>
                @endif

                <div class="ops-table-wrap">
                    <table class="ops-table ops-table--responsive min-w-full">
                        <thead>
                            <tr>
                                <th>เวลา</th>
                                <th>เหตุการณ์</th>
                                <th>Incident</th>
                                <th>ผู้รับ</th>
                                <th>ผลลัพธ์</th>
                                <th>HTTP</th>
                                <th>ข้อความระบบ</th>
                                <th>การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deliveries as $delivery)
                                <tr class="ops-table__row">
                                    <td data-label="เวลา" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ optional($delivery->attempted_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>
                                    <td data-label="เหตุการณ์" class="px-4 py-4 text-sm">
                                        <span class="ops-chip ops-chip--neutral">{{ $this->eventLabel($delivery->event_type) }}</span>
                                    </td>
                                    <td data-label="Incident" class="px-4 py-4 text-sm">
                                        @if($delivery->incident)
                                            <a href="{{ route('incidents.show', $delivery->incident) }}" class="ops-link" wire:navigate>
                                                #{{ $delivery->incident->id }} {{ $delivery->incident->title }}
                                            </a>
                                            <p class="ops-text-muted mt-1 text-xs">{{ $delivery->incident->room?->name ?? 'ไม่ระบุห้อง' }}</p>
                                        @else
                                            <span class="ops-text-muted">ไม่มี incident อ้างอิง</span>
                                        @endif
                                    </td>
                                    <td data-label="ผู้รับ" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $delivery->recipient_type ?? '-' }}
                                        @if($delivery->recipient_fingerprint)
                                            <span class="block text-xs">fp: {{ $delivery->recipient_fingerprint }}</span>
                                        @endif
                                    </td>
                                    <td data-label="ผลลัพธ์" class="px-4 py-4 text-sm">
                                        <span class="ops-chip {{ $this->statusChipClass($delivery->status) }}">
                                            {{ $this->statusLabel($delivery->status) }}
                                        </span>
                                    </td>
                                    <td data-label="HTTP" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $delivery->http_status ?? '-' }}
                                    </td>
                                    <td data-label="ข้อความระบบ" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $delivery->message ?? '-' }}
                                    </td>
                                    <td data-label="การดำเนินการ" class="px-4 py-4 text-sm">
                                        @if($delivery->status !== 'sent' && $delivery->incident && in_array($delivery->event_type, ['incident_created', 'incident_status_changed', 'incident_accountability_changed'], true))
                                            <button
                                                type="button"
                                                wire:click="redeliver({{ $delivery->id }})"
                                                wire:confirm="ส่ง LINE notification ซ้ำจากรายการนี้ใช่ไหม?"
                                                class="ops-button ops-button--secondary"
                                            >
                                                ส่งซ้ำ
                                            </button>
                                        @else
                                            <span class="ops-text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8">
                                        <x-ops.empty-state title="ยังไม่มี delivery log ในช่วงนี้" body="ลองขยายช่วงเวลา หรือทดสอบสร้าง incident ใหม่เพื่อให้ระบบส่ง LINE notification" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $deliveries->links() }}
                </div>
            </div>
        </section>
    </div>
</div>
