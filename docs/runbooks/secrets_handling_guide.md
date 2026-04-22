# Secrets Handling Guide
วันที่: 23 เมษายน 2026

## 1) Scope
guide นี้ใช้กับ secrets ขั้นต่ำของระบบ:
- `APP_KEY`
- DB credentials
- SMTP credentials
- optional cloud credentials
- webhook/monitoring credentials ในอนาคต

## 2) Core Rules
- ห้าม commit secrets ลง repo
- ห้ามเก็บ production secrets ในเอกสาร runbook
- ห้าม copy `.env` local ไปใช้ production ตรงๆ
- ห้ามส่ง secrets ผ่าน chat/email ที่ไม่เหมาะสม

## 3) Storage Rules
- production secrets ต้องอยู่ใน host/environment secret store เท่านั้น
- access ต้องจำกัดเฉพาะ owner/operator ที่จำเป็น
- ต้องรู้ว่าใครถือสิทธิ์แก้ secrets ได้

## 4) Rotation Rules
ขั้นต่ำต้องมี procedure ว่า:
- เปลี่ยน secret เมื่อใด
- ใครอนุมัติ
- เปลี่ยนแล้ว verify อะไร

Brutal truth:
- ถ้ายังไม่มี procedure นี้ อย่า claim secrets handling maturity เกินจริง

## 5) Leakage Response
ถ้าสงสัยว่า secret หลุด:
- เปลี่ยน secret ทันทีเมื่อทำได้
- ตรวจ config ที่ใช้ secret นั้น
- ตรวจ release/deploy surface ที่เกี่ยวข้อง
- บันทึก incident ตาม operational triage path

## 6) Known Limitation
guide นี้ยังไม่เท่ากับ secrets management platform
มันเป็น minimum handling policy สำหรับ current baseline เท่านั้น
