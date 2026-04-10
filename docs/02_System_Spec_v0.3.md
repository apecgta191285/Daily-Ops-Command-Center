**A-lite Foundation Documentation Set**

**02_System_Spec_v0.3**  
ข้อกำหนดเชิงระบบสำหรับ A-lite ก่อนเริ่ม implementation

| Document ID | DOC-02-SS |
| :---- | :---- |
| **Project** | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก |
| **Version** | v0.3 |
| **Status** | Specification baseline - implementation ready |
| **Reference Date** | 03/04/2569 |

วัตถุประสงค์: เอกสารฉบับนี้ใช้เป็นฐานอ้างอิงต้นน้ำของหัวข้อ A-lite เพื่อกัน scope drift, ลดการตัดสินใจแบบเฉพาะหน้า และทำให้การคุยกับ AI / การลงมือพัฒนา / การเตรียมสอบยึดข้อมูลชุดเดียวกัน.

# **Document Control**

| Related Master | 00_Project_Lock_v1.1 |
| :---- | :---- |
| **Scope Basis** | MVP only |
| **Use With** | 03_Evaluation_Protocol_v1.1, 06_Data_Definition_v1.2, 07_UI_Flow_Wireframe_v1.3 และ 11_Implementation_Task_List_v1.0 |

# **1\. System Context**

ระบบมี 3 actor หลัก: Admin, Supervisor, Staff ใช้งานผ่านเว็บแอปเดียวกัน โดย focus คือ checklist งานประจำวัน, incident tracking และ dashboard พื้นฐาน

# **2\. Functional Requirements**

| ID | Requirement | Priority |
| :---: | ----- | ----- |
| FR-01 | ผู้ใช้ล็อกอินเข้าสู่ระบบตามบทบาทได้ | Must |
| FR-02 | Admin สร้าง/แก้ไข Checklist Template ได้ | Must |
| FR-03 | Staff เปิด checklist ของวันและทำรายการตรวจเช็กได้ โดยระบบต้องสร้าง checklist run อัตโนมัติถ้ายังไม่มี run ของวันนั้น | Must |
| FR-04 | Staff สร้าง incident พร้อมหมวด/ความรุนแรง/รายละเอียด และ optional attachment ได้ | Must |
| FR-05 | Admin และ Supervisor เปลี่ยนสถานะ incident ได้ | Must |
| FR-06 | ระบบแสดง dashboard พื้นฐานของ checklist และ incident ได้ | Must |
| FR-07 | ระบบมีประวัติการกระทำขั้นต่ำเพื่อ trace ผู้ใช้และเวลาได้ | Should |

# **3\. Authorization Boundary ที่ล็อก**

| Role | สิทธิ์หลักใน v1 |
| :---: | ----- |
| Admin | จัดการ checklist templates, ดู dashboard, ดู incident list/detail, และอัปเดต status incident ได้ |
| Supervisor | ดู dashboard, ดู incident list/detail, และอัปเดต status incident ได้ |
| Staff | ทำ checklist run ของวัน, submit checklist, และสร้าง incident ได้ แต่ไม่มีสิทธิ์อัปเดต status incident |

# **4\. User Flows**

1. Admin login → ไปหน้า template management → สร้าง template และ checklist items → บันทึก  
2. Staff login → เปิด checklist run ของวัน; ถ้ายังไม่มีระบบสร้างให้อัตโนมัติ → ติ๊กแต่ละข้อ / ใส่หมายเหตุ → submit  
3. Staff พบปัญหา → สร้าง incident → ระบุ category + severity + description + optional attachment → บันทึก  
4. Supervisor หรือ Admin เปิดหน้า incidents → ดูรายการ open → อัปเดตสถานะเป็น In Progress / Resolved  
5. Supervisor หรือ Admin เปิด dashboard → เห็น completion summary และ incident overview

# **5\. Business Rules**

* Checklist item ต้องเก็บผลลัพธ์อย่างน้อยว่า Done / Not Done และ optional note  
* ทุก checklist item ต้องถูกตอบก่อน submit; ห้ามปล่อยค่า blank สำหรับข้อที่อยู่ใน run  
* Checklist run ใน v1 ถูกสร้างอัตโนมัติเมื่อ Staff เปิด checklist ของวันและยังไม่มี run ของวันนั้น (ระบบรองรับ Template ที่ active เพียง 1 อัน หากมีมากกว่า 1 ระบบจะฟ้อง error)
* Checklist run ใน v1 ไม่มี draft state อย่างเป็นทางการ; ใช้ `submitted_at` เป็นตัวบอกว่าถูก submit แล้วหรือยัง  
* Incident ต้องมี category, severity, status, description และผู้สร้างอย่างน้อย  
* Incident status ใน v1 จำกัดที่ Open / In Progress / Resolved  
* การอัปเดต incident status อนุญาตเฉพาะ Admin และ Supervisor เท่านั้น  
* Incident attachments เป็น optional และเก็บไฟล์แบบ local public disk เท่านั้น  
* Dashboard ใช้ข้อมูลจริงจาก checklist runs และ incidents เท่านั้น  
* v1 ไม่รองรับ workflow approval, incident assignment/reassignment หรือ checklist draft workflow

# **6\. Core Data Model**

| Entity | Fields ขั้นต่ำ |
| :---: | ----- |
| User | id, name, email, role, is_active |
| ChecklistTemplate | id, title, description, is_active |
| ChecklistItem | id, template_id, title, description, sort_order, is_required |
| ChecklistRun | id, template_id, run_date, assigned_team_or_scope, created_by, submitted_at, submitted_by |
| ChecklistRunItem | id, run_id, item_id, result, note, checked_by, checked_at |
| Incident | id, title, category, severity, status, description, attachment_path, created_by, created_at, resolved_at |
| IncidentActivity | id, incident_id, action_type, summary, actor_id, created_at |

# **7\. Acceptance Criteria**

* Checklist template สร้างได้และนำไปใช้สร้าง checklist run ได้จริง  
* Staff เปิด checklist run ของวันแล้วระบบสร้าง run ให้อัตโนมัติได้จริงเมื่อยังไม่มี  
* Checklist run บันทึกผลแต่ละข้อ, submit ได้จริง และดึงกลับมาอ่านได้จริง  
* Incident สร้างและเปลี่ยนสถานะได้จริงโดยไม่ใช้ข้อมูลจำลองลอย ๆ  
* Dashboard แสดงตัวเลข completion กับ incidents จากฐานข้อมูลจริง  
* ระบบไม่เปิด route หรือ action ที่ไม่เกี่ยวข้องให้บทบาทที่ไม่มีสิทธิ์เข้าถึง  
* Staff พยายามอัปเดต status incident แล้วต้องถูกปฏิเสธอย่างถูกต้อง

# **8\. Edge Cases to Handle**

* ผู้ใช้พยายาม submit checklist โดยยังตอบบางข้อไม่ครบ  
* incident ไม่มีรูปแนบแต่ยังต้องบันทึกได้  
* dashboard ต้องไม่พังเมื่อยังไม่มี incident หรือยังไม่มี checklist run  
* Staff เปิด checklist ของวันซ้ำ ต้องไม่สร้าง run ซ้ำถ้ามีของวันนั้นแล้ว  
* Staff พยายามเรียก action update status incident โดยตรงผ่าน URL หรือ request ปลอม  
* ผู้ใช้ที่ไม่มีสิทธิ์ต้องเข้า route ที่ไม่เกี่ยวข้องไม่ได้
