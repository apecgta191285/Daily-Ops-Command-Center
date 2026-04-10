# **Decision Log**

> Historical note: This document contains append-only decisions across multiple project phases. For the current repository baseline, use this log together with [22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md](./22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md), [24_Domain_Normalization_Design_2026-04-11.md](./24_Domain_Normalization_Design_2026-04-11.md), and [26_Architecture_Debt_Roadmap_2026-04-11.md](./26_Architecture_Debt_Roadmap_2026-04-11.md) as the active engineering reference.
>
> Historical impact labels below may still mention older working documents that were intentionally removed from the canonical repo set. Treat those names as historical context, not active source-of-truth files.

## *A-lite Foundation Documentation Set*

**DOC-05-DL | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก**  
**Version v1.3 | Live working document - append only | วันที่อ้างอิง 03/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้บันทึกการตัดสินใจเชิงวิศวกรรมของหัวข้อ A-lite เพื่อกันการย้อนเถียงจากความทรงจำหรือข้อความแชต และบังคับให้เอกสารอื่นสะท้อนการตัดสินใจที่สำคัญ.

# **1\. Policy**

* append only / ไม่ย้อนแก้ประวัติแบบไร้ร่องรอย  
* ใช้กับการเปลี่ยน scope, baseline, stack, schema, evaluation และ demo context  
* ถ้าตัดสินใจใหม่กระทบเอกสารอื่น ต้องอัปเดตเอกสารนั้นด้วย

# **2\. Initial Decisions**

**D-001 | Locked**

Decision: ล็อกหัวข้อโครงงานเป็น A-lite แทน Daily Ops Command Center แบบเต็ม

Rationale: เพื่อให้ขอบเขตพอดีกับผู้พัฒนาคนเดียวและทำ MVP จบจริงได้

Impact: Project Lock, Product Brief, System Spec ต้องสะท้อนขอบเขตใหม่

**D-002 | Locked**

Decision: กำหนดแกนระบบเป็น checklist รายวัน + incident tracking + dashboard พื้นฐาน

Rationale: เพื่อให้ระบบไม่กลายเป็น dashboard ลอย ๆ และยังไม่หนักเท่า operations suite เต็มรูปแบบ

Impact: ตัด feature พวก approval, notification, analytics ขั้นสูง ออกจาก v1

**D-003 | Locked**

Decision: เลือก baseline เป็น manual checklist / chat reporting เทียบกับระบบ A-lite

Rationale: เพราะเป็น baseline ที่อธิบายง่าย เหมาะกับการประเมินเชิงวิชาการ และใช้ข้อมูลชุดเดียวกันได้

Impact: Evaluation Protocol ต้องล็อก task set และ metrics ตาม comparison นี้

**D-004 | Locked**

Decision: ใช้ modular monolith เป็นสถาปัตยกรรมตั้งต้น

Rationale: ลด moving parts และเพิ่ม maintainability สำหรับ solo dev

Impact: repo structure และ coding plan ต้องไม่แตกเป็นหลาย service

**D-005 | Locked**

Decision: ล็อก demo domain เป็นห้องปฏิบัติการคอมพิวเตอร์ขนาดเล็กในมหาวิทยาลัย (ใช้ข้อมูลจำลอง)

Rationale: ทำให้ Data Definition, Wireframe, Seed Data และ Evaluation มีบริบทเดียวกัน และยังไม่แตะข้อมูลจริงที่อ่อนไหว

Impact: 06_Data_Definition, 07_UI_Flow_Wireframe, 04_Current_State ต้องสะท้อนบริบทนี้

# **3\. Added Decisions (02/04/2569)**

**D-006 | Locked**

Decision: ล็อก technology stack หลักเป็น Laravel 13 + PHP 8.3+, Livewire 4, Filament 5, Tailwind CSS v4+, MySQL local, Laravel local public disk, Pest เป็นแกนการทดสอบ และใช้ Laravel Dusk เฉพาะเมื่อ environment พร้อมโดยไม่กินเวลาเกิน scope

Rationale: ต้องใช้ stack ที่ build ได้เร็ว, มี ecosystem รองรับ, และไม่บังคับให้เสียเวลากับ infra เกินจำเป็น

Impact: 04_Current_State, 08_Test_and_Evidence_Plan, 09_Implementation_Foundation_Plan ต้องอัปเดตให้ตรงกัน

**D-007 | Locked**

Decision: ล็อก access strategy เป็น Admin/Supervisor ใช้ management surface เดียวกัน และ Staff ใช้ task-focused pages แยกจาก panel

Rationale: เร็วกว่า, แยก UX ของคนตั้งค่าออกจากคนปฏิบัติงาน, และเหมาะกับ deadline 15 วันมากกว่าการพยายามยัดทุก role เข้า panel เดียว

Impact: 07_UI_Flow_Wireframe และ 09_Implementation_Foundation_Plan ต้องสะท้อน strategy นี้

**D-008 | Locked**

Decision: ล็อก checklist run creation policy เป็น Staff เปิดหน้า checklist ของวันแล้วระบบสร้าง run ให้อัตโนมัติถ้ายังไม่มี โดยใช้ 1 run ต่อ 1 template ต่อ 1 วัน ต่อ 1 staff owner ใน MVP

Rationale: ตัด assignment workflow ที่ยังไม่จำเป็น และลดความซับซ้อนของ route, policy และ seed logic

Impact: 02_System_Spec, 06_Data_Definition, 07_UI_Flow_Wireframe, 09_Implementation_Foundation_Plan ต้องสะท้อน rule นี้

**D-009 | Locked**

Decision: ล็อก checklist submission model เป็นไม่มี draft state อย่างเป็นทางการใน v1; ใช้ `submitted_at` เป็นตัวบอกว่ารันถูก submit แล้วหรือยัง และทุก item ต้องถูกตอบก่อน submit

Rationale: ลดสถานะที่ต้องดูแล, ลด schema complexity และตรงกับเวลาพัฒนาแบบ solo dev

Impact: 02_System_Spec, 06_Data_Definition, 07_UI_Flow_Wireframe, 08_Test_and_Evidence_Plan ต้องสะท้อน rule นี้

**D-010 | Locked**

Decision: ล็อก incident attachment policy เป็น optional และใช้ Laravel local public disk เท่านั้น; ไม่ใช้ Supabase หรือ external storage ใน v1

Rationale: ตรงกับข้อจำกัดเรื่องเวลาและช่วยให้ feature path จบจริงโดยไม่เพิ่ม infra เกินจำเป็น

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 07_UI_Flow_Wireframe, 09_Implementation_Foundation_Plan ต้องสะท้อน rule นี้

**D-011 | Locked**

Decision: ล็อก incident workflow ใน v1 เป็น Create + Update Status + Resolve เท่านั้น; ไม่มี assign / reassign

Rationale: ลด state และ action ที่ไม่จำเป็นต่อการเดโม และกัน scope leak จากคำว่า command center เต็มรูปแบบ

Impact: 00_Project_Lock, 02_System_Spec, 07_UI_Flow_Wireframe, 09_Implementation_Foundation_Plan ต้องสะท้อนขอบเขตนี้

**D-012 | Locked**

Decision: ห้ามใช้คำอธิบายว่า production-ready system กับสถานะปัจจุบัน; คำอธิบายที่ถูกต้องคือ demo-ready MVP foundation

Rationale: เพื่อให้ narrative ตรงกับหลักฐานจริงและลดความเสี่ยงในการอ้างเกินสิ่งที่ build ได้

Impact: 00_Project_Lock, 01_Product_Brief, 03_Evaluation_Protocol, 09_Implementation_Foundation_Plan ต้องใช้ wording ให้สอดคล้องกัน

**D-013 | Locked**

Decision: ล็อก repo policy เป็น single repository / single Laravel app และยอมใช้ short-lived feature branches โดยไม่บังคับ CI ก่อน MVP

Rationale: ลด overhead ของ workflow และให้โฟกัสกับ end-to-end working path มากกว่าการตั้ง infra เกินตัว

Impact: 04_Current_State และ 09_Implementation_Foundation_Plan ต้องสะท้อนนโยบายนี้

# **4\. Added Decisions (03/04/2569)**

**D-014 | Locked**

Decision: ล็อก incident status permission ใน v1 เป็น Admin และ Supervisor เปลี่ยน status ได้ทั้งคู่ ส่วน Staff สร้าง incident ได้แต่ไม่มีสิทธิ์เปลี่ยน status

Rationale: ตัด conflict ระหว่าง System Spec กับ UI/Implementation docs, ลดการทำ policy ซ้ำซ้อนโดยไม่จำเป็น, และทำให้ management surface ของ Admin/Supervisor มีขอบเขตชัดเจน

Impact: 02_System_Spec, 04_Current_State, 07_UI_Flow_Wireframe, 08_Test_and_Evidence_Plan และ 09_Implementation_Foundation_Plan ต้องสะท้อน permission นี้ให้ตรงกัน

# **5\. Added Decisions (04/04/2569)**

**D-015 | Locked**

Decision: ปรับฐานข้อมูลสำหรับการพัฒนา local MVP จาก MySQL เป็น SQLite (Controlled pivot)

Rationale: เครื่อง local ไม่มี MySQL/XAMPP ติดตั้งอยู่ และโครงงานต้องการฐานข้อมูลแบบ standalone เพื่อให้การพัฒนาเดินหน้าสู่เดโมได้ฉับไวที่สุด การใช้ SQLite รองรับ natively โดย Laravel และตรงตามหลัก demo-first progress. โครงสร้างสถาปัตยกรรมยังคงเป็น modular monolith จบในที่เดียวเช่นเดิม

Impact: 04_Current_State และ 09_Implementation_Foundation_Plan ถูกปรับให้ระบุว่าใช้ SQLite เป็น Local Database แทน MySQL

# **6\. Added Decisions (05/04/2569)**

**D-016 | Locked**

Decision: ล็อก Daily Checklist Run singular resolution rule. `/checklists/runs/today` จะต้อง resolve template ที่ active เพียงอันเดียวเท่านั้น เพื่อลดความซับซ้อนของ flow ตาม UI แบบ singular. หากมี template ที่ active มากกว่า 1 อัน ระบบจะแสดงข้อความ error. การ demo จะคง template ปิดห้องให้เป็น inactive ไว้ชั่วคราวเพื่อให้มี 1 active template ใช้งานได้.

Rationale: ตัดปัญหาความคลุมเครือจากการมีหลาย template แข่งกันให้ staff เปิดทำ checklist ประจำวัน โดยไม่ซ้ำซ้อนกับ template selection UI ที่ไม่ได้วางแผนไว้ในกรอบ MVP 15 วัน

Impact: 02_System_Spec, 07_UI_Flow_Wireframe และ 11_Implementation_Task_List จะต้องอนุมาน rule ว่ามี 1 active template ตลอด flow ของ /checklists/runs/today. ปรับแก้ DatabaseSeeder ให้มี template แบบ active เพียง 1 อัน.

# **7\. Added Decisions (06/04/2569)**

**D-017 | Locked**

Decision: ล็อก `resolved_at` convention สำหรับ incident reopening ใน v1. เมื่อ incident ถูกเปลี่ยนสถานะเป็น `Resolved` ให้ set `resolved_at` เป็นเวลาปัจจุบัน; หากถูกเปลี่ยนกลับออกจาก `Resolved` ไปเป็น `Open` หรือ `In Progress` ให้ clear `resolved_at` กลับเป็น `null`

Rationale: เอกสารเดิมล็อกเพียงว่า resolved case ต้อง set timestamp แต่ยังไม่ล็อกพฤติกรรมตอน reopen. การ clear ค่ากลับเป็น null ทำให้ field นี้สะท้อนสถานะปัจจุบันตรงที่สุดและตรงกับ implementation ที่ถูกทดสอบแล้ว

Impact: 04_Current_State, 08_Test_and_Evidence_Plan, 09_Implementation_Foundation_Plan และ 11_Implementation_Task_List ต้องสะท้อน convention นี้ให้ตรงกับ implementation และ tests

# **8\. Added Decisions (11/04/2569)**

**D-018 | Locked**

Decision: ปิด foundation remediation และล็อก engineering baseline ใหม่ของ repository เป็น PHP 8.4 local/runtime baseline, SQLite local development profile, internal-account-only authentication, active-user enforcement, application-layer workflow orchestration สำหรับ core use cases, และ source-only repository policy ที่ไม่ track vendor-generated Filament assets

Rationale: baseline เดิมมี truth mismatch ระหว่าง docs, runtime, CI, auth policy, workflow placement และ repository artifacts ทำให้การพัฒนาต่อมีความเสี่ยงสูงต่อ drift และการแก้แบบเฉพาะหน้า

Impact: 04_Current_State, README, architecture boundary documentation, domain normalization references และ repository hygiene policy ต้องสะท้อน baseline นี้ให้ตรงกับ code ปัจจุบัน
