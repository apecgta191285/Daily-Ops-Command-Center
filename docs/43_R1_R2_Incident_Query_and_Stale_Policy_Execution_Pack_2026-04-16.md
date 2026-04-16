# **R1/R2 Incident Query and Stale Policy Execution Pack**

## *Execution pack for centralized stale-threshold ownership and incident-list query extraction*

**DOC-43-R1R2 | Codebase refinement execution reference | วันที่อ้างอิง 16/04/2569**

---

## **1. Objective**

เก็บ codebase refinement ที่คุ้มที่สุดรอบถัดไปหลัง `N1-N4` โดยไม่เปลี่ยน product scope:

* รวม `stale incident threshold` ให้เหลือ source of truth เดียว
* แยก incident list query ออกจาก Livewire component เพื่อให้ filter โตต่อได้โดยไม่ทำให้ component กลายเป็น God-object

---

## **2. Scope**

### **In scope**

* เพิ่ม owner กลางสำหรับ stale policy
* เพิ่ม query object สำหรับ incident list
* ปรับ dashboard, incident list, และ incident detail ให้ใช้ policy เดียวกัน
* เพิ่ม regression coverage สำหรับ policy และ query extraction

### **Out of scope**

* เปลี่ยน threshold จาก 2 วันเป็นค่าใหม่
* เพิ่ม filter family ใหม่
* เพิ่ม repository pattern หรือ interface layer

---

## **3. Decisions**

### **D1 — Stale policy owner**

ใช้ class เดียวเป็น owner ของ:

* threshold days
* cutoff calculation
* stale truth สำหรับ unresolved incidents
* query helper สำหรับ stale unresolved set

### **D2 — Query extraction style**

ใช้ application query แบบเบา (`ListIncidents`) + data object (`IncidentListFilters`)

ไม่เพิ่ม repository abstraction เพราะยังไม่คุ้มกับ scope ปัจจุบัน

---

## **4. Acceptance Criteria**

* dashboard/list/detail ใช้ stale threshold จาก owner เดียว
* `Index` Livewire component ไม่ build incident Eloquent query ตรง ๆ อีก
* feature behavior เดิมยังเท่าเดิม
* tests ผ่านทั้ง feature suite และ browser smoke

---

## **5. Verification**

* `composer lint:check`
* `php artisan test`
* `composer test:browser`
