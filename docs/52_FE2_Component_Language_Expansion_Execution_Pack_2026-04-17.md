# **FE2 Component Language Expansion Execution Pack**

## *DOC-52-FE2 | Execution pack for reusable frontend primitives*

**Version v1.0 | Execution reference | วันที่อ้างอิง 17/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้บันทึกงาน `FE2` ที่ขยาย component language ของระบบ เพื่อให้หน้า product หลักโตต่อได้บน primitive ที่ reusable จริง แทนการประกอบ UI ด้วย block เฉพาะหน้าแบบกระจาย

---

# **1. FE2 Objective**

สร้าง primitive ที่ frontend จำเป็นต้องมี ก่อนเข้าสู่ surface redesign รอบใหญ่:

* stat card
* empty state shell
* semantic callout
* chip/filter indicator
* timeline shell

---

# **2. Work Completed**

## **2.1 Added reusable primitives**

เพิ่ม Blade components ใหม่:

* [empty-state.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/components/ops/empty-state.blade.php)
* [stat-card.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/components/ops/stat-card.blade.php)
* [callout.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/components/ops/callout.blade.php)

และเพิ่ม CSS primitives ใน [ops.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app/ops.css):

* `ops-stat`
* `ops-empty`
* `ops-callout`
* `ops-chip`
* `ops-timeline`

## **2.2 Applied primitives to product surfaces**

นำ primitive ไปใช้จริงใน:

* [dashboard.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/dashboard.blade.php)
* [incidents/index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/management/incidents/index.blade.php)
* [incidents/show.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/management/incidents/show.blade.php)
* [templates/index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/admin/checklist-templates/index.blade.php)

## **2.3 Product effect**

ผลที่ได้จาก FE2:

* dashboard summary cards มี identity ชัดขึ้น
* empty states มี shell ที่ intentional มากขึ้น
* incident detail timeline อ่านง่ายขึ้น
* filter context ดูเป็น product element มากกว่าข้อความแปะชั่วคราว
* template administration เริ่มดูเป็น operational admin surface มากขึ้น

---

# **3. Engineering Decision**

## **3.1 สิ่งที่ FE2 จงใจยังไม่ทำ**

เพื่อคุม scope:

* ยังไม่ redesign dashboard/checklist/template ทั้งหน้า
* ยังไม่เพิ่ม chart, tabs, accordion system, หรือ large interaction framework
* ยังไม่แตกทุกส่วนเป็น Blade components แบบสุดโต่ง

## **3.2 เหตุผล**

FE2 มีหน้าที่สร้าง reusable building blocks  
ไม่ใช่ทำ full visual overhaul ทุกหน้าในครั้งเดียว

---

# **4. Acceptance Criteria**

ถือว่าผ่านเมื่อ:

* primitive ที่เพิ่มถูกใช้กับ product surface จริง ไม่ใช่มีแต่ไฟล์
* empty states หลักอย่างน้อย dashboard / incidents / templates ใช้ shell เดียวกัน
* incident detail timeline ไม่ใช้ ad-hoc border-left stack แบบเดิม
* summary cards หลักเริ่มใช้ visual language ร่วม
* build / lint / tests / browser smoke ต้องผ่าน

---

# **5. Resulting Baseline**

หลัง FE2:

* frontend มี component language ที่โตขึ้นอย่างมีทิศทาง
* phase ถัดไปสามารถ redesign page composition ได้บน primitive ที่ reuse ได้จริง
* system ขยับจาก token hardening ไปสู่ component-level maturity แล้ว

---

# **6. Next Recommended Step**

ขั้นที่ถูกต้องที่สุดถัดไปคือ:

## **FE3 — Dashboard / Checklist / Template Surface Redesign**

โดยใช้ FE1 + FE2 เป็นฐาน

