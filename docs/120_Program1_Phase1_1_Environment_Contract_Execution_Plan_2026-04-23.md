# Program 1 / Phase 1.1 — Environment Contract Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 1 / Phase 1.1 — Environment Contract` ให้เป็นก้อนงานที่เริ่มลงมือได้จริงหลัง defense
โดยยังไม่เปิด product wave ใหม่ และยังไม่ข้ามไปทำ deployment / rollback / backup ก่อนเวลา

## 2) Repo Truth Used For This Plan
สถานะปัจจุบันของ repo:
- product scope ระดับ capstone / Option A ปิดแล้ว
- current app ยังใช้ `.env.example` แบบ local-first ชัดเจน
- local baseline ปัจจุบันคือ `SQLite + local/public storage + database session/cache/queue`
- attachment flow ปัจจุบันผูกกับ `public` disk
- dashboard/query layer ยังมี raw aggregate และ `DATE(...)` expression อยู่ใน [GetDashboardSnapshot.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Dashboard/Queries/GetDashboardSnapshot.php)

คำแปลเชิงวิศวกรรม:
- รอบนี้ควรล็อก `supported production baseline` ที่แคบและซื่อสัตย์
- ยังไม่ควรอ้างว่ารองรับหลาย infra shapes พร้อมกัน
- ยังไม่ควรเปิด Redis/S3/HA wave ถ้ายังไม่มี operations ownership จริง

## 3) Executive Decision
Phase 1.1 ควรล็อก baseline แบบนี้:

- `local`:
  - SQLite
  - `public`/local storage
  - database session/cache/queue
  - debug เปิดได้
- `staging`:
  - MySQL 8.0 เป็น primary database baseline
  - single-node app baseline
  - `public` disk บน host เดียวกัน
  - database session/cache/queue
  - debug ปิด
- `production`:
  - MySQL 8.0 เป็น primary supported database
  - single-node deployment baseline v1
  - `public` disk สำหรับ attachment บน host เดียวกัน
  - database session/cache/queue
  - debug ปิด
  - explicit secrets handling และ prod-safe env policy

## 4) Why This Baseline
เหตุผลที่เลือก baseline นี้:

1. ใกล้กับ repo truth ปัจจุบันที่สุด
2. ไม่บังคับ Redis/S3 ก่อนมี infra owner
3. ไม่อ้าง support ทั้ง MySQL/PostgreSQL พร้อมกันโดยยังไม่ทดสอบจริง
4. ยังสอดคล้องกับ attachment path ปัจจุบันที่ผูกกับ `public` disk
5. ยอมรับตามจริงว่านี่คือ `single-node production baseline v1` ไม่ใช่ HA architecture

## 5) Explicit Non-Goals For Phase 1.1
Phase นี้ยังไม่ทำ:
- deployment runbook
- rollback runbook
- backup/restore drill
- observability integration
- security hardening wave
- Redis migration
- S3/object storage migration
- PostgreSQL support claim
- Option B discovery

## 6) Deliverables
Phase 1.1 ควรจบด้วย 3 deliverables หลัก:

1. [environment_matrix.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/environment_matrix.md)
2. [production_env_contract.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/production_env_contract.md)
3. [production_stack_decision_record.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/production_stack_decision_record.md)

## 7) Work Sequence
ลำดับงานที่ถูกต้องที่สุด:

1. ยืนยัน supported production baseline
2. ล็อก environment matrix ให้ครบ `local / staging / production`
3. ล็อก env variables ที่ `must differ` ระหว่าง local กับ production
4. ล็อก production-safe defaults
5. ล็อก secrets handling policy ขั้นต้น
6. ล็อก deployment prerequisites ที่ phase ถัดไปต้องใช้

## 8) Acceptance Criteria
Phase 1.1 จะถือว่าจบเมื่อ:

- repo มีเอกสารที่บอกชัดว่า local/staging/production ต่างกันอย่างไร
- มีคำตอบชัดว่า production รองรับ database อะไร
- มีคำตอบชัดว่า attachment จะอยู่บน storage แบบไหนใน production v1
- มีคำตอบชัดว่า queue/cache/session จะใช้ strategy อะไรใน production v1
- มีคำตอบชัดว่า env ใดบ้างที่ห้ามใช้ค่า local ใน production
- ไม่มีประโยคที่ overclaim ว่าระบบพร้อม production multi-node หรือ HA แล้ว

## 9) Risk Notes
ความเสี่ยงที่ต้องพูดตรง:

- current dashboard queries ยังไม่ได้พิสูจน์ cross-database matrix แบบเต็มชุด
- current attachment stance ยังเป็น host-local public storage
- queue usage ปัจจุบันยังเบา จึงไม่ควรรีบอ้าง Redis requirement ถ้ายังไม่มี workload proof
- phase นี้เป็น `contract-setting`, ไม่ใช่ `production deployment`

## 10) Recommended Next Step After This Phase
ถ้า Phase 1.1 ปิดได้จริง
ลำดับถัดไปคือ `Phase 1.2 — Deployment and Rollback Discipline`
ไม่ใช่กระโดดไป Option B หรือ product feature ใหม่
