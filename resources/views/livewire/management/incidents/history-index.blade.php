<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Room issue record') }}</p>
                <h2 class="ops-page__title">{{ __('Incident History') }}</h2>
                <p class="ops-page-intro__body">
                    ตรวจดูว่ามีปัญหาใดเปิดใหม่ ปัญหาใดแก้ไขแล้ว และปัญหาของห้องใดยังถูกส่งต่อค้างมาถึงทีมดูแล
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Recent incident slices') }}</span>
                    <span class="ops-shell-chip">{{ __('Opened vs resolved') }}</span>
                    <span class="ops-shell-chip">{{ __('Still-active carryover') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('incidents.index') }}" class="ops-button ops-button--secondary">
                    {{ __('Back to live queue') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-card__body">
                <div class="grid gap-4 md:grid-cols-[minmax(0,1fr)_12rem] md:items-end">
                    <div>
                        <p class="ops-eyebrow-label">{{ __('History window') }}</p>
                        <p class="ops-text-muted mt-2 text-sm">
                            หน้านี้ตั้งใจให้เบาและตรงประเด็น ใช้ทบทวนประวัติปัญหาของห้องในช่วงล่าสุด ไม่ใช่ระบบรายงานเชิงคลังข้อมูล
                        </p>
                    </div>

                    <div>
                        <label for="days" class="ops-field-label">{{ __('Recent days') }}</label>
                        <select id="days" wire:model.live="days" class="ops-control">
                            @foreach ($allowedDayRanges as $range)
                                <option value="{{ $range }}">{{ $range }} {{ __('days') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
            <div class="ops-card__body">
                <div class="ops-section-heading">
                    <div>
                        <p class="ops-section-heading__eyebrow">{{ __('History summary') }}</p>
                        <h3 class="ops-section-heading__title">{{ __('Recent incident movement') }}</h3>
                        <p class="ops-section-heading__body">
                            {{ __('Opened and resolved slices across the last :days days.', ['days' => $history['days']]) }}
                        </p>
                    </div>
                </div>

                <div class="ops-history-summary-grid ops-history-summary-grid--wide mt-6">
                    <div class="ops-history-summary-card">
                        <p class="ops-eyebrow-label">{{ __('Opened') }}</p>
                        <p class="ops-metric-value">{{ $history['opened_count'] }}</p>
                        <p class="ops-inline-note">{{ __('New incidents reported inside this review window.') }}</p>
                    </div>

                    <div class="ops-history-summary-card">
                        <p class="ops-eyebrow-label">{{ __('Resolved') }}</p>
                        <p class="ops-metric-value">{{ $history['resolved_count'] }}</p>
                        <p class="ops-inline-note">{{ __('Incidents closed inside the same recent window.') }}</p>
                    </div>

                    <div class="ops-history-summary-card">
                        <p class="ops-eyebrow-label">{{ __('Still active') }}</p>
                        <p class="ops-metric-value">{{ $history['still_active_count'] }}</p>
                        <p class="ops-inline-note">{{ __('Records opened in this window that still remain unresolved.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
            <div class="ops-card__body">
                <div class="ops-section-heading">
                    <div>
                        <p class="ops-section-heading__eyebrow">{{ __('Daily slices') }}</p>
                        <h3 class="ops-section-heading__title">{{ __('Opened and resolved by day') }}</h3>
                        <p class="ops-section-heading__body">
                            {{ __('Use these slices to understand recent room-by-room issue flow without leaving the management shell.') }}
                        </p>
                    </div>
                </div>

                @if ($history['slices'] === [])
                    <x-ops.empty-state
                        title="ไม่มีความเคลื่อนไหวของรายงานปัญหาในช่วงเวลานี้"
                        body="เมื่อมีการแจ้งหรือปิดรายงานปัญหาภายในช่วงเวลาที่เลือก หน้านี้จะสรุปข้อมูลรายวันให้ที่นี่"
                    />
                @else
                    <div class="ops-incident-history-grid mt-6" data-motion-group data-stagger-base="140" data-stagger-unit="35" data-stagger-max="320">
                        @foreach ($history['slices'] as $slice)
                            <article class="ops-incident-history-day" data-motion="fade-up">
                                <div class="ops-incident-history-day__header">
                                    <div>
                                        <p class="ops-incident-history-day__eyebrow">{{ __('Daily record') }}</p>
                                        <h4 class="ops-incident-history-day__title">{{ $slice['label'] }}</h4>
                                    </div>

                                    <div class="ops-incident-history-day__meta">
                                        <span class="ops-chip ops-chip--info">{{ $slice['opened_count'] }} {{ __('opened') }}</span>
                                        <span class="ops-chip">{{ $slice['resolved_count'] }} {{ __('resolved') }}</span>
                                        @if ($slice['still_active_count'] > 0)
                                            <span class="ops-chip ops-chip--warning">{{ $slice['still_active_count'] }} {{ __('still active') }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="ops-incident-history-day__body">
                                    <section class="ops-incident-history-column">
                                        <div class="ops-incident-history-column__header">
                                            <p class="ops-text-heading font-medium">{{ __('Opened') }}</p>
                                            <p class="ops-inline-note">{{ __('What entered the queue on this day.') }}</p>
                                        </div>

                                        @if ($slice['opened'] === [])
                                            <p class="ops-text-muted text-sm">{{ __('No incidents were opened on this day.') }}</p>
                                        @else
                                            <div class="ops-incident-history-list">
                                                @foreach ($slice['opened'] as $incident)
                                                    <div class="ops-incident-history-item">
                                                        <div class="space-y-2">
                                                            <a href="{{ $incident['url'] }}" class="ops-inline-link">{{ $incident['title'] }}</a>
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <x-incidents.severity-badge :severity="$incident['severity']" />
                                                                @if ($incident['status'] !== 'Resolved')
                                                                    <span class="ops-badge ops-badge--warning">{{ __('Still active') }}</span>
                                                                @endif
                                                            </div>
                                                            <p class="ops-inline-note">
                                                                {{ __('Reported by :creator', ['creator' => $incident['creator_name'] ?? __('Unknown')]) }}
                                                                @if ($incident['room_name'])
                                                                    {{ __(' • Room: :room', ['room' => $incident['room_name']]) }}
                                                                @endif
                                                                @if ($incident['equipment_reference'])
                                                                    {{ __(' • Equipment: :equipment', ['equipment' => $incident['equipment_reference']]) }}
                                                                @endif
                                                                @if ($incident['owner_name'])
                                                                    {{ __(' • Owner: :owner', ['owner' => $incident['owner_name']]) }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </section>

                                    <section class="ops-incident-history-column">
                                        <div class="ops-incident-history-column__header">
                                            <p class="ops-text-heading font-medium">{{ __('Resolved') }}</p>
                                            <p class="ops-inline-note">{{ __('What left the active queue on this day.') }}</p>
                                        </div>

                                        @if ($slice['resolved'] === [])
                                            <p class="ops-text-muted text-sm">{{ __('No incidents were resolved on this day.') }}</p>
                                        @else
                                            <div class="ops-incident-history-list">
                                                @foreach ($slice['resolved'] as $incident)
                                                    <div class="ops-incident-history-item">
                                                        <div class="space-y-2">
                                                            <a href="{{ $incident['url'] }}" class="ops-inline-link">{{ $incident['title'] }}</a>
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <x-incidents.severity-badge :severity="$incident['severity']" />
                                                                <x-incidents.status-badge :status="$incident['status']" />
                                                            </div>
                                                            <p class="ops-inline-note">
                                                                {{ __('Originally reported by :creator', ['creator' => $incident['creator_name'] ?? __('Unknown')]) }}
                                                                @if ($incident['room_name'])
                                                                    {{ __(' • Room: :room', ['room' => $incident['room_name']]) }}
                                                                @endif
                                                                @if ($incident['equipment_reference'])
                                                                    {{ __(' • Equipment: :equipment', ['equipment' => $incident['equipment_reference']]) }}
                                                                @endif
                                                                @if ($incident['owner_name'])
                                                                    {{ __(' • Last owner: :owner', ['owner' => $incident['owner_name']]) }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </section>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>
