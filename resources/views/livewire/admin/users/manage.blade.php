<div>
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('User administration') }}</p>
                <h2 class="ops-page__title">{{ __($this->pageTitle) }}</h2>
                <p class="ops-page-intro__body">
                    {{ __($this->pageDescription) }}
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ $user ? __('Lifecycle update') : __('Provisioning flow') }}</span>
                    <span class="ops-shell-chip">{{ __('Admin-owned') }}</span>
                    <span class="ops-shell-chip">{{ __('Internal account only') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('users.index') }}" class="ops-button ops-button--secondary" wire:navigate>
                    {{ __('Back to roster') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-6xl space-y-6">
        @php
            $roleGovernance = $this->roleGovernance;
            $lifecycleSignals = $this->lifecycleSignals;
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

        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">การตั้งค่าบัญชีผู้ใช้</p>
                    <h3 class="ops-hero__title">{{ __($this->pageTitle) }}</h3>
                    <p class="ops-hero__lead">
                        ทำให้การเข้าถึงของทีมแล็บชัดเจน: หนึ่งบทบาท หนึ่งสวิตช์เปิดปิดการใช้งาน และหนึ่งเส้นทางจัดการรหัสผ่านที่ดูแลได้จากในระบบ
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ $user ? 'กำลังแก้ไขบัญชีเดิม' : 'กำลังสร้างบัญชีภายในระบบ' }}</span>
                        <span class="ops-shell-chip">{{ __('Role-aware routing') }}</span>
                        <span class="ops-shell-chip">{{ __('Active gate enforced') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                            <p class="ops-hero__aside-title">บทบาทที่เลือก</p>
                        <p class="ops-hero__aside-value">{{ __($role ?: 'staff') }}</p>
                        <p class="ops-hero__aside-copy">
                            {{ $is_active ? 'บัญชีนี้จะสามารถเข้าสู่ระบบได้หลังบันทึก' : 'บัญชีนี้จะยังถูกบล็อกจากการเข้าสู่ระบบหลังบันทึก' }}
                        </p>
                    </div>

                    <div class="ops-authoring-metric-grid">
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('State') }}</p>
                            <p class="ops-authoring-metric__value">{{ $is_active ? __('Live') : __('Off') }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Mode') }}</p>
                            <p class="ops-authoring-metric__value">{{ $user ? 'แก้ไข' : 'ใหม่' }}</p>
                        </div>
                        <div class="ops-authoring-metric">
                            <p class="ops-authoring-metric__label">{{ __('Accounts') }}</p>
                            <p class="ops-authoring-metric__value">{{ $rosterSummary['total_count'] }}</p>
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
                                <p class="ops-section-heading__eyebrow">ลำดับการจัดการบัญชี</p>
                                <h3 class="ops-section-heading__title">กำหนดหรือแก้สิทธิ์ใน 3 ขั้นตอน</h3>
                                <p class="ops-section-heading__body">กำหนดข้อมูลผู้ใช้ก่อน เลือกบทบาทถัดมา แล้วค่อยตัดสินใจว่าบัญชีนี้ควรเปิดใช้งานทันทีหรือไม่</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-authoring-rhythm">
                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">1</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">ตั้งข้อมูลบัญชี</p>
                                        <p class="ops-authoring-rhythm__body">ใช้ชื่อจริงและอีเมลที่ใช้งานจริง เพื่อให้รายชื่อผู้ใช้เชื่อถือได้และใช้เป็นเครื่องมือปฏิบัติงานได้จริง</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">2</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">กำหนดบทบาท</p>
                                        <p class="ops-authoring-rhythm__body">เลือกบทบาทที่เล็กที่สุดแต่ตรงกับความรับผิดชอบจริงของบุคคลนั้นในระบบ</p>
                                    </div>
                                </div>

                                <div class="ops-authoring-rhythm__step">
                                    <span class="ops-step-index">3</span>
                                    <div>
                                        <p class="ops-authoring-rhythm__title">ยืนยันสถานะการใช้งาน</p>
                                        <p class="ops-authoring-rhythm__body">บัญชีที่เปิดใช้งานจะเข้าสู่ระบบได้ทันที ส่วนบัญชีที่ปิดใช้งานจะถูกบล็อกไว้จนกว่าจะเปิดใหม่</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="80">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ข้อมูลตัวตน</p>
                                <h3 class="ops-section-heading__title">ข้อมูลหลักของบัญชี</h3>
                                <p class="ops-section-heading__body">ทำให้รายชื่อผู้ใช้น่าเชื่อถือด้วยชื่อและอีเมลชุดเดียวกับที่ผู้ใช้จะใช้เข้าสู่ระบบจริง</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="name" class="ops-field-label">ชื่อ <span class="ops-required-mark">*</span></label>
                                <input id="name" type="text" wire:model="name" class="ops-control" placeholder="เช่น Somchai Ops Lead">
                                @error('name') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="email" class="ops-field-label">อีเมล <span class="ops-required-mark">*</span></label>
                                <input id="email" type="email" wire:model="email" class="ops-control" placeholder="name@example.com">
                                @error('email') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">นโยบายการเข้าถึง</p>
                                <h3 class="ops-section-heading__title">บทบาทและสถานะการใช้งาน</h3>
                                <p class="ops-section-heading__body">บทบาทจะกำหนดว่าผู้ใช้นี้ทำงานในส่วนใดได้ ส่วนสถานะการใช้งานจะกำหนดว่าเข้าสู่ระบบได้หรือไม่</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-6">
                            <div>
                                <label for="role" class="ops-field-label">บทบาท <span class="ops-required-mark">*</span></label>
                                <select id="role" wire:model.live="role" class="ops-control" @disabled($this->blocksSelfAdminRoleChange)>
                                    @foreach ($roles as $roleOption)
                                        <option value="{{ $roleOption }}">{{ __($roleOption) }}</option>
                                    @endforeach
                                </select>
                                @if ($this->blocksSelfAdminRoleChange)
                                    <p class="ops-field-help">{{ __('Your own administrator role cannot be changed from this screen.') }}</p>
                                @endif
                                @error('role') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <label class="ops-choice w-full justify-between">
                                <span>
                                    <span class="ops-text-heading text-sm font-medium">{{ __('Active account') }}</span>
                                    <span class="ops-text-muted mt-1 block text-sm">{{ __('When off, the user cannot authenticate even if the password is correct.') }}</span>
                                </span>
                                <input type="checkbox" wire:model="is_active" class="ops-choice__control" @disabled($this->blocksSelfAdminRoleChange)>
                            </label>
                            @if ($this->blocksSelfAdminRoleChange)
                                <p class="ops-field-help">{{ __('Your own administrator access cannot be deactivated from this screen.') }}</p>
                            @endif

                            <div class="ops-surface-soft px-4 py-4">
                                <p class="ops-admin-item__meta-label">{{ __('Access meaning') }}</p>
                                <p class="ops-admin-item__meta-value">
                                    {{ $is_active
                                        ? __('Saving active access keeps this account inside the live authentication pool.')
                                        : __('Saving inactive access intentionally removes this account from the sign-in path until an admin reactivates it.') }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="160">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">การจัดการรหัสผ่าน</p>
                                <h3 class="ops-section-heading__title">{{ $user ? __('Optional password reset') : __('Initial password') }}</h3>
                                <p class="ops-section-heading__body">
                                    {{ __($this->passwordHandoffNote) }}
                                </p>
                            </div>
                        </div>

                        <div class="ops-card__body grid gap-6 lg:grid-cols-2">
                            <div>
                                <label for="password" class="ops-field-label">
                                    {{ $user ? __('New password') : __('Password') }}
                                    @unless($user) <span class="ops-required-mark">*</span> @endunless
                                </label>
                                <input id="password" type="password" wire:model="password" class="ops-control">
                                @error('password') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="ops-field-label">
                                    {{ $user ? __('Confirm new password') : __('Confirm password') }}
                                    @unless($user) <span class="ops-required-mark">*</span> @endunless
                                </label>
                                <input id="password_confirmation" type="password" wire:model="password_confirmation" class="ops-control">
                            </div>
                        </div>
                    </section>
                </div>

                <div class="ops-stack">
                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="70">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ตรวจสอบบัญชี</p>
                                <h3 class="ops-section-heading__title">สรุปก่อนบันทึก</h3>
                                <p class="ops-section-heading__body">ใช้สัญญาณเหล่านี้ตรวจทานว่าบัญชีที่กำลังจะบันทึกตรงกับความตั้งใจในการใช้งานจริงของทีมหรือไม่</p>
                            </div>
                        </div>

                        <div class="ops-card__body space-y-4">
                            @foreach ($lifecycleSignals as $signal)
                                <x-ops.callout :title="$signal['title']" :tone="$signal['tone']">
                                    {{ __($signal['body']) }}
                                </x-ops.callout>
                            @endforeach
                        </div>
                    </section>

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="120">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">การกำกับตามบทบาท</p>
                                <h3 class="ops-section-heading__title">ตำแหน่งของบัญชีนี้ในระบบ</h3>
                                <p class="ops-section-heading__body">ดูบทบาทที่เลือกเทียบกับโครงสร้างบทบาทที่ใช้อยู่ เพื่อให้รายชื่อผู้ใช้สมดุลและไม่หลุดจากงานจริง</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <div class="ops-governance-grid ops-governance-grid--compact">
                                @foreach ($roleGovernance as $lane)
                                    <article class="ops-governance-card {{ $lane['is_selected_role'] ? 'ops-governance-card--selected' : ($lane['state'] === 'warning' ? 'ops-governance-card--warning' : 'ops-governance-card--covered') }}">
                                        <div class="ops-governance-card__header">
                                            <div>
                                                <p class="ops-admin-item__eyebrow">{{ __('Role lane') }}</p>
                                                <h4 class="ops-admin-item__title">{{ $lane['title'] }}</h4>
                                            </div>
                                            <span class="ops-chip {{ $lane['is_selected_role'] ? 'ops-chip--info' : ($lane['state'] === 'warning' ? 'ops-chip--warning' : 'ops-chip--success') }}">
                                                {{ $lane['is_selected_role'] ? __('Selected role') : ($lane['state'] === 'warning' ? __('No active accounts') : __('Live coverage')) }}
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

                    <section class="ops-card overflow-hidden" data-motion="fade-left" data-motion-delay="170">
                        <div class="ops-section-heading">
                            <div>
                                <p class="ops-section-heading__eyebrow">ผลหลังบันทึก</p>
                                <h3 class="ops-section-heading__title">สิ่งที่จะเกิดขึ้นหลังบันทึก</h3>
                                <p class="ops-section-heading__body">flow นี้ตั้งใจให้เล็กและเป็นงานภายใน เมื่อบันทึกแล้วสถานะวงจรชีวิตของบัญชีจะเปลี่ยนจริงในระบบทันที</p>
                            </div>
                        </div>

                        <div class="ops-card__body">
                            <x-ops.callout title="ไม่มีขั้นตอนส่งอีเมลเชิญในหน้านี้" tone="neutral">
                                {{ __('This screen does not introduce invitation emails, approval chains, or external identity sync. Saving here applies the account change directly against the app-owned lifecycle contract.') }}
                            </x-ops.callout>

                            <button type="submit" class="ops-button ops-button--primary mt-4 w-full">
                                {{ $user ? __('Save account changes') : __('Create user account') }}
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </div>
</div>
