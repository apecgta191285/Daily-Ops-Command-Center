# Post-Deploy Smoke Checklist
วันที่: 23 เมษายน 2026

## Goal
เช็กรวดเร็วว่าระบบหลัง deploy ยังใช้งาน flow หลักได้จริง
โดยไม่พยายามแทน test suite ทั้งหมด

## Guest / Entry
- หน้า home เปิดได้
- หน้า login เปิดได้
- login ด้วยบัญชีที่ใช้ตรวจ smoke ได้

## Staff Flow
- staff login สำเร็จ
- ถ้ามีหลายห้อง active ระบบยังให้เลือกห้องก่อนเข้าช่อง checklist
- เปิด checklist runtime ได้
- incident create surface เปิดได้

## Management Flow
- supervisor/admin login สำเร็จ
- dashboard เปิดได้
- incident queue/filter เปิดได้
- incident detail อย่างน้อย 1 รายการเปิดได้
- checklist history หรือ incident history เปิดได้

## Admin Flow
- template management เปิดได้
- user management เปิดได้

## Attachment / Storage
- incident detail ที่มี attachment ยังเปิดลิงก์ได้
- print summary/recap surface เปิดได้อย่างน้อย 1 หน้า

## Result Recording
บันทึกผลเป็นหนึ่งใน 3 แบบ:
- `pass`
- `pass with note`
- `fail`

ถ้ามี `fail` ใน flow หลัก:
- ห้ามปิด release ว่า `GO` ทันที
- ต้องประเมิน rollback ตาม [rollback_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/rollback_runbook.md)
