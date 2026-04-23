# Template Change Review Checklist
วันที่: 23 เมษายน 2026

ใช้ checklist นี้ก่อน `save` หรือ `activate` checklist template

## Scope and Intent
- ยืนยันว่า template นี้อยู่ใน scope ที่ถูกต้อง
- ยืนยันว่าการแก้ครั้งนี้เป็น minor update หรือ major revision
- ถ้าเป็น major revision ให้พิจารณา duplicate ก่อน

## Live vs Draft
- ยืนยันว่าต้องการให้ template นี้เป็น `active` หรือ `draft`
- ถ้า activate ให้ยืนยันว่าเข้าใจว่าจะ retire live template เดิมใน scope เดียวกัน
- ยืนยันว่า scope อื่นไม่ควรถูกกระทบ

## Checklist Structure
- title ชัดและไม่สับสน
- item order อ่านเป็น flow การทำงานจริง
- group labels ช่วยให้ scan ได้
- required flags สอดคล้องกับงานจริง
- description มีเฉพาะจุดที่ช่วยลด ambiguity

## Historical Safety
- ตรวจว่ากำลังลบ item ที่มี run history หรือไม่
- ถ้ามี run history และการเปลี่ยนใหญ่ ให้ duplicate แทนการฝืนแก้ของเดิม
- ยืนยันว่าหลังแก้แล้ว historical runs จะยังตีความได้ถูกต้อง

## Runtime Impact
- staff lane ที่เกี่ยวข้องยังมี live template ครอบอยู่
- ชื่อและ framing ของ template ไม่ทำให้ผู้ใช้สับสน
- การเปลี่ยนนี้ไม่ทำให้ lane สำคัญขาด coverage โดยไม่ตั้งใจ

## Final Decision
- `SAVE AS DRAFT`
- `SAVE AS ACTIVE`
- `DUPLICATE FIRST`
- `STOP AND REVIEW WITH TEAM`
