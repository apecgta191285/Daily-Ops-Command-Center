# Program 3 / Phase 3.2 — Template Governance SOP Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 3 / Phase 3.2 — Template Governance SOP` ให้เป็นก้อนงานที่ใช้ได้จริง
โดยยึด current repo truth ของ checklist template governance
และยังไม่อ้างว่ามี workflow approvals, audit platform, หรือ formal change board แล้ว

## 2) Repo Truth Used For This Plan
สิ่งที่ repo มีอยู่แล้ว:
- admin-only route family:
  - `/templates`
  - `/templates/create`
  - `/templates/{template}/edit`
  - `POST /templates/{template}/duplicate`
- current activation invariant คือ `at most one active template per scope`
- `SaveChecklistTemplate` จะ retire live template เฉพาะใน scope เดียวกันเมื่อบันทึก active template ใหม่
- `DuplicateChecklistTemplate` สร้าง inactive copy เพื่อใช้ปรับแก้ได้อย่างปลอดภัย
- items ที่มี run history แล้วห้ามลบออกจาก template เดิม
- legacy `/admin/checklist-templates*` routes ถูก retire แล้ว
- `TemplateScopeGovernanceBuilder` และ `TemplateActivationImpactBuilder` แสดง governance context จริงใน UI

คำแปลเชิงวิศวกรรม:
- phase นี้ไม่ต้องสร้าง feature เพิ่ม
- แต่ต้องเปลี่ยน template administration จาก “surface ที่ใช้งานได้” ให้กลายเป็น “process ที่คนในทีมใช้ซ้ำได้อย่างมีวินัย”
- SOP ต้องสะท้อน per-scope governance, safe duplication, และ history preservation ตาม behavior จริงของระบบ

## 3) Executive Decision
Phase 3.2 ควรจบด้วย baseline แบบนี้:
- มี `template_governance_sop.md`
- มี `template_change_review_checklist.md`
- ระบุชัดว่า template change เป็น operational governance activity ไม่ใช่ ad hoc CRUD

แต่ยังไม่ claim ว่า:
- มี multi-stage approval workflow แล้ว
- มี versioning system แบบ enterprise แล้ว
- มี audit log platform หรือ change ticketing integration แล้ว
- มี room-specific template architecture แล้ว

## 4) Deliverables
Phase นี้ควรจบด้วย 3 deliverables:

1. [134_Program3_Phase3_2_Template_Governance_SOP_Execution_Plan_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/134_Program3_Phase3_2_Template_Governance_SOP_Execution_Plan_2026-04-23.md)
2. [template_governance_sop.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/template_governance_sop.md)
3. [template_change_review_checklist.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/template_change_review_checklist.md)

## 5) Hard Boundaries
Phase นี้ยังไม่ทำ:
- approval automation
- workflow ticket integration
- template version comparison UI
- room-specific templates
- machine-specific templates
- support / incident ops SOP
- operational ownership matrix

## 6) Questions This Phase Must Answer
Phase นี้ต้องตอบให้ได้:

1. เมื่อไหร่ควรแก้ template เดิม และเมื่อไหร่ควร duplicate ก่อน
2. active template ต่อ scope มีความหมายอย่างไร
3. การ activate template ใหม่จะกระทบอะไรบ้าง
4. เพราะอะไร item ที่มี run history แล้วจึงไม่ควรถูกลบออก
5. admin ควร review อะไรก่อนกด save หรือ activate
6. จะป้องกันการทำให้ staff runtime สับสนได้อย่างไร

## 7) Acceptance Criteria
Phase นี้จะถือว่าจบเมื่อ:
- repo มี SOP ที่ผูกกับ route/action/invariant ปัจจุบันจริง
- repo มี checklist สำหรับ review template change ก่อน save/activate
- มีคำเตือนชัดเรื่อง per-scope activation, duplication-first for major change, และ historical-item protection
- ไม่มีประโยคที่ overclaim ว่าระบบมี change-management maturity เกินของจริง

## 8) Recommended Next Step After This Phase
หลัง Phase นี้ ควรไปต่อที่ `Program 3 / Phase 3.3 — Support / Incident Ops SOP`
ไม่ใช่ย้อนกลับไปเปิด feature expansion หรือ Option B
