<x-layouts::app :title="__('Dashboard')">
    @php
        $unresolvedCount = ($incidentCounts['Open'] ?? 0) + ($incidentCounts['In Progress'] ?? 0);
        $totalVisibleIncidents = max(array_sum($incidentCounts), 1);
        $openIncidentShare = (int) round((($incidentCounts['Open'] ?? 0) / $totalVisibleIncidents) * 100);
        $inProgressIncidentShare = (int) round((($incidentCounts['In Progress'] ?? 0) / $totalVisibleIncidents) * 100);
        $resolvedIncidentShare = (int) round((($incidentCounts['Resolved'] ?? 0) / $totalVisibleIncidents) * 100);
        $hotspotMaxCount = max(array_map(static fn (array $hotspot): int => $hotspot['unresolvedCount'], $hotspotCategories ?: [['unresolvedCount' => 1]]));
        $checklistTrendTone = match ($checklistTrend['direction']) {
            'up' => 'ops-trend-pill--up',
            'down' => 'ops-trend-pill--down',
            default => 'ops-trend-pill--flat',
        };
        $incidentTrendTone = match ($incidentIntakeTrend['direction']) {
            'up' => 'ops-trend-pill--down',
            'down' => 'ops-trend-pill--up',
            default => 'ops-trend-pill--flat',
        };
        $checklistTrendLabel = match ($checklistTrend['direction']) {
            'up' => 'ดีขึ้น',
            'down' => 'ต่ำกว่าเมื่อวาน',
            default => 'ทรงตัว',
        };
        $incidentTrendLabel = match ($incidentIntakeTrend['direction']) {
            'up' => 'รับปัญหามากขึ้น',
            'down' => 'รับปัญหาน้อยลง',
            default => 'จำนวนปัญหาทรงตัว',
        };
        $scopeLaneIncompleteCount = collect($scopeChecklistLanes)->filter(fn (array $lane) => in_array($lane['state'], ['not_started', 'in_progress'], true))->count();
        $ownershipActions = $ownershipPressure['actions'] ?? [];
        $workboardToneClass = ($workboard['state'] ?? 'attention') === 'calm'
            ? 'ops-workboard--calm'
            : 'ops-workboard--attention';
        $ownershipBucketToneClass = ($ownershipBuckets['state'] ?? 'active') === 'calm'
            ? 'ops-bucket-board--calm'
            : 'ops-bucket-board--active';
        $recentHistoryToneClass = match ($recentHistoryContext['state'] ?? 'watch') {
            'calm' => 'ops-context-board--calm',
            'unstable' => 'ops-context-board--unstable',
            default => 'ops-context-board--watch',
        };
    @endphp

    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Lab supervisor workboard') }}</p>
                <h2 class="ops-page__title">{{ __('Dashboard') }}</h2>
                <p class="ops-page-intro__body">
                    ติดตามว่าห้องใดยังต้องตรวจเช็ก ห้องใดยังมีปัญหาค้างอยู่ และจุดใดกำลังมีภาระติดตามเพิ่มขึ้นสำหรับทีมดูแลห้องแล็บ
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Today-first workboard') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope lane truth') }}</span>
                    <span class="ops-shell-chip">{{ __('Checklist momentum') }}</span>
                    <span class="ops-shell-chip">{{ __('Incident hotspots') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary">
                    {{ __('Review incidents') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="ops-screen ops-screen--dashboard flex h-full w-full flex-1 flex-col gap-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">งานปฏิบัติการแบบยึดห้องเป็นศูนย์กลาง</p>
                    <h3 class="ops-hero__title">กระดานงานห้องแล็บของวันนี้</h3>
                    <p class="ops-hero__lead">
                        ใช้หน้านี้ดูว่าห้องใดยังต้องตรวจ ห้องใดยังมีปัญหาค้างอยู่ และทีมกำลังตามงานประจำวันของห้องแล็บต่าง ๆ ได้ทันหรือไม่
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">กระดานงานประจำวันนี้</span>
                        <span class="ops-shell-chip">สถานะรอบตรวจ</span>
                        <span class="ops-shell-chip">ภาระติดตาม</span>
                        <span class="ops-shell-chip">แนวโน้มรายการตรวจเช็ก</span>
                        <span class="ops-shell-chip">จุดปัญหาของห้อง</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">ภาพรวมวันนี้</p>
                        <p class="ops-hero__aside-value">{{ $completionRate }}%</p>
                        <p class="ops-hero__aside-copy">
                            ความสำเร็จของรายการตรวจเช็กคำนวณจาก {{ $submittedTodayRuns }} รอบที่ส่งแล้ว จาก {{ $todayRuns }} รอบที่คาดว่าต้องมีในวันนี้
                        </p>
                    </div>

                    <div class="ops-glance-grid--hero">
                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">คิวปัญหาที่ยังไม่ปิด</p>
                            <p class="ops-glance-card__value">{{ $unresolvedCount }}</p>
                            <p class="ops-glance-card__meta">รายงานปัญหาที่เปิดใหม่หรือกำลังดำเนินการและยังรอการปิดงานจากผู้ดูแล</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">รอบตรวจที่ยังไม่ครบ</p>
                            <p class="ops-glance-card__value">{{ $scopeLaneIncompleteCount }}</p>
                            <p class="ops-glance-card__meta">การตรวจเปิดห้อง ระหว่างวัน หรือปิดห้อง ที่ยังต้องดำเนินการในห้องอย่างน้อยหนึ่งห้องวันนี้</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">ปัญหาที่ไม่มีผู้รับผิดชอบ</p>
                            <p class="ops-glance-card__value">{{ $ownershipPressure['unownedCount'] }}</p>
                            <p class="ops-glance-card__meta">รายงานปัญหาที่ยังไม่ปิดและยังไม่มีผู้ดูแลรับผิดชอบ</p>
                        </div>

                        <div class="ops-glance-card">
                            <p class="ops-glance-card__label">ติดตามเกินกำหนด</p>
                            <p class="ops-glance-card__value">{{ $ownershipPressure['overdueCount'] }}</p>
                            <p class="ops-glance-card__meta">รายงานปัญหาที่ยังไม่ปิดและเลยวันที่ควรทบทวนแล้ว</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <div class="ops-command-grid ops-command-grid--dashboard">
            <div class="ops-stack">
                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="20">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">กระดานงานของห้องวันนี้</p>
                            <h2 class="ops-section-heading__title">ภาพรวมการทำงาน</h2>
                            <p class="ops-section-heading__body">สรุปอย่างย่อว่าวันนี้ยังมีงานที่ต้องจัดการ หรืออยู่ในสถานะที่ควบคุมได้แล้ว</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <section class="ops-workboard {{ $workboardToneClass }}">
                            <div class="ops-workboard__summary">
                                <div>
                                    <p class="ops-workboard__eyebrow">
                                        {{ ($workboard['state'] ?? 'attention') === 'calm' ? 'สถานะห้องแล็บอยู่ในภาวะปกติ' : 'ภาระติดตามของห้องแล็บยังมีอยู่' }}
                                    </p>
                                    <h3 class="ops-workboard__title">{{ $workboard['headline'] }}</h3>
                                    <p class="ops-workboard__body">{{ $workboard['body'] }}</p>
                                </div>

                                <div class="ops-workboard__metrics">
                                    <div class="ops-workboard__metric">
                                        <span class="ops-workboard__metric-label">รอบตรวจที่ค้างอยู่</span>
                                        <strong class="ops-workboard__metric-value">{{ $workboard['pendingLaneCount'] }}</strong>
                                    </div>
                                    <div class="ops-workboard__metric">
                                        <span class="ops-workboard__metric-label">สัญญาณที่ต้องติดตาม</span>
                                        <strong class="ops-workboard__metric-value">{{ $workboard['attentionCount'] }}</strong>
                                    </div>
                                    <div class="ops-workboard__metric">
                                        <span class="ops-workboard__metric-label">รอบตรวจที่ส่งแล้ว</span>
                                        <strong class="ops-workboard__metric-value">{{ $workboard['submittedLaneCount'] }}</strong>
                                    </div>
                                </div>
                            </div>

                            @if (($workboard['lanes'] ?? []) !== [])
                                <div class="ops-workboard__lane-grid">
                                    @foreach ($workboard['lanes'] as $lane)
                                        @php
                                            $laneToneClass = match ($lane['state']) {
                                                'unavailable' => 'ops-workboard__lane--danger',
                                                'not_started', 'in_progress' => 'ops-workboard__lane--warning',
                                                default => 'ops-workboard__lane--neutral',
                                            };
                                        @endphp

                                        <article class="ops-workboard__lane {{ $laneToneClass }}">
                                            <div class="ops-workboard__lane-header">
                                                <div>
                                                    <p class="ops-workboard__lane-scope">{{ $lane['scope'] }}</p>
                                                    <p class="ops-workboard__lane-title">{{ $lane['template_title'] ?? __('No active template') }}</p>
                                                </div>

                                                <span class="ops-chip {{ $lane['state'] === 'unavailable' ? '' : 'ops-chip--warning' }}">
                                                    {{ $lane['state_label'] }}
                                                </span>
                                            </div>

                                            <p class="ops-workboard__lane-copy">{{ $lane['summary'] }}</p>

                                            <div class="ops-workboard__lane-meta">
                                                <span>ส่งแล้ว {{ $lane['completion_percentage'] }}%</span>
                                                <span>{{ $lane['submitted_runs'] }}/{{ $lane['total_runs'] }} รอบส่งแล้ว</span>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="ops-workboard__calm">
                                    <p class="ops-workboard__calm-title">วันนี้ไม่มีรอบตรวจที่ค้างอยู่แล้ว</p>
                                    <p class="ops-workboard__calm-body">
                                        รอบตรวจที่ใช้งานอยู่ถูกส่งครบแล้ว ผู้ดูแลจึงสามารถโฟกัสกับคิวปัญหาและการทบทวนประวัติได้ต่อ
                                    </p>
                                </div>
                            @endif

                            @if (($workboard['actions'] ?? []) !== [])
                                <div class="ops-workboard__actions">
                                    @foreach ($workboard['actions'] as $action)
                                        @if ($action['url'])
                                            <a href="{{ $action['url'] }}" class="ops-button {{ $action['tone'] === 'primary' ? 'ops-button--primary' : 'ops-button--secondary' }}">
                                                {{ $action['label'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">ลำดับงานสำคัญ</p>
                            <h2 class="ops-section-heading__title">สิ่งที่ต้องดูวันนี้</h2>
                            <p class="ops-section-heading__body">สัญญาณรวดเร็วสำหรับสิ่งที่ผู้ดูแลควรตรวจดูก่อน</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        @if ($attentionItems === [])
                            <x-ops.empty-state
                                title="ขณะนี้ยังไม่มีปัญหาเร่งด่วนของห้อง"
                                body="สัญญาณบนแดชบอร์ดตอนนี้ยังไม่พบปัญหาเสี่ยงสูงที่เกินกำหนด หรือความล่าช้าของรายการตรวจเช็กที่เกินจากงานประจำวัน"
                            />
                        @else
                            <div class="ops-signal-grid">
                                @foreach ($attentionItems as $item)
                                    @php
                                        $toneClass = match ($item['tone']) {
                                            'danger' => 'ops-signal-card--danger',
                                            'warning' => 'ops-signal-card--warning',
                                            default => 'ops-signal-card--neutral',
                                        };
                                    @endphp

                                    <article class="ops-signal-card {{ $toneClass }}">
                                        <div class="ops-signal-card__header">
                                            <div>
                                                <h3 class="ops-signal-card__title">{{ $item['title'] }}</h3>
                                                <p class="ops-signal-card__body">{{ $item['description'] }}</p>
                                            </div>

                                            <div class="text-right">
                                                <p class="ops-signal-card__count">{{ $item['count'] }}</p>
                                                <p class="ops-eyebrow-label">รายการ</p>
                                            </div>
                                        </div>

                                        @if ($item['url'] && $item['actionLabel'])
                                            <div class="ops-signal-card__footer">
                                                <a href="{{ $item['url'] }}" class="ops-button ops-button--secondary">
                                                    {{ $item['actionLabel'] }}
                                                </a>
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">ความครอบคลุมของการทำงาน</p>
                            <h2 class="ops-section-heading__title">รายการตรวจเช็กแยกตามรอบเวลา</h2>
                            <p class="ops-section-heading__body">ตรวจดูว่ารอบเปิดห้อง ระหว่างวัน และปิดห้อง ถูกตั้งค่าและมีความคืบหน้าอยู่จริงในวันนี้หรือไม่</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-signal-grid">
                            @foreach ($scopeChecklistLanes as $lane)
                                @php
                                    $toneClass = match ($lane['state']) {
                                        'unavailable' => 'ops-signal-card--danger',
                                        'not_started', 'in_progress' => 'ops-signal-card--warning',
                                        default => 'ops-signal-card--neutral',
                                    };
                                    $stateLabel = match ($lane['state']) {
                                        'unavailable' => 'ไม่มีแม่แบบใช้งานจริง',
                                        'not_started' => 'ยังไม่เริ่ม',
                                        'in_progress' => 'กำลังดำเนินการ',
                                        default => 'ส่งแล้ว',
                                    };
                                @endphp

                                <article class="ops-signal-card {{ $toneClass }}">
                                    <div class="ops-signal-card__header">
                                        <div>
                                            <h3 class="ops-signal-card__title">{{ $lane['scope'] }}</h3>
                                            <p class="ops-signal-card__body">
                                                {{ $lane['template_title'] ?? __('No active template') }}
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="ops-signal-card__count">{{ $lane['completion_percentage'] }}%</p>
                                            <p class="ops-eyebrow-label">ส่งแล้ว</p>
                                        </div>
                                    </div>

                                    <div class="ops-signal-card__body">
                                        @if ($lane['state'] === 'unavailable')
                                            ผู้ดูแลยังตรวจรอบเวลานี้ไม่ได้ เพราะยังไม่มีแม่แบบที่เปิดใช้งานอยู่สำหรับรอบนี้
                                        @elseif ($lane['state'] === 'not_started')
                                            มีแม่แบบที่ใช้งานจริงแล้ว แต่ผู้ตรวจห้องยังไม่ได้เริ่มรอบนี้ในวันนี้
                                        @elseif ($lane['state'] === 'in_progress')
                                            ผู้ตรวจห้องเริ่มรอบนี้แล้ว แต่ยังส่งผลไม่ครบทุกห้อง
                                        @else
                                            รอบตรวจทั้งหมดของรอบเวลานี้ในวันนี้ถูกส่งแล้ว
                                        @endif
                                    </div>

                                    <div class="ops-signal-card__footer flex items-center justify-between gap-3">
                                        <span class="ops-chip {{ $lane['state'] === 'submitted' ? 'ops-chip--success' : ($lane['state'] === 'unavailable' ? '' : 'ops-chip--warning') }}">
                                            {{ $stateLabel }}
                                        </span>
                                        <span class="ops-text-muted text-xs">
                                            {{ $lane['submitted_runs'] }}/{{ $lane['total_runs'] }} ส่งแล้ว
                                        </span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </section>

                <div class="ops-stat-grid" data-motion-group data-stagger-base="70" data-stagger-unit="40" data-stagger-max="220">
                    <x-ops.stat-card
                        kicker="ความสำเร็จของรายการตรวจเช็กวันนี้"
                        :value="$completionRate.'%'"
                        :meta="$submittedTodayRuns.' จาก '.$todayRuns.' รอบตรวจที่ส่งแล้ว'"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$completionRate" :size="58" />
                                <span class="ops-arc-wrapper__label">{{ $completionRate }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>

                    <x-ops.stat-card
                        kicker="รายงานปัญหาเปิดใหม่"
                        :value="$incidentCounts['Open']"
                        meta="รายงานปัญหาที่ยังรอการดำเนินการ"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$openIncidentShare" :size="58" tone="danger" />
                                <span class="ops-arc-wrapper__label">{{ $openIncidentShare }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>

                    <x-ops.stat-card
                        kicker="กำลังดำเนินการ"
                        :value="$incidentCounts['In Progress']"
                        meta="รายงานปัญหาที่กำลังดำเนินการอยู่"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$inProgressIncidentShare" :size="58" tone="warning" />
                                <span class="ops-arc-wrapper__label">{{ $inProgressIncidentShare }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>

                    <x-ops.stat-card
                        kicker="แก้ไขแล้ว"
                        :value="$incidentCounts['Resolved']"
                        meta="รายงานปัญหาที่ปิดแล้วในข้อมูลปัจจุบัน"
                        data-motion="scale-soft"
                    >
                        <x-slot:visual>
                            <div class="ops-arc-wrapper">
                                <x-ops.arc :value="$resolvedIncidentShare" :size="58" tone="success" />
                                <span class="ops-arc-wrapper__label">{{ $resolvedIncidentShare }}%</span>
                            </div>
                        </x-slot:visual>
                    </x-ops.stat-card>
                </div>

                <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">คิวปัจจุบัน</p>
                            <h2 class="ops-section-heading__title">รายงานปัญหาล่าสุด</h2>
                            <p class="ops-section-heading__body">รายงานปัญหาล่าสุด 5 รายการจากข้อมูลปัจจุบัน</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        @if ($recentIncidents->isEmpty())
                            <x-ops.empty-state
                                title="ยังไม่มีรายงานปัญหาในตอนนี้"
                                body="เมื่อมีการแจ้งปัญหาจากผู้ตรวจห้อง รายการล่าสุดจะแสดงที่นี่เพื่อให้ผู้ดูแลติดตามต่อจากแดชบอร์ดได้"
                            />
                        @else
                            <div class="ops-table-wrap">
                                <table class="ops-table ops-table--responsive min-w-full">
                                    <thead>
                                        <tr>
                                            <th>หัวข้อปัญหา</th>
                                            <th>ห้อง</th>
                                            <th>สถานะ</th>
                                            <th>ความรุนแรง</th>
                                            <th>รายละเอียด</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentIncidents as $incident)
                                            <tr class="ops-table__row">
                                                <td data-label="หัวข้อปัญหา" class="ops-text-heading px-4 py-4 text-sm font-medium">{{ $incident->title }}</td>
                                                <td data-label="ห้อง" class="ops-text-muted px-4 py-4 text-sm">
                                                    <div class="space-y-1">
                                                        <span>{{ $incident->room?->name ?? __('No room') }}</span>
                                                        @if ($incident->equipment_reference)
                                                            <p class="ops-inline-note">{{ $incident->equipment_reference }}</p>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td data-label="สถานะ" class="px-4 py-4 text-sm">
                                                    <x-incidents.status-badge :status="$incident->status" />
                                                </td>
                                                <td data-label="ความรุนแรง" class="px-4 py-4 text-sm">
                                                    <x-incidents.severity-badge :severity="$incident->severity" />
                                                </td>
                                                <td data-label="รายละเอียด" class="px-4 py-4 text-right text-sm">
                                                    <a href="{{ route('incidents.show', $incident) }}" class="ops-button ops-button--secondary">
                                                        ดูรายละเอียด
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="ops-stack">
                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="55">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">บริบทล่าสุด</p>
                            <h2 class="ops-section-heading__title">ชั้นสรุปจากประวัติ</h2>
                            <p class="ops-section-heading__body">ใช้บริบทล่าสุดแบบย่อเพื่อตัดสินว่าวันนี้นิ่ง ค้างงาน หรือเพิ่งมีความไม่นิ่งต่อเนื่องจากวันก่อน</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <section class="ops-context-board {{ $recentHistoryToneClass }}">
                            <div class="ops-context-board__summary">
                                <div>
                                    <p class="ops-context-board__eyebrow">
                                        {{ match ($recentHistoryContext['state'] ?? 'watch') {
                                            'calm' => 'ประวัติล่าสุดค่อนข้างนิ่ง',
                                            'unstable' => 'พบงานค้างต่อเนื่องจากประวัติล่าสุด',
                                            default => 'ควรทบทวนประวัติล่าสุด',
                                        } }}
                                    </p>
                                    <h3 class="ops-context-board__title">{{ $recentHistoryContext['headline'] }}</h3>
                                    <p class="ops-context-board__body">{{ $recentHistoryContext['body'] }}</p>
                                </div>

                                <span class="ops-trend-pill {{ match ($recentHistoryContext['state'] ?? 'watch') {
                                    'calm' => 'ops-trend-pill--up',
                                    'unstable' => 'ops-trend-pill--down',
                                    default => 'ops-trend-pill--flat',
                                } }}">
                                    {{ match ($recentHistoryContext['state'] ?? 'watch') {
                                        'calm' => 'นิ่ง',
                                        'unstable' => 'มีงานค้าง',
                                        default => 'ควรทบทวน',
                                    } }}
                                </span>
                            </div>

                            <div class="ops-context-board__grid">
                                <article class="ops-context-card">
                                    <div class="ops-context-card__header">
                                        <div>
                                            <p class="ops-context-card__eyebrow">ประวัติรายการตรวจเช็ก</p>
                                            <h4 class="ops-context-card__title">
                                                {{ $recentHistoryContext['archive']['focus_date'] ? \Carbon\Carbon::parse($recentHistoryContext['archive']['focus_date'])->format('d/m/Y') : __('No recent archived day') }}
                                            </h4>
                                        </div>

                                        <strong class="ops-context-card__count">{{ $recentHistoryContext['archive']['total_runs'] }}</strong>
                                    </div>

                                    <p class="ops-context-card__copy">
                                        ครบ {{ $recentHistoryContext['archive']['covered_lanes'] }} รอบตรวจ,
                                        ขาด {{ $recentHistoryContext['archive']['warning_lanes'] }} รอบตรวจ,
                                        พบ {{ $recentHistoryContext['archive']['total_not_done_items'] }} รายการที่ไม่เรียบร้อย
                                    </p>

                                    <div class="ops-context-card__meta">
                                        <span>{{ $recentHistoryContext['archive']['total_noted_items'] }} รายการที่มีบันทึก</span>
                                    </div>

                                    @if ($recentHistoryContext['archive']['url'])
                                        <div class="ops-context-card__footer">
                                            <a href="{{ $recentHistoryContext['archive']['url'] }}" class="ops-button ops-button--secondary">
                                                ดูประวัติของวันนั้น
                                            </a>
                                        </div>
                                    @endif
                                </article>

                                <article class="ops-context-card">
                                    <div class="ops-context-card__header">
                                        <div>
                                            <p class="ops-context-card__eyebrow">ประวัติรายงานปัญหา</p>
                                            <h4 class="ops-context-card__title">ย้อนหลัง {{ $recentHistoryContext['incidents']['days'] }} วัน</h4>
                                        </div>

                                        <strong class="ops-context-card__count">{{ $recentHistoryContext['incidents']['still_active_count'] }}</strong>
                                    </div>

                                    <p class="ops-context-card__copy">
                                        เปิดใหม่ {{ $recentHistoryContext['incidents']['opened_count'] }} รายการ แก้ไขแล้ว {{ $recentHistoryContext['incidents']['resolved_count'] }} รายการ และยังไม่ปิด {{ $recentHistoryContext['incidents']['still_active_count'] }} รายการในช่วงล่าสุด
                                    </p>

                                    <div class="ops-context-card__meta">
                                        <span>งานปัญหาค้างจากประวัติล่าสุด</span>
                                    </div>

                                    @if ($recentHistoryContext['incidents']['url'])
                                        <div class="ops-context-card__footer">
                                            <a href="{{ $recentHistoryContext['incidents']['url'] }}" class="ops-button ops-button--secondary">
                                                ดูประวัติรายงานปัญหา
                                            </a>
                                        </div>
                                    @endif
                                </article>
                            </div>
                        </section>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">แนวโน้ม</p>
                            <h2 class="ops-section-heading__title">แนวโน้มรายการตรวจเช็ก</h2>
                            <p class="ops-section-heading__body">เปรียบเทียบความครบถ้วนของวันนี้กับฐานเมื่อวาน</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-trend-card">
                            <div class="ops-trend-card__header">
                                <div>
                                    <p class="ops-trend-card__eyebrow">ความต่อเนื่องของการตรวจเช็ก</p>
                                    <p class="ops-trend-card__value">{{ $checklistTrend['todayRate'] }}%</p>
                                </div>

                                @if (($checklistTrend['series'] ?? []) !== [])
                                    <div class="ops-trend-card__visual">
                                        <x-ops.sparkline :points="$checklistTrend['series']" :width="88" :height="30" />
                                    </div>
                                @endif

                                <span class="ops-trend-pill {{ $checklistTrendTone }}">
                                    {{ $checklistTrendLabel }}
                                </span>
                            </div>

                            <p class="ops-trend-card__meta">เมื่อวาน: {{ $checklistTrend['yesterdayRate'] }}%</p>
                            <p class="ops-trend-card__copy">
                                @if ($checklistTrend['direction'] === 'up')
                                    สูงกว่าเมื่อวาน {{ $checklistTrend['difference'] }} จุด
                                @elseif ($checklistTrend['direction'] === 'down')
                                    ต่ำกว่าเมื่อวาน {{ $checklistTrend['difference'] }} จุด
                                @else
                                    เท่ากับเมื่อวาน
                                @endif
                            </p>

                            <div class="ops-compare-list">
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">วันนี้</span>
                                    <strong class="ops-compare-list__value">{{ $submittedTodayRuns }} / {{ $todayRuns ?: 0 }} ส่งแล้ว</strong>
                                </div>
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">เมื่อวาน</span>
                                    <strong class="ops-compare-list__value">{{ $checklistTrend['yesterdayRate'] }}% เป็นฐานอ้างอิง</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="120">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">ภาระงานติดตามต่อ</p>
                            <h2 class="ops-section-heading__title">กลุ่มงานและความรับผิดชอบ</h2>
                            <p class="ops-section-heading__body">จัดกลุ่มงานที่ยังไม่ปิดซึ่งยังต้องกำหนดผู้รับผิดชอบหรือกำหนดการขยับงานครั้งถัดไป</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <section class="ops-bucket-board {{ $ownershipBucketToneClass }}">
                            <div class="ops-bucket-board__summary">
                                <div>
                                    <p class="ops-bucket-board__eyebrow">
                                        {{ ($ownershipBuckets['state'] ?? 'active') === 'calm' ? 'ภาระความรับผิดชอบอยู่ในระดับปกติ' : 'ภาระความรับผิดชอบยังต้องติดตาม' }}
                                    </p>
                                    <h3 class="ops-bucket-board__title">{{ $ownershipBuckets['headline'] }}</h3>
                                    <p class="ops-bucket-board__body">{{ $ownershipBuckets['body'] }}</p>
                                </div>

                                <span class="ops-trend-pill {{ ($ownershipBuckets['state'] ?? 'active') === 'calm' ? 'ops-trend-pill--up' : 'ops-trend-pill--flat' }}">
                                    {{ ($ownershipBuckets['state'] ?? 'active') === 'calm' ? 'ควบคุมได้' : 'ต้องทบทวน' }}
                                </span>
                            </div>

                            <div class="ops-bucket-board__grid">
                                @foreach (($ownershipBuckets['buckets'] ?? []) as $bucket)
                                    <article class="ops-bucket-card ops-bucket-card--{{ $bucket['tone'] }}">
                                        <div class="ops-bucket-card__header">
                                            <div>
                                                <p class="ops-bucket-card__title">{{ $bucket['title'] }}</p>
                                                <p class="ops-bucket-card__copy">{{ $bucket['description'] }}</p>
                                            </div>

                                            <strong class="ops-bucket-card__count">{{ $bucket['count'] }}</strong>
                                        </div>

                                        <div class="ops-bucket-card__footer">
                                            @if ($bucket['url'])
                                                <a href="{{ $bucket['url'] }}" class="ops-button ops-button--secondary">
                                                    {{ $bucket['action_label'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            @if ($ownershipActions !== [])
                                <div class="ops-bucket-board__actions">
                                    @foreach ($ownershipActions as $action)
                                        <a href="{{ $action['url'] }}" class="ops-button ops-button--secondary">
                                            {{ $action['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="145">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">แรงกดดันจากการรับปัญหา</p>
                            <h2 class="ops-section-heading__title">แนวโน้มการรับรายงานปัญหา</h2>
                            <p class="ops-section-heading__body">ติดตามว่ามีรายงานปัญหาเข้ามาวันนี้เทียบกับเมื่อวานกี่รายการ</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-trend-card">
                            <div class="ops-trend-card__header">
                                <div>
                                    <p class="ops-trend-card__eyebrow">จำนวนรับเข้าต่อวัน</p>
                                    <p class="ops-trend-card__value">{{ $incidentIntakeTrend['todayCount'] }}</p>
                                </div>

                                @if (($incidentIntakeTrend['series'] ?? []) !== [])
                                    <div class="ops-trend-card__visual">
                                        <x-ops.sparkline
                                            :points="$incidentIntakeTrend['series']"
                                            :width="88"
                                            :height="30"
                                            :tone="$incidentIntakeTrend['direction'] === 'up' ? 'warning' : ($incidentIntakeTrend['direction'] === 'down' ? 'success' : 'primary')"
                                        />
                                    </div>
                                @endif

                                <span class="ops-trend-pill {{ $incidentTrendTone }}">
                                    {{ $incidentTrendLabel }}
                                </span>
                            </div>

                            <p class="ops-trend-card__meta">เมื่อวาน: มีรายงาน {{ $incidentIntakeTrend['yesterdayCount'] }} รายการ</p>
                            <p class="ops-trend-card__copy">
                                @if ($incidentIntakeTrend['direction'] === 'up')
                                    มากกว่าเมื่อวาน {{ $incidentIntakeTrend['difference'] }} รายการ
                                @elseif ($incidentIntakeTrend['direction'] === 'down')
                                    น้อยกว่าเมื่อวาน {{ $incidentIntakeTrend['difference'] }} รายการ
                                @else
                                    จำนวนรับเข้าทรงตัวจากเมื่อวาน
                                @endif
                            </p>

                            <div class="ops-compare-list">
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">เปิดใหม่ตอนนี้</span>
                                    <strong class="ops-compare-list__value">{{ $incidentCounts['Open'] }} รายการที่รอการดำเนินการครั้งแรก</strong>
                                </div>
                                <div class="ops-compare-list__item">
                                    <span class="ops-compare-list__label">กำลังดำเนินการ</span>
                                    <strong class="ops-compare-list__value">{{ $incidentCounts['In Progress'] }} รายการที่กำลังทำอยู่</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="170">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">จุดที่ต้องจับตา</p>
                            <h2 class="ops-section-heading__title">หมวดปัญหาที่มีภาระสูง</h2>
                            <p class="ops-section-heading__body">หมวดปัญหาที่กำลังมีงานค้างที่ยังไม่ปิดมากที่สุดในตอนนี้</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        @if ($hotspotCategories === [])
                            <x-ops.empty-state
                                title="ตอนนี้ยังไม่มีหมวดปัญหาที่ค้างสะสมเด่นชัด"
                                body="เมื่อมีรายงานปัญหาค้างสะสมในหมวดใดมากขึ้น หน้านี้จะช่วยชี้ให้เห็นจุดที่มีภาระงานสูง"
                            />
                        @else
                            <ol class="ops-hotspot-list">
                                @foreach ($hotspotCategories as $index => $hotspot)
                                    @php($intensity = max(18, (int) round(($hotspot['unresolvedCount'] / max($hotspotMaxCount, 1)) * 100)))
                                    <li class="ops-hotspot-list__item" data-hotspot-rank="{{ $index + 1 }}">
                                        <div class="ops-hotspot-list__row">
                                            <div class="ops-hotspot-list__identity">
                                                <span class="ops-hotspot-list__rank">{{ $index + 1 }}</span>
                                                <div class="min-w-0">
                                                    <p class="ops-hotspot-list__title">{{ $hotspot['category'] }}</p>
                                                    <p class="ops-hotspot-list__meta">
                                                        {{ $hotspot['unresolvedCount'] }} รายการที่ยังไม่ปิด
                                                        @if ($hotspot['staleCount'] > 0)
                                                            · {{ $hotspot['staleCount'] }} รายการค้างนาน
                                                        @else
                                                            · ตอนนี้ยังไม่มีรายการค้างนาน
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            @if ($hotspot['url'])
                                                <a href="{{ $hotspot['url'] }}" class="ops-button ops-button--secondary">
                                                    Review
                                                </a>
                                            @endif
                                        </div>

                                        <div class="ops-hotspot-list__meter" aria-hidden="true">
                                            <div class="ops-hotspot-list__meter-fill" data-meter-target="{{ $intensity }}"></div>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts::app>
