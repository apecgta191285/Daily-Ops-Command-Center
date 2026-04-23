# Machine Discovery Brief
วันที่: 23 เมษายน 2026

## 1) Purpose
brief นี้ใช้สรุป requirement truth ขั้นต้นสำหรับ `Option B / machine registry`
โดยตั้งต้นจากสิ่งที่ระบบปัจจุบันทำได้จริง
ไม่ใช่จากความอยากได้ feature ล้วน ๆ

## 2) Current Truth
ระบบปัจจุบันทำได้แล้ว:
- ผูก checklist runtime และ incident กับ `room`
- ให้ staff รายงาน incident พร้อม `optional equipment reference`
- ให้ management เห็น room context, queue, history, และ follow-up flow จริง

ระบบปัจจุบันยังไม่มี:
- canonical machine identity
- machine registry CRUD
- machine lifecycle/history
- machine-to-room relationship model
- machine-level reporting or analytics

## 3) What Problem Is Already Solved Without Machine Registry
โจทย์ที่ current model แก้ได้อยู่แล้ว:
- รู้ว่าปัญหาเกิดในห้องไหน
- รู้ว่า incident นี้เกี่ยวกับอุปกรณ์ชิ้นไหนแบบข้อความสั้น
- ให้ supervisor/admin follow-up งานรายห้องได้
- เชื่อม checklist follow-up ไป incident ได้ในกรอบ room-centered workflow

ดังนั้น machine registry ไม่ควรถูกอ้างว่าเป็น “สิ่งจำเป็นทันที” เพื่อให้ Option A ใช้งานได้

## 4) What Problem May Still Need a Machine Entity
machine entity อาจจำเป็นเมื่อโจทย์เริ่มต้องการ:
- แยกอุปกรณ์ที่ชื่อคล้ายกันแต่เป็นคนละตัวอย่างแม่นยำ
- เก็บประวัติปัญหาของอุปกรณ์ตัวเดิมข้ามเวลา
- วิเคราะห์ความถี่เสียของเครื่องแต่ละตัว
- รู้ว่าเครื่องย้ายห้องหรือเลิกใช้งานเมื่อไร
- ทำให้ incident หลายรายการอ้างถึง asset เดิมอย่างสม่ำเสมอ

## 5) Key Discovery Insight
จาก repo truth ปัจจุบัน ปัญหาไม่ได้อยู่ที่ “ไม่มี machine table เลยใช้ระบบไม่ได้”
แต่เป็น:
- current baseline ตั้งใจ optimize เรื่อง `room operations`
- machine-level traceability เป็นคนละระดับของ domain depth
- ถ้าจะเปิด domain นี้ ต้องตอบก่อนว่าคุณค่าที่ได้คุ้มกับ schema/workflow complexity หรือไม่

## 6) Discovery Hypothesis
สมมติฐานที่ phase ถัดไปควรทดสอบ:

1. ไม่ใช่ทุก incident ต้องผูก machine
2. room-first workflow ควรยังเป็น baseline ต่อไป แม้จะมี machine domain เพิ่ม
3. `equipment_reference` อาจยังต้องอยู่ต่อสำหรับ non-machine assets หรือกรณี ad hoc
4. machine registry จะคุ้มก็ต่อเมื่อทีมต้องการ `persistent asset identity` จริง ไม่ใช่แค่ label สำหรับเดโม

## 7) Honest Limitation
brief นี้ยังไม่ตอบ:
- machine schema ควรหน้าตาอย่างไร
- machine belongs-to-room เสมอไหม
- ต้องมี machine status states อะไรบ้าง
- ROI คุ้มพอให้ implement หรือไม่

สิ่งเหล่านี้เป็นงานของ Phase 4.2 และ 4.3
