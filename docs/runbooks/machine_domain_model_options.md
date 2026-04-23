# Machine Domain Model Options
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้ใช้เปรียบเทียบทางเลือกเชิง domain สำหรับ `Option B`
โดยยึดหลักว่า current system เป็น `room-centered operations baseline`
และ machine domain ต้องไม่ทำลายข้อดีนั้น

## 2) Fixed Truth Before Comparing Options
ข้อเท็จจริงที่ทุก option ต้องเคารพ:
- `room` เป็น first-class entity อยู่แล้ว
- incident domain ปัจจุบันเป็น `mixed issue domain`
- ไม่ใช่ทุก incident เป็น machine issue
- current product ยังต้องรองรับ `equipment_reference` แบบ lightweight
- current management flow ใช้ room เป็น operational anchor หลัก

## 3) Option A — Keep Free-Text Only
แนวคิด:
- ไม่สร้าง machine entity
- คง `equipment_reference` แบบ free text ต่อไป

ข้อดี:
- ง่ายที่สุด
- ไม่แตะ schema ใหญ่
- ไม่เพิ่ม UX burden

ข้อเสีย:
- ไม่มี canonical identity
- track ประวัติของ asset เดิมไม่ได้อย่างน่าเชื่อถือ
- analytics ต่อ machine ทำไม่ได้จริง
- label drift สูง

คำตัดสิน:
- ดีสำหรับ current Option A baseline
- แต่ไม่พอถ้า goal คือ machine registry จริง

## 4) Option B — Machine-First Everywhere
แนวคิด:
- สร้าง machine entity
- incident ทุกตัวต้องผูก machine
- machine กลายเป็น anchor หลักของ incident domain

ข้อดี:
- identity continuity ชัดมาก
- machine history และ analytics ทำได้ตรง

ข้อเสีย:
- ขัดกับ requirement truth ที่บอกว่าหลาย incident ไม่ใช่ machine-specific
- ทำให้ network/cleanliness/environment issues ต้องกรอกข้อมูลฝืนจริง
- เปลี่ยน mental model ของ product มากเกินไป
- เสี่ยงทำลาย room-first operational simplicity

คำตัดสิน:
- เป็น model ที่แรงเกินจริงสำหรับ current domain
- ไม่ควร carry forward

## 5) Option C — Room-First With Optional Machine Identity
แนวคิด:
- room ยังคงเป็น operational anchor หลัก
- machine เป็น first-class entity เพิ่มเติม
- incident สามารถมี `machine_id` ได้แบบ optional
- `equipment_reference` ยังอยู่ต่อสำหรับ:
  - non-machine assets
  - ad hoc references
  - transitional usage

ข้อดี:
- สอดคล้องกับ current repo truth ที่สุด
- รองรับ mixed issue domain
- เปิดทางให้ machine history ได้โดยไม่บังคับทุก incident
- ยังรักษาความยืดหยุ่นของ room-level incidents

ข้อเสีย:
- model ซับซ้อนกว่า free text only
- ต้องนิยามกติกาว่าเมื่อไรใช้ machine_id และเมื่อไรใช้ equipment_reference
- ต้องระวัง duplicate meaning ระหว่าง machine label กับ free-text reference

คำตัดสิน:
- เป็น candidate ที่สมดุลที่สุดตอนนี้
- ควรถูก carry forward ไปประเมินใน Phase 4.3

## 6) Option D — Room Inventory Without Full Machine Lifecycle
แนวคิด:
- สร้าง machine registry แบบเบา
- machine belongs to room
- มีแค่ label/basic status
- ยังไม่ทำ lifecycle/history เต็ม
- incident อาจผูก machine optional

ข้อดี:
- เริ่มได้ง่ายกว่า full machine model
- ให้ canonical identity ขั้นต้นได้

ข้อเสีย:
- เสี่ยงตกอยู่ในสภาพ “ครึ่งกลาง”
- ถ้า status model อ่อนเกินไป อาจได้ CRUD ที่ดูดีแต่ไม่ตอบโจทย์จริง
- ต้องชัดว่ามันเป็น stepping stone ไม่ใช่ปลายทาง

คำตัดสิน:
- เป็น sub-variant ที่น่าสนใจของ Option C
- แต่ยังต้องประเมินใน Phase 4.3 ว่าความครึ่งกลางนี้คุ้มไหม

## 7) Working Recommendation
recommendation ของ phase นี้คือ:

`carry forward Option C`

พร้อมใช้ Option D เป็น reference variant ย่อย
ถ้าทีมอยากสำรวจทางเริ่มต้นที่เบากว่า full lifecycle model

## 8) Anti-Requirements
ไม่ว่าเลือก option ไหน phase ถัดไปต้องไม่ลืมว่า:
- ห้ามบังคับทุก incident ให้มี machine
- ห้ามลบ room-first framing
- ห้ามทิ้ง `equipment_reference` โดยไม่มีทางรองรับ non-machine assets
- ห้ามยัด machine domain เข้ามาเพียงเพื่อให้ระบบดูใหญ่ขึ้น
