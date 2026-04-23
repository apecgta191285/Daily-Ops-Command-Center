# Template Governance SOP
วันที่: 23 เมษายน 2026

## 1) Purpose
SOP นี้ใช้สำหรับ admin ที่ดูแล checklist templates ในระบบปัจจุบัน
เพื่อให้การเปลี่ยน template เป็นงาน governance ที่สม่ำเสมอ
ไม่ใช่การแก้แบบ ad hoc

## 2) Current Governance Truth
repo ปัจจุบันมีข้อเท็จจริงดังนี้:
- template administration เป็น admin-only capability
- route truth หลักคือ:
  - `/templates`
  - `/templates/create`
  - `/templates/{template}/edit`
  - `POST /templates/{template}/duplicate`
- ระบบรองรับ `at most one active template per scope`
- scope ปัจจุบันคือ operational lanes เช่น `Opening`, `Midday`, `Closing`
- การ activate template ใหม่จะ retire live template เดิมเฉพาะใน scope เดียวกัน

## 3) Governance Goals
template governance ควรทำให้ได้:
- staff เห็น checklist ที่สอดคล้องกับ lane ที่เลือก
- แต่ละ scope มี live owner ชัดเจน
- draft กับ live template ไม่สับสนกัน
- historical runs ยังคงความหมายเดิม

สิ่งที่ SOP นี้ยังไม่ครอบคลุม:
- room-specific template governance
- machine-specific template governance
- enterprise document approval

## 4) Entry Points
admin ใช้งานผ่าน:
- `/templates` เพื่อดูภาพรวม governance ต่อ scope
- `/templates/create` เพื่อสร้าง draft หรือ live template ใหม่
- `/templates/{template}/edit` เพื่อแก้ template เดิม
- duplicate action เพื่อสร้าง copy ที่ inactive ก่อนแก้

## 5) When To Create, Edit, or Duplicate
### Create new template
ใช้เมื่อ:
- scope นั้นยังไม่มี template ที่เหมาะสม
- ต้องเริ่ม flow ใหม่ที่ยังไม่มีของเดิมรองรับ

### Edit existing template directly
ใช้เมื่อ:
- เป็นการแก้ wording หรือ ordering เล็กน้อย
- โครงสร้างหลักยังเหมือนเดิม
- ไม่มีความเสี่ยงทำให้ความหมายของ historical runs สับสน

### Duplicate first, then edit
ใช้เมื่อ:
- จะปรับโครงสร้าง checklist มาก
- จะเปลี่ยน grouping หรือ required flags หลายจุด
- template เดิมมี run history แล้ว
- ต้องการเตรียม draft ก่อน activate ภายหลัง

## 6) Activation Rules
- มี live template ได้ไม่เกิน 1 ตัวต่อ scope
- การ activate template ใหม่จะ retire live template เดิมใน scope เดียวกันเท่านั้น
- active template ของ scope อื่นต้องไม่ถูกกระทบ
- ก่อน activate ต้องเข้าใจผลกระทบต่อ staff runtime ใน lane นั้น

## 7) Historical Safety Rules
- ห้ามลบ checklist item ที่มี run history แล้วออกจาก template เดิม
- ถ้าต้องเปลี่ยนเชิงโครงสร้าง ให้ duplicate template ก่อน
- historical meaning ของ runs เดิมต้องยังอ่านเข้าใจได้
- อย่าแก้ของเดิมจนทำให้ของเก่าดูเหมือนเคยหมายถึงอย่างอื่น

## 8) Review Routine Before Save
admin ควรตรวจอย่างน้อย:
- title ชัดและไม่ซ้ำ
- scope ถูกต้อง
- live/draft state ถูกต้อง
- item ordering อ่านเป็น flow จริง
- group labels ช่วย scan ได้
- required flags สอดคล้องกับงานจริง
- ถ้าจะ activate ต้อง review activation impact ในหน้าจอ

## 9) Review Routine Before Activate
ก่อนเปิดใช้ live template ใหม่:
- ยืนยันว่า scope ถูก lane จริง
- ยืนยันว่า template เดิมใน scope นี้ควรถูก retire แล้ว
- ยืนยันว่าไม่ได้แก้แบบทำลายความหมายของ historical runs
- ยืนยันว่า staff จะไม่สับสนกับชื่อ, grouping, หรือ required items
- ถ้าเป็นการแก้ใหญ่ ให้ใช้ duplicate-based rollout

## 10) When To Escalate
ควรหยุดและคุยในทีมก่อน เมื่อ:
- มีข้อถกเถียงว่าควรแก้ template เดิมหรือ duplicate
- มีความเสี่ยงว่า activation จะกระทบการปฏิบัติงานวันนี้
- พบว่า scope นั้นไม่มี live template และยังไม่ชัดว่าจะใช้ draft ไหน
- พบว่าการเปลี่ยนที่ต้องการขัดกับ historical-item guard

## 11) Honest Limitation
SOP นี้เป็น `template governance baseline`
ไม่ใช่ change-management framework เต็มรูปแบบ

สิ่งที่ยังไม่มี:
- approval chain
- audit tooling
- rollout automation
- version comparison workflow
