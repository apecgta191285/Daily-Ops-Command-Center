# Case Study Lock for Option A
วันที่: 23 เมษายน 2026

## 1) Official Case Study Framing
โปรเจ็คนี้ใช้กรณีศึกษาเป็น **การปฏิบัติงานประจำวันของห้องปฏิบัติการคอมพิวเตอร์หลายห้องในมหาวิทยาลัยเดียว**

สิ่งที่ระบบช่วยจัดการ:
- การตรวจห้องตามรอบเวลา `opening / during-day / closing`
- การระบุว่าปัญหาเกิดที่ห้องใด
- การส่งต่อจาก checklist ไป incident โดยยังรู้ room context
- การติดตามปัญหาในมุมของผู้ดูแลห้องและอาจารย์ผู้รับผิดชอบ

สิ่งที่ระบบยังไม่ได้ทำ:
- machine registry
- machine lifecycle
- inventory subsystem
- machine analytics

## 2) Locked Rooms for Demo and Oral Defense
ให้ใช้ชื่อห้องชุดนี้เสมอเวลาอธิบายหรือสาธิต:

- `Lab 1` = ห้องปฏิบัติการหลักสำหรับการเรียนการสอน
- `Lab 2` = ห้องฝึกปฏิบัติทั่วไป
- `Lab 3` = ห้องสำหรับงานโครงงานและการนำเสนอ
- `Lab 4` = ห้องรองรับนักศึกษาเพิ่มเติม
- `Lab 5` = ห้องสำหรับ session พิเศษ

หมายเหตุ:
- ชุดชื่อห้องนี้ตรงกับ seeded data ปัจจุบัน
- ถ้าต้องอ้าง room code ให้ใช้ `LAB-01` ถึง `LAB-05`

## 3) Locked Demonstration Scenarios
ใช้ 2 scenario นี้เป็นหลักเวลาพูดหรือสาธิต

### Scenario A — Student On Duty Finds a Room Issue
1. นักศึกษาที่เข้าเวรล็อกอินเข้าสู่ระบบ
2. เลือกห้องที่จะตรวจ เช่น `Lab 1`
3. เปิด checklist ของรอบเวลาปัจจุบันสำหรับห้องนั้น
4. ทำ checklist แล้วพบปัญหา
5. สร้าง incident พร้อม room context และ optional equipment reference เช่น `PC-03`
6. ส่งต่อให้ผู้ดูแลห้องติดตามต่อ

### Scenario B — Room Caretaker Reviews Cross-Room Work
1. ผู้ดูแลห้องล็อกอินเข้าสู่ dashboard
2. เห็นว่าห้องใดมีงานค้างหรือ incident อะไรอยู่
3. เปิด queue หรือ incident detail เพื่อตามงานต่อ
4. ใช้ room context เพื่อแยกว่าเป็นปัญหาของห้องไหน ไม่ใช่ปัญหาลอย ๆ

## 4) Locked Oral Defense Summary
เวลาตอบอาจารย์ ให้ใช้ประโยคแกนนี้:

> ระบบนี้เป็น internal web app สำหรับการจัดการงานประจำวันของห้องปฏิบัติการคอมพิวเตอร์หลายห้องในมหาวิทยาลัยเดียว โดยใช้ room + time scope ในการทำ checklist และติดตาม incident ภายในทีมเล็ก

## 5) Limitation Statement to Keep Verbatim
ใช้ชุดคำนี้เวลาต้องตอบเรื่องขอบเขต:

- ระบบรองรับ **room-centered operations** แล้ว
- ระบบรองรับ **optional equipment reference** แบบข้อความสั้น เช่น `PC-03` หรือ `Printer Lab 2`
- ระบบ **ยังไม่ใช่ machine registry**
- ระบบ **ยังไม่ใช่ production-grade platform**

