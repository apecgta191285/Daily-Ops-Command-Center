<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('User administration') }}</p>
                <h2 class="ops-page__title">{{ __('Team Access Roster') }}</h2>
                <p class="ops-page-intro__body">
                    จัดการบัญชีของอาจารย์ผู้รับผิดชอบ เจ้าหน้าที่แล็บ และผู้ตรวจห้องที่อยู่เบื้องหลัง workflow การตรวจห้อง พร้อมทำให้สถานะเปิดใช้งานและปิดใช้งานอ่านเข้าใจได้ชัดเจน
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Internal roster') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin only') }}</span>
                    <span class="ops-shell-chip">{{ __('Access lifecycle') }}</span>
                    <span class="ops-shell-chip">{{ __('No public sign-up') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('users.create') }}" class="ops-button ops-button--primary" wire:navigate>
                    {{ __('Create user') }}
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

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">สิทธิ์การเข้าถึงของทีมแล็บ</p>
                    <h3 class="ops-hero__title">จัดการสิทธิ์ของทีมแล็บจากในระบบ</h3>
                    <p class="ops-hero__lead">
                        สร้างบัญชี ปรับบทบาท และปิดการเข้าถึงเมื่อจำเป็น โดยคงให้รายชื่อผู้ใช้มีขนาดเล็ก เป็นระบบภายใน และตรวจสอบได้ง่ายในกลุ่มอาจารย์ ผู้ดูแลห้องแล็บ และผู้ตรวจห้อง
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Internal roster') }}</span>
                    <span class="ops-shell-chip">วงจรบัญชีที่ผู้ดูแลระบบควบคุม</span>
                    <span class="ops-shell-chip">กำหนดรหัสผ่านเริ่มต้นอย่างชัดเจน</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">จำนวนบัญชีทั้งหมด</p>
                        <p class="ops-hero__aside-value">{{ $rosterSummary['total_count'] }}</p>
                        <p class="ops-hero__aside-copy">
                            จำนวนบัญชีภายในที่ถูกดูแลอยู่จากในระบบตอนนี้
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Active') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['active_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Inactive') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['inactive_count'] }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Management') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['management_count'] }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-section-heading">
                <div>
                    <p class="ops-section-heading__eyebrow">การกำกับตามบทบาท</p>
                    <h3 class="ops-section-heading__title">ความครอบคลุมของแต่ละบทบาท</h3>
                    <p class="ops-section-heading__body">ใช้บอร์ดนี้ดูว่าบัญชีของอาจารย์ ผู้ดูแลห้องแล็บ และผู้ตรวจห้องกระจายอยู่แบบใดก่อนจะเพิ่มหรือแก้ไขบัญชี</p>
                </div>
            </div>

            <div class="ops-card__body">
                <div class="ops-governance-grid">
                    @foreach ($rosterSummary['role_lanes'] as $lane)
                        <article class="ops-governance-card {{ $lane['state'] === 'warning' ? 'ops-governance-card--warning' : 'ops-governance-card--covered' }}">
                            <div class="ops-governance-card__header">
                                <div>
                                    <p class="ops-admin-item__eyebrow">{{ __('Role lane') }}</p>
                                    <h4 class="ops-admin-item__title">{{ $lane['title'] }}</h4>
                                </div>

                                <span class="ops-chip {{ $lane['state'] === 'warning' ? 'ops-chip--warning' : 'ops-chip--success' }}">
                                    {{ $lane['state'] === 'warning' ? __('No active accounts') : __('Active coverage') }}
                                </span>
                            </div>

                            <div class="ops-governance-card__body">
                                <p class="ops-governance-card__meta">{{ __($lane['description']) }}</p>
                            </div>

                            <div class="ops-governance-card__stats">
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Total') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['total_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Active') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['active_count'] }}</p>
                                </div>
                                <div>
                                    <p class="ops-admin-item__meta-label">{{ __('Inactive') }}</p>
                                    <p class="ops-admin-item__meta-value">{{ $lane['inactive_count'] }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body space-y-3">
                <x-ops.callout title="ความหมายของสถานะบัญชี" tone="neutral">
                    <p>
                        บัญชีที่เปิดใช้งานสามารถเข้าสู่ระบบและใช้งานหน้าที่มีสิทธิ์ได้ ส่วนบัญชีที่ปิดใช้งานจะถูกบล็อกตั้งแต่ขั้นตอนเข้าสู่ระบบโดยตั้งใจ
                    </p>
                    <p class="mt-3">
                        workflow นี้ถูกออกแบบให้เล็กและชัดเจน: มีรายชื่อภายในระบบเดียว เส้นทางสร้างและแก้ไขเดียว และสวิตช์เปิดปิดการใช้งานที่ชัดเจน โดยไม่มีระบบเชิญหรือสายอนุมัติ
                    </p>
                </x-ops.callout>
            </div>
        </section>

        <section class="ops-card overflow-hidden">
            <div class="ops-card__body">
                <div class="ops-table-wrap">
                    <table class="ops-table ops-table--responsive min-w-full">
                        <thead>
                            <tr>
                                <th>{{ __('Account') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('State') }}</th>
                                <th>{{ __('Owned incidents') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $managedUser)
                                <tr class="ops-table__row">
                                    <td data-label="Account" class="px-4 py-4 text-sm">
                                        <div class="ops-admin-item__identity">
                                            <span class="ops-user-avatar" aria-hidden="true">{{ $managedUser->initials() }}</span>
                                            <div>
                                                <p class="ops-text-heading text-sm font-semibold">
                                                    {{ $managedUser->name }}
                                                    @if (auth()->id() === $managedUser->id)
                                                        <span class="ops-chip ml-2">{{ __('You') }}</span>
                                                    @endif
                                                </p>
                                                <p class="ops-text-muted text-xs">{{ $managedUser->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Role" class="px-4 py-4 text-sm">
                                        <span class="ops-badge {{ $managedUser->role === \App\Domain\Access\Enums\UserRole::Admin ? 'ops-badge--info' : ($managedUser->role === \App\Domain\Access\Enums\UserRole::Supervisor ? 'ops-badge--warning' : 'ops-badge--neutral') }}">
                                            {{ __($managedUser->role->value) }}
                                        </span>
                                    </td>
                                    <td data-label="State" class="px-4 py-4 text-sm">
                                        <span class="ops-badge {{ $managedUser->is_active ? 'ops-badge--success' : 'ops-badge--danger' }}">
                                            {{ $managedUser->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </td>
                                    <td data-label="Owned incidents" class="ops-text-muted px-4 py-4 text-sm">
                                        {{ $managedUser->owned_incidents_count }}
                                    </td>
                                    <td data-label="Action" class="px-4 py-4 text-right text-sm">
                                        <a href="{{ route('users.edit', $managedUser) }}" class="ops-button ops-button--secondary" wire:navigate>
                                            {{ __('Edit account') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
