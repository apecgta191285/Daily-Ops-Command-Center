# FE8 Frontend Hardening and CSS Architecture Split Execution Pack

วันที่: 2026-04-18

## เป้าหมาย

ปิดงาน frontend hardening รอบสุดท้ายหลัง FE8 โดยเน้น maintainability, ownership clarity, และ production-grade CSS architecture โดยไม่เปลี่ยน UI contract ของ product surfaces ที่ส่งลงระบบแล้ว

## Scope ที่ลงมือจริง

1. แยก `resources/css/app/ops.css` ออกจาก giant stylesheet เดียว ไปเป็น concern-based modules ภายใต้ `resources/css/app/ops/`
   - `ops-shell.css`
   - `ops-layout.css`
   - `ops-data.css`
   - `ops-forms.css`
   - `ops-tables.css`
   - `ops-incident.css`
   - `ops-admin.css`
   - `ops-utils.css`
2. แยก `resources/css/app/settings.css` เป็น modules ภายใต้ `resources/css/app/settings/`
   - `settings-layout.css`
   - `settings-flux.css`
3. คง `resources/css/app/ops.css` และ `resources/css/app/settings.css` ให้ทำหน้าที่เป็น import aggregators เพื่อไม่เปลี่ยน public import contract ใน `resources/css/app.css`
4. ไม่เพิ่ม dependency ใหม่
5. ไม่เปลี่ยน Blade class API หรือ route/API/backend contracts

## ผลลัพธ์เชิงวิศวกรรม

- CSS ownership ชัดขึ้นตาม concern:
  - shell/navigation
  - screen/layout
  - data visualization and metrics
  - forms/feedback
  - tables/empty states
  - incident-specific surfaces
  - admin/template authoring
  - shared utilities
- giant file risk ลดลง:
  - team อ่านและ review diff ได้ตรง concern มากขึ้น
  - การแก้ regression บน incident/dashboard/admin/settings แยกกันได้ชัดขึ้น
- settings overrides ถูกแยกออกจาก settings surface composition ชัดเจนขึ้น โดย Flux-specific adaptation ไม่ปะปนกับ page/surface language แล้ว

## สิ่งที่ตั้งใจ “ไม่ทำ”

- ไม่ revive dark mode
- ไม่เพิ่ม chart library หรือ animation library
- ไม่เปลี่ยน theme direction
- ไม่รื้อ component class names ที่ใช้อยู่แล้ว

## Verification

รอบนี้ verify หลัง split โดยตรง:

- `npm run build` ผ่าน
- `composer lint:check` ผ่าน
- `php artisan test` ผ่าน `94 tests / 455 assertions`
- `composer test:browser` ผ่าน `7 tests / 72 assertions`

Build snapshot หลัง split:

- `public/build/assets/app-*.css` gzip ประมาณ `41.13 kB`
- JS bundle ยังเล็กและไม่เปลี่ยน architecture

## Brutal Truth

รอบนี้ไม่ได้เพิ่มความ “หวือหวา” ของ UI แต่เป็นงานที่ทำให้ frontend เดิมที่ดีอยู่แล้ว กลายเป็นระบบที่ดูแลต่อได้จริงมากขึ้น

ถ้าพูดแบบตรงที่สุด:

- FE1-FE8 ทำให้ product surfaces ไปถึงระดับ A+ เชิงประสบการณ์
- ก้อนนี้ทำให้ CSS architecture เริ่มสมกับคำว่า production-grade มากขึ้น
- หลังรอบนี้สิ่งที่เหลือไม่ใช่ execution debt ใหญ่ แต่เป็นการ audit/perf/accessibility รอบรักษามาตรฐานมากกว่า
