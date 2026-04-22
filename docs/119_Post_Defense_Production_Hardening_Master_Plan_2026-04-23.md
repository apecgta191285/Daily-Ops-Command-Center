# Post-Defense Production Hardening Master Plan
วันที่: 23 เมษายน 2026

## 1) Executive Verdict
จากการอ่าน:
- [production_grade_platform_foundation_gap_report_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/production_grade_platform_foundation_gap_report_2026-04-23.md)
- [production_hardening_roadmap_v1_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/production_hardening_roadmap_v1_2026-04-23.md)

และเทียบกับ repo truth ปัจจุบัน

คำตัดสินที่ถูกต้องที่สุดคือ:

> ระบบปัจจุบันเสร็จครบตาม scope ของ capstone / Option A แล้ว  
> แต่ยังไม่ใช่ production-grade platform foundation  
> ถ้าจะยกระดับต่อ ต้องเปิด **post-defense hardening program ใหม่**  
> และต้องเริ่มจาก **platform hardening baseline** ก่อน ไม่ใช่เริ่มจาก Option B

## 2) Current Repo Truth
สิ่งที่ repo ปัจจุบันมีอยู่แล้ว:
- room-centered operations baseline
- room-aware checklist runtime
- room-aware incidents
- optional lightweight `equipment_reference`
- dashboard / queue / detail / history / print surfaces
- admin template governance
- admin user lifecycle
- docs truth alignment สำหรับ current scope
- demo / defense support docs

สิ่งที่ repo ปัจจุบันยังไม่มี:
- machine registry
- machine lifecycle
- production environment contract
- deployment / rollback runbooks
- backup / restore proof
- logging / monitoring baseline
- explicit security baseline
- operational SOP set
- release gate / production release discipline

## 3) Planning Principle
แผนนี้ยึดหลัก 5 ข้อ:

1. **No scope confusion**  
   แยก capstone scope ออกจาก production hardening ให้ชัด

2. **Platform before feature expansion**  
   อย่าเริ่ม Option B ก่อน platform baseline แข็งพอ

3. **Evidence before claim**  
   อย่าใช้คำว่า production-grade ถ้ายังไม่มี runbook, backup, security, และ release discipline

4. **One program at a time**  
   ห้ามทำหลายโปรแกรมพร้อมกันจนเละ

5. **Known limitations stay explicit**  
   ทุกช่วงต้องพูดข้อจำกัดให้ตรง ไม่หลอกตัวเองว่าระบบเกินความจริง

## 4) Recommended Program Order
ลำดับที่ถูกต้องที่สุดจากนี้คือ:

1. `Program 1 — Platform Hardening Baseline`
2. `Program 2 — Product Hardening`
3. `Program 3 — Operations Readiness`
4. `Program 4 — Option B Discovery Only`
5. `Program 5 — Option B Implementation (Conditional Only)`

## 5) Program 1 — Platform Hardening Baseline
### Why first
เพราะ production-grade ถูกตัดสินจาก environment, deployment, recovery, security, logging, และ release discipline  
ไม่ใช่จากการมี machine entity เพิ่ม

### Phase 1.1 — Environment Contract
ต้องทำ:
- ล็อก environment matrix: `local / staging / production`
- ล็อก supported production DB
- ล็อก storage strategy สำหรับ production
- ล็อก queue / cache / session strategy สำหรับ production
- ล็อก prod-safe defaults
- ล็อก secrets handling baseline

ผลลัพธ์:
- `docs/runbooks/environment_matrix.md`
- `docs/runbooks/production_env_contract.md`
- `.env.production.example` หรือ policy equivalent
- decision memo เรื่อง DB / storage / queue baseline

Gate:
- ยังไม่ deploy production ถ้ายังไม่มี phase นี้

### Phase 1.2 — Deployment and Rollback Discipline
ต้องทำ:
- deployment procedure แบบ step-by-step
- rollback procedure
- migration safety rules
- asset build / release rules
- maintenance mode policy
- post-deploy smoke checklist

ผลลัพธ์:
- deployment runbook
- rollback runbook
- release checklist
- post-deploy smoke checklist

### Phase 1.3 — Backup and Recovery
ต้องทำ:
- database backup policy
- attachment backup policy
- restore verification policy
- pragmatic RPO / RTO baseline
- restore drill อย่างน้อย 1 รอบ

ผลลัพธ์:
- backup policy
- restore runbook
- restore evidence
- recovery readiness checklist

### Phase 1.4 — Logging and Observability
ต้องทำ:
- log policy
- prod log routing
- error aggregation / monitoring baseline
- alerting threshold baseline
- failure review cadence
- minimal operational tracing policy

ผลลัพธ์:
- observability baseline doc
- monitoring choice memo
- alerting baseline
- incident triage runbook

### Phase 1.5 — Security Baseline
ต้องทำ:
- threat model baseline
- auth/session/rate-limit review
- attachment risk policy
- admin account hardening policy
- secrets handling guide
- release security checklist

ผลลัพธ์:
- security baseline doc
- threat model note
- secrets handling guide
- release security checklist

## 6) Program 2 — Product Hardening
### Why second
หลัง platform baseline เริ่มชัด เราค่อยทำให้ product surfaces แข็งขึ้นโดยไม่เปิด domain ใหม่

### Phase 2.1 — Story Alignment Completion
ต้องทำ:
- review authenticated heavy screens
- เก็บ wording ที่ยัง abstract / theatrical
- ทำ actor mapping ให้คงที่ทุกหน้า
- ทำ room-centered story ให้ calm และตรง

ผลลัพธ์:
- story alignment checklist
- reviewed surfaces list

### Phase 2.2 — Heavy-Screen QA Expansion
ต้องทำ:
- แยก smoke / screenshot / accessibility / manual exploratory ให้ชัด
- เพิ่ม coverage สำหรับ authenticated heavy screens ที่คุ้มจริง
- ล็อก browser coverage policy

ผลลัพธ์:
- browser coverage matrix
- QA policy doc
- targeted tests only where justified

### Phase 2.3 — Workflow Edge-Case Hardening
ต้องทำ:
- review room-aware checklist edge cases
- review incident create/detail edge cases
- review missing room/template/empty state paths
- review concurrency-sensitive interactions แบบ pragmatic

ผลลัพธ์:
- edge-case checklist
- surgical bug-fix list
- added failure-path tests เฉพาะจุดสำคัญ

### Phase 2.4 — Attachment and Data Handling UX Hardening
ต้องทำ:
- attachment visibility/access stance
- validation/type/size/failure messaging review
- retention/cleanup policy
- privacy stance ของ attachment

ผลลัพธ์:
- attachment handling policy
- updated UX/error-state checklist

## 7) Program 3 — Operations Readiness
### Why third
เพราะต่อให้ระบบกับ platform ดีขึ้น ถ้าไม่มีคนดูแลต่อจริงก็ยังไม่เป็น long-term operations system

### Phase 3.1 — Admin SOP
ผลลัพธ์:
- admin SOP
- user lifecycle SOP

### Phase 3.2 — Template Governance SOP
ผลลัพธ์:
- template governance SOP
- template change review checklist

### Phase 3.3 — Support / Incident Ops SOP
ผลลัพธ์:
- supervisor SOP
- incident triage SOP
- workboard usage SOP

### Phase 3.4 — Operational Ownership Model
ผลลัพธ์:
- ownership matrix
- support responsibility matrix

## 8) Program 4 — Option B Discovery Only
### Why only after 1-3
เพราะ machine registry ไม่ได้แก้ platform, deployment, recovery, หรือ security debt

ต้องทำ:
- requirements truth
- room-machine relationship design
- incident semantics redesign evaluation
- non-machine issue modeling
- ROI / cost / complexity analysis

ผลลัพธ์:
- discovery brief
- design trade-off memo
- go/no-go memo for Option B

Gate:
- ห้ามแตะ schema ของ Option B ก่อน program นี้จบ

## 9) Program 5 — Option B Implementation
เริ่มได้เมื่อ:
- Program 1 ผ่าน
- Program 2 ผ่านขั้นต่ำ
- Program 3 ผ่านขั้นต่ำ
- Program 4 ตัดสิน `GO`
- มี owner / time / support เพียงพอ

ถ้ายังไม่ครบ:
- ห้ามเริ่ม

## 10) What Must Not Happen
- ห้ามกระโดดไป machine registry ก่อนทำ platform hardening
- ห้ามอ้าง production-grade โดยไม่มี runbooks/recovery/security baseline
- ห้ามเพิ่ม feature ใหม่เพราะอยากให้ดูครบ
- ห้ามทำหลาย program พร้อมกันโดยไม่มี gate
- ห้ามใช้ quick-and-dirty เพื่อเร่งภาพลักษณ์

## 11) Suggested Immediate Next Action
ถ้าจะเริ่มจริงหลัง defense

งานแรกที่ถูกต้องที่สุดคือ:

> `Program 1 / Phase 1.1 — Environment Contract`

เพราะนี่จะเป็นตัวบังคับให้ทุกการตัดสินใจหลังจากนั้นไม่ลอย เช่น:
- จะใช้ DB อะไรใน production
- จะเก็บไฟล์แนบยังไง
- queue/session/cache จะเดินยังไง
- debug/log/secrets จะจัดการยังไง

## 12) Final Brutal Truth
เอกสารสองฉบับที่แนบมาอ่านทิศถูกครับ  
และเมื่อเทียบกับ repo truth ปัจจุบันแล้ว ข้อสรุปที่ตรงที่สุดคือ:

- ตอนนี้ยังไม่ควรเริ่ม Option B
- ตอนนี้ยังไม่ควรเรียกระบบว่า production-grade
- ถ้าจะพัฒนาให้ถึงจุดนั้นจริง ต้องเริ่มจาก platform hardening ก่อน
- และต้องมองว่านี่คือ **โปรแกรมพัฒนาระยะยาวชุดใหม่** ไม่ใช่การต่อปลายจาก capstone wave เดิม

