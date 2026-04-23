# Incident Triage SOP
วันที่: 23 เมษายน 2026

## 1) Purpose
SOP นี้ใช้สำหรับการ triage `product incidents` ที่ถูกบันทึกในระบบงานประจำของห้องแล็บ
เช่นปัญหาที่ staff รายงานเข้ามาแล้ว management ต้อง review และติดตามต่อ

เอกสารนี้ไม่ใช่ตัวเดียวกับ [incident_triage_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/incident_triage_runbook.md)
เพราะ runbook เดิมใช้กับ `operational incident` ของตัวระบบเว็บเอง

## 2) Triage Scope
SOP นี้ใช้เมื่อ:
- staff สร้าง incident record ใน product workflow
- management ต้องตัดสินใจว่าใครจะเป็น owner
- ต้องกำหนด next action หรือ follow-up date
- ต้อง review ว่างานยัง open, in progress, หรือ resolved

ไม่ใช้เมื่อ:
- ระบบเว็บล่ม
- login ใช้ไม่ได้ทั้งระบบ
- deploy fail
- production logs มี exception ซ้ำ ๆ

กรณีเหล่านั้นให้ใช้ `incident_triage_runbook.md`

## 3) Triage Entry Points
จุดเริ่ม triage หลัก:
- `/dashboard` เมื่อ attention cards หรือ workboard ชี้ว่ามีงานค้าง
- `/incidents` เมื่อต้อง review queue โดยตรง
- `/incidents/{incident}` เมื่อต้องตัดสินใจเชิงรายละเอียด

## 4) Triage Procedure
ลำดับแนะนำ:

1. ยืนยันว่า incident นี้เป็น product incident จริง
2. ดู room context, severity, category, และเวลาที่สร้าง
3. ตัดสินว่า:
   - ต้องมี owner ทันทีหรือไม่
   - ต้องเริ่มงานตอนนี้หรือแค่ตั้ง follow-up
   - เป็น `Open`, `In Progress`, หรือ `Resolved`
4. ถ้างานต้องมีผู้รับผิดชอบ ให้ assign owner
5. ถ้ายังไม่จบในรอบนี้ ให้ตั้ง follow-up target
6. ถ้ามี next step หรือเหตุผลการปิดงาน ให้ใส่ note ลง activity timeline
7. กลับไป queue/dashboard เพื่อตรวจว่าไม่มีงานสำคัญหลุดค้าง

## 5) Filter-Driven Review Routine
queue ปัจจุบันมี filter ที่ควรใช้แบบมีวินัย:
- `unresolved` ใช้เป็น baseline view ของงานที่ยังไม่ปิด
- `unowned` ใช้จับงานที่ยังไม่มีเจ้าภาพ
- `mine` ใช้ review ภาระงานที่ตัวเองถืออยู่
- `overdue` ใช้จับงานที่เลย follow-up target แล้ว
- `stale` ใช้จับงาน unresolved ที่ค้างนานเกิน threshold
- `status`, `category`, `severity` ใช้เพื่อ zoom-in ตามโจทย์เฉพาะ

หลักการ:
- อย่า review queue แบบเลื่อนดูยาว ๆ โดยไม่ใช้ filter
- ให้ใช้ filter เพื่อสร้าง decision surface ที่ชัดและทำซ้ำได้

## 6) Ownership and Status Rules
- owner ควรเป็นคนที่มีอำนาจติดตามหรือปิดงานต่อได้จริง
- ในระบบปัจจุบัน owner ต้องเป็น `Admin` หรือ `Supervisor`
- status เปลี่ยนเมื่อ state ของงานเปลี่ยนจริง ไม่ใช่เพื่อให้ queue ดูสะอาด
- `Resolved` ควรถูกใช้เมื่อเงื่อนไขปัญหาสิ้นสุดแล้ว หรือมีเหตุผลชัดว่าปิดได้จริง

## 7) Notes and Activity Discipline
- ถ้าเปลี่ยน status และมี next step สำคัญ ให้ใส่ note
- ถ้าปิดงาน ให้เขียน resolution note แบบสั้นแต่มีความหมาย
- activity timeline ต้องช่วยให้คนถัดไปเข้าใจว่าเกิดอะไรขึ้น
- อย่าใช้ note เป็นเพียงข้อความกว้าง ๆ ที่ไม่ช่วยการส่งต่องาน

## 8) When To Escalate
ควรหยุดและ escalate เมื่อ:
- incident high severity ยังไม่มี owner
- มีหลาย incident ในห้องเดียวกันหรือ category เดียวกันจนเริ่มเป็น pattern
- follow-up ค้างจนเกินขอบเขตที่ supervisor จัดการเองได้
- ดูเหมือนปัญหาจะเป็น issue ของระบบเว็บเอง ไม่ใช่เหตุการณ์หน้างาน
- ข้อมูลใน incident ไม่พอให้ตัดสินใจอย่างรับผิดชอบ

## 9) Honest Limitation
SOP นี้เป็น `incident triage baseline` สำหรับ product support flow
ไม่ใช่ incident management framework เต็มรูปแบบ

สิ่งที่ยังไม่มี:
- SLA policy
- paging / on-call
- ticket handoff automation
- RCA / postmortem workflow
