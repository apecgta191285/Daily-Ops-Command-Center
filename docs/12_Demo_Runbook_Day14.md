# **12_Demo_Runbook_Day14**

## **Purpose**

runbook สั้นสำหรับเตรียมเดโม, reset baseline และใช้เป็น backup path ใน Day 14/15 โดยอ้างอิง implementation จริงของ repo ปัจจุบันเท่านั้น

## **1. Demo Accounts**

* Admin: `admin@example.com`
* Supervisor: `supervisor@example.com`
* Staff A: `operatora@example.com`
* Staff B: `operatorb@example.com`
* Password ทุกบัญชี: `password`

## **2. Pre-Demo Reset**

รันตามลำดับ:

```bash
php artisan migrate:fresh --seed
php artisan test
```

ผลที่ควรได้จาก seeded baseline:

* active checklist template มี 1 อัน
* Operator A มี daily checklist run ที่ submit แล้ว
* Operator B มี daily checklist run ที่ยังไม่ submit
* incident ตัวอย่างมีครบ 3 สถานะ: Open / In Progress / Resolved
* dashboard มีตัวเลขจริงให้อ่านได้ทันที

## **3. Main Walkthrough**

### **3.1 Staff Flow**

1. login เป็น `operatorb@example.com`
2. เปิด `/checklists/runs/today`
3. ตอบ checklist ให้ครบแล้ว submit
4. เปิด `/incidents/new`
5. สร้าง incident ใหม่แบบไม่แนบไฟล์

### **3.2 Management Flow**

1. logout แล้ว login เป็น `supervisor@example.com`
2. เปิด `/incidents`
3. เข้า incident detail ของ incident ที่เพิ่งสร้างหรือ incident ที่ยัง Open
4. เปลี่ยน status เป็น `In Progress` หรือ `Resolved`
5. ยืนยันว่า timeline แสดง `status_changed`
6. เปิด `/dashboard`
7. ชี้ให้เห็นว่า incident summary และ recent incidents สะท้อนข้อมูลจริง

### **3.3 Admin Flow**

1. login เป็น `admin@example.com`
2. เปิด `/templates`
3. แสดงว่า admin เข้า template management ได้
4. กลับไป `/dashboard` หรือ `/incidents` เพื่อยืนยัน management surface หลัก

## **4. Backup / Fallback Path**

ถ้า Staff checklist path มีปัญหา:

* ใช้ seeded proof แทน โดย login เป็น `operatora@example.com` แล้วแสดง run ที่ submit แล้ว

ถ้า incident ที่เพิ่งสร้างไม่พร้อมใช้:

* ใช้ incident ตัวอย่างจาก seed ใน `/incidents` แทน แล้วอัปเดต status ต่อหน้าผู้ดู

ถ้า dashboard ตัวเลขไม่ตรงกับสิ่งที่เพิ่งแก้:

* refresh หน้า `/dashboard`
* ถ้ายังผิด ให้ fallback ไปแสดง seeded baseline หลัง reset ใหม่ด้วย `php artisan migrate:fresh --seed`

ถ้า attachment path ใช้งานไม่พร้อม:

* ใช้ incident create แบบไม่แนบไฟล์ เพราะ attachment เป็น optional ใน scope v1

## **5. Demo-Path Critical Routes**

* `/dashboard`
* `/templates`
* `/checklists/runs/today`
* `/incidents/new`
* `/incidents`
* `/incidents/{incident}`

## **6. Final Pre-Demo Check**

* `php artisan test` ผ่าน
* login ตาม role ได้
* staff ถูกบล็อกจาก `/dashboard` และ `/incidents`
* dashboard เปิดได้
* incident list/detail/update status ใช้งานได้
* checklist run ใช้งานได้อย่างน้อย 1 path
