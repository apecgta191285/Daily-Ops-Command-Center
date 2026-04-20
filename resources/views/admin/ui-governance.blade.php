<x-layouts::app :title="__('UI Governance')">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Frontend governance') }}</p>
                <h2 class="ops-page__title">{{ __('UI Contract Guide') }}</h2>
                <p class="ops-page-intro__body">
                    This admin-only reference locks the product language, spacing rhythm, icon rules, and reusable screen contracts for the university computer lab story.
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Admin only') }}</span>
                    <span class="ops-shell-chip">{{ __('Not in main navigation') }}</span>
                    <span class="ops-shell-chip">{{ __('Blade-first style guide') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">Governance baseline</p>
                    <h3 class="ops-hero__title">One product, one lab story, one frontend contract.</h3>
                    <p class="ops-hero__lead">
                        Use this page to keep new work aligned. If a new surface cannot fit these contracts, we should pause and fix the contract instead of shipping one-off styling.
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('University Computer Lab Daily Ops') }}</span>
                        <span class="ops-shell-chip">{{ __('Shared page intro') }}</span>
                        <span class="ops-shell-chip">{{ __('One icon family') }}</span>
                        <span class="ops-shell-chip">{{ __('Copy before decoration') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">Primary rule</p>
                        <p class="ops-hero__aside-value">Grounded clarity</p>
                        <p class="ops-hero__aside-copy">
                            Copy and hierarchy must explain the lab workflow first. Atmosphere and polish only reinforce what is already clear.
                        </p>
                    </div>
                </aside>
            </div>
        </section>

        <div class="ops-command-grid ops-command-grid--dashboard">
            <div class="ops-stack">
                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Copy contract</p>
                            <h3 class="ops-section-heading__title">Use grounded domain language</h3>
                            <p class="ops-section-heading__body">Prefer language that sounds like a real lab team talking about real work, not generic control-room theatre.</p>
                        </div>
                    </div>

                    <div class="ops-card__body grid gap-4 lg:grid-cols-2">
                        <x-ops.callout title="Preferred language" tone="success">
                            lab opening check, during-day lab check, room closing check, workstation readiness, printer issue, network issue, room condition, lab supervisor, duty staff
                        </x-ops.callout>

                        <x-ops.callout title="Avoid by default" tone="warning">
                            industrial command, precision ops workspace, command frame, operational drift, control room, enterprise platform, multi-site command hub
                        </x-ops.callout>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Typography and spacing</p>
                            <h3 class="ops-section-heading__title">Page hierarchy comes before decoration</h3>
                            <p class="ops-section-heading__body">Every new page should use the same visual rank order: eyebrow, title, body, meta chips, then actions.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-governance-grid">
                            <article class="ops-governance-card ops-governance-card--covered">
                                <div class="ops-governance-card__header">
                                    <div>
                                        <p class="ops-admin-item__eyebrow">Tier 1</p>
                                        <h4 class="ops-admin-item__title">Page intro rhythm</h4>
                                    </div>
                                    <span class="ops-chip ops-chip--success">Required</span>
                                </div>
                                <div class="ops-governance-card__body">
                                    <p class="ops-governance-card__meta">Use `ops-page-intro` for the top frame of any major screen. One title only. Keep the intro body to the shortest honest explanation.</p>
                                </div>
                            </article>

                            <article class="ops-governance-card ops-governance-card--covered">
                                <div class="ops-governance-card__header">
                                    <div>
                                        <p class="ops-admin-item__eyebrow">Tier 2</p>
                                        <h4 class="ops-admin-item__title">Section rhythm</h4>
                                    </div>
                                    <span class="ops-chip ops-chip--success">Required</span>
                                </div>
                                <div class="ops-governance-card__body">
                                    <p class="ops-governance-card__meta">Sections should use `ops-section-heading`, then one dominant surface. Avoid stacking multiple equal-weight cards before deciding visual priority.</p>
                                </div>
                            </article>

                            <article class="ops-governance-card ops-governance-card--covered">
                                <div class="ops-governance-card__header">
                                    <div>
                                        <p class="ops-admin-item__eyebrow">Tier 3</p>
                                        <h4 class="ops-admin-item__title">Spacing tiers</h4>
                                    </div>
                                    <span class="ops-chip ops-chip--success">Required</span>
                                </div>
                                <div class="ops-governance-card__body">
                                    <p class="ops-governance-card__meta">Keep vertical rhythm to a small set of tiers: page sections, card interiors, compact metadata, and table density. Do not introduce ad hoc margins for one screen only.</p>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Reusable surface contracts</p>
                            <h3 class="ops-section-heading__title">Preferred building blocks</h3>
                            <p class="ops-section-heading__body">These are the first-choice surfaces before inventing page-specific markup.</p>
                        </div>
                    </div>

                    <div class="ops-card__body space-y-6">
                        <div class="grid gap-4 lg:grid-cols-2">
                            <x-ops.callout title="Callout" tone="neutral">
                                Use for one clear explanation, risk note, or calm context block. Not for dense prose.
                            </x-ops.callout>
                            <x-ops.callout title="Signal or stat card" tone="info">
                                Use when a number or status should be scanned quickly. Avoid mixing multiple unrelated stories into one card.
                            </x-ops.callout>
                        </div>

                        <x-ops.empty-state
                            title="Empty states must stay useful."
                            body="Explain what is missing, why it matters, and what the next action is. Never use decorative empties with no operational guidance."
                        />

                        <ul role="list" class="ops-timeline">
                            <li class="ops-timeline__item">
                                <span class="ops-timeline__dot" aria-hidden="true"></span>
                                <div class="ops-timeline__card">
                                    <div class="ops-incident-sequence__item">
                                        <div class="ops-incident-sequence__header">
                                            <div>
                                                <p class="ops-incident-sequence__title">Timeline contract</p>
                                                <p class="ops-incident-sequence__meta">Use timeline items only for chronological story, not for unrelated facts.</p>
                                            </div>
                                        </div>
                                        <p class="ops-incident-sequence__body">If the information is not sequential, it probably belongs in a recap panel or a governance card instead.</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </section>
            </div>

            <div class="ops-stack">
                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Icon contract</p>
                            <h3 class="ops-section-heading__title">One icon family only</h3>
                            <p class="ops-section-heading__body">Use the Flux sidebar icon family consistently. Outline icons are default; use stronger emphasis only for real attention states.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-detail-stack">
                            <div>
                                <dt class="ops-detail-stack__label">Default sizes</dt>
                                <dd class="ops-detail-stack__value">16, 18, 20, 24</dd>
                            </div>
                            <div>
                                <dt class="ops-detail-stack__label">Default style</dt>
                                <dd class="ops-detail-stack__value">Outline for navigation and neutral state</dd>
                            </div>
                            <div>
                                <dt class="ops-detail-stack__label">Do not do</dt>
                                <dd class="ops-detail-stack__value">Mix icon packs, add decorative icons without meaning, or use icons as the only label for important actions</dd>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Action labels</p>
                            <h3 class="ops-section-heading__title">Keep actions short and literal</h3>
                            <p class="ops-section-heading__body">Buttons should name the next action directly. Avoid vague words when the user needs to know exactly what will happen.</p>
                        </div>
                    </div>

                    <div class="ops-card__body grid gap-4">
                        <div class="ops-surface-soft px-4 py-4">
                            <p class="ops-admin-item__meta-label">Preferred</p>
                            <p class="ops-admin-item__meta-value">Review incidents, Enter lane, Create incident, Save account changes, View recap</p>
                        </div>
                        <div class="ops-surface-soft px-4 py-4">
                            <p class="ops-admin-item__meta-label">Avoid</p>
                            <p class="ops-admin-item__meta-value">Launch control, Open command view, Sync narrative, Continue sequence, Execute action</p>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">Screen QA checklist</p>
                            <h3 class="ops-section-heading__title">Before shipping a new screen</h3>
                            <p class="ops-section-heading__body">This is the minimum gate for new product-facing work.</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <ul role="list" class="ops-next-steps">
                            <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Can a first-time reader tell this is part of a university computer lab workflow?</span></li>
                            <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Does the page use shared intro, section, and action contracts before custom markup?</span></li>
                            <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>Do mobile layout, focus-visible, reduced-motion, screenshot baseline, and accessibility checks all still pass?</span></li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts::app>
