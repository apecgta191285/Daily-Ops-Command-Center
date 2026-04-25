<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Template authoring') }}</p>
                <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
                <p class="ops-page-intro__body">
                    {{ __($this->pageDescription) }}
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ $template ? __('Revision draft') : __('New draft') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope-aware activation') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin-owned') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('templates.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to templates') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @php
            $summary = $this->templateSummary;
            $authoringSignals = $this->authoringSignals;
            $previewGroups = $this->previewGroups;
            $scopeGovernance = $this->scopeGovernance;
        @endphp

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

        @if ($template)
            <form id="duplicate-template-form" method="POST" action="{{ route('templates.duplicate', $template) }}" class="hidden">
                @csrf
            </form>
        @endif

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">การจัดทำแม่แบบรายการตรวจ</p>
                    <h3 class="ops-hero__title">{{ __($this->pageTitle) }}</h3>
                    <p class="ops-hero__lead">
                        จัดทำรายการตรวจที่ผู้ตรวจห้องจะใช้งานจริง วางลำดับให้อ่านง่าย และทบทวนผลกระทบก่อนแทนที่แม่แบบที่ใช้อยู่
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $template ? 'โหมดแก้ไขแม่แบบ' : 'แบบร่างแม่แบบใหม่' }}</span>
                        <span class="ops-shell-chip">หน้าสำหรับผู้ดูแลระบบ</span>
                        <span class="ops-shell-chip">หนึ่งแม่แบบใช้งานจริงต่อหนึ่งรอบเวลา</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                            <p class="ops-hero__aside-title">ภาพรวมแบบร่าง</p>
                        <p class="ops-hero__aside-value">{{ $summary['item_count'] }}</p>
                        <p class="ops-hero__aside-copy">
                            จำนวนรายการตรวจที่กำหนดอยู่ในแบบร่างนี้
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Sections') }}</p>
                            <p class="ops-authoring-metric__value">{{ max($summary['grouped_section_count'], 1) }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Required') }}</p>
                            <p class="ops-authoring-metric__value">{{ $summary['required_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Optional') }}</p>
                            <p class="ops-authoring-metric__value">{{ $summary['optional_count'] }}</p>
                        </div>
                    </div>

                    <div class="ops-hero__aside-stack">
                        <div class="ops-shell-chip">
                            <span>{{ __('Scope') }}</span>
                            <strong class="font-semibold text-white">{{ __($scope) }}</strong>
                        </div>
                        <div class="ops-shell-chip">
                            <span>{{ __('State') }}</span>
                            <strong class="font-semibold text-white">{{ $is_active ? __('Active') : __('Draft') }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <form wire:submit="save" class="space-y-6">
            <div class="ops-command-grid ops-command-grid--template">
                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ลำดับการจัดทำแม่แบบ</p>
                                <h3 class="ops-section-heading__title">จัดทำแม่แบบที่ใช้งานจริงใน 3 ขั้นตอน</h3>
                                <p class="ops-section-heading__body">กำหนดข้อมูลหลัก วางลำดับการใช้งานของผู้ตรวจห้อง แล้วค่อยทบทวนผลกระทบก่อนเปิดใช้งานแบบร่างนี้</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-authoring-rhythm">
                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">1</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">ตั้งกรอบแม่แบบ</p>
                                        <p class="ops-authoring-rhythm__body">ตั้งชื่อให้ชัด อธิบายว่าใช้ในช่วงงานใด และทำให้คำอธิบายบอกเหตุผลของการมีรายการตรวจนี้อย่างตรงไปตรงมา</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">2</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">จัดลำดับการใช้งานจริง</p>
                                        <p class="ops-authoring-rhythm__body">ใช้ชื่อหมวดและลำดับรายการเพื่อให้รอบตรวจอ่านแล้วเหมือนงานประจำวันจริง ไม่ใช่แค่รายการงานแบบแบน ๆ</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">3</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">ทบทวนผลกระทบก่อนเปิดใช้งาน</p>
                                        <p class="ops-authoring-rhythm__body">หยุดดูบอร์ดกำกับก่อนบันทึก เพื่อให้แน่ใจว่าแบบร่างนี้เป็นเพียงฉบับร่างหรือจะเข้าไปแทนแม่แบบที่ใช้งานจริงอยู่</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ข้อมูลแม่แบบ</p>
                                <h3 class="ops-section-heading__title">คำนิยามหลัก</h3>
                                <p class="ops-section-heading__body">กำหนดชื่อและคำอธิบายที่บอกว่าแม่แบบนี้ควรใช้เมื่อใด</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="title" class="ops-field-label">ชื่อแม่แบบ <span class="ops-required-mark">*</span></label>
                                <input id="title" type="text" wire:model="title" class="ops-control" placeholder="เช่น เปิดห้องปฏิบัติการ">
                                @error('title') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="description" class="ops-field-label">คำอธิบาย</label>
                                <textarea id="description" wire:model="description" rows="4" class="ops-control" placeholder="อธิบายว่าทำไม template นี้จึงมีอยู่"></textarea>
                                @error('description') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">โครงสร้างรายการตรวจ</p>
                                <h3 class="ops-section-heading__title">รายการตรวจเช็ก</h3>
                                <p class="ops-section-heading__body">กำหนดขั้นตอนตามลำดับที่ผู้ตรวจห้องจะเห็นระหว่างการทำรายการตรวจเช็กประจำวัน</p>
                            </div>

                            <button type="button" wire:click="addItem" class="ops-button ops-button--secondary">
                                {{ __('Add item') }}
                            </button>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @error('items') <span class="ops-field-error">{{ $message }}</span> @enderror

                            <div class="space-y-4">
                                @foreach ($items as $index => $item)
                                    <section class="ops-admin-item ops-admin-item--authoring" data-motion="scale-soft" data-motion-delay="{{ 140 + ($index * 25) }}">
                                        <div class="ops-admin-item__header">
                                            <div class="ops-admin-item__identity">
                                                <span class="ops-step-index">{{ $index + 1 }}</span>
                                                <div>
                                                    <p class="ops-admin-item__eyebrow">{{ __('Checklist item') }}</p>
                                                    <h4 class="ops-admin-item__title">
                                                        {{ trim($item['title'] ?? '') !== '' ? $item['title'] : __('Untitled checklist item') }}
                                                    </h4>
                                                </div>
                                            </div>

                                            <div class="ops-admin-item__chips">
                                                <span class="ops-chip {{ ($item['is_required'] ?? false) ? 'ops-chip--info' : '' }}">
                                                    {{ ($item['is_required'] ?? false) ? __('Required') : __('Optional') }}
                                                </span>
                                                <span class="ops-chip">{{ __('Order') }} {{ $item['sort_order'] }}</span>
                                                @if (trim($item['group_label'] ?? '') !== '')
                                                    <span class="ops-chip ops-chip--success">{{ $item['group_label'] }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_190px]">
                                            <div class="space-y-5">
                                                
                                                <div>
                                                    <label for="item-title-{{ $index }}" class="ops-field-label">ชื่อรายการ <span class="ops-required-mark">*</span></label>
                                                    <input id="item-title-{{ $index }}" type="text" wire:model="items.{{ $index }}.title" class="ops-control" placeholder="เช่น ตรวจการเชื่อมต่ออินเทอร์เน็ต">
                                                    @error('items.'.$index.'.title') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label for="item-description-{{ $index }}" class="ops-field-label">คำอธิบายรายการ</label>
                                                    <textarea id="item-description-{{ $index }}" wire:model="items.{{ $index }}.description" rows="3" class="ops-control" placeholder="อธิบายความหมายหรือเหตุผลของรายการนี้"></textarea>
                                                    @error('items.'.$index.'.description') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <div>
                                                    <label for="item-group-{{ $index }}" class="ops-field-label">ชื่อหมวด</label>
                                                    <input id="item-group-{{ $index }}" type="text" wire:model="items.{{ $index }}.group_label" class="ops-control" placeholder="เช่น ตรวจความปลอดภัย">
                                                    <p class="ops-field-help">ไม่บังคับ ใช้ชื่อเดียวกันกับรายการที่เกี่ยวข้องเพื่อจัดเป็นหมวดแบบสั้น ๆ ในหน้ารายการตรวจประจำวัน</p>
                                                    @error('items.'.$index.'.group_label') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <div class="space-y-5">
                                                <div class="ops-surface-soft px-4 py-4">
                                                    <p class="ops-admin-item__meta-label">{{ __('Execution cue') }}</p>
                                                    <p class="ops-admin-item__meta-value">
                                                        {{ trim($item['group_label'] ?? '') !== '' ? __('Appears under :group', ['group' => $item['group_label']]) : __('Appears in the unlabelled sequence') }}
                                                    </p>
                                                </div>

                                                <div>
                                                    <label for="item-order-{{ $index }}" class="ops-field-label">ลำดับ</label>
                                                    <input id="item-order-{{ $index }}" type="number" min="1" wire:model="items.{{ $index }}.sort_order" class="ops-control">
                                                    @error('items.'.$index.'.sort_order') <span class="ops-field-error">{{ $message }}</span> @enderror
                                                </div>

                                                <label class="ops-choice w-full justify-between">
                                                    <span class="ops-text-heading text-sm font-medium">{{ __('Required') }}</span>
                                                    <input type="checkbox" wire:model="items.{{ $index }}.is_required" class="ops-choice__control">
                                                </label>

                                                <button
                                                    type="button"
                                                    wire:click="removeItem({{ $index }})"
                                                    class="ops-button ops-button--danger w-full"
                                                    @disabled(count($items) === 1)
                                                >
                                                    {{ __('Remove item') }}
                                                </button>
                                            </div>
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>

                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ตรวจแบบร่าง</p>
                                <h3 class="ops-section-heading__title">สรุปก่อนบันทึก</h3>
                                <p class="ops-section-heading__body">ทบทวนอย่างรวดเร็วว่าอะไรพร้อมแล้ว อะไรยังบางเกินไป และการตัดสินใจใดจะกระทบผู้ตรวจห้องมากที่สุดเมื่อแบบร่างนี้ถูกเปิดใช้งาน</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @foreach ($authoringSignals as $signal)
                                <x-ops.callout :title="$signal['title']" :tone="$signal['tone']">
                                    {{ __($signal['body']) }}
                                </x-ops.callout>
                            @endforeach
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ตัวอย่างตอนใช้งานจริง</p>
                                <h3 class="ops-section-heading__title">ผู้ตรวจห้องจะเห็นรายการนี้อย่างไร</h3>
                                <p class="ops-section-heading__body">ตัวอย่างย่อนี้สะท้อนลักษณะการอ่านรายการตรวจเช็กตอนใช้งานจริงเมื่อแบบร่างนี้ถูกเปิดใช้งาน</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-authoring-preview">
                                @foreach ($previewGroups as $group)
                                    <section class="ops-authoring-preview__group">
                                        <div class="ops-authoring-preview__label">{{ $group['label'] }}</div>

                                        <div class="ops-authoring-preview__items">
                                            @foreach ($group['items'] as $previewItem)
                                                <div class="ops-authoring-preview__item">
                                                    <p class="ops-authoring-preview__title">{{ $previewItem['title'] }}</p>
                                                    <span class="ops-chip {{ $previewItem['is_required'] ? 'ops-chip--info' : '' }}">
                                                        {{ $previewItem['is_required'] ? __('Required') : __('Optional') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    @if ($template)
                        <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="160">
                            <div class="ops-section-heading">
                                <div>
                                    <p class="ops-section-heading__eyebrow">ความปลอดภัยของการแก้ไข</p>
                                    <h3 class="ops-section-heading__title">เส้นทางที่ปลอดภัยกว่าสำหรับการปรับปรุง</h3>
                                    <p class="ops-section-heading__body">ทำสำเนาก่อนแก้โครงสร้างใหญ่เมื่อคุณต้องการประวัติการปรับปรุงที่อ่านย้อนกลับได้ชัดเจนกว่าเดิม</p>
                                </div>
                            </div>

                            <div class="ops-card__body">
                                <x-ops.callout title="เส้นทางที่ปลอดภัยกว่าสำหรับการปรับปรุง" tone="neutral">
                                    @if ($hasRunHistory)
                                        {{ __('แม่แบบนี้มีประวัติรอบตรวจเช็กแล้ว :count รอบ ควรทำสำเนาก่อนแก้โครงสร้างใหญ่เพื่อให้ประวัติเดิมยังอ่านและตีความได้ง่าย', ['count' => $runCount]) }}
                                    @elseif ($is_active)
                                        {{ __('แม่แบบนี้กำลังใช้งานจริงอยู่ในรอบเวลานี้ หากต้องการเตรียมฉบับปรับปรุงโดยยังไม่กระทบรอบตรวจจริงทันที ควรทำสำเนาก่อน') }}
                                    @else
                                        {{ __('ทำสำเนาแม่แบบนี้เมื่อคุณต้องการแตกฉบับปรับปรุงใหม่แทนการเขียนทับแบบร่างปัจจุบัน') }}
                                    @endif
                                </x-ops.callout>
                                @if ($currentLiveTemplateTitle)
                                    <p class="ops-text-muted mt-3 text-sm">
                                        {{ __('แม่แบบที่ใช้งานจริงของรอบเวลานี้ตอนนี้: :title', ['title' => $currentLiveTemplateTitle]) }}
                                    </p>
                                @endif
                                <button type="submit" form="duplicate-template-form" class="ops-button ops-button--secondary mt-4 w-full">
                                    {{ __('ทำสำเนาแม่แบบแทน') }}
                                </button>
                            </div>
                        </section>
                    @endif

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="210">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">การกำกับการใช้งาน</p>
                                <h3 class="ops-section-heading__title">ผลกระทบเมื่อเปิดใช้งาน</h3>
                                <p class="ops-section-heading__body">แต่ละรอบเวลามีแม่แบบที่ใช้งานจริงของตัวเอง การเปิดใช้งานจะสลับเฉพาะแม่แบบตัวจริงของรอบเวลาที่เลือกเท่านั้น</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="scope" class="ops-field-label">รอบเวลา <span class="ops-required-mark">*</span></label>
                                <select id="scope" wire:model="scope" class="ops-control">
                                    @foreach ($scopes as $scopeOption)
                                        <option value="{{ $scopeOption }}">{{ __($scopeOption) }}</option>
                                    @endforeach
                                </select>
                                <p class="ops-field-help">รอบเวลาจะกำหนดว่าแม่แบบนี้สามารถเป็นตัวใช้งานจริงของรอบตรวจใดได้ โดยในแต่ละรอบเวลาจะมีแม่แบบที่เปิดใช้งานได้เพียงหนึ่งตัวเท่านั้น</p>
                                @error('scope') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            @php
                                $activationImpact = $this->activationImpact;
                                $activationTone = $activationImpact['tone'] === 'warning' ? 'warning' : 'neutral';
                            @endphp

                            <x-ops.callout title="ผลกระทบเมื่อเปิดใช้งาน" :tone="$activationTone">
                                <p>{{ __($activationImpact['title']) }}</p>
                                <p class="mt-3">{{ __($activationImpact['description']) }}</p>
                            </x-ops.callout>

                            <label class="ops-choice w-full justify-between">
                                <span>
                                    <span class="ops-text-heading block font-medium">{{ __('แม่แบบที่เปิดใช้งาน') }}</span>
                                    <span class="ops-text-muted mt-1 block text-xs">{{ __('การบันทึกแบบเปิดใช้งานจะยกเลิกแม่แบบตัวจริงของรอบเวลานี้เท่านั้นโดยอัตโนมัติ') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="ops-choice__control">
                            </label>

                            <div class="ops-governance-grid ops-governance-grid--compact">
                                @foreach ($scopeGovernance as $lane)
                                    <article class="ops-governance-card {{ $lane['state'] === 'missing' ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }} {{ $lane['is_selected_scope'] ? 'ops-governance-card--selected' : '' }}">
                                        <div class="ops-governance-card__header">
                                            <div>
                                                <p class="ops-admin-item__eyebrow">{{ __('Scope lane') }}</p>
                                                <h4 class="ops-admin-item__title">{{ $lane['scope'] }}</h4>
                                            </div>

                                            <span class="ops-chip {{ $lane['state'] === 'missing' ? 'ops-chip--warning' : 'ops-chip--success' }}">
                                                {{ $lane['state'] === 'missing' ? __('Missing live template') : __('Live covered') }}
                                            </span>
                                        </div>

                                        <div class="ops-governance-card__body">
                                            <p class="ops-governance-card__title">
                                                {{ $lane['live_template_title'] ?? __('No active template') }}
                                            </p>
                                            <p class="ops-governance-card__meta">
                                                @if ($lane['is_selected_scope'])
                                                    {{ __('รอบตรวจนี้คือรอบเวลาที่เลือกอยู่ในแบบฟอร์มกำกับแม่แบบ') }}
                                                @elseif ($lane['state'] === 'missing')
                                                    {{ __('รอบเวลานี้ยังไม่มีแม่แบบที่ใช้งานจริงอยู่') }}
                                                @else
                                                    {{ __('รอบเวลานี้มีแม่แบบที่ใช้งานจริงอยู่แล้ว') }}
                                                @endif
                                            </p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('templates.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Cancel') }}
                </a>

                <button type="submit" class="ops-button ops-button--primary min-w-52">
                    <span wire:loading.remove wire:target="save">{{ $template ? __('บันทึกการเปลี่ยนแปลงแม่แบบ') : __('สร้างแม่แบบ') }}</span>
                    <span wire:loading wire:target="save">{{ __('กำลังบันทึก...') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
