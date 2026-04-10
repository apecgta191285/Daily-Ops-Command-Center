# **13_Freeze_Checklist_Day15**

## **Purpose**

checklist สั้นสำหรับตัดสินใจว่า baseline ปัจจุบันพร้อม freeze สำหรับเดโมหรือยัง โดยอ้างอิง smoke pass จริงจาก repo ปัจจุบัน

## **1. Smoke Routine**

รันตามลำดับ:

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan route:list --except-vendor
```

## **2. Freeze Gate**

ถือว่า ready to freeze เมื่อ:

* `migrate:fresh --seed` ผ่าน
* `php artisan test` ผ่านครบทั้งชุด
* route สำคัญยังอยู่ครบ:
  * `/dashboard`
  * `/templates`
  * `/checklists/runs/today`
  * `/incidents/new`
  * `/incidents`
  * `/incidents/{incident}`
* demo accounts จาก seeder login ได้
* role landing path ยังตรง:
  * Admin → `/dashboard`
  * Supervisor → `/dashboard`
  * Staff → `/checklists/runs/today`
* staff ยังถูกบล็อกจาก management surface

## **3. Current Day 15 Result (06/04/2569)**

* `php artisan migrate:fresh --seed` ผ่าน
* `php artisan test` ผ่าน: 32 tests, 182 assertions
* critical demo routes ยังอยู่ครบ
* seeded baseline ยังให้ demo data ตาม runbook:
  * Operator A มี submitted checklist run
  * Operator B มี non-submitted checklist run
  * incidents มีครบ Open / In Progress / Resolved
* navigation regression baseline ยังผ่าน

## **4. Freeze Recommendation**

สถานะปัจจุบัน: **ready to freeze**

## **5. Last-Minute Rule**

หลังจุดนี้ให้แก้เฉพาะ bug ที่ทำให้ demo path แตกจริงเท่านั้น  
ห้ามเพิ่ม feature ใหม่, ห้ามเปลี่ยน schema, และห้ามแก้ flow หลักโดยไม่มี blocker ชัดเจน
