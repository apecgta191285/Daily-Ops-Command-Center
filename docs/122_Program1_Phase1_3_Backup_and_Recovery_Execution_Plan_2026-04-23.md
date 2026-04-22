# Program 1 / Phase 1.3 — Backup and Recovery Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 1 / Phase 1.3 — Backup and Recovery` ให้เป็นก้อนงานที่ใช้ได้จริง
โดยยังไม่อ้างว่ามี recovery capability แล้ว และยังไม่เปิด observability/security wave ปนเข้ามา

## 2) Repo Truth Used For This Plan
repo ปัจจุบันมี data/storage truth แบบนี้:
- production v1 baseline ถูกล็อกเป็น `MySQL 8.0 + single-node host`
- attachment ปัจจุบันถูกเก็บผ่าน `public` disk
- incident attachment path ปัจจุบันผูกกับ `storage/app/public/incidents`
- ยังไม่มี backup automation proof ใน repo
- ยังไม่มี restore drill evidence จริงใน repo

คำแปลเชิงวิศวกรรม:
- phase นี้ต้องเริ่มจาก `policy + procedure + evidence template`
- ยังไม่ควรอ้าง recovery readiness จนกว่าจะมี restore drill จริง

## 3) Executive Decision
phase นี้ควรจบด้วย baseline แบบนี้:
- database backup policy ชัด
- attachment backup policy ชัด
- restore runbook ชัด
- recovery readiness checklist ชัด
- restore drill evidence template พร้อมใช้

แต่ยังไม่ claim ว่า:
- backup automation ปิดแล้ว
- restore tested แล้ว
- RPO/RTO ผ่านการพิสูจน์แล้ว

## 4) Deliverables
phase นี้ควรจบด้วย 5 deliverables:

1. [backup_policy.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/backup_policy.md)
2. [restore_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/restore_runbook.md)
3. [recovery_readiness_checklist.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/recovery_readiness_checklist.md)
4. [restore_drill_evidence_template.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/restore_drill_evidence_template.md)
5. decision note เรื่อง pragmatic `RPO / RTO` baseline ภายใน policy doc

## 5) Hard Boundaries
phase นี้ยังไม่ทำ:
- backup scheduler implementation
- cloud snapshot integration
- object storage migration
- DR region design
- HA failover design
- encryption-at-rest program
- observability/alerting automation

## 6) What Must Be Backed Up
phase นี้ต้องล็อกอย่างน้อย 2 data classes:

1. relational data:
   - users
   - rooms
   - checklist templates
   - checklist runs
   - incidents
   - incident activity
   - sessions/cache/queue tables ตาม baseline

2. attachment files:
   - `storage/app/public/incidents`

## 7) Acceptance Criteria
phase นี้จะถือว่าจบเมื่อ:
- repo มี backup policy ที่พูดชัดว่า backup อะไร เมื่อไร และใครเป็น owner
- repo มี restore runbook ที่ทำตามได้จริง
- repo มี recovery readiness checklist
- repo มี template สำหรับบันทึก restore drill evidence
- ไม่มีประโยคที่อ้างว่า recovery readiness ปิดแล้วทั้งที่ยังไม่มี drill proof

## 8) Recommended Next Step After This Phase
หลัง phase นี้ ควรไปต่อที่ `Program 1 / Phase 1.4 — Logging and Observability`
แต่ก่อนใช้คำว่า production-ready ควรมี restore drill จริงอย่างน้อย 1 รอบก่อน
