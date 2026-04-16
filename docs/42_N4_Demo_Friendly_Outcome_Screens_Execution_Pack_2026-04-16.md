# **N4 Demo-Friendly Outcome Screens Execution Pack**

## *Post-F5 Feature Wave / Execution Slice 4*

**DOC-42-N4-EXEC | วันที่อ้างอิง 16/04/2569**

วัตถุประสงค์: ทำให้ช่วง “ทำเสร็จแล้วเกิดอะไรต่อ” ของระบบดูจบและสื่อคุณค่าได้ดีขึ้น โดยเริ่มจาก outcome screen หลัง staff report incident สำเร็จ

---

## **1. Problem Statement**

ก่อนงานนี้ incident creation flow จบด้วย success flash อย่างเดียว:

* user รู้ว่า submit ผ่าน แต่ไม่เห็น recap ชัด
* ไม่มี guidance ว่าขั้นต่อไปคืออะไร
* ตอนเดโม ระบบยังขาดช่วง “เห็นผลลัพธ์ของการกระทำ”

Brutal truth: ถ้าจะให้ระบบดูเป็น product ที่คิดมาครบ เราต้องทำจังหวะ after-submit ให้มีความหมายกว่านี้

---

## **2. Scope**

อยู่ใน scope:

* outcome screen หลัง create incident สำเร็จ
* แสดง submission recap
* แสดง what-happens-next guidance
* ถ้ามาจาก checklist ให้มีปุ่มกลับไป flow เดิม
* มีปุ่ม report another incident
* เพิ่ม regression coverage

ไม่อยู่ใน scope:

* dedicated incident success route
* notifications
* PDF/email receipt
* per-user incident history page

---

## **3. Chosen Design**

แนวทางที่เลือก:

* ใช้ state ใน Livewire component แทนการ redirect ไป route ใหม่
* เก็บ recap เฉพาะข้อมูลที่ช่วย user เข้าใจ outcome
* ถ้ามาจาก checklist flow ให้รักษา return path ชัดเจน

เหตุผลที่เลือก:

* implementation เล็กและ reversible
* ไม่เพิ่ม route/URL complexity
* เหมาะกับ A-lite scope และช่วย demo quality ชัดเจน

---

## **4. Acceptance Criteria**

งานนี้ถือว่าสำเร็จเมื่อ:

* submit incident แล้วเห็น outcome screen แทน flash อย่างเดียว
* outcome screen แสดง title/category/severity/status/created-at
* outcome screen แสดง guidance ว่าทีม management จะเห็น incident นี้ต่ออย่างไร
* ถ้ามาจาก checklist มีปุ่มกลับไป checklist
* user เริ่ม report another incident ได้จากหน้าเดิม

---

## **5. Verification**

* `composer lint:check`
* `php artisan test tests/Feature/IncidentCreateTest.php`
* `php artisan test`
* `composer test:browser`

---

## **6. Decision Summary**

ก้อนนี้เป็นงาน polish ที่ `คุ้ม` เพราะช่วยให้ระบบดูจบขึ้นจริงโดยไม่เปิด complexity ใหม่ และยังเพิ่มทั้ง usability และ demo confidence พร้อมกัน
