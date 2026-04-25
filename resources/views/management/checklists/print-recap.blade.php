<x-layouts.print :title="$pageTitle">
    <x-slot name="toolbar">
        <div class="ops-print-toolbar__inner">
            <div>
                <p class="ops-print-toolbar__eyebrow">{{ __('Printable evidence surface') }}</p>
                <h1 class="ops-print-toolbar__title">{{ __('Checklist recap print view') }}</h1>
                <p class="ops-print-toolbar__copy">
                    ใช้หน้านี้เมื่อต้องการสรุปรายการตรวจเช็กของห้องแบบพิมพ์ได้ เพื่อทบทวน ใช้เป็นหลักฐานประกอบการนำเสนอ หรือพูดคุยกับผู้ดูแลห้องแล็บ
                </p>
            </div>

            <div class="ops-print-toolbar__actions">
                <a href="{{ route('checklists.history.show', $run) }}" class="ops-button ops-button--secondary">
                    {{ __('Back to archive recap') }}
                </a>
                <button type="button" class="ops-button" onclick="window.print()">
                    {{ __('Print recap') }}
                </button>
            </div>
        </div>
    </x-slot>

    <section class="ops-print-header">
        <div>
            <p class="ops-print-header__eyebrow">{{ __('Checklist evidence pack') }}</p>
            <h1 class="ops-print-header__title">{{ $run->template?->title ?? __('Checklist run') }}</h1>
            <p class="ops-print-header__body">
                สรุปแบบพิมพ์ได้นี้คงข้อมูลของรอบตรวจที่ส่งแล้วไว้ในหน้าเดียว เพื่อให้ทีมทบทวนได้ว่าอะไรเรียบร้อย อะไรไม่เรียบร้อย และมีบันทึกใดถูกเก็บไว้ตอนส่ง
            </p>
        </div>

        <div class="ops-print-chip-row">
            <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Submitted archive') }}</span>
            <span class="ops-shell-chip">{{ $run->room?->name ?? __('No room') }}</span>
            <span class="ops-shell-chip">{{ $scopeLabel }}</span>
            <span class="ops-shell-chip">{{ $run->run_date->format('d/m/Y') }}</span>
        </div>
    </section>

    <section class="ops-print-grid ops-print-grid--summary">
        <article class="ops-recap-panel">
            <p class="ops-recap-panel__title">{{ __('Run summary') }}</p>
            <dl class="ops-detail-stack">
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Room') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $run->room?->name ?? __('No room recorded') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Run date') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $run->run_date->format('d/m/Y') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Scope lane') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $scopeLabel }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Created by') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $run->creator?->name ?? __('Unknown') }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Submitted by') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $submittedByLabel }}</dd>
                </div>
                <div>
                    <dt class="ops-detail-stack__label">{{ __('Submitted at') }}</dt>
                    <dd class="ops-detail-stack__value">{{ $run->submitted_at?->format('d/m/Y H:i') ?? __('Unknown') }}</dd>
                </div>
            </dl>
        </article>

        <article class="ops-recap-panel ops-recap-panel--subtle">
            <p class="ops-recap-panel__title">{{ __('Evidence snapshot') }}</p>
            <div class="ops-history-summary-grid">
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Total items') }}</p>
                    <p class="ops-glance-card__value">{{ $recap['total_items'] }}</p>
                </div>
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Done') }}</p>
                    <p class="ops-glance-card__value">{{ $recap['done_items'] }}</p>
                </div>
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Not Done') }}</p>
                    <p class="ops-glance-card__value">{{ $recap['not_done_items'] }}</p>
                </div>
                <div class="ops-history-summary-card">
                    <p class="ops-glance-card__label">{{ __('Notes') }}</p>
                    <p class="ops-glance-card__value">{{ $recap['noted_items'] }}</p>
                </div>
            </div>
        </article>
    </section>

    @if ($recap['not_done_items'] > 0)
        <section class="ops-print-section">
            <x-ops.callout title="จุดที่ควรหยิบไปติดตามต่อ" tone="warning">
                รอบตรวจนี้มีคำตอบที่เป็นไม่เรียบร้อย ควรเริ่มทบทวนจากรายการเหล่านี้ก่อนเมื่อใช้เอกสารฉบับย่อเพื่อคุยงานหรือส่งต่อการติดตาม
            </x-ops.callout>
        </section>
    @endif

    @foreach ($recap['grouped_items'] as $group)
        <section class="ops-print-section">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">{{ __('Submitted responses') }}</p>
                    <h2 class="ops-section-heading__title">{{ $group['group'] }}</h2>
                    <p class="ops-section-heading__body">คำตอบด้านล่างพิมพ์จากข้อมูลที่ถูกเก็บไว้ตอนส่งจริง เพื่อใช้ทบทวนย้อนหลังโดยไม่เปลี่ยนความหมายของประวัติ</p>
                </div>
            </div>

            <div class="ops-history-answer-grid">
                @foreach ($group['items'] as $item)
                    <article class="ops-history-answer-card {{ $item['result'] === 'Not Done' ? 'ops-history-answer-card--warning' : '' }}">
                        <div class="ops-history-answer-card__header">
                            <div>
                                <p class="ops-text-heading text-sm font-semibold">{{ $item['title'] }}</p>
                                @if (! $item['is_required'])
                                    <p class="ops-text-muted text-xs">{{ __('Optional item') }}</p>
                                @endif
                            </div>

                            <span class="ops-badge {{ $item['result'] === 'Not Done' ? 'ops-badge--warning' : 'ops-badge--success' }}">
                                {{ $item['result'] ?? __('No answer') }}
                            </span>
                        </div>

                        @if (filled($item['note']))
                            <p class="ops-history-answer-card__note">{{ $item['note'] }}</p>
                        @else
                            <p class="ops-text-muted text-sm">{{ __('No note recorded for this item.') }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
</x-layouts.print>
