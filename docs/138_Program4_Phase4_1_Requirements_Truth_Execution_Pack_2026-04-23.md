# Program 4 / Phase 4.1 — Requirements Truth Execution Pack
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 4 / Phase 4.1 — Requirements Truth` ให้เป็นก้อน discovery ที่ grounded กับ repo truth ปัจจุบัน
เพื่อประเมินอย่างมีวินัยว่า `Option B / machine registry` ควรเกิดจริงไหม
โดยยังไม่แตะ schema, implementation, หรือ UI expansion

## 2) Repo Truth Used For This Phase
สิ่งที่ repo มีอยู่แล้ว:
- current product เป็น `room-centered lab operations web app`
- checklist runs และ incidents persist `room_id` จริง
- staff incident create ต้องผูกกับ room เสมอ
- incident รองรับ `equipment_reference` แบบ optional free text
- dashboard และ management flows เป็น room-aware แล้ว
- เอกสาร current state หลายฉบับยืนยันตรงกันว่า:
  - ระบบยังไม่ใช่ machine registry
  - `equipment_reference` ยังเป็น lightweight reference
  - ยังไม่มี machine lifecycle, machine history, หรือ machine analytics

คำแปลเชิงวิศวกรรม:
- phase นี้ต้องเริ่มจาก `problem truth`
- ไม่ใช่เริ่มจากคำตอบว่า “ควรมี machines table”
- ต้องแยกให้ได้ว่า pain points ไหนแก้ได้ด้วย process/discipline เดิม
  และ pain points ไหนต้องเปิด entity ใหม่จริง

## 3) Executive Decision
Phase 4.1 ควรจบด้วย baseline แบบนี้:
- มี `machine_discovery_brief.md`
- มี `machine_use_case_list.md`
- มี `machine_pain_point_matrix.md`
- มีข้อสรุปชัดว่า current room-centered model พอสำหรับอะไร และไม่พอสำหรับอะไร

แต่ยังไม่ claim ว่า:
- ควร implement Option B แล้ว
- machine ต้องเป็น first-class entity แน่นอน
- incident ทุกตัวต้องผูก machine
- room-machine relationship ถูกตัดสินจบแล้ว

## 4) Deliverables
Phase นี้ควรจบด้วย 4 deliverables:

1. [138_Program4_Phase4_1_Requirements_Truth_Execution_Pack_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/138_Program4_Phase4_1_Requirements_Truth_Execution_Pack_2026-04-23.md)
2. [machine_discovery_brief.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_discovery_brief.md)
3. [machine_use_case_list.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_use_case_list.md)
4. [machine_pain_point_matrix.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_pain_point_matrix.md)

## 5) Hard Boundaries
Phase นี้ยังไม่ทำ:
- `machines` table
- machine CRUD
- machine-room pivot
- `machine_id` on incidents
- machine lifecycle states
- machine analytics dashboard
- migration / seed / test wave สำหรับ Option B

## 6) Questions This Phase Must Answer
Phase นี้ต้องตอบให้ได้:

1. ปัญหาประเภทไหน current room-centered model แก้ได้อยู่แล้ว
2. ปัญหาประเภทไหนต้องการ machine identity จริง
3. `equipment_reference` แบบ free text พอในกรณีไหน
4. ถ้าจะเปิด machine domain จริง คุณค่าหลักคืออะไร
5. มี non-machine issues จำนวนเท่าไรที่ไม่ควรถูกบังคับให้ไปอยู่ใต้ machine model

## 7) What This Phase Found
การอ่าน repo truth และ existing defense/docs ชี้ตรงกันว่า:
- current product ถูกออกแบบมาเพื่อ `room-first operational coordination`
- `equipment_reference` มีไว้ช่วยระบุอุปกรณ์ที่เกี่ยวข้องในระดับ lightweight
- คำถาม “เครื่องไหนเสีย” ตอบได้ระดับ pragmatic แต่ยังไม่มี canonical machine identity
- pain point เรื่อง machine ยังเป็น `future design pressure`
  ไม่ใช่ defect ของ current Option A baseline

## 8) Acceptance Criteria
Phase นี้จะถือว่าจบเมื่อ:
- repo มี discovery docs ที่แยก current truth ออกจาก future wish list ชัดเจน
- มีการแยก machine-specific vs non-machine-specific pain points
- ไม่มีการ overclaim ว่า Phase นี้ตัดสิน domain model หรือ ROI แล้ว
- ไม่มี code/schema implementation ถูกแอบเริ่มในนามของ discovery

## 9) Recommended Next Step
หลัง Phase นี้ ควรไปต่อที่ `Program 4 / Phase 4.2 — Domain Modeling Decision`
เพื่อออกแบบ options ของ room-machine relationship และ machine identity
โดยอิงจาก requirement truth ที่ phase นี้ล็อกไว้แล้ว
