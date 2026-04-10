# **11_Implementation_Task_List_v1.0**

## **1. Purpose**

เอกสารนี้เป็น execution plan สำหรับ 15 วันที่เหลือ โดยแตกจาก Project Lock, System Spec, Data Definition, UI Flow และ Test Plan ให้กลายเป็นลำดับลงมือทำจริงใน Laravel

เป้าหมายของเอกสารนี้ไม่ใช่เพิ่ม scope แต่คือกันการหลงทางระหว่าง build และทำให้ solo developer ตัดสินใจได้เร็วขึ้นในแต่ละวัน

## **2. Brutal Rule**

* ห้ามเริ่มจาก dashboard ก่อน feature หลักจบ  
* ห้ามทำ feature ใหม่ที่ไม่มีใน 00_Project_Lock_v1.1 และ 05_Decision_Log_v1.3  
* ถ้างานวันไหน delay ให้ตัด polish ก่อน ห้ามตัด data path หลัก  
* ทุกวันต้องได้ของที่เปิดดูหรือทดสอบได้จริง ไม่ใช่แค่ไฟล์เพิ่มขึ้น

## **3. Locked Build Order (ห้ามสลับมั่ว)**

1. Repo + auth + role access  
2. Core schema + seed data  
3. Admin template CRUD  
4. Staff daily checklist run + auto-create + submit  
5. Staff incident create  
6. Admin/Supervisor incident monitoring + status update  
7. Dashboard summary  
8. Smoke tests + evidence + demo path

## **4. Migration Order ที่ควรทำจริง**

1. `users` (เพิ่ม `role`, `is_active`)  
2. `checklist_templates`  
3. `checklist_items`  
4. `checklist_runs`  
5. `checklist_run_items`  
6. `incidents`  
7. `incident_activities`

### **4.1 Minimum columns ที่ห้ามลืม**

**users**
- name, email, password, role, is_active

**checklist_templates**
- title, description, scope, is_active

**checklist_items**
- checklist_template_id, title, description, sort_order, is_required

**checklist_runs**
- checklist_template_id, run_date, assigned_team_or_scope, created_by, submitted_at, submitted_by

**checklist_run_items**
- checklist_run_id, checklist_item_id, result, note, checked_by, checked_at

**incidents**
- title, category, severity, status, description, attachment_path, created_by, resolved_at

**incident_activities**
- incident_id, action_type, summary, actor_id, created_at

### **4.2 Constraints ที่ต้องทำตั้งแต่ migration**

* unique `(checklist_template_id, run_date, created_by)` บน checklist_runs  
* foreign keys ให้ครบ  
* `status`, `severity`, `category`, `result`, `role` ใช้ค่าที่ล็อกในเอกสาร  
* attachment path เป็น optional เท่านั้น

## **5. Model / Policy Order ที่ควรทำจริง**

### **5.1 Models**
1. User  
2. ChecklistTemplate  
3. ChecklistItem  
4. ChecklistRun  
5. ChecklistRunItem  
6. Incident  
7. IncidentActivity

### **5.2 Policies / Access**
1. Role middleware หรือ helper กลาง  
2. Admin-only: template CRUD  
3. Staff-only: daily checklist run, incident create  
4. Admin/Supervisor: incident list/detail/status update, dashboard

## **6. Page / Feature Order ที่ควรทำจริง**

### **6.1 Management side**
1. Login + post-login redirect  
2. Dashboard shell  
3. Checklist Template list/create/edit  
4. Incident list/detail/update status

### **6.2 Staff side**
1. `/checklists/runs/today`  
2. Checklist submit flow  
3. `/incidents/new`

## **7. Test Order ที่ควรทำจริง**

1. login/role redirect  
2. staff เปิด checklist run แล้ว auto-create สำเร็จ  
3. staff เปิด checklist run ซ้ำแล้วไม่ create ซ้ำ  
4. submit checklist สำเร็จและมี `submitted_at`  
5. create incident สำเร็จโดยไม่แนบไฟล์ก็ได้  
6. admin update incident status สำเร็จ  
7. supervisor update incident status สำเร็จ  
8. staff update incident status ไม่ได้  
9. dashboard เปิดได้แม้ข้อมูลน้อย

## **8. Day-by-Day Execution Plan**

### **Day 1 — Repo Foundation + Auth Start** **(✅ Completed)**
**Goal:** เปิดโปรเจกต์ Laravel และทำให้ login ได้

**Do:**
* สร้าง Laravel project  
* ตั้ง `.env`, database connection, app key  
* ติดตั้ง auth starter ที่ง่ายที่สุดที่เข้ากับ stack ที่เลือก  
* ให้ login/logout ใช้งานได้  
* วางโครง route redirect หลัง login ตาม role แบบขั้นต่ำ

**Done when:**
* เปิดหน้า login ได้  
* ล็อกอินแล้ว redirect ไปหน้าตาม role ได้อย่างน้อยแบบ placeholder

### **Day 2 — Roles + Core Schema**
**Goal:** ทำให้ฐานข้อมูลหลักพร้อม

**Do:**
* เพิ่ม `role`, `is_active` ใน users  
* สร้าง migrations ตามลำดับที่ล็อก  
* ใส่ foreign keys และ unique constraint ให้ครบ  
* รัน migrate ผ่าน

**Done when:**
* migrate fresh ผ่านโดยไม่มี error  
* schema ตรงกับ 06_Data_Definition_v1.2

### **Day 3 — Models + Seed Data + Access Skeleton**
**Goal:** มีข้อมูลจริงพอให้ build และ demo feature ต่อได้

**Do:**
* สร้าง models + relations  
* สร้าง seed users 4 คน, templates 2 ชุด, items 12 ข้อ, incidents 10 รายการ  
* ทำ middleware/policy skeleton สำหรับ 3 roles  
* สร้าง route groups management/staff แบบขั้นต่ำ

**Done when:**
* seed ขึ้นครบ  
* login ด้วยแต่ละ role แล้วโดน route ไปพื้นที่ถูกต้อง  
* staff เข้า template management ไม่ได้

### **Day 4 — Admin Template CRUD (List/Create)**
**Goal:** ปิด feature ฝั่ง admin ชิ้นแรก

**Do:**
* ทำหน้า list/create template  
* ทำ form สำหรับ title, description, scope, is_active  
* ทำ repeatable rows สำหรับ checklist items  
* validation ขั้นต่ำให้ครบ

**Done when:**
* admin สร้าง template ใหม่พร้อม items ได้จริง  
* ข้อมูลถูกบันทึกใน DB จริง

### **Day 5 — Admin Template CRUD (Edit/Polish)**
**Goal:** ทำ template CRUD ให้ usable

**Do:**
* เพิ่ม edit/update template และ item ordering  
* เช็ก access control admin-only  
* เก็บ screenshot หรือ smoke note ของ feature นี้

**Done when:**
* admin แก้ template เดิมได้  
* supervisor/staff เข้า CRUD นี้ไม่ได้

### **Day 6 — Staff Checklist Run Page Skeleton**
**Goal:** เปิดหน้า `/checklists/runs/today` และอ่าน template ได้

**Do:**
* ทำ Livewire page สำหรับ daily checklist  
* โหลด template ที่จะใช้วันนี้  
* วางฟอร์ม result + note ต่อ item  
* ยังไม่ต้อง submit ให้จบก็ได้ แต่ต้องแสดงข้อมูลครบ

**Done when:**
* staff เปิดหน้า checklist ของวันแล้วเห็นรายการครบ  
* UI ใช้งานได้ในหน้าเดียว

### **Day 7 — Auto-create Run Logic**
**Goal:** ปิด logic ที่ critical ที่สุดตัวหนึ่ง

**Do:**
* เมื่อเปิด `/checklists/runs/today` ให้ระบบค้นหา run ของวันนี้  
* ถ้ายังไม่มีให้ create checklist_run + checklist_run_items อัตโนมัติ  
* ถ้ามีแล้วห้าม create ซ้ำ  
* เขียน test สำหรับ create / no-duplicate

**Done when:**
* auto-create ทำงานจริง  
* เปิดซ้ำแล้วไม่เกิด duplicate run

### **Day 8 — Checklist Submit Flow**
**Goal:** ทำให้ daily checklist จบ end-to-end

**Do:**
* บันทึก Done/Not Done + note  
* enforce ว่าต้องตอบทุกข้อก่อน submit  
* เมื่อ submit สำเร็จให้ set `submitted_at`, `submitted_by`  
* เขียน test สำหรับ incomplete submit และ success submit

**Done when:**
* staff submit checklist ได้จริง  
* DB มี result ครบและมี `submitted_at`

### **Day 9 — Staff Incident Create**
**Goal:** ปิด flow แจ้งปัญหาฝั่ง staff

**Do:**
* ทำหน้า `/incidents/new`  
* ฟอร์ม title, category, severity, description, optional attachment  
* บันทึก incident ใหม่ + default status = Open  
* บันทึก incident activity แบบ `created`

**Done when:**
* staff สร้าง incident ได้จริง  
* ไม่แนบไฟล์ก็ submit ได้  
* แนบไฟล์แล้ว path ถูกเก็บบน local public disk

### **Day 10 — Incident Management (List/Detail)**
**Goal:** ทำ surface สำหรับ Admin/Supervisor

**Do:**
* ทำ incident list พร้อม filter status/category/severity  
* ทำ incident detail  
* แสดง timeline ขั้นต่ำ  
* ล็อก access ให้เฉพาะ Admin/Supervisor

**Done when:**
* Admin/Supervisor เปิด list/detail ได้  
* Staff เข้าไม่ได้

### **Day 11 — Incident Status Update**
**Goal:** ปิด permission conflict และ workflow หลักฝั่ง management

**Do:**
* ทำ action update status เป็น In Progress / Resolved  
* บันทึก incident activity แบบ `status_changed`  
* set `resolved_at` เมื่อ resolved  
* เขียน tests: admin allowed, supervisor allowed, staff forbidden

**Done when:**
* ทั้ง Admin และ Supervisor อัปเดต status ได้จริง  
* Staff ถูกบล็อกจริง

### **Day 12 — Dashboard Summary**
**Goal:** ทำหน้าสรุปที่ใช้ข้อมูลจริง

**Do:**
* completion rate วันนี้  
* incident counts ตาม status  
* latest incidents 5 รายการ  
* เช็ก empty states

**Done when:**
* dashboard ดึงข้อมูลจาก DB จริง  
* ไม่มีข้อมูลก็ไม่พัง

### **Day 13 — Navigation Polish + Regression Pass**
**Goal:** ทำให้ flow ไม่แตก

**Do:**
* เช็ก role-based navigation  
* เช็ก redirect หลัง login  
* เก็บ regression smoke test รอบแรก  
* แก้ bug หลักที่กระทบ demo path

**Done when:**
* Happy path หลักรันได้ตั้งแต่ login ถึง dashboard

### **Day 14 — Evidence + Demo Data + Backup Path**
**Goal:** เตรียมสอบ ไม่ใช่แค่เตรียมโค้ด

**Do:**
* เก็บ screenshots / screen recording  
* เตรียม seed reset หรือ demo dataset  
* รัน smoke tests แล้วบันทึกผล  
* เขียน demo script แบบสั้น

**Done when:**
* มี evidence bundle พร้อม  
* มีข้อมูลเดโมที่คุมได้

**Status Sync (06/04/2569):**
* มี test baseline สีเขียวแล้ว  
* Day 14 ควรโฟกัสที่ runbook, evidence checklist, demo reset routine และ backup path ไม่ใช่การเพิ่ม feature

### **Day 15 — Final Demo Rehearsal**
**Goal:** ลดความเสี่ยงวันสอบ

**Do:**
* ซ้อม demo path 2 รอบ  
* เตรียม backup script ถ้า dashboard หรือ upload มีปัญหา  
* freeze scope  
* แก้เฉพาะ bug ที่ทำให้ demo แตก

**Done when:**
* demo path หลักรันจบ  
* รู้ว่าจะพูดอะไร ถ้าถูกถามเรื่อง scope, constraints, และ future work

## **9. Non-Negotiable Definition of Build Progress**

ห้ามนับว่า “คืบหน้า” ถ้ายังเป็นแค่หนึ่งในสิ่งต่อไปนี้:
* มี migration แต่ยังไม่มี seed data ใช้งานจริง  
* มีหน้า UI แต่ยังไม่ต่อ DB  
* มี form แต่ยังไม่มี validation/access control  
* มี feature แต่ยังไม่มี smoke test หรือ manual evidence ขั้นต่ำ

## **10. Final Verdict**

เอกสารชุดหลักตอนนี้พร้อมพอสำหรับเริ่มพัฒนาแล้ว และ task list นี้คือสิ่งที่ทำให้คุณไม่ต้องเริ่มแบบสุ่ม. สำหรับเวลา 15 วัน วิธีที่ถูกคือ **build ตาม dependency และปิด happy path ให้เร็วที่สุด** ไม่ใช่เขียนทุกอย่างพร้อมกัน.

## **11. Implementation Status Sync (06/04/2569)**

สถานะที่ตรงกับ repo ปัจจุบัน:

* Day 1 ปิดแล้ว
* Day 2A schema foundation ปิดแล้ว
* Day 2B seed foundation ปิดแล้ว
* Day 3A access skeleton ปิดแล้ว
* Day 3B checklist template management ปิดแล้ว
* Day 4A daily checklist run ปิดแล้ว
* Day 4B staff incident create ปิดแล้ว
* Day 5A incident management list/detail/status update ปิดแล้ว
* Day 5B dashboard summary ปิดแล้ว
* full test suite baseline ปัจจุบันผ่านบน WSL แล้ว

งานถัดไปตาม execution order:

* Day 13 navigation polish + regression pass
* Day 14 evidence + demo data + backup path
* Day 15 final demo rehearsal
