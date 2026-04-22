**A-lite Foundation Documentation Set**

**00_Project_Lock_v1.1**  
เอกสารแม่สำหรับล็อกตัวตนของโครงงาน A-lite และหยุด scope drift

| Document ID | DOC-00-PL |
| :---- | :---- |
| **Project** | ระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย |
| **Version** | v1.1 |
| **Status** | Locked master direction |
| **Reference Date** | 02/04/2569 |

วัตถุประสงค์: เอกสารฉบับนี้ใช้เป็นฐานอ้างอิงต้นน้ำของหัวข้อ A-lite เพื่อกัน scope drift, ลดการตัดสินใจแบบเฉพาะหน้า และทำให้การคุยกับ AI / การลงมือพัฒนา / การเตรียมสอบยึดข้อมูลชุดเดียวกัน.

# **Document Control**

| Owner | ผู้พัฒนาโครงงาน |
| :---- | :---- |
| **Approver** | ผู้พัฒนา / ที่ปรึกษาเมื่อทบทวน |
| **Update Trigger** | เมื่อมีการเปลี่ยนโดเมน, baseline, stack หลัก, role หลัก, หรือเพิ่มฟีเจอร์ใหญ่ |
| **Single Source of Truth** | หากเอกสารอื่นขัดกับฉบับนี้ ให้ยึดฉบับนี้ก่อน |

# **1\. Final Project Definition**

Daily Operations Command Center เป็นเว็บแอปแบบ modular monolith สำหรับการจัดการงานประจำวันของห้องคอมหลายห้องในมหาวิทยาลัย โดยใน case study นี้มีอาจารย์ผู้รับผิดชอบดูแลภาพรวม, เจ้าหน้าที่หรือผู้ดูแลห้องติดตามสภาพการใช้งาน, และนักศึกษาที่เข้าเวรตรวจห้องตามรอบ ระบบช่วยให้การเปิดห้อง ระหว่างวัน ปิดห้อง การแจ้งเหตุผิดปกติ และการมองภาพรวมของหัวหน้าทีม อยู่ใน workflow เดียวที่ตรวจสอบย้อนหลังได้

# **2\. This System Is / Is Not**

| ประเด็น | เป็น | ไม่เป็น |
| :---: | ----- | ----- |
| Product Type | Internal daily-operations web app สำหรับทีมดูแลห้องคอม | แพลตฟอร์ม enterprise แบบครบวงจร |
| Core Value | รวม daily checklist + incident follow-up + supervisor workboard ในที่เดียว | แอป chat, ERP, ticketing suite, หรือ LMS |
| Target Team | ทีมปฏิบัติการขนาดเล็กที่ดูแลห้องคอมหลายห้องในมหาวิทยาลัยเดียว | องค์กรใหญ่หลายแผนกหลายสาขา |
| Scope v1 | single-organization, multi-room case study with lightweight internal governance | multi-site, approval, notification ขั้นสูง |

# **3\. Problem Statement**

* งานเปิดห้อง ระหว่างวัน และปิดห้องคอมมักกระจัดกระจายอยู่ในกระดาษ, แชต, หรือการจำเอาเอง ทำให้งานตกหล่นง่าย  
* เมื่อพบปัญหาอย่างเครื่องคอมพิวเตอร์, เครือข่าย, โปรเจกเตอร์, เครื่องพิมพ์, หรือสภาพห้อง มักไม่มีระบบบันทึกที่เป็นมาตรฐาน ทำให้ติดตามสถานะย้อนหลังยาก  
* ห้องหนึ่งอาจพร้อมใช้งาน แต่อีกห้องยังมีปัญหา ทำให้หัวหน้าทีมมองภาพรวมเชิงห้องจริงได้ยาก หากระบบรู้แค่ lane ของเวลาแต่ยังไม่รู้ว่าปัญหาเกิดที่ห้องไหน  
* หัวหน้าทีมมองภาพรวมของวันยากว่า lane ไหนทำครบหรือยัง, ห้องไหนมีปัญหาอะไร, และ incident ใดยังค้างอยู่  
* เครื่องมือระดับโลกมีอยู่จริง แต่หลายตัวใหญ่เกินความจำเป็นสำหรับการสาธิตเชิงวิชาการของทีมเล็ก

# **4\. Locked Contribution**

Contribution ของโครงงานนี้ไม่ใช่การสร้างระบบ enterprise ใหม่ แต่คือการออกแบบระบบงานขนาดเล็กที่ใช้งานได้จริงและตรวจสอบได้ โดยรวม lab opening checks, during-day checks, room closing checks, incident follow-up, และ supervisor workboard เข้าด้วยกันในเส้นทางงานที่ชัดและเดโมได้จริง สำหรับหลายห้องคอมในมหาวิทยาลัยเดียว

# **5\. Scope v1 (Must-Have)**

1. มีบัญชีผู้ใช้พื้นฐานอย่างน้อย 3 บทบาท: Admin, Supervisor, Staff  
2. สร้างและจัดการ Checklist Template ได้  
3. รัน checklist รายวันและบันทึกผลแต่ละข้อได้  
4. สร้าง incident พร้อมหมวด, ระดับความรุนแรง, รายละเอียด และหลักฐานแนบพื้นฐานแบบ optional ได้  
5. ติดตามสถานะ incident แบบ Open / In Progress / Resolved ได้  
6. แสดง dashboard workboard สำหรับหัวหน้าทีมที่เห็น checklist lane truth, incident pressure, และ recent operational context ได้

หมายเหตุ baseline ปัจจุบัน:

* daily checklist runtime รองรับ `one active template per scope` แล้ว โดยแต่ละ lane ของ opening / midday / closing มี live owner ของตัวเอง  
* `Checklist Scope` เป็น runtime dimension จริง ทั้งใน template governance, staff entry board, dashboard workboard, และ history surfaces  
* account lifecycle เป็น internal provisioning only: ใช้ 3 roles คือ `admin`, `supervisor`, และ `staff` โดยไม่รองรับ public sign-up หรือ multi-tenant model
* actor mapping ของ case study ถูกล็อกเป็น: `admin` = อาจารย์ผู้รับผิดชอบ / ผู้ได้รับมอบหมายดูแลระบบ, `supervisor` = lab boy / เจ้าหน้าที่แล็บ / ผู้ดูแลห้อง, `staff` = นักศึกษาที่เข้าเวรตรวจห้องตามรอบ
* current correction path ถูกล็อกเป็น **room-centered lab operations** สำหรับหลายห้องคอมในมหาวิทยาลัยเดียว โดย machine registry แบบเต็มยังไม่อยู่ใน scope ปัจจุบัน

# **6\. Out of Scope**

* Approval workflow, sign-off หลายชั้น, SLA ขั้นสูง, notification อัตโนมัติ  
* Mobile app แยก, realtime chat, AI assistant, predictive analytics  
* Multi-tenant / multi-branch / marketplace style management  
* Incident assignment / reassignment, checklist draft workflow, และ advanced analytics  
* Machine registry, asset CRUD, room-machine inventory management, และ machine lifecycle/history subsystem
* ระบบบัญชี, เอกสารทางกฎหมาย, สัญญา, หรือข้อมูลราชการ

# **7\. Engineering Principles**

* **Simple architecture:** เริ่มจาก modular monolith และหลีกเลี่ยงการแยกบริการโดยไม่จำเป็น  
* **Spec before implementation:** ทุกงานใหญ่ต้องอธิบาย objective, constraints, acceptance criteria ก่อนลงมือ  
* **Evidence over assumption:** ทุกข้ออ้างเรื่องใช้งานได้จริงต้องมี smoke test, screenshot, หรือ log รองรับ  
* **No scope drift:** การเพิ่มฟีเจอร์ใหญ่ต้องผ่าน Decision Log และสะท้อนกลับมาใน Project Lock  
* **Demo-ready over production claims:** ห้ามอ้างว่าเป็น production-ready หากยังไม่มีหลักฐานรองรับเกินระดับ MVP/demo
* **Identity before expansion:** ถ้าภาษา product, seeded demo, และ UI framing ไม่พูดเรื่องเดียวกัน ห้ามเปิด wave feature ใหม่ก่อน

# **8\. High-Level Roadmap**

| Phase | ผลลัพธ์หลัก |
| :---: | ----- |
| 0 | Foundation docs, data definition, wireframe, repo plan |
| 1 | Auth + core schema + checklist templates |
| 2 | Daily checklist execution flow |
| 3 | Incident creation and status tracking |
| 4 | Dashboard + evidence + evaluation + polish |

# **9\. Definition of Done**

* ผู้ใช้สร้าง checklist template และรัน checklist รายวันได้จริง  
* ผู้ใช้สร้าง incident และติดตามสถานะได้จริง  
* dashboard แสดง workboard ของวัน, incident ownership pressure, และ recent operational context ได้จริง  
* เส้นทางหลักมี smoke tests และหลักฐานพร้อมใช้ในการสอบ  
* เอกสารชุด foundation สอดคล้องกันและอ้างอิงข้ามกันได้  
* เอกสาร implementation ไม่อ้างเกินจริงว่าเป็น production-ready system
