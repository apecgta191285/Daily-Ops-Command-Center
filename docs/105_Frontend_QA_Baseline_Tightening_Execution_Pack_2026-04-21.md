# **Frontend QA Baseline Tightening — Execution Pack**

**DOC-105 | Post-alignment QA tightening only**  
**Date:** 2026-04-21  
**Status:** Implemented

---

## **1. Intent**

ปิดช่องว่างที่ยังเหลือหลัง `Story Alignment + Docs Pruning` โดยเพิ่ม QA proof เฉพาะจอ authenticated ที่ deterministic พอสำหรับ screenshot gate จริง

รอบนี้ตั้งใจ **ไม่เปิด feature wave ใหม่**

สิ่งที่ทำคือ:

* ขยาย browser visual baseline จาก guest surfaces ไปยัง authenticated admin governance reference surface ที่นิ่งพอ
* คง accessibility assertions ไว้คู่กับ screenshot proof
* อัปเดต current-state docs ให้รายงานสถานะ QA อย่างแม่นขึ้น

---

## **2. Why this round matters**

หลัง DOC-104 repo มี:

* guest screenshot baseline
* browser smoke coverage
* accessibility assertions
* UI governance artifact

แต่ยังเหลือ gap ตรง:

* authenticated screenshot proof ยังบางเกินไป
* current-state wording ยังพูดกว้างกว่าของจริงเล็กน้อย

ดังนั้นรอบนี้จึงเก็บเฉพาะ `QA baseline tightening only`

---

## **3. Implemented scope**

### **3.1 Browser visual proof expanded**

เพิ่ม deterministic screenshot gate สำหรับ:

* `/ui-governance`

โดยใช้ browser flow ที่:

* authenticate เป็น admin
* สร้างข้อมูลขั้นต่ำที่ deterministic
* ยืนยัน smoke + accessibility + screenshot baseline บน `ui-governance`
* ยืนยัน smoke + accessibility proof บน `/templates` และ `/users`

ทั้งหมดถูกเพิ่มใน:

* `tests/Browser/SmokeTest.php`

### **3.2 Docs truth tightened**

อัปเดต:

* `docs/04_Current_State_v1.3.md`

ให้สะท้อนความจริงล่าสุดว่า:

* guest visual baseline มีจริง
* deterministic admin governance visual baseline มีจริง
* authenticated QA coverage ยังไม่ครบทั้ง runtime-heavy surfaces

---

## **4. Explicit non-goals**

รอบนี้ไม่ได้ทำ:

* feature ใหม่
* schema change
* route contract change
* runtime snapshot gate สำหรับ dashboard/checklist/incident detail ที่ยังมีความแปรผันสูง
* export/reporting expansion

---

## **5. Acceptance criteria**

รอบนี้ถือว่าสำเร็จเมื่อ:

* browser suite มี screenshot baseline สำหรับ guest + deterministic admin governance reference surface
* accessibility assertions ยังผ่าน
* docs current truth พูดตรงกับของที่ลงจริง
* ไม่มี scope creep ออกนอก `QA tightening only`

---

## **6. Result**

คำตัดสินของรอบนี้:

**frontend QA baseline แน่นขึ้นอย่างมีวินัย**

แต่ยังไม่ overclaim ว่า screenshot gate ครอบคลุมทุก authenticated surface แล้ว

สถานะที่ถูกต้องที่สุดหลังรอบนี้คือ:

* guest surfaces: baseline landed
* deterministic admin governance surfaces: baseline landed
* runtime-heavy authenticated surfaces: smoke/accessibility proof present, but not yet full screenshot gate
