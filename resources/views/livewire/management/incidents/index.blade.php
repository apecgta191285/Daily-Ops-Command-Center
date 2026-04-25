<div class="ops-screen ops-screen--incident-queue">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Management queue') }}</p>
                <h2 class="ops-page__title">{{ __('Incident List') }}</h2>
                <p class="ops-page-intro__body">
                    ตรวจสอบรายงานปัญหาที่เข้ามา กรองคิวตามเงื่อนไขที่ต้องการ และเข้าไปดูรายละเอียดเพื่อติดตามต่อได้อย่างรวดเร็ว
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Live triage') }}</span>
                    <span class="ops-shell-chip">{{ __('Filterable queue') }}</span>
                    <span class="ops-shell-chip">{{ __('Management follow-up') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('dashboard') }}" class="ops-button ops-button--secondary">
                    {{ __('Back to dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-card__body">
                <div class="grid gap-4 md:grid-cols-3">
                    <div>
                        <label for="status" class="ops-field-label">สถานะ</label>
                        <select id="status" wire:model.live="status" class="ops-control">
                            <option value="">ทุกสถานะ</option>
                            @foreach($statuses as $statusOption)
                                <option value="{{ $statusOption }}">{{ __($statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="category" class="ops-field-label">หมวดหมู่</label>
                        <select id="category" wire:model.live="category" class="ops-control">
                            <option value="">ทุกหมวดหมู่</option>
                            @foreach($categories as $categoryOption)
                                <option value="{{ $categoryOption }}">{{ __($categoryOption) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="severity" class="ops-field-label">ความรุนแรง</label>
                        <select id="severity" wire:model.live="severity" class="ops-control">
                            <option value="">ทุกระดับความรุนแรง</option>
                            @foreach($severities as $severityOption)
                                <option value="{{ $severityOption }}">{{ __($severityOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3">
                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="unresolved" class="ops-choice__control">
                        <span>เฉพาะปัญหาที่ยังไม่ปิด</span>
                    </label>

                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="stale" class="ops-choice__control">
                        <span>เฉพาะปัญหาที่ค้างเกิน {{ $this->staleThresholdDays }} วัน</span>
                    </label>

                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="unowned" class="ops-choice__control">
                        <span>เฉพาะปัญหาที่ไม่มีผู้รับผิดชอบ</span>
                    </label>

                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="mine" class="ops-choice__control">
                        <span>เฉพาะปัญหาที่ฉันรับผิดชอบ</span>
                    </label>

                    <label class="ops-choice">
                        <input type="checkbox" wire:model.live="overdue" class="ops-choice__control">
                        <span>เฉพาะรายการติดตามเกินกำหนด</span>
                    </label>

                    @if ($status !== '' || $category !== '' || $severity !== '' || $unresolved || $stale || $unowned || $mine || $overdue)
                        <button type="button" wire:click="clearFilters" class="ops-button ops-button--secondary">
                            ล้างตัวกรอง
                        </button>
                    @endif
                </div>
            </div>
        </section>

        @if ($unresolved || $stale || $unowned || $mine || $overdue)
            <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                <div class="ops-card__body ops-text-muted flex flex-wrap items-center gap-3 text-sm">
                    <span class="ops-text-heading font-medium">ตัวกรองที่กำลังใช้งาน:</span>
                    @if ($unresolved)
                        <span class="ops-chip ops-chip--info">ยังไม่ปิดเท่านั้น</span>
                    @endif
                    @if ($stale)
                        <span class="ops-chip ops-chip--warning">ค้างเกิน {{ $this->staleThresholdDays }} วัน</span>
                    @endif
                    @if ($unowned)
                        <span class="ops-chip ops-chip--warning">ไม่มีผู้รับผิดชอบ</span>
                    @endif
                    @if ($mine)
                        <span class="ops-chip ops-chip--info">ฉันรับผิดชอบ</span>
                    @endif
                    @if ($overdue)
                        <span class="ops-chip ops-chip--danger">ติดตามเกินกำหนด</span>
                    @endif
                </div>
            </section>
        @endif

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="{{ ($unresolved || $stale || $unowned || $mine || $overdue) ? '120' : '80' }}">
            <div class="ops-card__body">
                @if($incidents->count() === 0)
                    <x-ops.empty-state
                        title="ไม่พบรายงานปัญหาตามตัวกรองที่เลือก"
                        body="ลองล้างตัวกรองบางส่วน หรือรอให้มีรายงานปัญหาใหม่ที่ตรงกับมุมมองนี้"
                    />
                @else
                    <div class="ops-table-wrap">
                        <table class="ops-table ops-table--responsive min-w-full">
                            <thead>
                                <tr>
                                    <th>หัวข้อปัญหา</th>
                                    <th>ห้อง</th>
                                    <th>หมวดหมู่</th>
                                    <th>ความรุนแรง</th>
                                    <th>สถานะ</th>
                                    <th>ผู้รับผิดชอบ</th>
                                    <th>กำหนดติดตาม</th>
                                    <th>สิ่งที่ต้องระวัง</th>
                                    <th>ผู้แจ้ง</th>
                                    <th>การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incidents as $index => $incident)
                                    <tr class="ops-table__row" data-motion="scale-soft" data-motion-delay="{{ 140 + ($index * 15) }}">
                                        <td data-label="หัวข้อปัญหา" class="ops-text-heading px-4 py-4 text-sm font-medium">{{ $incident->title }}</td>
                                        <td data-label="ห้อง" class="ops-text-muted px-4 py-4 text-sm">
                                            <div class="space-y-1">
                                                <span>{{ $incident->room?->name ?? __('No room') }}</span>
                                                @if ($incident->equipment_reference)
                                                    <p class="ops-inline-note">{{ $incident->equipment_reference }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="หมวดหมู่" class="ops-text-muted px-4 py-4 text-sm">{{ __($incident->category->value) }}</td>
                                        <td data-label="ความรุนแรง" class="px-4 py-4 text-sm">
                                            <x-incidents.severity-badge :severity="$incident->severity" />
                                        </td>
                                        <td data-label="สถานะ" class="px-4 py-4 text-sm">
                                            <x-incidents.status-badge :status="$incident->status" />
                                        </td>
                                        <td data-label="ผู้รับผิดชอบ" class="ops-text-muted px-4 py-4 text-sm">
                                            @if ($incident->owner)
                                                <span class="ops-text-heading font-medium">{{ $incident->owner->name }}</span>
                                            @elseif ($incident->status !== \App\Domain\Incidents\Enums\IncidentStatus::Resolved)
                                                    <span class="ops-badge ops-badge--warning">ไม่มีผู้รับผิดชอบ</span>
                                            @else
                                                <span class="text-xs">-</span>
                                            @endif
                                        </td>
                                        <td data-label="กำหนดติดตาม" class="ops-text-muted px-4 py-4 text-sm">
                                            @if ($incident->follow_up_due_at)
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span>{{ $incident->follow_up_due_at->format('d/m/Y') }}</span>
                                                    @if ($incident->is_overdue_follow_up)
                                                        <span class="ops-badge ops-badge--danger">เกินกำหนด</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs">ยังไม่กำหนด</span>
                                            @endif
                                        </td>
                                        <td data-label="สิ่งที่ต้องระวัง" class="ops-text-muted px-4 py-4 text-sm">
                                            @if ($incident->is_overdue_follow_up)
                                                <span class="ops-badge ops-badge--danger">ติดตามเกินกำหนด</span>
                                            @elseif ($incident->is_stale_for_attention)
                                                <span class="ops-badge ops-badge--warning">ค้างนาน</span>
                                            @elseif ($incident->status !== \App\Domain\Incidents\Enums\IncidentStatus::Resolved && $incident->owner_id === null)
                                                <span class="ops-badge ops-badge--warning">ต้องกำหนดผู้รับผิดชอบ</span>
                                            @else
                                                <span class="text-xs">-</span>
                                            @endif
                                        </td>
                                        <td data-label="ผู้แจ้ง" class="ops-text-muted px-4 py-4 text-sm">{{ $incident->creator?->name ?? 'ไม่ทราบ' }}</td>
                                        <td data-label="การดำเนินการ" class="px-4 py-4 text-right text-sm">
                                            <a href="{{ route('incidents.show', $incident) }}" class="ops-button ops-button--secondary">
                                                ดูรายละเอียด
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($incidents->hasPages())
                        <div class="mt-6">
                            {{ $incidents->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </div>
</div>
