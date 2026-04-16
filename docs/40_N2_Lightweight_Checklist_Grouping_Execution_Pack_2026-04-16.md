# **N2 Lightweight Checklist Grouping Execution Pack**

## *Post-F5 Feature Wave / Execution Slice 2*

**DOC-40-N2-EXEC | วันที่อ้างอิง 16/04/2569**

วัตถุประสงค์: เพิ่มโครงสร้างให้ checklist รายวันดูเป็น workflow ที่อ่านง่ายขึ้น โดยใช้ `group label` แบบเบา ๆ ต่อ item แทนการเปิดระบบ section hierarchy เต็มรูปแบบ

---

## **1. Problem Statement**

หลัง F3 checklist ใช้งานได้ลื่นขึ้น แต่ยังเป็นรายการยาวชุดเดียวในเชิงการรับรู้ของผู้ใช้:

* operator ต้องไล่อ่านรายการต่อกันแบบไม่มี section cue
* template management ยังไม่มีทางแบ่งกลุ่มงานอย่างเบา
* product perception ยังดูเป็น “ฟอร์มเช็กงาน” มากกว่า “workflow ที่ถูกออกแบบ”

Brutal truth: ถ้าไม่เพิ่ม grouping ตอนนี้ checklist จะยังดูบาง แม้ functional flow จะดีขึ้นแล้ว

---

## **2. Scope**

อยู่ใน scope:

* เพิ่ม `group_label` แบบ optional ให้ checklist items
* รองรับการกรอก group label ใน template manage surface
* render section headings ใน daily checklist ตามลำดับ item
* copy `group_label` ใน duplicate/save flows
* เพิ่ม regression coverage

ไม่อยู่ใน scope:

* nested groups
* collapsible sections
* analytics by group
* conditional branching checklist

---

## **3. Chosen Design**

แนวทางที่เลือก:

* ใช้ field `group_label` nullable ตรงบน `checklist_items`
* section heading เกิดจาก label ที่ซ้ำกันใน items ที่เรียงติดกัน
* item ที่ไม่มี label ใช้ fallback `General checks`

เหตุผลที่เลือก:

* schema change เล็กและตรงไปตรงมา
* ไม่ต้องเพิ่ม table ใหม่
* render ง่ายใน Blade และไม่เพิ่ม logic layer ที่เกินจำเป็น
* พอสำหรับ solo-dev project ที่ต้องการความเป็นระบบมากขึ้นโดยไม่ over-engineer

---

## **4. Acceptance Criteria**

งานนี้ถือว่าสำเร็จเมื่อ:

* admin ใส่ group label ให้แต่ละ checklist item ได้
* save/update/duplicate คงค่า group label ถูกต้อง
* staff เห็น section headings ใน daily checklist ตาม group label
* item ที่ไม่มี group label ยังใช้งานได้โดยไม่พัง
* tests ครอบคลุม grouped rendering และ template persistence

---

## **5. Verification**

* `php artisan migrate`
* `composer lint:check`
* `php artisan test tests/Feature/AdminSurfaceBoundaryTest.php`
* `php artisan test tests/Feature/ChecklistDailyRunTest.php`
* `php artisan test`
* `composer test:browser`

---

## **6. Decision Summary**

งานนี้เป็นการเพิ่ม “structure perception” ให้ checklist แบบคุ้มที่สุดในบริบท A-lite: schema change เล็ก, UX ดีขึ้นชัด, และไม่บวมไปเป็น checklist builder เต็มระบบ
