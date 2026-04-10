# **09_Implementation_Foundation_Plan_v1.2**

## **1\. Purpose**

เอกสารนี้กำหนดแผนการพัฒนาเชิงเทคนิค (Implementation Plan) สำหรับระบบ  
**A-lite: Daily Checklist + Incident Tracking + Dashboard**  
เพื่อให้สามารถพัฒนาได้ทันภายในระยะเวลาโครงงาน, เดโมได้จริง, และไม่เสียเวลาไปกับ architecture ที่เกินตัว

---

## **2\. Final Technology Stack**

### **2.1 Backend**

* Laravel 13  
* PHP 8.4+ (ตาม dependency/runtime baseline ที่รันจริงใน WSL)

### **2.2 Frontend**

* Livewire 4  
* Filament 5  
* Tailwind CSS v4+

### **2.3 Database**

* SQLite (Local MVP Database Pivot)

### **2.4 Testing**

* Pest (required for feature/logic tests)  
* Laravel Dusk (optional only if environment setup stable enough)

### **2.5 File Storage**

* Laravel Local Storage (public disk only)

---

## **3\. Architecture Approach**

### **3.1 Architecture Style**

* Modular Monolith (Laravel Standard Structure)

### **3.2 Access / UI Strategy**

* Admin + Supervisor: ใช้ management surface เดียวกันสำหรับ dashboard, incident monitoring และงานจัดการข้อมูล  
* Staff: ใช้ task-focused pages ด้วย Laravel auth + Livewire สำหรับ checklist และ incident creation  
* ไม่สร้าง multi-panel แยกหลายอันใน MVP ถ้ายังไม่จำเป็น

### **3.3 Directory Structure**

app/  
├── Models/  
├── Http/Controllers/  
├── Livewire/  
├── Filament/  
├── Policies/  
├── Providers/

resources/views/  
routes/web.php  
database/migrations/  
database/seeders/  
tests/Feature/

### **3.4 Repo / Branch Policy**

* Single repository / single Laravel app  
* `main` = demo baseline  
* ใช้ short-lived feature branches ได้ถ้าจำเป็น  
* ไม่บังคับ CI ก่อน MVP; manual smoke test log ใช้ได้

### **3.5 Design Principles**

* Keep it simple (KISS)  
* Avoid over-engineering  
* Feature-first development  
* Incremental delivery  
* Demo-ready over production claims

---

## **4\. Core Modules**

### **4.1 Checklist Module**

* Checklist Template  
* Daily Checklist Run  
* Checklist Submission

### **4.2 Incident Module**

* Create Incident  
* Update Status  
* Resolve

### **4.3 Dashboard Module**

* Daily completion rate  
* Incident summary  
* Status overview

### **4.4 User Module**

* Roles: Admin / Supervisor / Staff  
* Basic authentication + role-based access control

### **4.5 Locked Supporting Rules**

* Staff เปิด checklist ของวันแล้วระบบ auto-create run ให้ถ้ายังไม่มี  
* ใช้ 1 run ต่อ 1 template ต่อ 1 วัน ต่อ 1 staff owner ใน MVP  
* ไม่มี checklist draft state ใน v1  
* Incident attachment เป็น optional และเก็บ local public disk เท่านั้น  
* ไม่มี incident assignment / reassignment ใน v1  
* Admin และ Supervisor เปลี่ยน incident status ได้ทั้งคู่; Staff สร้าง incident ได้แต่เปลี่ยน status ไม่ได้
* ถ้า incident ถูก reopen ออกจาก `Resolved`, `resolved_at` ต้องถูก clear กลับเป็น `null`

---

## **5\. Feature Mapping (Tech Usage)**

| Feature | Technology |
| ----- | ----- |
| Template CRUD (Admin) | Filament Resources |
| Incident monitoring / status update (Supervisor/Admin) | Livewire + Blade pages |
| Dashboard | Controller + Blade summary view |
| Staff Checklist | Livewire |
| Staff Incident Form | Livewire |
| Auth / Access Control | Laravel auth + Policies / middleware |
| File Upload | Laravel Storage |

---

## **6\. Testing Strategy**

### **6.1 Feature Tests (Pest)**

* Role access control  
* Checklist run auto-create  
* Checklist submit success  
* Incident creation  
* Incident status update allowed for Admin/Supervisor  
* Incident status update blocked for Staff

### **6.2 Browser / Demo Flow**

* Happy path: Login → Checklist → Submit → Incident → Dashboard update  
* ใช้ Dusk ถ้า environment พร้อม; ถ้าไม่พร้อมให้ใช้ screen recording + smoke test evidence แทน

### **6.3 Goal**

* Demonstrate working system in demo  
* Provide test evidence for evaluation  
* ป้องกันคำอ้างลอย ๆ ว่า “ทำงานได้” โดยไม่มีหลักฐาน

---

## **7\. Development Phases (15-Day Plan)**

### **Phase 1 (Day 1–3)**

* Setup Laravel project  
* Auth system + roles  
* Database schema + seed data

### **Phase 2 (Day 4–6)**

* Template CRUD (Admin)  
* Access control  
* Basic management UI

### **Phase 3 (Day 7–9)**

* Staff Checklist Run  
* Auto-create run logic  
* Submit flow

### **Phase 4 (Day 10–11)**

* Incident system  
* Status tracking  
* Activity timeline ขั้นต่ำ

### **Phase 5 (Day 12–13)**

* Dashboard summary cards/widgets  
* Role-based navigation polish

### **Phase 6 (Day 14)**

* Smoke tests  
* Bug fixing  
* Evidence collection

### **Phase 7 (Day 15)**

* Final polish  
* Demo preparation  
* Backup demo script

### **7.1 Execution Rule**

* การเดินงานจริงต้องอ้างอิง 11_Implementation_Task_List_v1.0 สำหรับลำดับ migration/model/page/test รายวัน  
* ห้ามข้ามไปทำ dashboard ก่อน login, template CRUD, checklist run และ incident flow จบแบบ end-to-end  
* ถ้าวันใด delay ให้เลื่อนงาน polish ออกก่อน ห้ามดัน feature ใหม่เข้ามาแทน

---

## **8\. Constraints & Assumptions**

* No external API integration  
* Single organization / single context system  
* Local deployment only  
* No sensitive real-world data  
* No mobile app in v1  
* No unsupported production-grade claims

---

## **9\. Future Ideas (Not Part of Current Build)**

* Multi-organization support  
* Notification system  
* Advanced analytics  
* Better mobile optimization

---

## **10\. Risk Management**

| Risk | Mitigation |
| ----- | ----- |
| Scope creep | Strict MVP scope + Decision Log |
| Learning / framework friction | Use official docs + focused AI assistance |
| Time shortage | Finish end-to-end happy path before polish |
| Bug issues | Early smoke tests + seed data-driven development |
| Browser automation setup delay | Treat Dusk as optional, not as MVP blocker |
| Role permission mismatch | เขียน access tests ตั้งแต่ phase แรกและเช็กทั้ง Admin/Supervisor/Staff |

---

## **11\. Final Notes**

ระบบนี้ถูกออกแบบให้:

* Build ได้จริง  
* Demo ได้จริง  
* Extend ได้จริงในระดับ foundation

ไม่ใช่ prototype ลอย ๆ  
และไม่ควรถูกอธิบายว่าเป็น production-ready system ในสถานะปัจจุบัน  
คำอธิบายที่ถูกต้องที่สุดคือ **demo-ready MVP foundation**
