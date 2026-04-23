# Supervisor SOP
วันที่: 23 เมษายน 2026

## 1) Purpose
SOP นี้ใช้สำหรับ `Supervisor` ที่ดูแลงานประจำวันใน Daily Ops Command Center
โดยยึด capability ปัจจุบันของ dashboard, workboard, incident queue, และ incident detail

## 2) Current Supervisor Responsibilities
supervisor ใน repo ปัจจุบันรับผิดชอบอย่างน้อย:
- เปิดดู dashboard เพื่อดูภาพรวมของวัน
- ใช้ workboard เพื่อตรวจ lane ที่ยังไม่ปิดงาน
- review incident queue และ incident detail
- รับ ownership ของ incident เมื่อควรมีผู้รับผิดชอบชัดเจน
- ตั้ง follow-up target สำหรับงานที่ยังไม่ปิด
- ขยับ status ไปตามสภาพงานจริง

สิ่งที่ supervisor role ปัจจุบันยังไม่ใช่:
- admin ผู้ดูแล users หรือ templates
- helpdesk manager เต็มรูปแบบ
- on-call incident commander
- infrastructure operator

## 3) Supervisor Entry Points
supervisor ใช้งานผ่าน route หลัก:
- `/dashboard`
- `/incidents`
- `/incidents/{incident}`

## 4) Start-of-Shift Routine
เมื่อเริ่มงาน ควรทำอย่างน้อย:
- เปิด `/dashboard`
- ดู `Needs Attention Today`
- ดู `Ownership and Work Buckets`
- ดู `Today's room workboard`
- ถ้ามีสัญญาณ `unowned`, `overdue`, `stale`, หรือ `high severity`
  ให้ drill-down เข้า `/incidents` ต่อทันที

## 5) Incident Review Routine
เมื่อเข้า queue:
- ใช้ `unresolved` เพื่อดูงานที่ยังไม่ปิด
- ใช้ `unowned` เพื่อหา incident ที่ยังไม่มีเจ้าภาพ
- ใช้ `mine` เพื่อตามงานที่ตัวเองรับไว้
- ใช้ `overdue` เพื่อตามงานที่ follow-up target เลยกำหนด
- ใช้ `stale` เพื่อตามงานที่ค้างเกิน threshold ของระบบ

หลักคิด:
- อย่าใช้ status อย่างเดียวแล้วคิดว่าคุมงานพอแล้ว
- ให้ดู `owner`, `follow-up target`, และ `latest note` ร่วมกันเสมอ

## 6) Ownership Rules
- incident ที่ยังไม่ resolved ควรมี owner เมื่อเริ่มมีงานติดตามจริง
- owner ในระบบปัจจุบันต้องเป็น `Admin` หรือ `Supervisor`
- ถ้ายังไม่พร้อมรับผิดชอบจริง อย่ารับ owner ไว้เฉย ๆ
- ถ้าจะ clear owner ต้องมีเหตุผลชัดว่าใครจะติดตามต่อ

## 7) Status Rules
- `Open` ใช้กับงานที่เพิ่งรับรู้หรือยังไม่เริ่มลงมือจริง
- `In Progress` ใช้เมื่อมี owner หรือมี next step ที่กำลังดำเนินอยู่
- `Resolved` ใช้เมื่อเงื่อนไขปัญหาสิ้นสุดจริง ไม่ใช่แค่เงียบชั่วคราว
- เมื่อเปลี่ยน status ควรใส่ note ถ้ามีข้อมูลที่คนถัดไปจำเป็นต้องเห็น

## 8) Follow-Up Discipline
- ถ้างานยังไม่จบและต้องกลับมาติดตาม ควรตั้ง follow-up target
- follow-up target ที่เลยกำหนดจะกลายเป็น `overdue`
- อย่าตั้งวันที่เพื่อให้ดูสวย แต่ไม่มี intent จะติดตามจริง
- ถ้าปัญหาจบแล้ว ให้ปิด status เป็น `Resolved` แทนการปล่อย target ค้าง

## 9) Escalation Guidance
ควร escalate ไปหา admin หรือทีมที่เกี่ยวข้องเมื่อ:
- incident มีผลต่อหลายห้องหรือหลาย lane
- incident high severity ยังไม่มี owner
- follow-up ค้างหลายวันและติด dependency ที่ supervisor ปิดเองไม่ได้
- พบว่า room context หรือ historical activity ใน incident ดูไม่สอดคล้องกับความจริง
- ปัญหาไม่ใช่ product incident แต่เป็น operational incident ของตัวระบบเว็บเอง

## 10) Honest Limitation
SOP นี้เป็น `supervisor operating baseline`
ไม่ใช่ dispatch center manual เต็มรูปแบบ

สิ่งที่ยังไม่มี:
- queue assignment automation
- formal SLA ownership
- support ticket integration
- after-hours escalation framework
