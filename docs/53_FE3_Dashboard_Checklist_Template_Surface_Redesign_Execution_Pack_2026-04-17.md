# **FE3 Dashboard, Checklist, and Template Surface Redesign**

## *DOC-53-FE3 | Frontend composition redesign for the highest-traffic product surfaces*

**Version v1.0 | Execution pack | วันที่อ้างอิง 17/04/2569**

---

# **1. Objective**

ยกระดับ 3 หน้าหลักที่ผู้ใช้เห็นบ่อยที่สุดให้มีภาษาภาพเดียวกันชัดเจนขึ้น:

* management dashboard
* staff daily checklist
* admin checklist template manage surface

เป้าหมายไม่ใช่ “แต่งให้สวยขึ้นเฉย ๆ” แต่คือทำให้ product ดูเป็นระบบที่ออกแบบอย่างตั้งใจ มี hierarchy, guidance, และ state framing ที่ชัดขึ้น โดยยังอยู่บน frontend contract ที่ FE1/FE2 วางไว้แล้ว

---

# **2. Why This Phase Now**

หลัง FE1 และ FE2 ระบบมี:

* token baseline
* motion baseline
* shared component primitives
* reusable stat/empty/callout/timeline vocabulary

แต่ perception ฝั่งผู้ใช้ยังติดอยู่ที่:

**“ข้อมูลดีขึ้นแล้ว แต่หน้าหลักยังไม่ดูจบพอ”**

ดังนั้น FE3 คือ phase ที่เปลี่ยน primitive ให้กลายเป็นหน้าจริงที่ดูมีน้ำหนักทางสายตา, มีจังหวะการอ่าน, และมีความน่าเชื่อถือแบบ product screen จริง

---

# **3. Design Direction**

Direction ที่ใช้ใน phase นี้คือ:

**Industrial Command, Refined Edition**

หลักของ direction นี้:

* hero band ที่ให้ความรู้สึกเป็น command surface
* dark shell + bright surface contrast ที่ intentional
* status-heavy layout ที่ชัด ไม่หวือหวาเกินจำเป็น
* emphasis ผ่าน hierarchy, spacing, chip language, และ grouped panels
* เลี่ยง AI-slop patterns แบบ bland SaaS cards เรียงอย่างเดียว

---

# **4. Scope**

## **4.1 Included**

* เพิ่ม composition classes ใหม่ใน frontend layer
* redesign dashboard ให้เป็น command surface ชัดขึ้น
* redesign daily checklist ให้มี run-state framing และ execution rhythm ชัดขึ้น
* redesign template manage page ให้แยก edit surface กับ governance rail ชัดขึ้น

## **4.2 Not Included**

* schema changes
* product logic changes
* accessibility/polish wave ระดับลึกทั้งระบบ
* redesign ทุกหน้าของระบบพร้อมกัน

---

# **5. Implementation Summary**

## **5.1 New Composition Language**

เพิ่ม composition classes ใน `resources/css/app/ops.css`:

* `ops-hero*`
* `ops-shell-chip*`
* `ops-command-grid*`
* `ops-section-heading*`
* `ops-signal-card*`
* `ops-detail-list*`
* `ops-progress-panel`
* `ops-progress-bar*`
* `ops-item-group*`
* `ops-item-card*`
* `ops-admin-item*`

ผลคือ frontend มี layer ใหม่ที่อยู่เหนือ primitive เดิม และใช้วาง “screen composition” อย่างเป็นระบบ

## **5.2 Dashboard**

อัปเดต [dashboard.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/dashboard.blade.php)

แนวทาง:

* เพิ่ม hero band สำหรับ management visibility
* แยก attention queue ออกมาเป็น signal cards
* รักษา trend และ hotspot ไว้ แต่ยก hierarchy ให้ชัดขึ้น
* คงข้อความ canonical เดิมเพื่อไม่ทำลาย regression contract โดยไม่จำเป็น

## **5.3 Daily Checklist**

อัปเดต [daily-run.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/staff/checklists/daily-run.blade.php)

แนวทาง:

* เพิ่ม hero framing ให้ checklist ดูเป็น shift task จริง
* ย้าย progress + run guidance ไปอยู่ใน surface ที่เด่นขึ้น
* ทำ checklist item stack ให้อ่านเหมือน execution surface มากกว่าฟอร์มยาวธรรมดา
* เก็บ recent context ไว้ใน rail ที่อ่านง่ายขึ้น

## **5.4 Template Manage Surface**

อัปเดต [manage.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/admin/checklist-templates/manage.blade.php)

แนวทาง:

* เพิ่ม hero framing สำหรับ admin template authoring
* แยก `core definition + item editing` ออกจาก `governance + activation impact`
* ทำ item editor ให้ดูเป็น authoring canvas มากขึ้น
* เก็บ safer-iteration guidance ไว้เป็น card ที่ชัดและตั้งใจ

---

# **6. Expected Outcome**

หลัง FE3:

* dashboard ดูเป็น “command screen” มากขึ้น
* checklist ดูเป็น workflow surface ไม่ใช่ form bundle
* template management ดูเป็น admin authoring space ที่น่าเชื่อถือขึ้น
* FE1/FE2 primitives ถูกใช้งานในระดับ composition จริง ไม่หยุดแค่ reusable pieces

---

# **7. Verification**

ต้องผ่านอย่างน้อย:

* `composer lint:check`
* `php artisan test`
* `npm run build`
* `composer test:browser`

---

# **8. Exit Gate**

Phase FE3 ถือว่าเสร็จเมื่อ:

* 3 หน้าหลักถูก redesign แล้วโดยไม่หลุด regression contract เดิม
* screen composition ใหม่ใช้ app-owned classes มากกว่า ad-hoc utility overrides
* product surfaces ดูเป็นระบบเดียวกันและมี visual hierarchy สูงขึ้นอย่างชัดเจน

