<div class="ops-screen ops-screen--incident-report">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Operational reporting') }}</p>
                <h2 class="ops-page__title">{{ __('Incident Report') }}</h2>
                <p class="ops-page-intro__body">
                    สรุปรายงานปัญหาตามช่วงเวลา ห้อง หมวดหมู่ และหมวดหมู่ย่อย เพื่อใช้ดู pattern ของปัญหาและเตรียมข้อมูลสำหรับการนำเสนอหรือปรับปรุงงานจริง
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Date range') }}</span>
                    <span class="ops-shell-chip">{{ __('Room-aware') }}</span>
                    <span class="ops-shell-chip">{{ __('Category + subcategory') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ $this->exportUrl }}" class="ops-button ops-button--primary">
                    {{ __('Export CSV') }}
                </a>
                <a href="{{ route('dashboard') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to dashboard') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">ช่วงรายงาน</p>
                    <h3 class="ops-hero__title">{{ $this->dateRangeLabel }}</h3>
                    <p class="ops-hero__lead">
                        รายงานนี้นับจากวันที่สร้าง incident เป็นหลัก เหมาะสำหรับดูปริมาณงานเข้า สัดส่วนปัญหา และพื้นที่ที่เกิดปัญหาซ้ำในช่วงเวลาที่เลือก
                    </p>
                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $report['summary']['total_count'] }} รายการ</span>
                        <span class="ops-shell-chip">{{ $report['summary']['rooms_impacted_count'] }} ห้องที่เกี่ยวข้อง</span>
                        <span class="ops-shell-chip">{{ $report['summary']['high_severity_count'] }} รายการความรุนแรงสูง</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">รายการทั้งหมด</p>
                        <p class="ops-hero__aside-value">{{ $report['summary']['total_count'] }}</p>
                        <p class="ops-hero__aside-copy">จำนวน incident ที่ตรงกับตัวกรองปัจจุบัน</p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Unresolved') }}</p>
                            <p class="ops-authoring-metric__value">{{ $report['summary']['unresolved_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Resolved') }}</p>
                            <p class="ops-authoring-metric__value">{{ $report['summary']['resolved_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Rooms') }}</p>
                            <p class="ops-authoring-metric__value">{{ $report['summary']['rooms_impacted_count'] }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">ตัวกรองรายงาน</p>
                    <h3 class="ops-section-heading__title">เลือกช่วงเวลาและมุมมองข้อมูล</h3>
                    <p class="ops-section-heading__body">เริ่มจากช่วงเวลา แล้วค่อยแคบลงด้วยห้อง หมวดหมู่ย่อย สถานะ หรือความรุนแรงตามคำถามที่ต้องการตอบ</p>
                </div>
            </div>

            <div class="ops-card__body space-y-5">
                <div class="flex flex-wrap gap-2">
                    <button type="button" wire:click="applyPreset('7d')" class="ops-button ops-button--secondary">7 วันล่าสุด</button>
                    <button type="button" wire:click="applyPreset('30d')" class="ops-button ops-button--secondary">30 วันล่าสุด</button>
                    <button type="button" wire:click="applyPreset('month')" class="ops-button ops-button--secondary">เดือนนี้</button>
                    <button type="button" wire:click="clearFilters" class="ops-button ops-button--secondary">ล้างตัวกรองข้อมูล</button>
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
                        <label for="room_id" class="ops-field-label">ห้อง</label>
                        <select id="room_id" wire:model.live="roomId" class="ops-control">
                            <option value="">ทุกห้อง</option>
                            @foreach($rooms as $roomOption)
                                <option value="{{ $roomOption['id'] }}">{{ $roomOption['name'] }} ({{ $roomOption['code'] }})</option>
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
                        <label for="subcategory" class="ops-field-label">หมวดหมู่ย่อย</label>
                        <select id="subcategory" wire:model.live="subcategory" class="ops-control">
                            <option value="">ทุกหมวดหมู่ย่อย</option>
                            @foreach($subcategories as $subcategoryOption)
                                <option value="{{ $subcategoryOption }}">{{ __($subcategoryOption) }}</option>
                            @endforeach
                        </select>
                    </div>

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
                        <label for="severity" class="ops-field-label">ความรุนแรง</label>
                        <select id="severity" wire:model.live="severity" class="ops-control">
                            <option value="">ทุกระดับ</option>
                            @foreach($severities as $severityOption)
                                <option value="{{ $severityOption }}">{{ __($severityOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(24rem,0.9fr)]">
            <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                <div class="ops-section-heading">
                    <div>
                        <p class="ops-section-heading__eyebrow">หมวดหมู่ย่อย</p>
                        <h3 class="ops-section-heading__title">ต้นตอปัญหาที่เกิดบ่อย</h3>
                        <p class="ops-section-heading__body">ใช้ส่วนนี้ตอบว่า incident ส่วนใหญ่เป็นชนิดใด ไม่ใช่แค่หมวดกว้าง ๆ</p>
                    </div>
                </div>
                <div class="ops-card__body">
                    <div class="ops-table-wrap">
                        <table class="ops-table ops-table--responsive min-w-full">
                            <thead>
                                <tr>
                                    <th>หมวดหมู่ย่อย</th>
                                    <th>หมวดหลัก</th>
                                    <th>ทั้งหมด</th>
                                    <th>ยังไม่ปิด</th>
                                    <th>สูง</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['subcategory_rows'] as $row)
                                    <tr class="ops-table__row">
                                        <td data-label="หมวดหมู่ย่อย" class="ops-text-heading px-4 py-4 text-sm font-medium">{{ __($row['subcategory']) }}</td>
                                        <td data-label="หมวดหลัก" class="ops-text-muted px-4 py-4 text-sm">{{ __($row['category']) }}</td>
                                        <td data-label="ทั้งหมด" class="px-4 py-4 text-sm">{{ $row['total_count'] }}</td>
                                        <td data-label="ยังไม่ปิด" class="px-4 py-4 text-sm">{{ $row['unresolved_count'] }}</td>
                                        <td data-label="สูง" class="px-4 py-4 text-sm">{{ $row['high_severity_count'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8">
                                            <x-ops.empty-state title="ยังไม่มีข้อมูลในช่วงเวลานี้" body="ลองขยายช่วงเวลา หรือปลดตัวกรองบางส่วนเพื่อดูภาพรวมกว้างขึ้น" />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="100">
                <div class="ops-section-heading">
                    <div>
                        <p class="ops-section-heading__eyebrow">ห้องที่ได้รับผลกระทบ</p>
                        <h3 class="ops-section-heading__title">จัดอันดับตามจำนวน incident</h3>
                        <p class="ops-section-heading__body">เหมาะสำหรับเลือกห้องที่ควรตรวจเชิงลึกหรือจัดสรรทรัพยากรเพิ่ม</p>
                    </div>
                </div>
                <div class="ops-card__body space-y-3">
                    @forelse($report['room_rows'] as $row)
                        <article class="ops-governance-card {{ $row['high_severity_count'] > 0 ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }}">
                            <div class="ops-governance-card__header">
                                <div>
                                    <p class="ops-admin-item__eyebrow">{{ $row['room_code'] ?? __('No code') }}</p>
                                    <h4 class="ops-admin-item__title">{{ $row['room_name'] }}</h4>
                                </div>
                                <span class="ops-chip {{ $row['unresolved_count'] > 0 ? 'ops-chip--warning' : 'ops-chip--success' }}">{{ $row['total_count'] }} รายการ</span>
                            </div>
                            <div class="ops-governance-card__stats">
                                <div>
                                    <p class="ops-admin-item__meta-label">ยังไม่ปิด</p>
                                    <p class="ops-admin-item__meta-value">{{ $row['unresolved_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">ปิดแล้ว</p>
                                    <p class="ops-admin-item__meta-value">{{ $row['resolved_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">รุนแรงสูง</p>
                                    <p class="ops-admin-item__meta-value">{{ $row['high_severity_count'] }}</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <x-ops.empty-state title="ยังไม่มีห้องในรายงานนี้" body="ไม่มี incident ที่ตรงกับตัวกรองปัจจุบัน" />
                    @endforelse
                </div>
            </section>
        </div>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="130">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">รายการล่าสุดในช่วงรายงาน</p>
                    <h3 class="ops-section-heading__title">incident ที่ใช้ประกอบตัวเลข</h3>
                    <p class="ops-section-heading__body">ตารางนี้ช่วยตรวจทานว่าตัวเลข aggregate เกิดจากรายการจริงใดบ้าง</p>
                </div>
            </div>
            <div class="ops-card__body">
                <div class="ops-table-wrap">
                    <table class="ops-table ops-table--responsive min-w-full">
                        <thead>
                            <tr>
                                <th>หัวข้อ</th>
                                <th>เวลา</th>
                                <th>ห้อง</th>
                                <th>หมวด</th>
                                <th>สถานะ</th>
                                <th>การดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($report['recent_incidents'] as $incident)
                                <tr class="ops-table__row">
                                    <td data-label="หัวข้อ" class="ops-text-heading px-4 py-4 text-sm font-medium">{{ $incident['title'] }}</td>
                                    <td data-label="เวลา" class="ops-text-muted px-4 py-4 text-sm">{{ $incident['created_at'] }}</td>
                                    <td data-label="ห้อง" class="ops-text-muted px-4 py-4 text-sm">{{ $incident['room_name'] }}</td>
                                    <td data-label="หมวด" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ __($incident['category']) }}
                                        @if($incident['subcategory'])
                                            <p class="ops-inline-note">{{ __($incident['subcategory']) }}</p>
                                        @endif
                                    </td>
                                    <td data-label="สถานะ" class="px-4 py-4 text-sm">{{ __($incident['status']) }}</td>
                                    <td data-label="การดำเนินการ" class="px-4 py-4 text-right text-sm">
                                        <a href="{{ $incident['url'] }}" class="ops-button ops-button--secondary" wire:navigate>ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8">
                                        <x-ops.empty-state title="ยังไม่มี incident ในช่วงนี้" body="รายงานจะเริ่มแสดงเมื่อมีรายการที่ตรงกับตัวกรอง" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
