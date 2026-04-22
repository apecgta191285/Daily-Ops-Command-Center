<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Historical recap') }}</p>
                <h2 class="ops-page__title">{{ $this->pageTitle }}</h2>
                <p class="ops-page-intro__body">
                    Re-open a submitted room check as operational recap so management can review what happened in that room without going back into the live checklist flow.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Submitted archive') }}</span>
                    <span class="ops-shell-chip">{{ $this->roomLabel }}</span>
                    <span class="ops-shell-chip">{{ $this->scopeLabel }}</span>
                    <span class="ops-shell-chip">{{ $run->run_date->format('M d, Y') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('checklists.history.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to archive') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">{{ __('Run archive') }}</p>
                    <h3 class="ops-hero__title">{{ $run->template?->title ?? __('Checklist run') }}</h3>
                    <p class="ops-hero__lead">
                        This historical recap freezes what was submitted for {{ $this->roomLabel }} on {{ $run->run_date->format('M d, Y') }} so the team can review execution truth after the live checklist lane has moved on.
                    </p>
                    <div class="ops-hero__meta">
                        <span class="ops-badge ops-badge--neutral">{{ __('Room:') }} {{ $this->roomLabel }}</span>
                        <span class="ops-badge ops-badge--neutral">{{ __('Operator:') }} {{ $run->creator?->name ?? __('Unknown') }}</span>
                        <span class="ops-badge ops-badge--neutral">{{ __('Submitted by:') }} {{ $this->submittedByLabel }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">{{ __('Run state') }}</p>
                        <p class="ops-hero__aside-value">{{ $recap['not_done_items'] > 0 ? $recap['not_done_items'] : $recap['done_items'] }}</p>
                        <p class="ops-hero__aside-copy">
                            {{ $recap['not_done_items'] > 0
                                ? __('items were submitted as Not Done and deserve review.')
                                : __('items were completed without any Not Done answers.') }}
                        </p>
                    </div>

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
                </aside>
            </div>
        </section>

        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-card__body space-y-4">
                @if ($recap['not_done_items'] > 0)
                    <x-ops.callout title="Follow-up worth reviewing" tone="warning">
                        {{ __('This archived run contains Not Done answers. Use the grouped responses below to understand what was incomplete and what note context was captured at the time of submission.') }}
                    </x-ops.callout>
                @else
                    <x-ops.callout title="Clean submission" tone="success">
                        {{ __('All checklist items were submitted as Done in this run. Review the response groups below only if you need note-level context or execution audit detail.') }}
                    </x-ops.callout>
                @endif

                <dl class="ops-detail-stack">
                    <div>
                        <dt class="ops-detail-stack__label">{{ __('Room') }}</dt>
                        <dd class="ops-detail-stack__value">{{ $this->roomLabel }}</dd>
                    </div>
                    <div>
                        <dt class="ops-detail-stack__label">{{ __('Scope lane') }}</dt>
                        <dd class="ops-detail-stack__value">{{ $this->scopeLabel }}</dd>
                    </div>
                    <div>
                        <dt class="ops-detail-stack__label">{{ __('Run date') }}</dt>
                        <dd class="ops-detail-stack__value">{{ $run->run_date->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="ops-detail-stack__label">{{ __('Created by') }}</dt>
                        <dd class="ops-detail-stack__value">{{ $run->creator?->name ?? __('Unknown') }}</dd>
                    </div>
                    <div>
                        <dt class="ops-detail-stack__label">{{ __('Submitted at') }}</dt>
                        <dd class="ops-detail-stack__value">{{ $run->submitted_at?->format('M d, Y H:i') ?? __('Unknown') }}</dd>
                    </div>
                </dl>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('checklists.history.print', $run) }}" class="ops-button" target="_blank" rel="noopener noreferrer">
                        {{ __('Printable recap') }}
                    </a>
                    <a href="{{ $this->dateArchiveUrl }}" class="ops-button ops-button--secondary" wire:navigate>
                        {{ __('Review same day') }}
                    </a>
                    <a href="{{ $this->scopeArchiveUrl }}" class="ops-button ops-button--secondary" wire:navigate>
                        {{ __('Review same scope') }}
                    </a>
                    <a href="{{ $this->operatorArchiveUrl }}" class="ops-button ops-button--secondary" wire:navigate>
                        {{ __('Review same operator') }}
                    </a>
                    <a href="{{ route('incidents.index', ['unresolved' => 1]) }}" class="ops-button ops-button--secondary" wire:navigate>
                        {{ __('Review active incidents') }}
                    </a>
                </div>
            </div>
        </section>

        @foreach ($recap['grouped_items'] as $groupIndex => $group)
            <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="{{ 70 + ($groupIndex * 20) }}">
                <div class="ops-card__body">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">{{ __('Submitted responses') }}</p>
                            <h3 class="ops-section-heading__title">{{ $group['group'] }}</h3>
                            <p class="ops-section-heading__body">{{ __('Review the item-level results exactly as they were stored for this submitted checklist run.') }}</p>
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
                </div>
            </section>
        @endforeach
    </div>
</div>
