# **FE4 Feedback, Accessibility, and Responsive Polish**

## *DOC-54-FE4 | Frontend finishing pass for interaction clarity and small-screen resilience*

**Version v1.0 | Execution pack | วันที่อ้างอิง 17/04/2569**

---

# **1. Objective**

ปิด frontend wave รอบแรกให้ครบด้วยก้อน polish ที่คุ้มที่สุด:

* accessibility baseline ที่ผู้ใช้สัมผัสได้จริง
* feedback behavior ที่ชัดและไม่รบกวน
* responsive behavior สำหรับ table-heavy screens ที่ดีขึ้นบนจอแคบ

เป้าหมายคือทำให้ระบบที่ FE1-FE3 วางไว้ “ใช้ง่ายขึ้นจริง” ไม่ใช่แค่ดูดีขึ้นบน desktop เท่านั้น

---

# **2. Why This Phase Matters**

หลัง FE3 ระบบมี:

* token contract
* component primitives
* page composition ที่ชัดขึ้น

แต่ถ้าหยุดตรงนั้น จะยังมี gap สำคัญ:

* keyboard users ยังไม่มี skip link
* focus visibility ยังไม่สม่ำเสมอ
* table-heavy pages ยังอึดอัดบน mobile
* smoke coverage ยังไม่ยืนยัน accessibility baseline ใหม่

ดังนั้น FE4 คือ phase ที่ทำให้ frontend wave ปิดแบบ “ดูดีและใช้งานได้ดี” พร้อมกัน

---

# **3. Scope**

## **3.1 Included**

* เพิ่ม skip link ให้ app shell, auth shell, และ home
* เพิ่ม focus-visible baseline สำหรับ interactive controls
* เพิ่ม responsive table behavior สำหรับ dashboard, incident list, และ template list
* เพิ่ม smoke coverage สำหรับ accessibility baseline ที่เพิ่งเพิ่ม

## **3.2 Not Included**

* full WCAG remediation audit ทั้งระบบ
* dedicated mobile redesign ทุกหน้า
* skeleton/loading wave ใหม่
* interaction rewrite ระดับใหญ่

---

# **4. Implementation Summary**

## **4.1 Accessibility Baseline**

อัปเดต `resources/css/app/base.css` ให้มี:

* `ops-skip-link`
* focus-visible treatment สำหรับ links, buttons, pseudo-buttons, radio/checkbox

และอัปเดต layouts ให้มี:

* skip link ไปยัง `#main-content`
* `<main id="main-content">` เป็น landmark ชัดเจน

ไฟล์ที่แตะ:

* [base.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app/base.css)
* [sidebar.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/layouts/app/sidebar.blade.php)
* [simple.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/layouts/auth/simple.blade.php)
* [welcome.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/welcome.blade.php)

## **4.2 Responsive Table Polish**

เพิ่ม `ops-table-wrap` และ `ops-table--responsive` เพื่อให้ table-heavy surfaces stack เป็น card-like rows บนหน้าจอเล็ก

หน้าที่ใช้:

* [dashboard.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/dashboard.blade.php)
* [incidents/index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/management/incidents/index.blade.php)
* [templates/index.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/livewire/admin/checklist-templates/index.blade.php)

## **4.3 Browser Regression Coverage**

เพิ่ม smoke assertions ให้ยืนยันว่า:

* guest-facing home/login มี skip link จริง
* authenticated checklist flow render ผ่าน `main#main-content`

ไฟล์:

* [SmokeTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Browser/SmokeTest.php)

---

# **5. Expected Outcome**

หลัง FE4:

* frontend wave แรกปิดได้อย่างสมบูรณ์ขึ้น
* app shell และ auth shell มี keyboard entry ที่ชัดเจนขึ้น
* mobile/tablet users อ่าน data tables ง่ายขึ้น
* browser smoke มีหลักฐานรองรับ polish layer สำคัญ ไม่ใช่แค่ visual intent

---

# **6. Verification**

ต้องผ่าน:

* `composer lint:check`
* `php artisan test`
* `npm run build`
* `composer test:browser`

---

# **7. Exit Gate**

Phase FE4 ถือว่าเสร็จเมื่อ:

* skip links และ main landmarks ถูกเพิ่มใน shell หลัก
* responsive table contract ถูกใช้จริงกับหน้าหลักที่มีตาราง
* browser smoke ผ่านพร้อม assertions สำหรับ accessibility baseline ใหม่
* frontend wave FE1-FE4 ปิดได้โดยไม่เหลือ polish debt ก้อนใหญ่ที่ควรเก็บทันที

