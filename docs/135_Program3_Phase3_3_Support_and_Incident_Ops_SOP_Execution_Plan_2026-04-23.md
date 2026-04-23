# Program 3 / Phase 3.3 — Support / Incident Ops SOP Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 3 / Phase 3.3 — Support / Incident Ops SOP` ให้เป็นก้อนงานที่ใช้งานได้จริง
โดยยึด current repo truth ของ dashboard, workboard, incident queue, incident detail, และ accountability flow
และตั้งใจแยกให้ชัดระหว่าง `product incident operations` กับ `operational incident triage` ของตัวระบบเอง

## 2) Repo Truth Used For This Plan
สิ่งที่ repo มีอยู่แล้ว:
- management-only route family สำหรับงานติดตาม incident:
  - `/dashboard`
  - `/incidents`
  - `/incidents/{incident}`
- incident list รองรับ filters จริง:
  - `status`
  - `category`
  - `severity`
  - `unresolved`
  - `stale`
  - `unowned`
  - `mine`
  - `overdue`
- incident detail รองรับงานปฏิบัติจริง:
  - เปลี่ยน status
  - ระบุ owner
  - ระบุ follow-up target date
  - บันทึก next action note
  - บันทึก resolution note
- owner ของ incident ต้องเป็น `Admin` หรือ `Supervisor`
- dashboard/workboard เป็น `today-first management surface`
  ไม่ใช่ ticket queue เต็มรูปแบบ
- มี `incident_triage_runbook.md` อยู่แล้ว
  แต่ runbook นั้นใช้กับ `operational incident` ของตัวระบบเว็บเอง
  ไม่ใช่ product incident records ที่เกิดจากงานในห้องแล็บ

คำแปลเชิงวิศวกรรม:
- phase นี้ไม่ควรสร้าง feature ใหม่เพื่อ “ทำให้ SOP ดูสมบูรณ์”
- แต่ต้องยกระดับ management flow ที่มีอยู่แล้วให้กลายเป็น routine operating discipline
- เอกสารต้องสะท้อนข้อจำกัดจริงของ roles, filters, ownership, และ dashboard semantics

## 3) Executive Decision
Phase 3.3 ควรจบด้วย baseline แบบนี้:
- มี `supervisor_sop.md`
- มี `incident_triage_sop.md`
- มี `workboard_usage_sop.md`
- มีข้อความชัดว่ามันคือ `product support / incident handling baseline`

แต่ยังไม่ claim ว่า:
- มี helpdesk platform แล้ว
- มี SLA / SLO management framework แล้ว
- มี on-call rota แล้ว
- มี escalation automation แล้ว
- มี machine-level incident model แล้ว

## 4) Deliverables
Phase นี้ควรจบด้วย 4 deliverables:

1. [135_Program3_Phase3_3_Support_and_Incident_Ops_SOP_Execution_Plan_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/135_Program3_Phase3_3_Support_and_Incident_Ops_SOP_Execution_Plan_2026-04-23.md)
2. [supervisor_sop.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/supervisor_sop.md)
3. [incident_triage_sop.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/incident_triage_sop.md)
4. [workboard_usage_sop.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/workboard_usage_sop.md)

## 5) Hard Boundaries
Phase นี้ยังไม่ทำ:
- helpdesk / ticketing integration
- SLA automation
- paging / on-call workflow
- machine-incident redesign
- incident semantics redesign
- external support team process
- postmortem program framework

## 6) Questions This Phase Must Answer
Phase นี้ต้องตอบให้ได้:

1. supervisor ควรเริ่มงานจาก dashboard/workboard อย่างไร
2. เมื่อไหร่ควรเข้า incident queue และใช้ filter ไหน
3. unowned / overdue / stale incidents ต้องจัดการอย่างไร
4. owner, follow-up target, และ status ควรถูกใช้ร่วมกันอย่างไร
5. product incident ต่างจาก operational incident ของระบบเองอย่างไร
6. workboard ใช้เป็น routine command surface อย่างไรโดยไม่ overclaim ว่าเป็น full dispatch tool

## 7) Acceptance Criteria
Phase นี้จะถือว่าจบเมื่อ:
- repo มี SOP ที่ผูกกับ current route/action/filter จริง
- มีคำอธิบายชัดเรื่อง ownership, overdue follow-up, และ stale review
- มีขอบเขตชัดว่า dashboard/workboard เป็น today-first control surface
- มีข้อความชัดว่า `incident_triage_runbook.md` ใช้กับ operational incident ของระบบเองคนละชนิดกับ product incident queue
- ไม่มีประโยคที่ overclaim ว่าระบบมี support-ops maturity เกินของจริง

## 8) Recommended Next Step After This Phase
หลัง Phase นี้ ควรไปต่อที่ `Program 3 closure review`
เพื่อสรุปว่า operations readiness baseline ผ่านขั้นต่ำแล้วจริง
