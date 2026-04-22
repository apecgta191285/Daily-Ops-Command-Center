# Restore Runbook
วันที่: 23 เมษายน 2026

## 1) Scope
runbook นี้ใช้กับ `production v1 single-node baseline`
และใช้สำหรับการกู้คืน:
- MySQL application database
- attachment files ใน `storage/app/public/incidents`

## 2) Preconditions
ก่อน restore ต้องมี:
- approved restore owner
- ชัดเจนว่าจะ restore อะไร:
  - database only
  - attachments only
  - ทั้งสองส่วน
- ระบุ backup artifact ที่จะใช้ได้
- มี deployment/maintenance decision ชัดว่าจะหยุด user traffic หรือไม่

## 3) Restore Scenarios
scenario หลักที่ runbook นี้รองรับ:

1. database corruption / accidental destructive migration
2. attachment loss บางส่วน
3. host replacement ที่ต้อง restore app data กลับขึ้นมา

## 4) Database Restore Procedure
ลำดับแนะนำ:

1. เปิด maintenance mode ถ้าระบบยังรับ traffic อยู่
2. ระบุ backup snapshot ที่จะใช้
3. บันทึก current DB state ก่อน restore ถ้ายังทำได้
4. restore database ไปยัง target MySQL instance
5. รัน migration status/check เพื่อตรวจว่า schema หลัง restore ตรงกับ expected release หรือไม่
6. ตรวจ user login และ core record counts แบบ pragmatic
7. ทำ post-restore smoke

## 5) Attachment Restore Procedure
ลำดับแนะนำ:

1. ระบุ attachment backup artifact ที่จะใช้
2. restore target path `storage/app/public/incidents`
3. ตรวจ owner/permissions ของ restored files
4. ตรวจ `public/storage` symlink และ path visibility
5. เปิด incident detail อย่างน้อย 1 รายการที่มี attachment เพื่อตรวจลิงก์จริง

## 6) Combined Recovery Procedure
ถ้า database และ attachments เสียพร้อมกัน:

1. restore database ก่อน
2. restore attachments ตามหลัง
3. ตรวจ record-to-file linkage แบบ pragmatic
4. รัน post-restore smoke checklist

## 7) Validation After Restore
ขั้นต่ำต้องตรวจ:
- login ใช้งานได้
- dashboard เปิดได้
- staff checklist flow เปิดได้
- incident queue/detail เปิดได้
- incident ที่มี attachment เปิดลิงก์ได้
- admin surface เปิดได้

## 8) Recording Requirements
ทุกครั้งที่ restore ต้องบันทึก:
- วันที่/เวลา
- สาเหตุ
- backup artifact ที่ใช้
- owner ที่ทำ restore
- scope ที่ restore
- ผลลัพธ์
- สิ่งที่ยังผิดปกติหลัง restore

## 9) Known Limitation
runbook นี้ยังเป็น `procedure baseline`

จนกว่าจะมี:
- restore drill จริง
- timing evidence
- owner confirmation

เรายังไม่ควร claim ว่า recovery capability ถูกพิสูจน์แล้ว
