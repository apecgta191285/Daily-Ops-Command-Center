<x-layouts::app :title="__('แนวทางคุมคุณภาพ UI')">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('แนวทางคุมคุณภาพส่วนติดต่อผู้ใช้') }}</p>
                <h2 class="ops-page__title">{{ __('คู่มือคุมสัญญาหน้าจอ') }}</h2>
                <p class="ops-page-intro__body">
                    เอกสารอ้างอิงสำหรับผู้ดูแลระบบหน้านี้ใช้ล็อกภาษาของผลิตภัณฑ์ จังหวะการจัดวาง กติกาไอคอน และสัญญาหน้าจอที่นำกลับมาใช้ซ้ำได้ให้ตรงกับบริบทของงานห้องคอมในมหาวิทยาลัย
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Admin only') }}</span>
                    <span class="ops-shell-chip">{{ __('ไม่แสดงในเมนูหลัก') }}</span>
                    <span class="ops-shell-chip">{{ __('คู่มือที่ยึด Blade เป็นหลัก') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="ops-hero" data-motion="glance-rise">
            <div class="ops-hero__inner">
                <div>
                    <p class="ops-hero__eyebrow">ฐานการกำกับหน้าจอ</p>
                    <h3 class="ops-hero__title">หนึ่งผลิตภัณฑ์ หนึ่งเรื่องเล่าของห้องแล็บ หนึ่งสัญญาหน้าจอเดียวกัน</h3>
                    <p class="ops-hero__lead">
                        ใช้หน้านี้เป็นตัวคุมให้งานใหม่ไปทางเดียวกัน ถ้าหน้าใหม่ใดไม่สามารถอยู่ในสัญญาเหล่านี้ได้ เราควรหยุดและแก้สัญญาให้ชัดก่อน แทนการปล่อยสไตล์เฉพาะกิจออกไป
                    </p>

                    <div class="ops-hero__meta">
                        <span class="ops-shell-chip ops-shell-chip--accent">{{ __('งานประจำวันของห้องปฏิบัติการคอมพิวเตอร์ในมหาวิทยาลัย') }}</span>
                        <span class="ops-shell-chip">{{ __('โครงหน้าเปิดร่วมกัน') }}</span>
                        <span class="ops-shell-chip">{{ __('ใช้ชุดไอคอนเดียว') }}</span>
                        <span class="ops-shell-chip">{{ __('ให้ข้อความชัดก่อนการตกแต่ง') }}</span>
                    </div>
                </div>

                <aside class="ops-hero__aside">
                    <div>
                        <p class="ops-hero__aside-title">กติกาหลัก</p>
                        <p class="ops-hero__aside-value">ชัดเจนและยึดกับงานจริง</p>
                        <p class="ops-hero__aside-copy">
                            ข้อความและลำดับชั้นของหน้าจอต้องอธิบาย workflow ของห้องแล็บให้เข้าใจก่อน บรรยากาศและความสวยงามมีหน้าที่เพียงช่วยเสริมสิ่งที่ชัดอยู่แล้ว
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
                            <p class="ops-section-heading__eyebrow">สัญญาด้านภาษา</p>
                            <h3 class="ops-section-heading__title">ใช้ภาษาที่ผูกกับงานจริงของห้องแล็บ</h3>
                            <p class="ops-section-heading__body">เลือกใช้ภาษาที่เหมือนทีมแล็บพูดถึงงานจริงของตัวเอง ไม่ใช่ภาษานามธรรมแบบห้องควบคุมทั่วไป</p>
                        </div>
                    </div>

                    <div class="ops-card__body grid gap-4 lg:grid-cols-2">
                        <x-ops.callout title="ภาษาที่ควรใช้" tone="success">
                            เปิดห้อง, ระหว่างวัน, ปิดห้อง, ความพร้อมของจุดใช้งาน, ปัญหาเครื่องพิมพ์, ปัญหาเครือข่าย, สภาพห้อง, ผู้ดูแลห้องแล็บ, ผู้ตรวจห้อง
                        </x-ops.callout>

                        <x-ops.callout title="ภาษาที่ควรหลีกเลี่ยง" tone="warning">
                            command center, precision ops workspace, operational drift, control room, enterprise platform, multi-site command hub
                        </x-ops.callout>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">ตัวอักษรและระยะห่าง</p>
                            <h3 class="ops-section-heading__title">ลำดับชั้นของหน้าต้องมาก่อนการตกแต่ง</h3>
                            <p class="ops-section-heading__body">ทุกหน้าหลักควรใช้ลำดับภาพเดียวกัน คือ eyebrow, title, body, meta chips แล้วค่อยเป็น actions</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <div class="ops-governance-grid">
                            <article class="ops-governance-card ops-governance-card--covered">
                                <div class="ops-governance-card__header">
                                    <div>
                                        <p class="ops-admin-item__eyebrow">ระดับ 1</p>
                                        <h4 class="ops-admin-item__title">จังหวะของส่วนเปิดหน้า</h4>
                                    </div>
                                    <span class="ops-chip ops-chip--success">ต้องมี</span>
                                </div>
                                <div class="ops-governance-card__body">
                                    <p class="ops-governance-card__meta">ใช้ `ops-page-intro` เป็นกรอบบนสุดของทุกหน้าหลัก ใช้ชื่อเรื่องเพียงหนึ่งชื่อ และทำข้อความอธิบายให้สั้น ตรง และซื่อสัตย์ที่สุด</p>
                                </div>
                            </article>

                            <article class="ops-governance-card ops-governance-card--covered">
                                <div class="ops-governance-card__header">
                                    <div>
                                        <p class="ops-admin-item__eyebrow">ระดับ 2</p>
                                        <h4 class="ops-admin-item__title">จังหวะของแต่ละส่วน</h4>
                                    </div>
                                    <span class="ops-chip ops-chip--success">ต้องมี</span>
                                </div>
                                <div class="ops-governance-card__body">
                                    <p class="ops-governance-card__meta">แต่ละส่วนควรเริ่มจาก `ops-section-heading` แล้วตามด้วย surface หลักเพียงหนึ่งชุดก่อน อย่าวางการ์ดน้ำหนักเท่ากันหลายใบโดยไม่ตัดสินลำดับความสำคัญให้ชัด</p>
                                </div>
                            </article>

                            <article class="ops-governance-card ops-governance-card--covered">
                                <div class="ops-governance-card__header">
                                    <div>
                                        <p class="ops-admin-item__eyebrow">ระดับ 3</p>
                                        <h4 class="ops-admin-item__title">ระดับของระยะห่าง</h4>
                                    </div>
                                    <span class="ops-chip ops-chip--success">ต้องมี</span>
                                </div>
                                <div class="ops-governance-card__body">
                                    <p class="ops-governance-card__meta">คุมจังหวะแนวตั้งให้อยู่ในระดับหลักไม่กี่ระดับ เช่น ระยะระหว่าง section, ภายในการ์ด, metadata แบบย่อ และความแน่นของตาราง อย่าเพิ่ม margin เฉพาะกิจสำหรับหน้าจอใดหน้าจอหนึ่ง</p>
                                </div>
                            </article>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">สัญญาของ surface ที่ใช้ซ้ำได้</p>
                            <h3 class="ops-section-heading__title">ชุดประกอบหลักที่ควรเลือกใช้ก่อน</h3>
                            <p class="ops-section-heading__body">ให้เริ่มจาก surface เหล่านี้ก่อนเสมอ ก่อนจะสร้าง markup เฉพาะหน้าขึ้นใหม่</p>
                        </div>
                    </div>

                    <div class="ops-card__body space-y-6">
                        <div class="grid gap-4 lg:grid-cols-2">
                            <x-ops.callout title="กล่องอธิบาย" tone="neutral">
                                ใช้สำหรับคำอธิบายสั้นหนึ่งประเด็น หมายเหตุความเสี่ยง หรือบริบทที่ต้องการให้คนอ่านตั้งหลัก ไม่ใช่พื้นที่สำหรับย่อหน้ายาวหนาแน่น
                            </x-ops.callout>
                            <x-ops.callout title="การ์ดสัญญาณหรือค่าสรุป" tone="info">
                                ใช้เมื่อมีตัวเลขหรือสถานะที่ควรถูกมองเห็นได้เร็ว หลีกเลี่ยงการยัดหลายเรื่องที่ไม่เกี่ยวกันไว้ในการ์ดใบเดียว
                            </x-ops.callout>
                        </div>

                        <x-ops.empty-state
                            title="สถานะว่างต้องยังมีประโยชน์"
                            body="อธิบายให้ชัดว่าขาดอะไร เรื่องนั้นสำคัญอย่างไร และผู้ใช้ควรทำอะไรต่อ อย่าใช้ empty state เพื่อการตกแต่งโดยไม่มีคำแนะนำที่ใช้งานได้จริง"
                        />

                        <ul role="list" class="ops-timeline">
                            <li class="ops-timeline__item">
                                <span class="ops-timeline__dot" aria-hidden="true"></span>
                                <div class="ops-timeline__card">
                                    <div class="ops-incident-sequence__item">
                                        <div class="ops-incident-sequence__header">
                                            <div>
                                                <p class="ops-incident-sequence__title">สัญญาของไทม์ไลน์</p>
                                                <p class="ops-incident-sequence__meta">ใช้ไทม์ไลน์เฉพาะเมื่อข้อมูลต้องเล่าแบบเรียงลำดับเวลา ไม่ใช่เอาข้อเท็จจริงที่ไม่เกี่ยวกับลำดับเวลาเข้ามาปะปน</p>
                                            </div>
                                        </div>
                                        <p class="ops-incident-sequence__body">ถ้าข้อมูลไม่ได้เป็นเหตุการณ์ต่อเนื่องกัน มันมักควรอยู่ใน recap panel หรือ governance card มากกว่า</p>
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
                            <p class="ops-section-heading__eyebrow">สัญญาด้านไอคอน</p>
                            <h3 class="ops-section-heading__title">ใช้ชุดไอคอนเดียวเท่านั้น</h3>
                            <p class="ops-section-heading__body">ใช้ชุดไอคอนของ Flux ให้สม่ำเสมอ ไอคอนแบบเส้นเป็นค่าเริ่มต้น และให้เน้นหนักขึ้นเฉพาะจุดที่เป็นสถานะต้องใส่ใจจริง</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <dl class="ops-detail-stack">
                            <div>
                                <dt class="ops-detail-stack__label">ขนาดมาตรฐาน</dt>
                                <dd class="ops-detail-stack__value">16, 18, 20, 24</dd>
                            </div>
                            <div>
                                <dt class="ops-detail-stack__label">สไตล์มาตรฐาน</dt>
                                <dd class="ops-detail-stack__value">แบบเส้นสำหรับเมนูนำทางและสถานะปกติ</dd>
                            </div>
                            <div>
                                <dt class="ops-detail-stack__label">สิ่งที่ไม่ควรทำ</dt>
                                <dd class="ops-detail-stack__value">อย่าผสมหลายชุดไอคอน อย่าใส่ไอคอนเพื่อความสวยอย่างเดียว และอย่าใช้ไอคอนแทนคำอธิบายทั้งหมดใน action สำคัญ</dd>
                            </div>
                        </dl>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">ชื่อของปุ่มและการกระทำ</p>
                            <h3 class="ops-section-heading__title">ให้ปุ่มสั้น ตรง และบอกสิ่งที่จะเกิดขึ้น</h3>
                            <p class="ops-section-heading__body">ข้อความบนปุ่มควรบอกการกระทำถัดไปตรง ๆ หลีกเลี่ยงคำกว้าง ๆ เมื่อผู้ใช้ต้องรู้ให้ชัดว่ากดแล้วจะเกิดอะไรขึ้น</p>
                        </div>
                    </div>

                    <div class="ops-card__body grid gap-4">
                        <div class="ops-surface-soft px-4 py-4">
                            <p class="ops-admin-item__meta-label">ควรใช้</p>
                            <p class="ops-admin-item__meta-value">ตรวจคิวปัญหา, เข้ารอบตรวจ, แจ้งรายงานปัญหา, บันทึกการเปลี่ยนแปลงบัญชี, ดูสรุปรอบตรวจ</p>
                        </div>
                        <div class="ops-surface-soft px-4 py-4">
                            <p class="ops-admin-item__meta-label">ควรหลีกเลี่ยง</p>
                            <p class="ops-admin-item__meta-value">Launch control, Open command view, Sync narrative, Continue sequence, Execute action</p>
                        </div>
                    </div>
                </section>

                <section class="ops-card overflow-hidden">
                    <div class="ops-section-heading">
                        <div>
                            <p class="ops-section-heading__eyebrow">เช็กลิสต์ QA ของหน้าจอ</p>
                            <h3 class="ops-section-heading__title">ก่อนปล่อยหน้าจอใหม่</h3>
                            <p class="ops-section-heading__body">นี่คือ gate ขั้นต่ำสำหรับงานใหม่ที่ผู้ใช้มองเห็นในผลิตภัณฑ์</p>
                        </div>
                    </div>

                    <div class="ops-card__body">
                        <ul role="list" class="ops-next-steps">
                            <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>คนที่เพิ่งเข้ามาเห็นครั้งแรกพอบอกได้ไหมว่านี่เป็น workflow ของห้องคอมในมหาวิทยาลัย</span></li>
                            <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>หน้าใหม่นี้ใช้โครง intro, section และ action ที่ใช้ร่วมกันก่อนจะพึ่ง markup เฉพาะหน้าหรือยัง</span></li>
                            <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>layout มือถือ, focus-visible, reduced-motion, screenshot baseline และ accessibility checks ยังผ่านครบอยู่หรือไม่</span></li>
                        </ul>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts::app>
