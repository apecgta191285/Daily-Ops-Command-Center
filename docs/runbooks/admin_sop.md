# Admin SOP
วันที่: 23 เมษายน 2026

## 1) Purpose
SOP นี้ใช้สำหรับ admin ที่ดูแล Daily Ops Command Center ในสภาพ repo ปัจจุบัน
โดยครอบคลุมงานประจำที่มี capability รองรับอยู่แล้วในระบบ

## 2) Current Admin Responsibilities
admin ในระบบปัจจุบันรับผิดชอบอย่างน้อย:
- ดู dashboard และ queue ของระบบ
- ดู incident detail / history / print surfaces
- ดูแล checklist template governance
- ดูแล user lifecycle ภายใน app shell
- รักษาความสอดคล้องของ active/inactive access

สิ่งที่ admin role ปัจจุบันยังไม่ใช่:
- enterprise IAM administrator
- infrastructure owner โดยสมบูรณ์
- security operations team
- machine registry operator

## 3) Admin Entry Points
admin ใช้งานผ่าน route หลักดังนี้:
- `/dashboard`
- `/incidents`
- `/incidents/history`
- `/templates`
- `/users`

## 4) Daily / Routine Checklist
admin ควรตรวจอย่างน้อย:
- login เข้าใช้งานได้ตามปกติ
- dashboard เปิดได้และไม่เห็นข้อความ error ผิดปกติ
- incident queue ยังโหลดได้
- user roster และ template surfaces ยังเปิดได้
- มี active admin อย่างน้อย 1 คน
- ไม่มีบัญชีที่ถูกปิดโดยไม่ตั้งใจระหว่างการดูแลระบบ

## 5) When To Use User Administration
ให้ใช้ user administration เมื่อ:
- มีบุคลากรใหม่ต้องเข้าระบบ
- มีการเปลี่ยนบทบาทจาก staff เป็น supervisor หรือ admin
- ต้องปิด access ชั่วคราวหรือถาวร
- ต้องตั้ง password ใหม่ด้วยการ handoff ภายในทีม

ไม่ควรใช้เมื่อ:
- ต้องการลบประวัติการใช้งานเก่า
- ต้องการ “ซ่อนปัญหา” โดยเปลี่ยน role แบบไม่บันทึก owner

## 6) Admin Operating Rules
- ใช้ `is_active` เป็น access gate หลักของ account
- ถ้าผู้ใช้ไม่ควรเข้าใช้งานแล้ว ให้ deactivate ก่อน ไม่ใช่แก้ข้อมูลมั่ว
- อย่าลดสิทธิ์หรือปิด admin account โดยไม่มี active admin คนอื่นเหลือ
- อย่าแชร์ password ผ่านช่องทางที่ไม่ไว้ใจได้
- ถ้าตั้ง password ให้ผู้อื่น ต้องมีการ handoff ผ่าน internal channel ที่ทีมยอมรับร่วมกัน
- ถ้ามีการเปลี่ยนแปลงสำคัญ ควรมี written note ภายในทีม แม้ phase นี้ยังไม่มี ticket automation

## 7) Incident / Dashboard Review Baseline
admin ควรใช้ dashboard และ incident surfaces เพื่อ:
- ดูว่ามี unresolved, stale, unowned, หรือ overdue follow-up ค้างหรือไม่
- ตรวจว่าประเด็นของห้องถูกส่งต่อให้ supervisor ได้ชัด
- ตรวจว่าไม่เกิดความสับสนเรื่อง room context

SOP นี้ยังไม่เท่ากับ on-call procedure
แต่เป็น baseline review discipline ขั้นต้น

## 8) Escalation Guidance
ควร escalate ภายในทีมเมื่อ:
- ไม่มี active admin เหลือพอสำหรับดูแลระบบ
- พบว่าบัญชีสำคัญถูกปิดหรือ role ผิด
- login ของผู้ใช้ที่ควร active ใช้งานไม่ได้
- incident/dashboard surfaces แสดง behavior ที่ขัดกับ repo truth

## 9) Honest Limitation
SOP นี้ยังไม่ครอบคลุม:
- infra deployment ownership
- restore drill execution
- enterprise approval chain
- external identity provider

ดังนั้นเอกสารนี้คือ `admin operating baseline`
ไม่ใช่ full operations manual
