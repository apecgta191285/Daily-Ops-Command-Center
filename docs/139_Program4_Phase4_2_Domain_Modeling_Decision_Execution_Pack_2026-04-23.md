# Program 4 / Phase 4.2 — Domain Modeling Decision Execution Pack
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 4 / Phase 4.2 — Domain Modeling Decision` ให้เป็นก้อน discovery ที่ใช้ตัดสินทางเลือกเชิง domain
โดยต่อยอดจาก requirement truth ใน Phase 4.1
และยังคงเป็น `discovery only` ไม่ใช่ implementation wave

## 2) Inputs Used For This Phase
phase นี้อิงจาก:
- [138_Program4_Phase4_1_Requirements_Truth_Execution_Pack_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/138_Program4_Phase4_1_Requirements_Truth_Execution_Pack_2026-04-23.md)
- [machine_discovery_brief.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_discovery_brief.md)
- [machine_use_case_list.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_use_case_list.md)
- [machine_pain_point_matrix.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_pain_point_matrix.md)
- current schema truth ของ `rooms`, `incidents`, `checklist_runs`
- current incident create flow ที่ยังใช้ `room_id + equipment_reference`

## 3) Executive Decision
Phase 4.2 ควรจบด้วย baseline แบบนี้:
- มี `machine_domain_model_options.md`
- มี `machine_design_tradeoff_memo.md`
- ได้ข้อสรุปเชิงสถาปัตยกรรมว่า model ไหน `ควรถูก carry forward` ไป Phase 4.3

แต่ยังไม่ claim ว่า:
- Option B ต้อง implement แล้ว
- schema สุดท้ายถูกอนุมัติ production-ready แล้ว
- UX flow สุดท้ายถูกตัดสินครบแล้ว

## 4) Domain Questions This Phase Must Answer
Phase นี้ต้องตอบให้ได้:

1. machine ควรเป็น first-class entity หรือไม่
2. machine ควรผูกกับ room แบบใด
3. incident ควรผูก machine เสมอหรือ optional
4. `equipment_reference` ควรถูกยุบหรือคงไว้
5. machine lifecycle ต้องอยู่ใน model เริ่มต้นเลยหรือไม่

## 5) Core Decision From This Phase
จาก repo truth และ requirement truth ตอนนี้
candidate ที่สมเหตุสมผลที่สุดสำหรับ carry-forward คือ:

`Room-first model with optional machine identity`

ความหมาย:
- room ยังเป็น operational anchor หลัก
- machine เป็น first-class entity เฉพาะเมื่อทีมต้องการ persistent identity
- incident ไม่ควรถูกบังคับให้มี machine ทุกตัว
- `equipment_reference` ควรถูกเก็บไว้ต่อสำหรับ non-machine assets และ ad hoc references

## 6) Why This Is The Current Best Candidate
เพราะ model นี้:
- ไม่ทำลาย room-centered baseline ที่พิสูจน์แล้วใน Option A
- รองรับ mixed incident domain ที่มีทั้ง machine และ non-machine issues
- เปิดทางไปสู่ asset history ได้
- หลีกเลี่ยงการบังคับข้อมูลปลอมใน incident ที่ไม่ใช่ machine issue

## 7) Hard Boundaries
Phase นี้ยังไม่ทำ:
- migration
- model code
- Livewire/UI changes
- seed changes
- test expansion
- analytics implementation

## 8) Deliverables
Phase นี้ควรจบด้วย 3 deliverables:

1. [139_Program4_Phase4_2_Domain_Modeling_Decision_Execution_Pack_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/139_Program4_Phase4_2_Domain_Modeling_Decision_Execution_Pack_2026-04-23.md)
2. [machine_domain_model_options.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_domain_model_options.md)
3. [machine_design_tradeoff_memo.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/machine_design_tradeoff_memo.md)

## 9) Recommended Next Step
หลัง Phase นี้ ควรไปต่อที่ `Program 4 / Phase 4.3 — ROI / Cost / Complexity Evaluation`
เพื่อประเมินอย่างตรงไปตรงมาว่า candidate model นี้คุ้มพอให้เปิด implementation wave หรือไม่
