<div class="ops-screen ops-screen--staff-incident">
    <x-slot name="header">
        <div class="ops-page-intro">
            <div class="ops-page-intro__copy">
                <p class="ops-page-intro__eyebrow">{{ __('Duty staff issue handoff') }}</p>
                <h2 class="ops-page__title">{{ __('Report Incident') }}</h2>
                <p class="ops-page-intro__body">
                    บันทึกปัญหาของห้องให้ชัดเจน เพื่อให้ผู้ดูแลเห็นว่าเกิดที่ไหน กระทบอะไร และผู้ตรวจห้องพบอะไรจริง
                </p>
                <div class="ops-page-intro__meta">
                    <span class="ops-shell-chip ops-shell-chip--accent">{{ __('Room-tied handoff') }}</span>
                    @if ($roomId !== '')
                        <span class="ops-shell-chip">{{ collect($rooms)->firstWhere('id', $roomId)['name'] ?? __('Selected room') }}</span>
                    @endif
                    <span class="ops-shell-chip">{{ __('Optional equipment reference') }}</span>
                    <span class="ops-shell-chip">{{ __('Management visible') }}</span>
                </div>
            </div>

            <div class="ops-page-intro__actions">
                <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                    {{ __('Back to checklist') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl">
        <section class="ops-card overflow-hidden" data-motion="fade-up" data-motion-delay="40">
            <div class="ops-card__body">
                @if ($submissionRecap)
                    <div class="space-y-6">
                        <div class="ops-alert ops-alert--success">
                            ส่งรายงานปัญหาเรียบร้อยแล้ว ผู้ดูแลสามารถตรวจต่อได้พร้อมบริบทของห้องครบถ้วนบนแดชบอร์ดและหน้าติดตาม
                        </div>

                        <div class="ops-recap-grid">
                            <div class="ops-recap-panel">
                                <h3 class="ops-recap-panel__title">สรุปการส่งรายงาน</h3>
                                <dl class="ops-detail-stack">
                                    <div>
                                        <dt class="ops-detail-stack__label">ชื่อเรื่อง</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['title'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">หมวดหมู่</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['category'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">ความรุนแรง</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['severity'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">ห้อง</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['room_name'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">อุปกรณ์/เครื่องที่เกี่ยวข้อง</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['equipment_reference'] ?? __('Not provided') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="ops-detail-stack__label">เวลาที่สร้าง</dt>
                                        <dd class="ops-detail-stack__value">{{ $submissionRecap['created_at'] }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="ops-recap-panel ops-recap-panel--subtle">
                                <h3 class="ops-recap-panel__title">ขั้นตอนถัดไป</h3>
                                <ul role="list" class="ops-next-steps">
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>ผู้ดูแลจะเห็นรายงานนี้บนแดชบอร์ดและหน้าติดตามปัญหา</span></li>
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>ข้อมูลห้องและอุปกรณ์/เครื่องที่เกี่ยวข้องจะถูกเก็บไว้กับรายงานนี้ เพื่อให้ผู้ตรวจทวนคนถัดไปรู้ว่าต้องไปดูที่จุดใด</span></li>
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>รายงานนี้จะเริ่มต้นด้วยสถานะ <span class="ops-text-heading font-medium">{{ $submissionRecap['status'] }}</span> จนกว่าจะมีผู้มาปรับสถานะ</span></li>
                                    @if ($submissionRecap['has_attachment'])
                                        <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>ไฟล์แนบของคุณถูกส่งไปพร้อมรายงานแล้ว</span></li>
                                    @endif
                                    @if ($submissionRecap['from_checklist'])
                                    <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>รายงานนี้เชื่อมกับเส้นทางติดตามจากรายการตรวจเช็ก คุณจึงสามารถกลับไปทำงานประจำวันต่อได้</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            @if ($submissionRecap['from_checklist'])
                                <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                                    กลับไปหน้ารายการตรวจเช็กของวันนี้
                                </a>
                            @endif

                            <button type="button" wire:click="startAnother" class="ops-button ops-button--primary min-w-52">
                                แจ้งรายงานปัญหาอีกใบ
                            </button>
                        </div>
                    </div>
                @elseif (count($rooms) === 0)
                    <div class="space-y-6">
                        <div class="ops-alert ops-alert--warning">
                            ตอนนี้ยังไม่มีห้องที่เปิดใช้งานอยู่ จึงยังส่งรายงานปัญหานี้ไม่ได้ กรุณาให้ผู้ดูแลระบบหรือผู้ดูแลห้องแล็บเปิดใช้งานห้องอย่างน้อยหนึ่งห้องก่อน
                        </div>

                        <div class="ops-recap-panel ops-recap-panel--subtle">
                            <h3 class="ops-recap-panel__title">เหตุผลที่ยังแจ้งรายงานไม่ได้</h3>
                            <ul role="list" class="ops-next-steps">
                                <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>รายงานปัญหาทุกใบใน workflow นี้ต้องผูกกับห้องหนึ่งห้องเสมอ</span></li>
                                <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>ตอนนี้ยังไม่มีห้องที่เปิดใช้งานอยู่ให้ผูกกับรายงานนี้</span></li>
                                <li class="ops-next-steps__item"><span class="ops-next-steps__bullet" aria-hidden="true"></span><span>เมื่อมีห้องถูกเปิดใช้งานอีกครั้ง แบบฟอร์มนี้จะกลับมาใช้งานได้ตามปกติ</span></li>
                            </ul>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                                กลับไปหน้ารายการตรวจเช็ก
                            </a>
                        </div>
                    </div>
                @else
                    <form wire:submit="submit" class="space-y-6">
                        <div class="grid gap-6">
                            <div>
                                <label for="title" class="ops-field-label">ชื่อเรื่อง <span class="ops-required-mark">*</span></label>
                                <input type="text" id="title" wire:model="title" class="ops-control" placeholder="e.g. เครื่อง PC-03 เปิดไม่ติด">
                                @error('title') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label for="category" class="ops-field-label">หมวดหมู่ <span class="ops-required-mark">*</span></label>
                                    <select id="category" wire:model="category" class="ops-control">
                                        <option value="">-- เลือกหมวดหมู่ --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat }}">{{ __($cat) }}</option>
                                        @endforeach
                                    </select>
                                    @error('category') <span class="ops-field-error">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="room_id" class="ops-field-label">ห้อง <span class="ops-required-mark">*</span></label>
                                    <select id="room_id" wire:model="roomId" class="ops-control">
                                        <option value="">-- เลือกห้อง --</option>
                                        @foreach($rooms as $roomOption)
                                            <option value="{{ $roomOption['id'] }}">{{ $roomOption['name'] }} ({{ $roomOption['code'] }})</option>
                                        @endforeach
                                    </select>
                                    @error('roomId') <span class="ops-field-error">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="severity" class="ops-field-label">ความรุนแรง <span class="ops-required-mark">*</span></label>
                                    <select id="severity" wire:model="severity" class="ops-control">
                                        <option value="">-- เลือกความรุนแรง --</option>
                                        @foreach($severities as $sev)
                                            <option value="{{ $sev }}">{{ __($sev) }}</option>
                                        @endforeach
                                    </select>
                                    @error('severity') <span class="ops-field-error">{{ $message }}</span> @enderror
                                    <p class="ops-field-help">Low = รบกวนเล็กน้อย, Medium = กระทบการใช้งานบางส่วน, High = กระทบหลัก/ความปลอดภัย</p>
                                </div>
                            </div>

                            <div>
                                <label for="equipment_reference" class="ops-field-label">อุปกรณ์/เครื่องที่เกี่ยวข้อง (ไม่บังคับ)</label>
                                <input type="text" id="equipment_reference" wire:model="equipmentReference" class="ops-control" placeholder="เช่น PC-12, Printer Lab 2, Plug A3">
                                @error('equipmentReference') <span class="ops-field-error">{{ $message }}</span> @enderror
                                <p class="ops-field-help">ระบุชื่ออุปกรณ์หรือจุดที่เกี่ยวข้องแบบสั้น ๆ เมื่อปัญหากระทบเครื่องคอมพิวเตอร์ เครื่องพิมพ์ โปรเจกเตอร์ หรือจุดไฟฟ้า</p>
                            </div>

                            <div>
                                <label for="description" class="ops-field-label">รายละเอียด <span class="ops-required-mark">*</span></label>
                                <textarea id="description" wire:model="description" rows="5" class="ops-control"></textarea>
                                @error('description') <span class="ops-field-error">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="attachment" class="ops-field-label">ไฟล์แนบ (ไม่บังคับ)</label>
                                <input type="file" id="attachment" wire:model="attachment" class="ops-control ops-control--file">
                                @error('attachment') <span class="ops-field-error">{{ $message }}</span> @enderror
                                <p class="ops-field-help">ไฟล์ที่รองรับ: PDF, JPG, PNG และ WEBP ขนาดไม่เกิน 10MB</p>
                            </div>
                        </div>

                        <div class="ops-divider-top flex flex-col-reverse gap-3 pt-6 sm:flex-row sm:justify-end">
                            <a href="{{ route('checklists.runs.today', $this->checklistReturnParameters()) }}" class="ops-button ops-button--secondary">
                                ยกเลิก
                            </a>
                            <button type="submit" class="ops-button ops-button--primary min-w-44">
                                <span wire:loading.remove wire:target="submit">ส่งรายงานปัญหา</span>
                                <span wire:loading wire:target="submit">กำลังบันทึก...</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </div>
</div>
