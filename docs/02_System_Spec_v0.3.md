**A-lite Foundation Documentation Set**

**02_System_Spec_v0.3**  
ข้อกำหนดเชิงระบบสำหรับ A-lite ก่อนเริ่ม implementation

| Document ID | DOC-02-SS |
| :---- | :---- |
| **Project** | ระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย |
| **Version** | v0.3 |
| **Status** | Specification baseline - implementation ready |
| **Reference Date** | 03/04/2569 |

วัตถุประสงค์: เอกสารฉบับนี้ใช้เป็นฐานอ้างอิงต้นน้ำของหัวข้อ A-lite เพื่อกัน scope drift, ลดการตัดสินใจแบบเฉพาะหน้า และทำให้การคุยกับ AI / การลงมือพัฒนา / การเตรียมสอบยึดข้อมูลชุดเดียวกัน.

# **Document Control**

| Related Master | 00_Project_Lock_v1.1 |
| :---- | :---- |
| **Scope Basis** | MVP only |
| **Use With** | 03_Evaluation_Protocol_v1.1, 05_Decision_Log_v1.3, 06_Data_Definition_v1.2 และ 22_Architecture_Boundary_and_Execution_Standards_2026-04-11 |

# **1\. System Context**

ระบบมี 3 actor หลัก: Admin, Supervisor, Staff ใช้งานผ่านเว็บแอปเดียวกัน โดย focus คือ lab opening checks, during-day checks, room closing checks, incident follow-up, และ supervisor workboard สำหรับการดูแลห้องคอมหลายห้องในมหาวิทยาลัยเดียว

actor mapping ของ case study ถูกล็อกดังนี้:

* **Admin** = อาจารย์ผู้รับผิดชอบ / ผู้ได้รับมอบหมายดูแลระบบในบริบทวิชาการ
* **Supervisor** = lab boy / เจ้าหน้าที่แล็บ / ผู้ดูแลห้อง
* **Staff** = นักศึกษาที่เข้าเวรตรวจห้องตามรอบ

หมายเหตุสำคัญของ Phase A1:

* real case study = หลายห้องคอม / หลายห้องปฏิบัติการในมหาวิทยาลัยเดียว
* current implementation baseline = ยังขับเคลื่อนด้วย `scope` ของเวลาเป็นหลัก
* current correction path = มุ่งไปสู่ **room-centered operations**
* machine registry แบบเต็มยังไม่อยู่ใน scope ปัจจุบัน

# **2\. Functional Requirements**

| ID | Requirement | Priority |
| :---: | ----- | ----- |
| FR-01 | ผู้ใช้ล็อกอินเข้าสู่ระบบตามบทบาทได้ | Must |
| FR-02 | Admin สร้าง/แก้ไข Checklist Template และจัดการ user lifecycle ภายในระบบได้ในฐานะผู้รับผิดชอบเชิงวิชาการ | Must |
| FR-03 | Staff เปิด checklist ของวันและทำรายการตรวจเช็กได้ โดยระบบต้องสร้าง checklist run อัตโนมัติถ้ายังไม่มี run ของวันนั้น | Must |
| FR-04 | Staff สร้าง incident พร้อมหมวด/ความรุนแรง/รายละเอียด และ optional attachment ได้เมื่อพบปัญหาในห้องที่ตนรับผิดชอบรอบนั้น | Must |
| FR-05 | Admin และ Supervisor เปลี่ยนสถานะ incident ได้ และสามารถตั้ง owner/follow-up target แบบ lightweight ได้ | Must |
| FR-06 | ระบบแสดง dashboard workboard สำหรับ management โดยสรุป checklist, incident, ownership pressure, และ recent operational context จากข้อมูลจริงได้ เพื่อใช้ติดตามสถานะของหลายห้องในภาพรวม | Must |
| FR-07 | ระบบมีประวัติการกระทำขั้นต่ำเพื่อ trace ผู้ใช้และเวลาได้ | Should |
| FR-08 | Management review ประวัติ checklist run และ incident ล่าสุดในระบบได้โดยไม่ต้องพึ่ง analytics/reporting subsystem | Should |

# **3\. Authorization Boundary ที่ล็อก**

| Role | สิทธิ์หลักใน v1 |
| :---: | ----- |
| Admin | อาจารย์ผู้รับผิดชอบหรือผู้ได้รับมอบหมายดูแลระบบ สามารถจัดการ checklist templates, จัดการ user lifecycle ภายในระบบ, ดู dashboard, ดู incident list/detail, และอัปเดต status incident ได้ |
| Supervisor | lab boy / เจ้าหน้าที่แล็บ / ผู้ดูแลห้อง สามารถดู dashboard, ดู incident list/detail, และอัปเดต status incident ได้ |
| Staff | นักศึกษาที่เข้าเวรตรวจห้องตามรอบ สามารถทำ checklist run ของวัน, submit checklist, และสร้าง incident ได้ แต่ไม่มีสิทธิ์อัปเดต status incident |

# **4\. User Flows**

1. Admin login → ไปหน้า template management → สร้าง template และ checklist items → บันทึก  
2. Staff login → เปิด checklist run ของวัน; ถ้ายังไม่มีระบบสร้างให้อัตโนมัติ → ติ๊กแต่ละข้อ / ใส่หมายเหตุ → submit  
2.1 ถ้ามี live checklist หลาย scope ระบบต้องแสดง workboard ของวันเพื่อให้ staff เลือก lane เช่น เปิดห้อง / ตรวจระหว่างวัน / ปิดห้อง ก่อนเข้า run ของ scope นั้น  
2.2 ในการอธิบายต่ออาจารย์ ให้ย้ำว่านี่คือ baseline ปัจจุบันก่อนเพิ่ม room dimension; case study จริงมีหลายห้อง และ Phase ถัดไปจะทำให้ run รู้จักห้องอย่างเป็นทางการ  
3. Staff พบปัญหา → สร้าง incident → ระบุ category + severity + description + optional attachment → บันทึก  
4. Supervisor หรือ Admin เปิดหน้า incidents → ดูรายการ open → ตั้ง owner/follow-up target เมื่อจำเป็น → อัปเดตสถานะเป็น In Progress / Resolved  
5. Supervisor หรือ Admin เปิด dashboard → เห็น workboard ของวันซึ่งตอบว่า lane ไหนยังค้าง, ownership pressure อยู่ตรงไหน, และ recent operational context ช่วงล่าสุดบอกอะไรเกี่ยวกับวันนี้ โดยใน oral exam ต้องอธิบายตรงๆ ว่าระบบกำลังมุ่งไปสู่ room-centered view สำหรับหลายห้อง
6. Admin เปิดหน้า template administration → เห็น live checklist ownership ของแต่ละ scope, duplicate draft อย่างปลอดภัย, และ activate template เฉพาะ lane ที่เกี่ยวข้อง
7. Admin เปิดหน้า user administration → เห็น roster ปัจจุบัน, สร้าง account ภายใน, ปรับ role/active state, และตั้งหรือเปลี่ยน password แบบ explicit จากใน app shell
8. Supervisor หรือ Admin เปิดหน้า checklist/incident history → review สิ่งที่เกิดขึ้นในช่วงที่ผ่านมา, pivot ไปยัง recap/detail ที่เกี่ยวข้อง, และใช้ประวัติในระบบเพื่อทบทวนงานจริงโดยไม่ต้องพึ่ง reporting layer ภายนอก

# **5\. Business Rules**

* Checklist item ต้องเก็บผลลัพธ์อย่างน้อยว่า Done / Not Done และ optional note  
* ทุก checklist item ต้องถูกตอบก่อน submit; ห้ามปล่อยค่า blank สำหรับข้อที่อยู่ใน run  
* Checklist run ใน v1 ถูกสร้างอัตโนมัติเมื่อ Staff เปิด lane ของวันและยังไม่มี run ของ scope นั้น
* baseline ปัจจุบันรองรับ active daily checklist template ได้ 1 อันต่อ 1 scope; `ChecklistScope` เป็น runtime dimension สำหรับ opening / midday / closing แล้ว ไม่ได้เป็นเพียง metadata สำหรับ template administration และ reporting เท่านั้น
* เมื่อมี live scope มากกว่า 1 อัน `/checklists/runs/today` ต้องแสดง scope-aware workboard แทนการ force เข้า checklist เดียวทั้งระบบ
* Checklist run ใน v1 ไม่มี draft state อย่างเป็นทางการ; ใช้ `submitted_at` เป็นตัวบอกว่าถูก submit แล้วหรือยัง  
* Incident ต้องมี category, severity, status, description และผู้สร้างอย่างน้อย  
* Incident ใน v1 หลัง WF2 อาจมี `owner_id` และ `follow_up_due_at` แบบ optional ได้ โดย owner ต้องเป็น management-capable user เท่านั้น  
* Incident status ใน v1 จำกัดที่ Open / In Progress / Resolved  
* การอัปเดต incident status อนุญาตเฉพาะ Admin และ Supervisor เท่านั้น  
* Operational history ใน v1 หลัง WF4 เป็น lightweight review layer เท่านั้น: รองรับ checklist run archive และ incident history slices รวมถึง print-friendly recap/summary surfaces แบบรายรายการเพื่อใช้เป็น evidence convenience ได้ แต่ยังไม่มี exports, analytics warehouse, retrospective KPI builder, หรือ reassignment history
* User administration ใน v1 เป็น admin-only route family แบบ lightweight: `/users`, `/users/create`, `/users/{user}/edit`
* `is_active` เป็น access gate หลักของ user lifecycle; inactive accounts ต้องไม่สามารถ authenticate ได้
* Admin lifecycle ต้องไม่อนุญาตให้ระบบไม่มี active admin เหลืออยู่ และต้องกัน self-deactivation / self-demotion ภายใน workflow เดียวกัน
* Ownership model ของ incident ใน v1 เป็น lightweight accountability เท่านั้น: 1 owner แบบ optional, 1 follow-up target date แบบ optional, ไม่มี reassignment history, notification, SLA, หรือ escalation workflow  
* Incident attachments เป็น optional และเก็บไฟล์แบบ local public disk เท่านั้น  
* Dashboard ใช้ข้อมูลจริงจาก checklist runs, incidents, และ operational history ที่มีอยู่จริงเท่านั้น และต้องสามารถสะท้อน missing / incomplete scope lanes ของวัน, `unowned / overdue / owned by me` accountability pressure, และ recent command context ได้แบบย่อโดยไม่กลายเป็น analytics subsystem  
* v1 ไม่รองรับ workflow approval, incident reassignment history, notifications, SLA engine, หรือ checklist draft workflow
* real case study ปัจจุบันถือว่ามีหลายห้องคอมในมหาวิทยาลัยเดียว แต่ room ยังไม่เป็น first-class entity ใน implementation baseline ของ v1 ณ ตอนนี้
* correction path ที่ล็อกไว้คือ room-centered operations ก่อน machine-centered operations; machine registry, inventory, และ machine lifecycle ยังไม่อยู่ใน scope ปัจจุบัน

# **6\. Core Data Model**

| Entity | Fields ขั้นต่ำ |
| :---: | ----- |
| User | id, name, email, role, is_active |
| ChecklistTemplate | id, title, description, scope, is_active |
| ChecklistItem | id, template_id, title, description, sort_order, is_required |
| ChecklistRun | id, template_id, run_date, assigned_team_or_scope, created_by, submitted_at, submitted_by |
| ChecklistRunItem | id, run_id, item_id, result, note, checked_by, checked_at |
| Incident | id, title, category, severity, status, description, attachment_path, created_by, owner_id, follow_up_due_at, created_at, resolved_at |
| IncidentActivity | id, incident_id, action_type, summary, actor_id, created_at |

หมายเหตุสำหรับ oral exam:

* ตารางข้อมูลหลักปัจจุบันยังไม่รวม `room_id`
* นี่ไม่ใช่การปฏิเสธ case study หลายห้อง แต่เป็น baseline implementation ก่อน Phase A2 — Schema Slice
* ดังนั้นคำอธิบายที่ถูกต้องคือ “repo ปัจจุบัน grounded กับ case study หลายห้องแล้วในเชิง product truth แต่ room-centered persistence ยังเป็นงาน phase ถัดไป”

# **7\. Acceptance Criteria**

* Checklist template สร้างได้และนำไปใช้สร้าง checklist run ได้จริง  
* Staff เปิด checklist run ของวันแล้วระบบสร้าง run ให้อัตโนมัติได้จริงเมื่อยังไม่มี และสามารถเลือก scope lane ได้เมื่อมีหลาย live scopes  
* Checklist run บันทึกผลแต่ละข้อ, submit ได้จริง และดึงกลับมาอ่านได้จริง  
* Incident สร้างและเปลี่ยนสถานะได้จริงโดยไม่ใช้ข้อมูลจำลองลอย ๆ  
* Dashboard แสดง workboard signals ของวันจากฐานข้อมูลจริง ทั้ง completion, scope-lane coverage, incidents, accountability pressure, และ recent-history command context  
* Management ต้องสามารถเปิด `/checklists/history` และ `/incidents/history` เพื่อ review operational history ล่าสุดได้จากข้อมูลจริงในระบบ
* Management ต้องสามารถเปิด printable checklist recap และ printable incident summary ได้จากข้อมูลจริงในระบบ โดยไม่ต้องสร้าง report subsystem ใหม่
* ระบบไม่เปิด route หรือ action ที่ไม่เกี่ยวข้องให้บทบาทที่ไม่มีสิทธิ์เข้าถึง  
* Staff พยายามอัปเดต status incident แล้วต้องถูกปฏิเสธอย่างถูกต้อง
* Management ต้องสามารถเห็น incident ที่ไม่มี owner, incident ที่ follow-up เลยกำหนด, และ incident ที่ตัวเองรับผิดชอบอยู่ได้โดยไม่ต้องตีความจาก activity timeline อย่างเดียว
* Admin ต้องสามารถจัดการ account ภายในจาก product shell ได้โดยไม่ต้องแก้ฐานข้อมูลตรง
* ระบบต้องกัน self-deactivation, self-demotion, และการทำให้ไม่มี active admin เหลืออยู่

# **8\. Edge Cases to Handle**

* ผู้ใช้พยายาม submit checklist โดยยังตอบบางข้อไม่ครบ  
* incident ไม่มีรูปแนบแต่ยังต้องบันทึกได้  
* dashboard ต้องไม่พังเมื่อยังไม่มี incident หรือยังไม่มี checklist run  
* Staff เปิด checklist ของวันซ้ำ ต้องไม่สร้าง run ซ้ำถ้ามีของวันนั้นแล้ว  
* staff เลือก scope ที่ยังไม่มี active template ต้องเห็น calm configuration state แทน hard failure  
* dashboard ต้องเตือนเมื่อมี live scope lane หายหรือยัง submit ไม่ครบ  
* Staff พยายามเรียก action update status incident โดยตรงผ่าน URL หรือ request ปลอม  
* ผู้ใช้ที่ไม่มีสิทธิ์ต้องเข้า route ที่ไม่เกี่ยวข้องไม่ได้
