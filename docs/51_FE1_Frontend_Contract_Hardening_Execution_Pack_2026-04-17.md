# **FE1 Frontend Contract Hardening Execution Pack**

## *DOC-51-FE1 | Execution pack for frontend token hardening and interaction baseline*

**Version v1.0 | Execution reference | วันที่อ้างอิง 17/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้บันทึกงานรอบ `FE1` ของ frontend engineering wave โดยเน้นปิด contract gaps ที่กระทบทั้ง maintainability และ perceived quality ก่อนเริ่ม redesign surface ใหญ่ใน wave ถัดไป

---

# **1. FE1 Objective**

ทำให้ frontend contract มี owner ชัดขึ้นใน 4 เรื่อง:

* design token completeness
* hardcoded visual residue reduction
* motion / feedback baseline
* app-owned lightweight interaction behavior

---

# **2. Work Completed**

## **2.1 Token hardening**

เพิ่ม token สำคัญใน [tokens.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app/tokens.css):

* `--app-surface-subtle`
* action danger token family
* brand token family
* shadow scale
* radius scale
* motion / easing / duration scale

ผลคือ view layer ไม่ต้องพึ่ง hardcoded fallback สำคัญแบบเดิม และ frontend มีฐานสำหรับขยาย visual language ต่อได้

## **2.2 Motion baseline**

เพิ่ม [motion.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app/motion.css) และ import ผ่าน [app.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app.css)

สิ่งที่เพิ่ม:

* `ops-fade-up`
* `ops-skeleton`
* dismiss transition สำหรับ alerts
* reduced-motion baseline

นี่เป็น motion system ขั้นต้นที่ยังคุม complexity ได้และไม่หลุด scope A-lite

## **2.3 Residue reduction**

เก็บ hardcoded visual residue สำคัญใน:

* [ops.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app/ops.css)
* [settings.css](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/css/app/settings.css)
* [welcome.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/welcome.blade.php)
* [app-logo.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/components/app-logo.blade.php)
* [simple.blade.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/views/layouts/auth/simple.blade.php)

โดยเฉพาะ:

* `blue-*` brand residue
* `#f8fafc`
* `#f6f8fb`
* ad-hoc danger button styling

## **2.4 Alert interaction baseline**

เพิ่ม app-owned interaction layer ที่ [app.js](/home/home_pc/projects/Daily%20Ops%20Command%20Center/resources/js/app.js)

สิ่งที่เพิ่ม:

* dismissible alerts
* optional auto-dismiss
* re-bind after Livewire navigation

และทำให้ success/status alerts บางจุดหลักรองรับ:

* `data-alert`
* `data-auto-dismiss`
* close button
* `role="status"` / `aria-live="polite"`

---

# **3. Engineering Decision**

## **3.1 สิ่งที่ FE1 จงใจยังไม่ทำ**

เพื่อกัน scope drift:

* ยังไม่ redesign dashboard/checklist/template surfaces ครั้งใหญ่
* ยังไม่เพิ่ม chart library
* ยังไม่ทำ toast framework เต็มรูปแบบ
* ยังไม่ทำ JS-heavy interaction system
* ยังไม่ย้าย settings surface architecture

## **3.2 เหตุผล**

FE1 ต้องเป็น “foundation for frontend wave” ไม่ใช่ cosmetic rewrite  
ดังนั้นสิ่งที่ทำใน phase นี้ต้อง:

* ขยายต่อได้
* ลด debt ได้จริง
* ไม่สร้าง maintenance burden ใหม่เกินจำเป็น

---

# **4. Acceptance Criteria**

เกณฑ์ที่ถือว่าผ่านใน FE1:

* token ที่ view ใช้จริงต้องมี owner ใน token layer
* hardcoded brand residue หลักต้องลดลง
* alert/message system ต้อง dismiss ได้อย่างน้อยใน flow สำคัญ
* motion baseline ต้องมีจริงและรองรับ reduced-motion
* build/test/lint ต้องผ่าน

---

# **5. Resulting Baseline**

หลัง FE1:

* frontend มี token contract ที่เข้มขึ้น
* shell/auth/ops/pages หลักใช้ visual vocabulary ที่สอดคล้องกันขึ้น
* frontend มี motion and feedback baseline จริงแล้ว
* surface redesign รอบถัดไปสามารถทำบนฐานที่สะอาดกว่าเดิมได้

---

# **6. Next Recommended Step**

ขั้นที่ถูกต้องที่สุดถัดไปคือ:

## **FE2 — Component Language Expansion**

เพื่อเพิ่ม primitive ที่ frontend ยังขาด เช่น:

* stat cards
* empty states
* skeletons
* chips
* timeline shell
* denser feedback blocks

โดยใช้ FE1 เป็นฐานร่วม

