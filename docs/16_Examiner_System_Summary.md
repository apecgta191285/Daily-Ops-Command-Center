# **16_Examiner_System_Summary**

## **Problem**

งานตรวจเช็กประจำวันและการรายงาน incident แบบ manual หรือผ่านแชตมักมีปัญหาเรื่องความครบถ้วน, การติดตามสถานะ, และการย้อนดูหลักฐานว่าใครทำอะไรเมื่อไร

## **Solution**

A-lite เป็นเว็บแอป MVP สำหรับทีมขนาดเล็กที่รวม:

* daily checklist run
* structured incident reporting
* management incident tracking
* dashboard summary จากข้อมูลจริง

บริบทเดโมที่ใช้คือห้องปฏิบัติการคอมพิวเตอร์ในมหาวิทยาลัย โดยใช้ข้อมูลจำลองทั้งหมด

## **Main Modules**

* Staff Checklist
  * เปิด checklist ของวัน
  * auto-create run ถ้ายังไม่มี
  * submit พร้อมบันทึกผลจริง

* Staff Incident Create
  * สร้าง incident พร้อม category, severity, description
  * attachment เป็น optional

* Management Incident Monitoring
  * Admin/Supervisor ดู incident list และ detail
  * อัปเดต status ได้
  * มี activity timeline ขั้นต่ำ

* Dashboard Summary
  * checklist completion วันนี้
  * incident counts ตาม status
  * recent incidents

## **Roles**

* Admin
  * dashboard
  * incident management
  * template management

* Supervisor
  * dashboard
  * incident management

* Staff
  * checklist ของวันนี้
  * incident create
  * ไม่มีสิทธิ์เข้า management surface

## **Proof**

* ระบบใช้ SQLite local database และ seeded demo data
* มี WSL runtime baseline ที่ตรวจแล้ว
* full automated test suite ผ่าน
* มี demo reset path:

```bash
php artisan migrate:fresh --seed
php artisan test
```

## **Scope Boundary**

สิ่งที่ intentionally ไม่อยู่ใน v1:

* incident assignment / reassignment
* advanced analytics
* exports
* notifications
* multi-organization support
