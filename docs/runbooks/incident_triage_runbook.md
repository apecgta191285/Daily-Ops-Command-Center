# Incident Triage Runbook
วันที่: 23 เมษายน 2026

## 1) Scope
runbook นี้ใช้กับ operational failure ของระบบเอง
ไม่ใช่ incident records ที่ผู้ใช้สร้างใน product workflow

พูดง่ายๆ:
- product incident = ปัญหาในห้องแล็บ
- operational incident = ปัญหาของตัวระบบเว็บเอง

## 2) Triage Entry Points
เหตุการณ์ที่ควรเริ่ม triage:
- deploy smoke fail
- user report ว่า login/checklist/incident flow ใช้ไม่ได้
- repeated exception in production logs
- queue failure ที่มีผลต่อ workflow
- attachment access failure

## 3) Triage Procedure
ลำดับแนะนำ:

1. ระบุว่าเป็น `operational incident` ไม่ใช่ product incident
2. บันทึกเวลาและช่องทางที่พบ
3. ประเมิน severity ตาม [alerting_baseline.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/alerting_baseline.md)
4. ระบุ scope:
   - guest only
   - staff only
   - management/admin only
   - whole app
5. ตรวจ logs ล่าสุด
6. ตรวจว่ามี release/change ล่าสุดหรือไม่
7. ตัดสินใจ:
   - monitor
   - fix forward
   - rollback
   - maintenance mode

## 4) Minimum Evidence To Capture
- time detected
- affected flow
- impacted roles
- latest deploy revision
- visible error message / exception class
- temporary mitigation ถ้ามี

## 5) Resolution Recording
เมื่อ incident จบ ควรบันทึก:
- root cause แบบสั้น
- fix or mitigation ที่ใช้
- rollback occurred or not
- follow-up needed or not

## 6) Honest Limitation
runbook นี้ยังไม่มี:
- on-call rota
- ticketing integration
- SLA framework

มันเป็น `single-team operational triage baseline` เท่านั้น
