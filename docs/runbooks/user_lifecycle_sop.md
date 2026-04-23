# User Lifecycle SOP
วันที่: 23 เมษายน 2026

## 1) Purpose
SOP นี้ใช้สำหรับการสร้าง แก้ไข และปิดการใช้งานบัญชีผู้ใช้ภายในระบบ
โดยยึด current implementation truth ของ admin-managed user lifecycle

## 2) Current Lifecycle Truth
ระบบปัจจุบันมีข้อเท็จจริงดังนี้:
- การสร้าง/แก้ไขบัญชีทำผ่าน admin surfaces ใน app shell
- ไม่มี public registration
- ไม่มี invitation email flow
- `is_active` คือ access gate หลัก
- บัญชี inactive ต้องไม่สามารถ authenticate หรือใช้ protected routes ต่อได้

## 3) Lifecycle States
baseline ปัจจุบันใช้เพียงสถานะหลักนี้:
- `active`: เข้าใช้งานได้ตาม role
- `inactive`: เข้าใช้งานไม่ได้

ไม่ควร invent shadow states เพิ่ม เช่น:
- suspended
- pending invite
- archived account

ถ้ายังไม่มี capability รองรับจริง

## 4) Create User Procedure
ใช้ route:
- `/users/create`

ขั้นตอน:
- ยืนยันว่าผู้ใช้ใหม่ควรอยู่ role ใด: `admin`, `supervisor`, หรือ `staff`
- กรอก `name`
- กรอก `email`
- เลือก `role`
- กำหนด `is_active`
- ตั้ง initial password
- ส่งมอบรหัสผ่านผ่าน internal channel ที่ทีมยอมรับ

ข้อควรระวัง:
- admin role เป็น governance lane ไม่ควรใช้พร่ำเพรื่อ
- ถ้ายังไม่พร้อมให้ใช้งานจริง อย่าเปิด active state โดยไม่มี owner

## 5) Update User Procedure
ใช้ route:
- `/users/{user}/edit`

ปรับได้:
- name
- email
- role
- is_active
- password

ใช้เมื่อ:
- ผู้ใช้เปลี่ยนหน้าที่
- ต้องแก้อีเมล
- ต้องรีเซ็ตรหัสผ่าน
- ต้องปิด access

## 6) Guard Rails That Must Be Respected
ระบบบังคับ guard rails สำคัญดังนี้:
- admin ไม่สามารถลดสิทธิ์ตัวเองจากหน้าแก้ไขนี้
- admin ไม่สามารถปิด active state ของตัวเองจากหน้าแก้ไขนี้
- ต้องเหลือ active administrator อย่างน้อย 1 คนในระบบ

ดังนั้นถ้าต้องเปลี่ยนโครงสร้าง admin:
- ต้องมี active admin คนอื่นอยู่ก่อน
- ต้องตรวจให้แน่ใจว่าไม่ได้ตัดทางดูแลระบบของทีม

## 7) Deactivate vs Delete Guidance
baseline ปัจจุบันให้ใช้ `deactivate`
แทนการลบ account เมื่อ:
- ผู้ใช้หยุดใช้งานระบบ
- ต้องปิด access ชั่วคราว
- ต้องเก็บ traceability ของงานเดิมไว้

ไม่ควรลบ account เพื่อ “ทำให้รายการสวยขึ้น”

## 8) Password Handling Guidance
- ใช้ password reset/set เฉพาะเมื่อจำเป็น
- ถ้าแก้เฉพาะ role หรือ active state ให้ปล่อย password fields ว่าง
- ถ้าตั้ง password ใหม่ ต้อง handoff อย่างมี owner
- ห้ามส่งรหัสผ่านผ่านช่องทางไม่ปลอดภัย

## 9) Verification After Change
หลังสร้างหรือแก้ไข user ควรตรวจอย่างน้อย:
- user ปรากฏใน `/users`
- role ตรงตามที่ตั้ง
- active/inactive state ตรงตามที่ตั้ง
- ถ้า deactivate แล้ว บัญชีไม่ควรเข้าใช้งานได้
- ถ้าเปลี่ยน role สำคัญ ควรมี written note ภายในทีม

## 10) Honest Limitation
SOP นี้ยังไม่ครอบคลุม:
- HR onboarding workflow
- automated approval
- external identity sync
- audit trail platform

ดังนั้นนี่คือ `user lifecycle baseline SOP`
สำหรับระบบปัจจุบัน ไม่ใช่ enterprise IAM manual
