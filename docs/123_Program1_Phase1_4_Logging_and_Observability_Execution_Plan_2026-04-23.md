# Program 1 / Phase 1.4 — Logging and Observability Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 1 / Phase 1.4 — Logging and Observability` ให้เป็นก้อนงานที่ใช้ได้จริง
โดยยังไม่อ้างว่าระบบมี production observability stack พร้อมแล้ว และยังไม่เปิด automation/integration wave เกินหลักฐาน

## 2) Repo Truth Used For This Plan
สิ่งที่ repo มีอยู่ตอนนี้:
- `config/logging.php` ยังใกล้ Laravel baseline มาก
- app ยังไม่มี structured domain log contract ที่เข้มชัด
- มี dashboard/workboard/application signals ใน product layer แต่ยังไม่เท่ากับ production observability
- browser/feature tests มีในฐานะ quality evidence ไม่ใช่ runtime monitoring

คำแปลเชิงวิศวกรรม:
- phase นี้ควรเริ่มจาก `policy + routing stance + triage discipline`
- ยังไม่ควรอ้างว่า alerting/monitoring integration ปิดแล้ว

## 3) Executive Decision
phase นี้ควรจบด้วย baseline แบบนี้:
- log policy ชัด
- prod log routing stance ชัด
- monitoring choice memo ชัด
- alerting baseline ชัด
- incident triage runbook ชัด

แต่ยังไม่ claim ว่า:
- external monitoring integrated แล้ว
- SLO/SLI mature แล้ว
- end-to-end request tracing พร้อมแล้ว

## 4) Deliverables
phase นี้ควรจบด้วย 5 deliverables:

1. [observability_baseline.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/observability_baseline.md)
2. [monitoring_choice_memo.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/monitoring_choice_memo.md)
3. [alerting_baseline.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/alerting_baseline.md)
4. [incident_triage_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/incident_triage_runbook.md)
5. optional follow-up list สำหรับ future instrumentation work ภายใน observability baseline doc

## 5) Hard Boundaries
phase นี้ยังไม่ทำ:
- Sentry/Bugsnag/DataDog/New Relic integration
- log shipping implementation
- distributed tracing
- metrics dashboard implementation
- uptime probe automation
- on-call rota system
- security event monitoring program

## 6) Observability Questions This Phase Must Answer
phase นี้ต้องตอบให้ได้:

1. production ควรใช้ log channel/routing แบบไหนเป็น baseline
2. อะไรควรถูก log และอะไรไม่ควรถูก log
3. app failures แบบไหนถือว่า critical
4. ใครเป็นคน review failures/logs
5. ถ้าเกิด incident จริง ต้อง triage อย่างไร

## 7) Acceptance Criteria
phase นี้จะถือว่าจบเมื่อ:
- มีเอกสารที่บอกชัดว่า log policy คืออะไร
- มีคำตอบชัดว่า production v1 ใช้ monitoring stance แบบไหน
- มี alerting baseline ขั้นต่ำ
- มี incident triage runbook
- ไม่มีประโยคที่ overclaim ว่าระบบมี observability platform ครบแล้วทั้งที่ยังไม่มี integration

## 8) Recommended Next Step After This Phase
หลัง phase นี้ ควรไปต่อที่ `Program 1 / Phase 1.5 — Security Baseline`
เพราะ production hardening ยังไม่ครบถ้ายังไม่มี security stance ที่เขียนชัด
