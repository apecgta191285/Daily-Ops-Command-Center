# **Local Demo Runbook**

## *DOC-37-LDR | Lightweight local demonstration and reset guide*

**Version v1.0 | Local demo reference | วันที่อ้างอิง 14/04/2569**

# **1. Demo Accounts**

ใช้บัญชีมาตรฐานจาก `DatabaseSeeder`:

* `admin@example.com` / `password` = อาจารย์ผู้รับผิดชอบ / ผู้ได้รับมอบหมายดูแลระบบ
* `supervisor@example.com` / `password` = lab boy / เจ้าหน้าที่แล็บ / ผู้ดูแลห้อง
* `operatora@example.com` / `password` = นักศึกษาที่เข้าเวรตรวจห้องตามรอบ
* `operatorb@example.com` / `password` = นักศึกษาที่เข้าเวรตรวจห้องตามรอบ

# **2. Recommended Demo Order**

## **Step 1 — Staff workflow**

ล็อกอินเป็น `operatora@example.com`

สิ่งที่ควรโชว์:

* การเลือกห้องก่อนเริ่มงาน เมื่อมีหลายห้อง active
* หน้า checklist ของวันสำหรับห้องที่เลือก
* progress summary และ recent submission context
* การกด `Report Incident` พร้อม room context
* optional `equipment_reference` เช่น `PC-03` หรือ `Projector Front`

ผลที่ควรเล่า:

* นักศึกษาที่เข้าเวรรู้ว่ากำลังตรวจห้องไหน
* checklist ไม่ใช่ฟอร์มลอย แต่เชื่อมกับ incident handoff ได้โดยไม่ทำ room context หาย

## **Step 2 — Supervisor workflow**

ล็อกอินเป็น `supervisor@example.com`

สิ่งที่ควรโชว์:

* dashboard attention panel
* quick drill-down ไป incident list ที่ถูก filter แล้ว
* incident detail timeline และ next action note
* room context ที่ช่วยแยกว่าเป็นปัญหาของห้องไหน

ผลที่ควรเล่า:

* supervisor เห็นว่าห้องไหนมีอะไรต้องตามก่อน
* incident workflow มี sense ของ follow-up มากกว่าแค่รายการข้อมูล

## **Step 3 — Admin workflow**

ล็อกอินเป็น `admin@example.com`

สิ่งที่ควรโชว์:

* dashboard เหมือน management
* checklist template list/create/edit ใน shell เดียวกับระบบหลัก

ผลที่ควรเล่า:

* อาจารย์ผู้รับผิดชอบไม่ต้องออกไปอีกระบบ
* การดูแล template และบัญชีผู้ใช้ยังอยู่ใน product เดียวกับ daily operations

# **3. Expected Story in Seeded Data**

seed ปัจจุบันถูกออกแบบให้รองรับ demo narrative นี้:

* มี opening template ที่ active อยู่ 1 อัน
* มีข้อมูลห้อง `Lab 1` ถึง `Lab 5`
* มี checklist run ที่ผูกกับ room context อยู่แล้ว
* มี mix ของ incidents แบบ open / in progress / resolved พร้อม room context
* มี incident ที่ high severity และ stale เพื่อให้ dashboard มี attention state
* optional equipment reference ใช้แค่เป็นข้อความสั้น ไม่ใช่ machine registry

# **4. Local Reset**

ถ้าต้อง reset local data ให้กลับสู่ baseline เดโม:

```bash
php artisan migrate:fresh --seed
```

ถ้าจะรัน smoke suite หลัง reset:

```bash
php artisan test
composer test:browser
```

# **5. Demo Safety Notes**

* หน้า login แสดง demo accounts เฉพาะ `local/testing`
* อย่าอ้าง feature ที่ระบบยังไม่มี เช่น assignment, notifications, approvals
* ถ้าจะเดโม checklist follow-up handoff ให้สร้าง incident จาก checklist ของห้องที่เลือกจริง เพื่อให้เรื่องเล่าดูต่อเนื่อง
* อย่า claim ว่าระบบมี machine registry หรือ machine history ถ้ายังไม่มีจริง
