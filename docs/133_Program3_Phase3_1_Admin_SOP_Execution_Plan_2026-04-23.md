# Program 3 / Phase 3.1 — Admin SOP Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 3 / Phase 3.1 — Admin SOP` ให้เป็นก้อนงานที่เริ่มใช้งานได้จริง
โดยยึด repo truth ปัจจุบันของ admin governance และ user lifecycle
และยังไม่อ้างว่ามี full operations organization หรือ enterprise IAM process แล้ว

## 2) Repo Truth Used For This Plan
สิ่งที่ repo มีอยู่แล้ว:
- admin-only route family สำหรับ user administration:
  - `/users`
  - `/users/create`
  - `/users/{user}/edit`
- admin-only route family สำหรับ checklist template governance
- `CreateManagedUser` และ `UpdateManagedUser` เป็น application owners ของ user lifecycle
- `UserLifecycleGuardRail` บังคับ:
  - admin ห้ามลดสิทธิ์ตัวเอง
  - admin ห้ามปิด active state ของตัวเอง
  - ต้องเหลือ active admin อย่างน้อย 1 คน
- Fortify authentication และ `EnsureActiveUser` middleware ใช้ `is_active` เป็น access gate จริง

คำแปลเชิงวิศวกรรม:
- Phase นี้ไม่ต้อง “ออกแบบ capability ใหม่”
- แต่ต้องแปลง capability ที่มีอยู่แล้วให้เป็น operating procedure ที่คนใช้ตามได้จริง
- SOP ต้องผูกกับ route, guard rail, และข้อจำกัดจริงของระบบ

## 3) Executive Decision
Phase 3.1 ควรจบด้วย baseline แบบนี้:
- มี `admin_sop.md` สำหรับงานดูแลระบบที่ admin ทำเป็นประจำ
- มี `user_lifecycle_sop.md` สำหรับการสร้าง/แก้ไข/ปิดการใช้งานบัญชี
- มีขอบเขตชัดว่า phase นี้เป็น `operating discipline baseline`

แต่ยังไม่ claim ว่า:
- มี HR/IAM integration แล้ว
- มี invitation flow หรือ passwordless onboarding แล้ว
- มี ticketing workflow automation แล้ว
- มี approval matrix แบบ enterprise แล้ว

## 4) Deliverables
Phase นี้ควรจบด้วย 3 deliverables:

1. [133_Program3_Phase3_1_Admin_SOP_Execution_Plan_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/133_Program3_Phase3_1_Admin_SOP_Execution_Plan_2026-04-23.md)
2. [admin_sop.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/admin_sop.md)
3. [user_lifecycle_sop.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/user_lifecycle_sop.md)

## 5) Hard Boundaries
Phase นี้ยังไม่ทำ:
- automated approvals
- SSO / external IAM integration
- invitation email flow
- audit log platform integration
- ticket workflow tooling
- template governance SOP
- support / incident ops SOP
- ownership matrix

## 6) Questions This Phase Must Answer
Phase นี้ต้องตอบให้ได้:

1. admin มีหน้าที่ operational อะไรบ้างในระบบปัจจุบัน
2. การสร้าง user ใหม่ต้องทำอย่างไรให้ไม่หลุด guard rail
3. การเปลี่ยน role/active state ต้องระวังอะไร
4. การตั้งหรือรีเซ็ตรหัสผ่านต้องสื่อสารอย่างไร
5. เมื่อไหร่ควร deactivate แทนการลบ account
6. admin ควรตรวจอะไรเป็น routine baseline

## 7) Acceptance Criteria
Phase นี้จะถือว่าจบเมื่อ:
- repo มี SOP ที่ผูกกับ current routes/actions จริง
- repo มี user lifecycle SOP ที่อธิบาย `is_active` ตาม behavior จริงของระบบ
- มีคำเตือนชัดเรื่อง self-demotion / self-deactivation / last-active-admin rule
- ไม่มีประโยคที่ overclaim ว่ามี IAM maturity เกินของจริง

## 8) Recommended Next Step After This Phase
หลัง Phase นี้ ควรไปต่อที่ `Program 3 / Phase 3.2 — Template Governance SOP`
ไม่ใช่กระโดดกลับไปเปิด feature wave ใหม่
