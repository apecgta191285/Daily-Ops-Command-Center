# Submission-Ready Document Set for Option A
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้ใช้ล็อกว่า ถ้าจะเตรียมส่งงาน เตรียมสไลด์ หรือเตรียมตอบคำถาม
ควรยึดเอกสารชุดไหนเป็นหลัก และควรใช้ wording ใดให้ตรงกัน

เป้าหมายคือ:
- ลดการพูดไม่ตรงกันระหว่าง README, canonical docs, เอกสารพูดสอบ, และสไลด์
- ลดความเสี่ยงที่เอกสารคนละชุดจะอ้างขอบเขตไม่เท่ากัน
- ทำให้การส่งและการสอบใช้ story เดียวกัน

## 2) The Submission-Ready Core Set
ให้ถือเอกสารชุดนี้เป็น baseline สำหรับการส่งและการอ้างอิง

### A. Canonical Product Truth
- [README.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/README.md)
- [00_Project_Lock_v1.1.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/00_Project_Lock_v1.1.md)
- [01_Product_Brief_v1.1.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/01_Product_Brief_v1.1.md)
- [02_System_Spec_v0.3.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/02_System_Spec_v0.3.md)
- [04_Current_State_v1.3.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/04_Current_State_v1.3.md)

### B. Oral Defense / Demo Support
- [109_Case_Study_Lock_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/109_Case_Study_Lock_Option_A_2026-04-23.md)
- [110_Role_Explanation_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/110_Role_Explanation_Option_A_2026-04-23.md)
- [111_System_Is_Is_Not_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/111_System_Is_Is_Not_Option_A_2026-04-23.md)
- [112_Demo_Data_and_Flow_Validation_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/112_Demo_Data_and_Flow_Validation_Option_A_2026-04-23.md)
- [113_Verbal_Defense_Notes_Thai_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/113_Verbal_Defense_Notes_Thai_Option_A_2026-04-23.md)
- [114_Page_By_Page_Explanation_Thai_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/114_Page_By_Page_Explanation_Thai_Option_A_2026-04-23.md)
- [115_FAQ_Strong_Questions_Thai_Option_A_2026-04-23.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/115_FAQ_Strong_Questions_Thai_Option_A_2026-04-23.md)

### C. Local Demo Operation
- [37_Local_Demo_Runbook_2026-04-14.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/37_Local_Demo_Runbook_2026-04-14.md)

## 3) Locked Wording That Must Stay Consistent

### Project Definition
ใช้ประโยคแกนนี้ให้ตรงกัน:

> ระบบนี้เป็น internal web app สำหรับการจัดการงานประจำวันของห้องปฏิบัติการคอมพิวเตอร์หลายห้องในมหาวิทยาลัยเดียว โดยใช้ room + time scope เพื่อทำ checklist, แจ้ง incident, และติดตามงานของผู้ดูแลห้องในระบบเดียว

### Actor Mapping
- `admin` = อาจารย์ผู้รับผิดชอบ / ผู้ได้รับมอบหมายดูแลระบบ
- `supervisor` = lab boy / เจ้าหน้าที่แล็บ / ผู้ดูแลห้อง
- `staff` = นักศึกษาที่เข้าเวรตรวจห้องตามรอบ

### Scope Statement
ใช้ความหมายนี้ให้ตรง:
- ระบบรองรับ `room-centered operations`
- ระบบยังคง `opening / during-day / closing` เป็นมิติของเวลา
- ระบบมี `optional equipment reference` แบบข้อความสั้น
- ระบบยังไม่ใช่ `machine registry`

### Claim Boundary
ใช้คำนี้เวลาต้องประเมินระดับระบบ:
- `strong capstone`
- `disciplined MVP+`
- `credible internal prototype`

ห้ามเปลี่ยนเป็น:
- production-grade platform
- enterprise-ready platform
- machine management system

## 4) What Slides / Report Must Not Say
ถ้าเจอคำพวกนี้ในสไลด์หรือรายงาน ให้ถือว่าเสี่ยงและควรแก้:
- machine registry
- asset inventory
- machine lifecycle
- predictive maintenance
- multi-tenant
- public signup
- production-ready
- enterprise platform

## 5) What Slides / Report Should Say Instead
- room-centered lab operations
- internal-only provisioning
- optional equipment reference
- multiple university computer labs / rooms
- checklist to incident handoff
- supervisor workboard / dashboard
- reviewable history and printable evidence surfaces

## 6) Final Submission Rule
ถ้าเอกสารส่ง, สไลด์, README, หรือคำพูดหน้าห้องไม่ตรงกัน
ให้ยึด canonical truth ก่อน แล้วปรับส่วนที่เหลือให้ตาม

ลำดับที่ควรยึด:
1. `00_Project_Lock_v1.1.md`
2. `01_Product_Brief_v1.1.md`
3. `02_System_Spec_v0.3.md`
4. `04_Current_State_v1.3.md`
5. เอกสาร support ชุด `109-115`

