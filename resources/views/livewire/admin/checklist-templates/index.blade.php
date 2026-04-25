<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Admin checklist control') }}</p>
                <h2 class="ops-page__title">{{ __('Checklist Templates') }}</h2>
                <p class="ops-page-intro__body">
                    จัดการแม่แบบรายการตรวจที่ผู้ตรวจห้องใช้จริง ควบคุมการแก้ไขแบบร่างอย่างปลอดภัย และทำให้ทีมแล็บทำงานตามรอบประจำวันของแต่ละห้องได้ตรงกัน
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Live template control') }}</span>
                    <span class="ops-shell-chip">{{ __('Scope-aware lanes') }}</span>
                    <span class="ops-shell-chip">{{ __('Draft-safe editing') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin only') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('templates.create') }}" class="ops-button ops-button--primary" wire:navigate>
                    {{ __('Create template') }}
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

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-3">
                <x-ops.callout title="บริบทการจัดการแม่แบบ" tone="neutral">
                    <p>
                        หน้านี้อยู่ภายในแอปหลักแล้ว ทำให้การจัดการแม่แบบใช้เมนู การยืนยันตัวตน และภาษาหน้าจอเดียวกับส่วนอื่นของระบบ
                    </p>
                    <p class="mt-3">
                        แต่ละรอบเวลายังคงมีแม่แบบที่ใช้งานจริงของตัวเอง และตอนนี้แม่แบบเหล่านั้นถูกใช้ในงานแบบยึดห้องเป็นศูนย์กลาง ดังนั้นการเปลี่ยนแม่แบบหนึ่งครั้งอาจกระทบหลายห้องในช่วงเวลาเดียวกัน
                    </p>
                </x-ops.callout>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">การกำกับตามรอบเวลา</p>
                    <h3 class="ops-section-heading__title">แม่แบบที่ใช้งานจริงในแต่ละรอบตรวจ</h3>
                    <p class="ops-section-heading__body">ใช้บอร์ดนี้ตรวจว่าตอนนี้แม่แบบใดกำลังเป็นตัวจริงของแต่ละรอบตรวจก่อนจะทำสำเนา แก้ไข หรือสลับแม่แบบ</p>
                </div>
            </div>

            <div class="ops-card__body">
                <div class="ops-governance-grid">
                    @foreach ($scopeGovernance as $lane)
                        <article class="ops-governance-card {{ $lane['state'] === 'missing' ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }}">
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
                                    @if ($lane['state'] === 'missing')
                                        {{ __('This operating lane has drafts only or no template at all, so staff cannot receive a live checklist here yet.') }}
                                    @else
                                        {{ __('This template currently owns the live :scope checklist lane.', ['scope' => strtolower($lane['scope'])]) }}
                                    @endif
                                </p>
                            </div>

                            <div class="ops-governance-card__stats">
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Templates') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['template_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Drafts') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['draft_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Live runs') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['live_run_count'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                @if ($templates->isEmpty())
                    <x-ops.empty-state
                        title="ยังไม่มีแม่แบบรายการตรวจ"
                        body="สร้างแม่แบบแรกเพื่อกำหนดว่าผู้ตรวจห้องต้องทำอะไรบ้างใน workflow รายการตรวจเช็กประจำวัน"
                    >
                        <a href="{{ route('templates.create') }}" class="ops-button ops-button--primary" wire:navigate>
                            {{ __('Create first template') }}
                        </a>
                    </x-ops.empty-state>
                @else
                    <div class="ops-table-wrap">
                        <table class="ops-table ops-table--responsive min-w-full">
                            <thead>
                                <tr>
                                    <th>ชื่อแม่แบบ</th>
                                    <th>รอบเวลา</th>
                                    <th>จำนวนรายการ</th>
                                    <th>สถานะ</th>
                                    <th>การดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($templates as $template)
                                    <tr class="ops-table__row" data-template-active="{{ $template->is_active ? 'true' : 'false' }}">
                                        <td data-label="ชื่อแม่แบบ" class="ops-text-heading px-4 py-4 text-sm font-medium">
                                            <div class="space-y-1">
                                                <p>{{ $template->title }}</p>
                                                @if (filled($template->description))
                                                    <p class="ops-text-muted text-xs">{{ $template->description }}</p>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="รอบเวลา" class="ops-text-muted px-4 py-4 text-sm">{{ __($template->scope->value) }}</td>
                                        <td data-label="จำนวนรายการ" class="ops-text-muted px-4 py-4 text-sm">{{ $template->items_count }}</td>
                                        <td data-label="สถานะ" class="px-4 py-4 text-sm">
                                            <span class="ops-badge {{ $template->is_active ? 'ops-badge--success' : 'ops-badge--neutral' }}">
                                                {{ $template->is_active ? __('Live in scope') : __('Draft') }}
                                            </span>
                                        </td>
                                        <td data-label="การดำเนินการ" class="px-4 py-4 text-right text-sm">
                                            <div class="flex flex-wrap justify-end gap-2">
                                                <form method="POST" action="{{ route('templates.duplicate', $template) }}">
                                                    @csrf
                                                    <button type="submit" class="ops-button ops-button--secondary">
                                                        {{ __('Duplicate') }}
                                                    </button>
                                                </form>

                                                <a href="{{ route('templates.edit', $template) }}" class="ops-button ops-button--secondary" wire:navigate>
                                                    {{ __('Edit template') }}
                                                </a>
                                            </div>
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
</div>
