# Machine Pain-Point Matrix
วันที่: 23 เมษายน 2026

## 1) Purpose
matrix นี้ใช้แยก pain points ที่ current room-centered model แก้ได้พอแล้ว
ออกจาก pain points ที่อาจต้องเปิด machine registry เป็น first-class domain

## 2) Pain-Point Matrix
| Pain point | Current room-centered model enough? | Why | Discovery signal |
| --- | --- | --- | --- |
| รู้ว่าปัญหาเกิดที่ห้องไหน | Yes | `room_id` เป็น first-class entity แล้ว | ไม่ใช่เหตุผลพอให้เปิด machine registry |
| ระบุอุปกรณ์ที่เกี่ยวข้องแบบคร่าว ๆ | Yes, for lightweight use | `equipment_reference` รองรับ label สั้นได้ | ยังไม่ต้องมี machine entity ถ้าต้องการแค่ reference |
| ติดตามงาน incident ตาม room/owner/follow-up | Yes | queue/detail/dashboard/history รองรับแล้ว | ไม่ใช่ machine-only problem |
| รู้ว่า incident หลายรายการเป็นของ asset เดิมแน่ ๆ หรือไม่ | No, not reliably | free text ไม่มี canonical identity | เป็นสัญญาณว่าต้องพิจารณา machine identity |
| วิเคราะห์เครื่องที่เสียซ้ำบ่อย | No | ไม่มี persistent asset history | เป็นสัญญาณสำหรับ machine-level analytics ในอนาคต |
| รู้ว่าเครื่องย้ายห้องเมื่อไร | No | current schema ไม่มี machine lifecycle | เป็นสัญญาณว่าต้องมี entity และ relationship model |
| บริหาร non-machine issues เช่น network/cleanliness/room environment | Yes | current incident domain รองรับอยู่แล้ว | ห้ามให้ machine model ไปทำลายความยืดหยุ่นนี้ |
| บังคับให้ทุก incident มี machine | No | หลาย incident ไม่ใช่ machine-specific | เป็น anti-requirement สำหรับ phase ถัดไป |

## 3) Key Anti-Requirements
จาก pain-point truth ตอนนี้ มีสิ่งที่ `ไม่ควร` ทำชัดเจน:
- ไม่ควรบังคับให้ทุก incident ต้องมี machine
- ไม่ควรลบ `equipment_reference` ทิ้งทันที
- ไม่ควรทำ room context ให้กลายเป็นเรื่องรอง
- ไม่ควรเปิด machine schema เพราะอยากให้ระบบดูใหญ่ขึ้นเฉย ๆ

## 4) Discovery Outcome
matrix นี้ชี้ว่า:
- current model ยังพอสำหรับ operational coordination ของ Option A
- machine registry จะมีเหตุผลก็ต่อเมื่อทีมต้องการ `identity continuity` และ `asset history`
- ปัญหาที่เหลือเป็นเรื่อง domain depth มากกว่าการแก้ defect ปัจจุบัน

## 5) Honest Limitation
matrix นี้ยังไม่ตัดสิน:
- machine model แบบไหนดีที่สุด
- cost ของ implementation สูงแค่ไหน
- UX burden จะเพิ่มเท่าไร

สิ่งเหล่านี้เป็นงานของ Phase 4.2 และ Phase 4.3
