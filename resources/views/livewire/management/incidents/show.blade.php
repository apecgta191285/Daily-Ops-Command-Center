<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Issue review') }}</p>
                <h2 class="ops-page__title">{{ __('Incident Detail') }}</h2>
                <p class="ops-page-intro__body">
                    ตรวจสอบปัญหาของห้อง ยืนยันว่าเกิดขึ้นที่ใด และขยับคิวปัญหาต่อด้วยการตัดสินใจที่ชัดเจน
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Issue detail') }}</span>
                    <span class="ops-shell-chip">{{ $incident->room?->name ?? __('No room') }}</span>
                    <span class="ops-shell-chip">{{ __($incident->status->value) }}</span>
                    <span class="ops-shell-chip">{{ __($incident->severity->value) }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('incidents.print', $incident) }}" class="ops-button" target="_blank" rel="noopener noreferrer">
                    {{ __('Printable summary') }}
                </a>
                <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary">
                    {{ __('Back to incident list') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="ops-incident-shell">
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
                    <p class="ops-hero__eyebrow">บันทึกรายงานปัญหา</p>
                    <h3 class="ops-hero__title">{{ $incident->title }}</h3>
                    <p class="ops-hero__lead">
                        ใช้หน้านี้เพื่อตรวจสิ่งที่ถูกรายงานในห้องนี้ ดูบันทึกล่าสุด และตัดสินใจว่าควรติดตามต่ออย่างไรสำหรับห้องหรืออุปกรณ์/เครื่องที่เกี่ยวข้อง
                    </p>
                    <p class="ops-text-shell-muted mt-3 text-sm">
                        ผู้รายงาน {{ $incident->creator?->name ?? 'ไม่ทราบ' }} เมื่อ {{ $incident->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="ops-text-shell-muted mt-2 text-sm">
                        {{ __('Room: :room', ['room' => $incident->room?->name ?? __('Not recorded')]) }}
                        @if ($incident->equipment_reference)
                            {{ __(' • Equipment: :equipment', ['equipment' => $incident->equipment_reference]) }}
                        @endif
                    </p>

                    <div class="ops-incident-meta mt-4">
                        <span class="ops-badge ops-badge--neutral">เปิดค้างมา {{ $this->ageInDays }} วัน</span>
                        @if ($this->isStale)
                            <span class="ops-badge ops-badge--warning">ค้างเกิน {{ $this->staleThresholdDays }} วัน</span>
                        @endif
                        @if ($this->needsOwner)
                            <span class="ops-badge ops-badge--warning">ต้องกำหนดผู้รับผิดชอบ</span>
                        @endif
                        @if ($this->isFollowUpOverdue)
                            <span class="ops-badge ops-badge--danger">ติดตามเกินกำหนด</span>
                        @endif
                        @if ($incident->owner)
                            <span class="ops-badge ops-badge--neutral">ผู้รับผิดชอบ: {{ $incident->owner->name }}</span>
                        @endif
                        @if ($incident->follow_up_due_at)
                            <span class="ops-badge ops-badge--warning">ติดตาม {{ $incident->follow_up_due_at->format('d/m/Y') }}</span>
                        @endif
                        <x-incidents.status-badge :status="$incident->status" />
                        <x-incidents.severity-badge :severity="$incident->severity" />
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">สถานะปัจจุบัน</p>
                        <p class="ops-hero__aside-value">{{ __($incident->category->value) }}</p>
                        <p class="ops-hero__aside-copy">
                            {{ $incident->resolved_at ? 'แก้ไขแล้วเมื่อ '.$incident->resolved_at->format('d/m/Y H:i') : 'ยังอยู่ในคิวของทีม' }}
                        </p>
                    </div>

                    <div class="ops-glance-grid--hero">
                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">ความรุนแรง</p>
                            <div class="mt-3"><x-incidents.severity-badge :severity="$incident->severity" /></div>
                            <p class="ops-glance-card__meta">สัญญาณว่ารายการนี้ควรถูกจัดการเร่งด่วนแค่ไหน</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">สถานะ</p>
                            <div class="mt-3"><x-incidents.status-badge :status="$incident->status" /></div>
                            <p class="ops-glance-card__meta">สถานะการดำเนินงานล่าสุดตามการอัปเดตครั้งสุดท้ายของผู้ดูแล</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">อายุรายการ</p>
                            <p class="ops-glance-card__value">{{ $this->ageInDays }}</p>
                            <p class="ops-glance-card__meta">จำนวนวันนับจากรายงานปัญหานี้เข้าสู่คิวครั้งแรก</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">ห้อง</p>
                            <p class="ops-glance-card__value">{{ $incident->room?->name ?? 'ยังไม่กำหนด' }}</p>
                            <p class="ops-glance-card__meta">ห้องที่ผูกอยู่กับรายงานปัญหานี้ในปัจจุบัน</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">อุปกรณ์/เครื่องที่เกี่ยวข้อง</p>
                            <p class="ops-glance-card__value">{{ $incident->equipment_reference ?? 'ไม่ได้ระบุ' }}</p>
                            <p class="ops-glance-card__meta">ข้อมูลเพิ่มเติมของเครื่องหรืออุปกรณ์ที่ผู้ตรวจห้องระบุไว้</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">ผู้รับผิดชอบ</p>
                            <p class="ops-glance-card__value">{{ $incident->owner?->name ?? 'ยังไม่มีผู้รับผิดชอบ' }}</p>
                            <p class="ops-glance-card__meta">ผู้ดูแลที่รับผิดชอบการขยับงานปัญหานี้ต่อในตอนนี้</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">กำหนดติดตาม</p>
                            <p class="ops-glance-card__value">{{ $incident->follow_up_due_at?->format('d/m/Y') ?? 'ยังไม่กำหนด' }}</p>
                            <p class="ops-glance-card__meta">วันที่เป้าหมายสำหรับการทบทวนหรือการดำเนินการครั้งถัดไป</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="ops-incident-grid">
            <div class="ops-incident-story">
                @if ($this->latestNextActionNote || $this->latestResolutionNote)
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-card__body ops-incident-summary-stack">
                            <div class="ops-section-heading">
                                <div>
                                    <p class="ops-section-heading__eyebrow">บริบทล่าสุดของการดำเนินงาน</p>
                                    <h2 class="ops-section-heading__title">สิ่งที่ผู้ตรวจทวนคนถัดไปควรรู้ก่อน</h2>
                                    <p class="ops-section-heading__body">แสดงแนวทางล่าสุดและสรุปการแก้ไขล่าสุดก่อนเข้าสู่ไทม์ไลน์เต็ม</p>
                                </div>
                            </div>

                            <div class="grid gap-4 xl:grid-cols-2">
                                @if ($this->latestNextActionNote)
                                    <x-ops.callout title="แนวทางติดตามล่าสุด" tone="warning">
                                        {{ $this->latestNextActionNote }}
                                    </x-ops.callout>
                                @endif

                                @if ($this->latestResolutionNote)
                                    <x-ops.callout title="สรุปการแก้ไขล่าสุด" tone="success">
                                        {{ $this->latestResolutionNote }}
                                    </x-ops.callout>
                                @endif
                            </div>
                        </div>
                    </section>
                @endif

                @if ($this->needsOwner || $this->isFollowUpOverdue)
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="65">
                        <div class="ops-card__body">
                            @if ($this->isFollowUpOverdue)
                                <x-ops.callout title="เลยกำหนดติดตามแล้ว" tone="danger">
                                    กำหนดติดตามวันที่ {{ $incident->follow_up_due_at?->format('d/m/Y') }} ผ่านไปแล้ว แต่รายงานนี้ยังไม่ถูกปิด กรุณาทบทวนผู้รับผิดชอบและการดำเนินการถัดไป
                                </x-ops.callout>
                            @elseif ($this->needsOwner)
                                <x-ops.callout title="ยังไม่มีผู้รับผิดชอบ" tone="warning">
                                    รายงานปัญหาที่ยังไม่ปิดนี้ยังไม่มีผู้ดูแลรับผิดชอบ กรุณากำหนดผู้รับผิดชอบในส่วนจัดการงานเพื่อให้การติดตามชัดเจน
                                </x-ops.callout>
                            @endif
                        </div>
                    </section>
                @endif

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="90">
                    <div class="ops-card__body">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">บริบทที่ถูกรายงาน</p>
                                <h2 class="ops-section-heading__title">รายละเอียดและหลักฐาน</h2>
                                <p class="ops-section-heading__body">นี่คือข้อมูลต้นฉบับของรายงานปัญหาของห้อง รวมถึงไฟล์แนบประกอบถ้ามี</p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(18rem,1fr)]">
                            <article class="ops-incident-panel" data-severity="{{ $incident->severity->value }}">
                                <p class="ops-incident-panel__eyebrow">รายละเอียด</p>
                                <h3 class="ops-incident-panel__title">สิ่งที่ถูกรายงาน</h3>
                                <p class="ops-incident-panel__body whitespace-pre-line">{{ $incident->description }}</p>
                            </article>

                            <article class="ops-incident-panel" data-severity="{{ $incident->severity->value }}">
                                <p class="ops-incident-panel__eyebrow">ห้องและหลักฐาน</p>
                                <h3 class="ops-incident-panel__title">{{ $incident->room?->name ?? 'ไม่มีข้อมูลห้อง' }}</h3>
                                <p class="ops-incident-panel__body">
                                    {{ $incident->equipment_reference
                                        ? __('Equipment reference: :equipment', ['equipment' => $incident->equipment_reference])
                                        : 'ไม่ได้ระบุอุปกรณ์หรือเครื่องที่เกี่ยวข้องไว้กับรายงานปัญหานี้' }}
                                </p>

                                <p class="ops-incident-panel__body">
                                    {{ $incident->attachment_path
                                        ? 'เปิดไฟล์แนบเมื่อจำเป็นต้องดูหลักฐานหรือข้อมูลประกอบเพิ่มเติมก่อนเปลี่ยนสถานะ'
                                        : 'รายงานนี้ถูกส่งมาโดยไม่มีไฟล์แนบ กรุณาใช้รายละเอียดและไทม์ไลน์ประกอบการตัดสินใจก่อนดำเนินการต่อ' }}
                                </p>

                                @if($incident->attachment_path)
                                    <div class="ops-incident-panel__actions">
                                        <a href="{{ route('incidents.attachment', $incident) }}" target="_blank" rel="noopener noreferrer" class="ops-button ops-button--secondary">
                                            เปิดไฟล์แนบ
                                        </a>
                                    </div>
                                @endif
                            </article>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="140">
                    <div class="ops-card__body">
                        <div class="ops-section-heading">
                            <div>
                            <p class="ops-section-heading__eyebrow">ประวัติการดำเนินงาน</p>
                            <h2 class="ops-section-heading__title">ลำดับเหตุการณ์</h2>
                            <p class="ops-section-heading__body">อ่านลำดับด้านล่างเพื่อดูว่ารายงานปัญหานี้เคลื่อนจากการแจ้งครั้งแรกมาถึงสถานะปัจจุบันได้อย่างไร</p>
                            </div>
                        </div>

                        <ul role="list" class="ops-timeline mt-6">
                            @foreach($incident->activities->sortByDesc('created_at') as $activity)
                                <li class="ops-timeline__item">
                                    <span class="ops-timeline__dot" aria-hidden="true"></span>
                                    <div class="ops-timeline__card">
                                        <div class="ops-incident-sequence__item">
                                            <div class="ops-incident-sequence__header">
                                                <div>
                                                    <p class="ops-incident-sequence__title">{{ $this->getActivityTypeLabel($activity->action_type) }}</p>
                                                    <p class="ops-incident-sequence__meta">
                                                        {{ $activity->actor?->name ?? 'ไม่ทราบ' }} · {{ $activity->created_at?->format('d/m/Y H:i') ?? 'ไม่ทราบเวลา' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <p class="ops-incident-sequence__body">{{ $activity->summary }}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            </div>

            <div class="ops-stack">
                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="40">
                    <div class="ops-card__body ops-incident-lane">
                        <div>
                            <p class="ops-section-heading__eyebrow">ส่วนกำหนดผู้รับผิดชอบ</p>
                            <h2 class="ops-incident-lane__title">กำหนดผู้รับผิดชอบและวันติดตาม</h2>
                            <p class="ops-incident-lane__body">ใช้ส่วนนี้เพื่อกำหนดความรับผิดชอบให้ชัด โดยยังไม่เปลี่ยนสถานะของรายงาน</p>
                        </div>

                        <form wire:submit="updateAccountability" class="space-y-4">
                            <div>
                                <label for="owner-id" class="ops-field-label">ผู้รับผิดชอบรายงานปัญหา</label>
                                <select id="owner-id" wire:model="ownerId" class="ops-control">
                                    <option value="">{{ __('Unowned') }}</option>
                                    @foreach($managementOwners as $owner)
                                        <option value="{{ $owner['id'] }}">{{ $owner['name'] }} ({{ __($owner['role']) }})</option>
                                    @endforeach
                                </select>
                                <p class="ops-field-help">เฉพาะผู้ดูแลระบบและผู้ดูแลห้องแล็บเท่านั้นที่เป็นผู้รับผิดชอบการติดตามรายงานปัญหาได้</p>
                                @error('ownerId') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="follow-up-due-at" class="ops-field-label">วันที่เป้าหมายในการติดตาม</label>
                                <input
                                    id="follow-up-due-at"
                                    type="date"
                                    wire:model="followUpDueAt"
                                    class="ops-control"
                                >
                                <p class="ops-field-help">นี่คือวันที่เป้าหมายภายในระบบสำหรับการทบทวนหรือการดำเนินการครั้งถัดไป ไม่ใช่ SLA อย่างเป็นทางการ</p>
                                @error('followUpDueAt') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="ops-divider-top flex justify-end pt-5">
                                <button type="submit" class="ops-button ops-button--secondary min-w-44">
                                    <span wire:loading.remove wire:target="updateAccountability">บันทึกข้อมูลการรับผิดชอบ</span>
                                    <span wire:loading wire:target="updateAccountability">กำลังบันทึก...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="80">
                    <div class="ops-card__body ops-incident-lane">
                        <div>
                            <p class="ops-section-heading__eyebrow">ส่วนอัปเดตสถานะ</p>
                            <h2 class="ops-incident-lane__title">อัปเดตสถานะอย่างมีบริบท</h2>
                            <p class="ops-incident-lane__body">ใช้ฟอร์มนี้เมื่อคุณต้องการให้ไทม์ไลน์แสดงการส่งต่องาน ความคืบหน้า หรือสรุปการแก้ไขอย่างชัดเจน</p>
                        </div>

                        <form wire:submit="updateStatus" class="space-y-4">
                            <div>
                                <label for="status" class="ops-field-label">อัปเดตสถานะ</label>
                                <select id="status" wire:model="status" class="ops-control">
                                    @foreach($statuses as $statusOption)
                                        <option value="{{ $statusOption }}">{{ __($statusOption) }}</option>
                                    @endforeach
                                </select>
                                @error('status') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="follow-up-note" class="ops-field-label">{{ $this->followUpNoteLabel }}</label>
                                <textarea
                                    id="follow-up-note"
                                    wire:model="followUpNote"
                                    rows="3"
                                    class="ops-control"
                                    placeholder="{{ $status === 'Resolved' ? 'สรุปว่าแก้ปัญหานี้ได้อย่างไร...' : 'เพิ่มบันทึกสั้น ๆ สำหรับผู้ที่เข้ามาตรวจทวนรายงานนี้ต่อ...' }}"
                                ></textarea>
                                <p class="ops-field-help">{{ $this->followUpNoteHelp }}</p>
                                @error('followUpNote') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="ops-divider-top flex justify-end pt-5">
                                <button type="submit" class="ops-button ops-button--primary min-w-44">
                                    <span wire:loading.remove wire:target="updateStatus">บันทึกการอัปเดตสถานะ</span>
                                    <span wire:loading wire:target="updateStatus">กำลังอัปเดต...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="140">
                    <div class="ops-card__body ops-incident-lane">
                        <div>
                            <p class="ops-section-heading__eyebrow">สรุปอ้างอิง</p>
                            <h2 class="ops-incident-lane__title">ข้อมูลสำคัญ</h2>
                            <p class="ops-incident-lane__body">ใช้ข้อมูลชุดนี้ประกอบการตัดสินใจ เพื่อให้การอัปเดตครั้งถัดไปยังยึดตามบริบทของรายงานปัญหาที่แจ้งเข้ามาจริง</p>
                        </div>

                        <div class="ops-stat-grid">
                            <x-ops.stat-card kicker="หมวดหมู่" :value="__($incident->category->value)" />
                            <x-ops.stat-card kicker="ห้อง" :value="$incident->room?->name ?? 'ยังไม่กำหนด'" />
                            <x-ops.stat-card kicker="อุปกรณ์/เครื่องที่เกี่ยวข้อง" :value="$incident->equipment_reference ?? 'ไม่ได้ระบุ'" />
                            <x-ops.stat-card kicker="ผู้รับผิดชอบ" :value="$incident->owner?->name ?? 'ยังไม่มีผู้รับผิดชอบ'" />
                            <x-ops.stat-card kicker="กำหนดติดตาม" :value="$incident->follow_up_due_at?->format('d/m/Y') ?? 'ยังไม่กำหนด'" />
                            <x-ops.stat-card kicker="เวลาที่แก้ไขแล้ว" :value="$incident->resolved_at?->format('d/m/Y H:i') ?? 'ยังไม่ปิดรายงาน'" />
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
