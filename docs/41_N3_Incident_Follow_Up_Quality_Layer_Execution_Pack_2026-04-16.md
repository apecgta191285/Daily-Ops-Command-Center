# **N3 Incident Follow-up Quality Layer Execution Pack**

## *Post-F5 Feature Wave / Execution Slice 3*

**DOC-41-N3-EXEC | วันที่อ้างอิง 16/04/2569**

วัตถุประสงค์: ทำให้ incident detail ดูเป็นพื้นที่ติดตามงานที่ “มีความต่อเนื่อง” มากขึ้น โดยเพิ่มการสื่อสารทิศทาง follow-up ล่าสุดและสรุปการปิดงานเมื่อ incident ถูก resolve

---

## **1. Problem Statement**

หลัง F2 incident workflow ดีขึ้นแล้ว แต่ยังมีช่องว่างเชิงคุณภาพ:

* next action note มีอยู่ แต่ต้องไล่อ่านใน timeline
* เมื่อ resolve incident แล้วยังไม่มีช่องสรุปว่า “แก้อะไรไป”
* incident detail ยังบอกสถานะได้ดี แต่ยังไม่ช่วยสื่อ context ล่าสุดเร็วพอ

Brutal truth: ถ้าอยากให้ incident module ดูจริงจังขึ้น เราต้องเพิ่ม “latest follow-up meaning” ไม่ใช่แค่เพิ่มสถานะอีกตัว

---

## **2. Scope**

อยู่ใน scope:

* เปลี่ยน note field ให้รองรับทั้ง next action และ resolution summary ตาม target status
* เก็บ resolution summary เป็น append-only activity
* แสดง latest follow-up direction และ latest resolution summary เป็น summary cards บน incident detail
* ปรับ timeline labels ให้อ่านง่ายขึ้น
* เพิ่ม regression coverage

ไม่อยู่ใน scope:

* incident assignment
* SLA / due dates
* notifications
* threaded comments

---

## **3. Chosen Design**

แนวทางที่เลือก:

* ใช้ช่องกรอกเดียวใน UI แต่ label/help เปลี่ยนตาม target status
* เมื่อ resolve incident ให้บันทึก `resolution_note`
* เมื่อเปลี่ยนสถานะอื่นให้บันทึก `next_action_note`
* ดึง latest summary cards จาก append-only activity trail เดิม ไม่สร้าง table ใหม่

เหตุผลที่เลือก:

* complexity ต่ำ
* รักษา append-only audit story
* value ชัดกับ UX ของ management users
* ไม่เปิดระบบ discussion หรือ workflow engine ก่อนเวลา

---

## **4. Acceptance Criteria**

งานนี้ถือว่าสำเร็จเมื่อ:

* เปลี่ยนสถานะเป็น `Resolved` แล้วใส่ resolution summary ได้
* resolution summary ถูกเก็บเป็น activity แยกจาก next action note
* หน้า incident detail แสดง latest next action และ latest resolution summary ได้
* timeline labels อ่านง่ายขึ้น
* tests ครอบคลุมทั้ง action layer และ Livewire detail surface

---

## **5. Verification**

* `composer lint:check`
* `php artisan test tests/Feature/Application/TransitionIncidentStatusActionTest.php`
* `php artisan test tests/Feature/IncidentManagementTest.php`
* `php artisan test`
* `composer test:browser`

---

## **6. Decision Summary**

ก้อนนี้เป็นการเพิ่ม “คุณภาพของการติดตามงาน” โดยไม่ข้ามไปทำระบบ assignment หรือ collaboration เต็มรูปแบบ จึงเหมาะกับ A-lite baseline และให้ผลลัพธ์เชิง perception สูงกว่าความซับซ้อนที่เพิ่มเข้าไป
