# Machine Use-Case List
วันที่: 23 เมษายน 2026

## 1) Purpose
รายการนี้ใช้แยก use cases ที่เกี่ยวกับ machine/entity tracking
ออกจาก use cases ที่ current room-centered baseline รองรับอยู่แล้ว

## 2) Use Cases Already Covered By Current Model
use cases เหล่านี้ current repo truth รองรับแล้ว:
- staff รายงานปัญหาว่าเกิดในห้องไหน
- staff ระบุอุปกรณ์ที่เกี่ยวข้องแบบข้อความสั้น เช่น `PC-12` หรือ `Projector Front`
- supervisor review incident ตามห้อง, severity, ownership, และ follow-up state
- admin/supervisor ใช้ room context เพื่อติดตามงานประจำวัน
- checklist follow-up สร้าง incident พร้อม room context ได้

## 3) Candidate Use Cases That May Justify Machine Registry
use cases ที่อาจต้องมี machine entity จริง:
- ดูประวัติปัญหาของ `PC-12` ตัวเดิมข้ามหลาย incident
- แยก `PC-12` ใน `Lab 1` ออกจาก label คล้ายกันในอนาคตอย่างแน่นอน
- track การย้ายเครื่องจากห้องหนึ่งไปอีกห้องหนึ่ง
- รู้ว่าเครื่องตัวไหน active, retired, missing, or under maintenance
- ออกรายการเครื่องในแต่ละห้องอย่างมีตัวตนถาวร
- วิเคราะห์เครื่องที่เสียซ้ำหรือมี incident frequency สูง

## 4) Candidate Use Cases That Must Not Be Forced Into Machine Model
use cases เหล่านี้ไม่ควรถูกบังคับว่าเป็น machine issue เสมอ:
- เครือข่ายของทั้งห้องล่ม
- ห้องสกปรก
- แอร์เสีย
- projector หรือ peripheral ชั่วคราวที่ยังไม่มี canonical asset record
- ปัญหาที่เกิดกับ room infrastructure รวม ไม่ใช่เครื่องเดี่ยว

## 5) Discovery Interpretation
ข้อสังเกตสำคัญ:
- incident domain ปัจจุบันเป็น `mixed operational issue domain`
- บาง incident เป็น machine-like
- แต่หลาย incident เป็น room/environment/process issues

ดังนั้น phase ถัดไปต้องระวัง:
- อย่าออกแบบ machine domain แล้วบังคับให้ incident ทุกตัวมี machine
- อย่าทำให้ non-machine issue ต้องกรอกข้อมูลปลอมเพื่อผ่านฟอร์ม

## 6) Working Conclusion
จาก requirement truth ตอนนี้:
- machine registry มี `possible value`
- แต่ value นั้นอยู่ใน subset ของ use cases
- จึงต้องออกแบบ domain แบบที่ machine เป็น optional or scoped capability
  ไม่ใช่ assumption ที่ครอบทุก incident ตั้งแต่ต้น
