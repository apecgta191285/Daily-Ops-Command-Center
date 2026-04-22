# Release Security Checklist
วันที่: 23 เมษายน 2026

## Environment Safety
- `APP_ENV=production`
- `APP_DEBUG=false`
- production secrets ไม่ได้มาจาก local `.env`
- session cookie secure policy ถูกตั้งสำหรับ production
- HTTPS availability ถูกยืนยัน

## Auth / Access Safety
- login throttling ยังทำงานตาม baseline
- active-user enforcement ยังทำงาน
- admin accounts ที่ใช้ปล่อย release มี owner ชัดเจน
- ไม่มี test/demo accounts หลุดเข้า production โดยไม่ตั้งใจ

## Attachment / Storage Safety
- writable storage paths พร้อม
- attachment path ไม่แตกหรือเปิดกว้างเกิน baseline
- release นี้ไม่ได้ทำให้ attachment access model เปลี่ยนโดยไม่ review

## Secrets Safety
- DB credentials ถูกต้อง
- SMTP credentials ถูกต้อง
- APP_KEY ถูกต้อง
- ไม่มี secrets ถูก hardcode หรือหลุดใน release notes

## Release Honesty
- release นี้ไม่ได้อ้าง security maturity เกินหลักฐาน
- ถ้ามี known limitation ด้าน security ต้องถูกบันทึกไว้
- ถ้ามี auth/attachment risk ใหม่ ต้องถูก review ก่อน `GO`
