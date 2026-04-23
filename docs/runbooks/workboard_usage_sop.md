# Workboard Usage SOP
วันที่: 23 เมษายน 2026

## 1) Purpose
SOP นี้อธิบายวิธีใช้ dashboard/workboard ของ Daily Ops Command Center
ให้เป็น routine control surface สำหรับวันปัจจุบัน
ไม่ใช่ใช้แบบ “เปิดดูผ่าน ๆ” หรือเข้าใจผิดว่าเป็น ticket board เต็มรูปแบบ

## 2) Workboard Truth
workboard ปัจจุบันใน `/dashboard` เป็น `today-first management surface`
โดยผูกกับ:
- checklist lanes ของวันปัจจุบัน
- attention signals ของ incident และ checklist
- recent operating context

ดังนั้น workboard ใช้ตอบคำถามว่า:
- วันนี้ lane ไหนยังไม่ปิด
- วันนี้มี pressure อะไรที่ควร review ต่อ
- วันนี้ควร drill-down ไป queue หรือ detail ตรงไหน

ไม่ใช่ใช้ตอบทุกคำถามของงานย้อนหลังหรือ support history ทั้งหมด

## 3) When To Use Workboard
ควรใช้ workboard:
- ตอนเริ่มกะหรือเริ่มวัน
- หลังมีการรับ incident ใหม่
- ก่อนสรุปว่าวันนี้ไม่มีงานค้าง
- ก่อน handoff ระหว่างคนดูแลงาน

ไม่ควรใช้ workboard อย่างเดียวเมื่อ:
- ต้องวิเคราะห์ history ลึกหลายวัน
- ต้องดู incident detail ระดับรายเคส
- ต้องตัดสินใจเรื่อง template หรือ user governance

## 4) Core Reading Order
ลำดับแนะนำเวลาเปิด dashboard:

1. ดู `Needs Attention Today`
2. ดู `Ownership and Work Buckets`
3. ดู `Today's room workboard`
4. ดู `Recent Incidents`
5. drill-down ไปหน้า queue/detail ที่เกี่ยวข้อง

หลักการ:
- อ่านจาก signal ไป action
- อย่าอ่านเฉพาะตัวเลขรวมแล้วคิดว่างานจบ

## 5) How To Read Workboard Lanes
lane ใน workboard ปัจจุบันอาจอยู่ใน state หลัก:
- `unavailable`
- `not_started`
- `in_progress`
- `submitted`

การใช้งานเชิงปฏิบัติ:
- `unavailable` หมายถึง lane นั้นยังไม่มี active template ให้ใช้งาน
- `not_started` หมายถึงวันนี้ยังไม่มี run เริ่มต้น
- `in_progress` หมายถึงมี run แล้วแต่ยังไม่ submitted
- `submitted` หมายถึง lane นั้นปิดรอบของวันแล้ว

## 6) How To Read Attention Signals
attention signals สำคัญที่ควรตอบสนอง:
- high severity unresolved incidents
- stale unresolved incidents
- unowned incidents
- overdue follow-up
- scope lanes ที่ยังไม่ครบหรือยังไม่มี template

หลักการ:
- signal คือคำเชิญให้ review ต่อ
- ไม่ใช่คำตัดสินสุดท้ายแทน detail page

## 7) Drill-Down Rules
เมื่อ workboard หรือ dashboard ชี้สัญญาณ:
- ถ้าเป็น ownership/follow-up issue ให้เข้า `/incidents` พร้อม filter ที่ตรงโจทย์
- ถ้าเป็น lane coverage issue ให้เข้า checklist runtime หรือ review template governance ตามบทบาท
- ถ้าเป็น recent incident ที่สำคัญ ให้เปิด `/incidents/{incident}` เพื่อดู activity timeline ก่อนตัดสินใจ

## 8) Workboard Discipline
- ใช้ workboard เป็น `start here` surface ไม่ใช่ `only here` surface
- อย่าปล่อยให้ signal ค้างโดยไม่มี owner หรือ next action
- อย่าใช้ความเงียบของ dashboard เป็นหลักฐานว่าปัญหาทุกอย่างหมดแล้ว ถ้ายังไม่ได้ review queue/detail
- ถ้าข้อมูลดูขัดกัน ให้เช็ค queue/detail ก่อนสรุป

## 9) Honest Limitation
workboard ปัจจุบันยังไม่ใช่:
- dispatch console
- ticket lifecycle board
- cross-team command center
- long-horizon analytics workspace

ดังนั้น SOP นี้คือ `today-first workboard usage baseline`
ไม่ใช่ full support operations console guide
