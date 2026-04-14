# **Local Demo Runbook**

## *DOC-37-LDR | Lightweight local demonstration and reset guide*

**Version v1.0 | Local demo reference | วันที่อ้างอิง 14/04/2569**

# **1. Demo Accounts**

ใช้บัญชีมาตรฐานจาก `DatabaseSeeder`:

* `admin@example.com` / `password`
* `supervisor@example.com` / `password`
* `operatora@example.com` / `password`
* `operatorb@example.com` / `password`

# **2. Recommended Demo Order**

## **Step 1 — Staff workflow**

ล็อกอินเป็น `operatora@example.com`

สิ่งที่ควรโชว์:

* หน้า checklist ของวัน
* progress summary และ recent submission context
* การกด `Report Incident`

ผลที่ควรเล่า:

* staff ทำ routine work ใน flow เดียว
* checklist ไม่ใช่ฟอร์มลอย แต่เชื่อมกับ incident handoff ได้

## **Step 2 — Supervisor workflow**

ล็อกอินเป็น `supervisor@example.com`

สิ่งที่ควรโชว์:

* dashboard attention panel
* quick drill-down ไป incident list ที่ถูก filter แล้ว
* incident detail timeline และ next action note

ผลที่ควรเล่า:

* supervisor เห็นว่ามีอะไรต้องตามก่อน
* incident workflow มี sense ของ follow-up มากกว่าแค่รายการข้อมูล

## **Step 3 — Admin workflow**

ล็อกอินเป็น `admin@example.com`

สิ่งที่ควรโชว์:

* dashboard เหมือน management
* checklist template list/create/edit ใน shell เดียวกับระบบหลัก

ผลที่ควรเล่า:

* admin ไม่ได้ออกไปอีกระบบ
* การดูแล template อยู่ใน product เดียวกับ daily operations

# **3. Expected Story in Seeded Data**

seed ปัจจุบันถูกออกแบบให้รองรับ demo narrative นี้:

* มี opening template ที่ active อยู่ 1 อัน
* มี checklist run ล่าสุดของ staff บางส่วนที่ถูก submit แล้ว
* มี mix ของ incidents แบบ open / in progress / resolved
* มี incident ที่ high severity และ stale เพื่อให้ dashboard มี attention state

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
* ถ้าจะเดโม checklist follow-up handoff ให้สร้าง incident จาก checklist ที่มี `Not Done` เพื่อให้เรื่องเล่าดูต่อเนื่อง
