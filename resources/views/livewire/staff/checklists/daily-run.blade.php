<div class="ops-screen ops-screen--checklist-run">
    @php
        $activeScopeCount = collect($scopeBoard)->where('state', '!=', 'unavailable')->count();
    @endphp
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Duty staff checklist') }}</p>
                <h2 class="ops-page__title">{{ __('Daily Checklist') }}</h2>
                <p class="ops-page-intro__body">
                    @if ($errorState === 'room_missing')
                        ตอนนี้ยังไม่มีห้องที่เปิดใช้งานอยู่ จึงยังเริ่มรายการตรวจเช็กของวันนี้ไม่ได้ กรุณาให้ผู้ดูแลระบบหรือผู้ดูแลห้องแล็บเปิดใช้งานห้องอย่างน้อยหนึ่งห้องก่อน
                    @elseif ($errorState === 'room_required')
                        เลือกห้องที่ต้องตรวจวันนี้ก่อน แล้วจึงเข้าสู่รอบตรวจที่ตรงกับสถานการณ์จริงของห้องนั้น
                    @elseif ($errorState === 'scope_required')
                        เลือกรอบตรวจของห้องนี้ก่อน แล้วค่อยดำเนินการตรวจเปิดห้อง ระหว่างวัน หรือปิดห้องให้ตรงกับสิ่งที่กำลังเกิดขึ้นจริง
                    @elseif ($errorState === 'scope_missing' && $this->scopeLabel)
                        ยังไม่ได้ตั้งค่ารอบตรวจ {{ $this->scopeLabel }} ในระบบ กรุณาเลือกรอบตรวจอื่นที่ใช้งานอยู่ หรือให้ผู้ดูแลระบบเปิดใช้งานแม่แบบของช่วงเวลานี้ก่อน
                    @else
                        ตรวจเช็กห้องให้ครบ บันทึกสิ่งที่เกิดขึ้นจริง และส่งต่อปัญหาของห้องโดยไม่ทำให้บริบทของรายการตรวจเช็กหายไป
                    @endif
                </p>
                @if (! $errorState)
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room duty run') }}</span>
                        @if ($this->selectedRoomLabel)
                            <span class="ops-shell-chip">{{ $this->selectedRoomLabel }}</span>
                        @endif
                        <span class="ops-shell-chip">{{ $this->answeredItems }}/{{ $this->totalItems }} {{ __('answered') }}</span>
                        <span class="ops-shell-chip">{{ $isSubmitted ? __('Submitted') : __('Pending') }}</span>
                    </div>
                @elseif ($errorState === 'room_missing')
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room setup required') }}</span>
                        <span class="ops-shell-chip">{{ __('0 active rooms') }}</span>
                    </div>
                @elseif ($errorState === 'room_required')
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room-centered checklist') }}</span>
                        <span class="ops-shell-chip">{{ count($rooms) }} {{ __('active room(s)') }}</span>
                    </div>
                @elseif ($errorState === 'scope_required')
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room and lane selection') }}</span>
                        @if ($this->selectedRoomLabel)
                            <span class="ops-shell-chip">{{ $this->selectedRoomLabel }}</span>
                        @endif
                        <span class="ops-shell-chip">{{ $activeScopeCount }} {{ __('live lane(s) today') }}</span>
                    </div>
                @elseif ($errorState === 'scope_missing' && $this->scopeLabel)
                    <div class="ops-page-intro__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Missing checklist lane') }}</span>
                        @if ($this->selectedRoomLabel)
                            <span class="ops-shell-chip">{{ $this->selectedRoomLabel }}</span>
                        @endif
                        <span class="ops-shell-chip">{{ $this->scopeLabel }}</span>
                    </div>
                @endif
            </div>

            @if (! $errorState)
                <div class="ops-page-intro__actions">
                    <span class="ops-shell-chip">
                        {{ \Carbon\Carbon::parse($run->run_date)->format('d/m/Y') }}
                    </span>
                </div>
            @elseif ($errorState !== 'zero')
                <div class="ops-page-intro__actions">
                    <span class="ops-shell-chip">
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errorState === 'zero')
            <div data-motion="fade-up" class="ops-alert ops-alert--danger">
                <strong class="font-semibold">การตั้งค่าระบบไม่สมบูรณ์:</strong>
                <span class="block sm:inline">ยังไม่มีแม่แบบรายการตรวจที่เปิดใช้งานอยู่ กรุณาติดต่อผู้ดูแลระบบ</span>
            </div>
        @elseif ($errorState === 'room_missing')
            <div data-motion="fade-up" class="ops-alert ops-alert--warning">
                <strong class="font-semibold">ต้องตั้งค่าห้องก่อน:</strong>
                <span class="block sm:inline">ยังไม่มีห้องที่เปิดใช้งานอยู่ ผู้ตรวจจึงยังเริ่มรอบการตรวจที่ผูกกับห้องไม่ได้ กรุณาให้ผู้ดูแลระบบเปิดใช้งานห้องอย่างน้อยหนึ่งห้องก่อน</span>
            </div>
        @elseif ($errorState === 'room_required')
            <section class="ops-hero" data-motion="glance-rise">
                <div class="ops-hero__inner">
                    <div>
                        <p class="ops-hero__eyebrow">เลือกห้อง</p>
                        <h3 class="ops-hero__title">เริ่มจากเลือกห้องที่จะตรวจวันนี้</h3>
                        <p class="ops-hero__lead">
                            ขั้นตอนนี้ยึดห้องเป็นศูนย์กลาง กรุณาเลือกห้องที่กำลังตรวจวันนี้ เพื่อให้รอบตรวจ การส่งต่อปัญหา และประวัติย้อนหลังอ้างอิงห้องเดียวกันทั้งหมด
                        </p>

                        <div class="ops-hero__meta">
                            <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room-centered checklist') }}</span>
                            <span class="ops-shell-chip">{{ count($rooms) }} {{ __('active room(s)') }}</span>
                            <span class="ops-shell-chip">{{ __('Today') }} {{ now()->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <aside class="ops-hero__aside">
                        <div>
                            <p class="ops-hero__aside-title">ห้องที่ใช้งานอยู่</p>
                            <p class="ops-hero__aside-value">{{ count($rooms) }}</p>
                            <p class="ops-hero__aside-copy">
                                เลือกห้องจริงก่อน เพื่อให้บันทึกรอบตรวจนี้อ้างอิงห้องเดียวกันตลอด
                            </p>
                        </div>
                    </aside>
                </div>
            </section>

            <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                <div class="ops-card__body">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">ห้องในระบบ</p>
                            <h3 class="ops-section-heading__title">เลือกห้องที่คุณกำลังตรวจ</h3>
                            <p class="ops-section-heading__body">เริ่มจากเลือกห้อง แล้วค่อยเข้าไปยังรอบตรวจเปิดห้อง ระหว่างวัน หรือปิดห้องของห้องนั้น</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3" data-motion-group data-stagger-base="50" data-stagger-unit="35" data-stagger-max="180">
                        @foreach ($rooms as $roomOption)
                            <article data-motion="scale-soft" class="ops-signal-card ops-signal-card--neutral">
                                <div class="ops-signal-card__header">
                                    <div>
                                        <p class="ops-signal-card__title">{{ $roomOption['name'] }}</p>
                                        <p class="ops-signal-card__body">{{ $roomOption['code'] }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <a href="{{ route('checklists.runs.today', ['room' => $roomOption['id']]) }}" class="ops-button ops-button--primary w-full">
                                        {{ __('เลือกห้องนี้') }}
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @elseif ($errorState === 'scope_required' || $errorState === 'scope_missing')
            <section class="ops-hero" data-motion="glance-rise">
                <div class="ops-hero__inner">
                    <div>
                        <p class="ops-hero__eyebrow">กระดานรายการตรวจประจำวัน</p>
                        <h3 class="ops-hero__title">
                            @if ($errorState === 'scope_required')
                                เลือกรอบตรวจของห้องนี้
                            @else
                                ยังไม่มีรอบตรวจ {{ $this->scopeLabel }} ที่ใช้งานอยู่
                            @endif
                        </h3>
                        <p class="ops-hero__lead">
                            @if ($errorState === 'scope_required')
                                เลือกรอบตรวจเปิดห้อง ระหว่างวัน หรือปิดห้อง ที่ตรงกับสิ่งที่กำลังเกิดขึ้นใน{{ $this->selectedRoomLabel ?? 'ห้องนี้' }} เพื่อให้การตรวจและการส่งต่อปัญหาผูกกับห้องเดียวกัน
                            @else
                                ตอนนี้ยังไม่มีแม่แบบที่เปิดใช้งานสำหรับรอบ {{ $this->scopeLabel }} คุณสามารถเลือกรอบตรวจอื่นที่เปิดใช้งานอยู่ หรือให้ผู้ดูแลระบบเปิดใช้งานแม่แบบที่ถูกต้องก่อน
                            @endif
                        </p>

                        <div class="ops-hero__meta">
                            <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room and lane selection') }}</span>
                            @if ($this->selectedRoomLabel)
                                <span class="ops-shell-chip">{{ $this->selectedRoomLabel }}</span>
                            @endif
                            <span class="ops-shell-chip">{{ $activeScopeCount }} {{ __('live lane(s)') }}</span>
                            <span class="ops-shell-chip">{{ __('Today') }} {{ now()->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <aside class="ops-hero__aside">
                        <div>
                            <p class="ops-hero__aside-title">จำนวนรอบตรวจ</p>
                            <p class="ops-hero__aside-value">{{ $activeScopeCount }}</p>
                            <p class="ops-hero__aside-copy">
                                จำนวนรอบตรวจที่ใช้งานได้ของห้องที่เลือกในวันนี้
                            </p>
                        </div>

                        <div class="ops-hero__aside-stack">
                            <div class="ops-shell-chip">
                                <span>ยังไม่เริ่ม</span>
                                <strong class="font-semibold text-white">{{ collect($scopeBoard)->where('state', 'not_started')->count() }}</strong>
                            </div>
                            <div class="ops-shell-chip">
                                <span>กำลังดำเนินการ</span>
                                <strong class="font-semibold text-white">{{ collect($scopeBoard)->where('state', 'in_progress')->count() }}</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            @if ($errorState === 'scope_missing')
                <div data-motion="fade-up" class="ops-alert ops-alert--warning">
                    <strong class="font-semibold">รอบตรวจที่เลือกยังใช้ไม่ได้:</strong>
                    <span class="block sm:inline">รอบ {{ $this->scopeLabel }} ยังไม่มีแม่แบบรายการตรวจที่เปิดใช้งานอยู่ กรุณาเลือกรอบอื่นด้านล่าง หรือให้ผู้ดูแลระบบเปิดใช้งานแม่แบบก่อน</span>
                </div>
            @endif

            <div class="ops-command-grid">
                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">รอบตรวจของวันนี้</p>
                                <h3 class="ops-section-heading__title">เลือกรอบตรวจที่ถูกต้อง</h3>
                                <p class="ops-section-heading__body">แต่ละรอบตรวจจะใช้รายการตรวจที่เหมาะกับห้องนี้ เพื่อให้การตรวจ การส่งต่อปัญหา และประวัติย้อนหลังสอดคล้องกัน</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="grid gap-4 lg:grid-cols-3" data-motion-group data-stagger-base="50" data-stagger-unit="35" data-stagger-max="160">
                                @foreach ($scopeBoard as $lane)
                                    @php
                                        $laneTone = match ($lane['state']) {
                                            'submitted' => 'neutral',
                                            'in_progress' => 'warning',
                                            default => 'neutral',
                                        };
                                        $laneHref = route('checklists.runs.today', $this->checklistRouteParameters($lane['scope_key']));
                                    @endphp

                                    <article data-motion="scale-soft" class="ops-signal-card {{ $laneTone === 'warning' ? 'ops-signal-card--warning' : 'ops-signal-card--neutral' }}">
                                        <div class="ops-signal-card__header">
                                            <div>
                                                <p class="ops-signal-card__title">{{ $lane['scope'] }}</p>
                                                <p class="ops-signal-card__body">
                                                    {{ $lane['template_title'] ?? __('No active template') }}
                                                </p>
                                            </div>
                                            <div class="ops-signal-card__count">
                                                {{ $lane['completion_percentage'] }}%
                                            </div>
                                        </div>

                                        <div class="ops-signal-card__body">
                                            @if ($lane['state'] === 'unavailable')
                                                ยังไม่มีแม่แบบที่เปิดใช้งานสำหรับรอบตรวจนี้
                                            @elseif ($lane['state'] === 'submitted')
                                                รอบตรวจของวันนี้สำหรับรอบนี้ถูกส่งแล้ว
                                            @elseif ($lane['state'] === 'in_progress')
                                                กลับไปทำรอบตรวจที่เริ่มไว้แล้วของวันนี้
                                            @else
                                                เริ่มรายการตรวจเช็กของรอบนี้
                                            @endif
                                        </div>

                                        <div class="ops-signal-card__footer">
                                            <span class="ops-chip {{ $lane['state'] === 'submitted' ? 'ops-chip--success' : ($lane['state'] === 'in_progress' ? 'ops-chip--warning' : '') }}">
                                                {{ match($lane['state']) { 'submitted' => 'ส่งแล้ว', 'in_progress' => 'กำลังดำเนินการ', 'not_started' => 'ยังไม่เริ่ม', 'unavailable' => 'ยังใช้ไม่ได้', default => $lane['state'] } }}
                                            </span>
                                                <span class="ops-text-muted text-xs">
                                                {{ $lane['answered_items'] }}/{{ $lane['total_items'] }} {{ __('answered') }}
                                            </span>
                                        </div>

                                        <div class="mt-4">
                                            @if ($lane['state'] === 'unavailable')
                                                <button type="button" class="ops-button ops-button--secondary w-full" disabled>
                                                    {{ __('Unavailable') }}
                                                </button>
                                            @else
                                                <a href="{{ $laneHref }}" class="ops-button {{ $lane['state'] === 'submitted' ? 'ops-button--secondary' : 'ops-button--primary' }} w-full">
                                                    {{ $lane['state'] === 'submitted' ? __('Review lane') : __('Enter lane') }}
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        @else
            <section class="ops-hero" data-motion="glance-rise">
                <div class="ops-hero__inner">
                    <div>
                        <p class="ops-hero__eyebrow">รายการตรวจเช็กประจำวัน</p>
                        <h3 class="ops-hero__title">{{ $template->title }}</h3>
                        <p class="ops-hero__lead">
                            ดำเนินการตรวจของวันที่ {{ \Carbon\Carbon::parse($run->run_date)->format('d/m/Y') }} ให้ครบ บันทึกหมายเหตุให้ชัดเมื่อพบความผิดปกติ และส่งต่อปัญหาเข้าสู่คิวรายงานโดยไม่ทำให้บริบทหายไป
                        </p>

                        <div class="ops-hero__meta">
                            @if ($this->selectedRoomLabel)
                                <span class="ops-shell-chip ops-shell-chip--accent">ห้อง: {{ $this->selectedRoomLabel }}</span>
                            @endif
                            <span class="ops-shell-chip ops-shell-chip--accent">รอบเวลา: {{ $template->scope->value }}</span>
                            <span class="ops-shell-chip">{{ $this->answeredItems }}/{{ $this->totalItems }} ตอบแล้ว</span>
                            @if ($isSubmitted)
                                <span class="ops-shell-chip">ส่งแล้ว</span>
                            @else
                                <span class="ops-shell-chip">รอดำเนินการ</span>
                            @endif
                        </div>
                    </div>

                    <aside class="ops-hero__aside">
                        <div>
                            <p class="ops-hero__aside-title">ความคืบหน้า</p>
                            <p class="ops-hero__aside-value">{{ $this->completionPercentage }}%</p>
                            <p class="ops-hero__aside-copy">{{ $this->remainingItems }} รายการที่ยังเหลือก่อนจะถือว่าการตรวจเสร็จสมบูรณ์</p>
                        </div>

                        <div class="ops-hero__aside-stack">
                            <div class="ops-shell-chip">
                                <span>ทำเครื่องหมายว่าไม่เรียบร้อย</span>
                                <strong class="font-semibold text-white">{{ $this->notDoneItems }}</strong>
                            </div>
                            <div class="ops-shell-chip">
                                <span>รอบตรวจอ้างอิงล่าสุด</span>
                                <strong class="font-semibold text-white">{{ count($recentRuns) }}</strong>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>

            <div class="ops-command-grid ops-command-grid--checklist">
                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">สถานะรอบตรวจ</p>
                                <h3 class="ops-section-heading__title">ความคืบหน้าของวันนี้</h3>
                                <p class="ops-section-heading__body">ตรวจเช็กให้ครบก่อนส่งต่องานในรอบนี้</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            <div class="grid gap-3 sm:grid-cols-3">
                                <div class="ops-progress-panel">
                                    <div class="ops-eyebrow-label">ตอบแล้ว</div>
                                    <div class="ops-metric-value mt-2 text-2xl font-semibold">
                                        {{ $this->answeredItems }}/{{ $this->totalItems }}
                                    </div>
                                </div>
                                <div class="ops-progress-panel">
                                    <div class="ops-eyebrow-label">คงเหลือ</div>
                                    <div class="ops-metric-value mt-2 text-2xl font-semibold">
                                        {{ $this->remainingItems }}
                                    </div>
                                </div>
                                <div class="ops-progress-panel">
                                    <div class="ops-eyebrow-label">ไม่เรียบร้อย</div>
                                    <div class="ops-metric-value mt-2 text-2xl font-semibold">
                                        {{ $this->notDoneItems }}
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="ops-text-muted flex items-center justify-between text-sm">
                                    <span>ความคืบหน้า</span>
                                    <span>{{ $this->completionPercentage }}%</span>
                                </div>
                                <div class="ops-progress-bar">
                                    <div
                                        class="ops-progress-bar__value"
                                        style="width: {{ $this->completionPercentage }}%;"
                                    ></div>
                                </div>
                            </div>

                            <x-ops.callout title="คำแนะนำสำหรับรอบตรวจ" tone="neutral">
                                @if ($isSubmitted)
                                    ส่งรายการตรวจเช็กของวันนี้แล้ว คุณยังย้อนกลับมาตรวจคำตอบด้านล่างได้ แต่จะแก้ไขเพิ่มเติมไม่ได้
                                @elseif ($this->remainingItems === 0)
                                    กรอกคำตอบที่จำเป็นครบแล้ว ตรวจทานบันทึกเพิ่มเติมแล้วส่งรายการตรวจเช็กได้เลย
                                @else
                                    ยังมีอีก {{ $this->remainingItems }} รายการที่ต้องเลือกผลตรวจให้ครบก่อนส่ง
                                @endif
                            </x-ops.callout>

                            @if ($this->notDoneItems > 0 && ! $isSubmitted)
                                <x-ops.callout title="คำเตือนสำหรับการติดตามต่อ" tone="warning">
                                    ตอนนี้มี {{ $this->notDoneItems }} รายการที่ถูกทำเครื่องหมายว่าไม่เรียบร้อย หากเป็นปัญหาจริงในห้อง ให้เตรียมแจ้งรายงานปัญหาหลังส่งรายการตรวจเช็กเพื่อให้ผู้ดูแลติดตามต่อ
                                </x-ops.callout>
                            @endif
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="90">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">พื้นที่ตรวจเช็ก</p>
                                <h3 class="ops-section-heading__title">{{ $template->title }}</h3>
                                <p class="ops-section-heading__body">ทำรายการตรวจเช็กตามลำดับ เพิ่มบันทึกเมื่อจำเป็น และเตรียมข้อมูลไว้สำหรับส่งต่อเป็นรายงานปัญหาเมื่อพบจุดที่ไม่ผ่าน</p>
                            </div>

                            <div class="flex shrink-0 flex-wrap gap-3">
                                @if ($activeScopeCount > 1)
                                    <a href="{{ route('checklists.runs.today', $this->checklistRouteParameters()) }}" class="ops-button ops-button--secondary">
                                        {{ __('กลับไปหน้ากระดานรายการตรวจเช็ก') }}
                                    </a>
                                @endif
                                <a href="{{ $this->incidentPrefillUrl }}" class="ops-button ops-button--danger">
                                    แจ้งรายงานปัญหา
                                </a>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            @if (session()->has('message'))
                                <div data-alert data-auto-dismiss="5000" role="status" aria-live="polite" class="ops-alert ops-alert--success mb-5">
                                    <div class="ops-alert__inner">
                                        <div class="ops-alert__copy">{{ session('message') }}</div>
                                        <button type="button" class="ops-alert__dismiss" data-dismiss-alert aria-label="{{ __('Dismiss message') }}">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if ($isSubmitted)
                                <div class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-start">
                                    <div class="ops-progress-panel">
                                        <h4 class="ops-text-heading text-sm font-semibold">สรุปการส่งรอบตรวจ</h4>
                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            <span class="ops-badge ops-badge--neutral">
                                                ตอบแล้ว {{ $this->answeredItems }} รายการ
                                            </span>
                                            <span class="ops-badge ops-badge--neutral">
                                                ไม่เรียบร้อย {{ $this->notDoneItems }} รายการ
                                            </span>
                                            <span class="ops-badge ops-badge--neutral">
                                                มีบันทึก {{ $this->notedItems }} รายการ
                                            </span>
                                        </div>
                                        <p class="ops-text-muted mt-3 text-sm">
                                            @if ($this->notDoneItems > 0)
                                                รอบตรวจนี้มีรายการที่ถูกทำเครื่องหมายว่าไม่เรียบร้อย หากสะท้อนถึงปัญหาจริงของห้อง ควรแจ้งรายงานปัญหาเพื่อให้ทีมแล็บติดตามต่อ
                                            @else
                                                รอบตรวจนี้เสร็จโดยไม่มีรายการที่ทำเครื่องหมายว่าไม่เรียบร้อย หากต้องการทบทวนสิ่งที่เปลี่ยนไป ให้ดูบันทึกย้อนหลังด้านล่าง
                                            @endif
                                        </p>
                                        @if ($this->repeatedNotDoneTitles !== [])
                                            <div class="ops-tone-warning mt-3 rounded-2xl border px-3 py-3 text-sm">
                                                <span class="font-semibold">ประวัติปัญหาที่เกิดซ้ำ:</span>
                                                {{ collect($this->repeatedNotDoneTitles)->join(', ') }}
                                                เคยถูกทำเครื่องหมายว่าไม่เรียบร้อยมาก่อน ควรพิจารณาแจ้งรายงานปัญหาต่อเนื่องพร้อมบริบทนี้
                                            </div>
                                        @endif
                                    </div>

                                    @if ($this->notDoneItems > 0)
                                        <a href="{{ $this->incidentPrefillUrl }}" class="ops-button ops-button--primary min-w-56">
                                            แจ้งรายงานปัญหาเพื่อติดตามต่อ
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <form wire:submit="submit" class="space-y-5">
                                @php($previousGroupLabel = null)
                                <ul role="list" class="ops-item-stack">
                                    @foreach($run->items as $index => $runItem)
                                        @php($groupLabel = $runItem->checklistItem->group_label ?: 'รายการตรวจทั่วไป')
                                        @php($showGroupHeader = $groupLabel !== $previousGroupLabel)

                                        @if ($showGroupHeader)
                                            <li class="ops-item-group" data-motion="fade-up" data-motion-delay="{{ 110 + ($index * 15) }}">
                                                <div class="ops-item-group__label">{{ $groupLabel }}</div>
                                                @php($previousGroupLabel = $groupLabel)
                                            </li>
                                        @endif

                                        <li class="ops-item-card" data-motion="scale-soft" data-motion-delay="{{ 130 + ($index * 20) }}">
                                            <div class="ops-item-card__content">
                                                <div class="min-w-0">
                                                    <h4 class="ops-item-card__title">
                                                        {{ $runItem->checklistItem->title }}
                                                        @if($runItem->checklistItem->is_required)
                                                            <span class="ops-required-mark">*</span>
                                                        @endif
                                                    </h4>
                                                    @if($runItem->checklistItem->description)
                                                        <p class="ops-item-card__description">{{ $runItem->checklistItem->description }}</p>
                                                    @endif
                                                    @php($anomalyMemory = $itemAnomalyMemory[$runItem->checklist_item_id] ?? null)
                                                    @if (($anomalyMemory['recent_not_done_count'] ?? 0) > 0)
                                                        <div class="ops-tone-warning mt-3 rounded-2xl border px-3 py-2 text-xs">
                                                            <span class="font-semibold">ประวัติปัญหาล่าสุด:</span>
                                                            ถูกทำเครื่องหมายว่าไม่เรียบร้อย {{ $anomalyMemory['recent_not_done_count'] }} ครั้ง
                                                            ใน {{ $anomalyMemory['sample_run_count'] }} รอบตรวจที่ส่งล่าสุด
                                                            @if (filled($anomalyMemory['last_not_done_at']))
                                                                พบล่าสุดเมื่อ {{ $anomalyMemory['last_not_done_at'] }}
                                                            @endif
                                                            @if (filled($anomalyMemory['last_note']))
                                                                บันทึกล่าสุด: {{ $anomalyMemory['last_note'] }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="flex flex-col gap-3">
                                                    <div class="flex flex-wrap gap-3">
                                                        <label class="ops-choice {{ $isSubmitted ? 'opacity-70' : '' }}">
                                                            <input type="radio" wire:model="runItems.{{ $runItem->id }}.result" value="Done" class="ops-choice__control" {{ $isSubmitted ? 'disabled' : '' }}>
                                                            <span>เรียบร้อย</span>
                                                        </label>
                                                        <label class="ops-choice {{ $isSubmitted ? 'opacity-70' : '' }}">
                                                            <input type="radio" wire:model="runItems.{{ $runItem->id }}.result" value="Not Done" class="ops-choice__control ops-choice__control--danger" {{ $isSubmitted ? 'disabled' : '' }}>
                                                            <span>ไม่เรียบร้อย</span>
                                                        </label>
                                                    </div>
                                                    @error("runItems.{$runItem->id}.result")
                                                        <span class="ops-field-error">{{ $message }}</span>
                                                    @enderror

                                                    <input type="text" wire:model="runItems.{{ $runItem->id }}.note" placeholder="บันทึกเพิ่มเติม (ถ้ามี)" class="ops-control" {{ $isSubmitted ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                @if(!$isSubmitted)
                                    <div class="ops-divider-top flex justify-end pt-5">
                                        <button type="submit" class="ops-button ops-button--primary min-w-44 disabled:opacity-50">
                                            <span wire:loading.remove wire:target="submit">ส่งรายการตรวจเช็ก</span>
                                            <span wire:loading wire:target="submit">กำลังส่ง...</span>
                                        </button>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </section>
                </div>

                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ข้อมูลอ้างอิงย้อนหลัง</p>
                                <h3 class="ops-section-heading__title">บริบทจากการส่งล่าสุด</h3>
                                <p class="ops-section-heading__body">ใช้รอบตรวจล่าสุดเป็นข้อมูลอ้างอิงแบบเร็วก่อนส่งรายการตรวจเช็กของวันนี้</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            @if ($recentRuns !== [])
                                <ul role="list" class="ops-detail-list">
                                    @foreach ($recentRuns as $recentRun)
                                        <li class="ops-detail-list__item">
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="ops-text-heading text-sm font-semibold">
                                                    {{ \Carbon\Carbon::parse($recentRun['run_date'])->format('d/m/Y') }}
                                                </div>
                                                <div class="ops-text-muted text-xs">
                                                    ส่งเมื่อ {{ \Carbon\Carbon::parse($recentRun['submitted_at'])->locale('th')->diffForHumans() }}
                                                </div>
                                            </div>
                                            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                                <span class="ops-badge ops-badge--neutral">
                                                    ไม่เรียบร้อย {{ $recentRun['not_done_count'] }} รายการ
                                                </span>
                                                <span class="ops-badge ops-badge--neutral">
                                                    มีบันทึก {{ $recentRun['noted_items_count'] }} รายการ
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <x-ops.empty-state
                                    title="ยังไม่มีประวัติการส่งรายการตรวจเช็ก"
                                    body="เมื่อส่งรอบตรวจไปสักระยะ แผงนี้จะแสดงรูปแบบล่าสุดเพื่อช่วยให้ทบทวนได้เร็วขึ้น"
                                />
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        @endif
    </div>
</div>
