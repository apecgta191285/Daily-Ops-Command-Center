**A-lite Foundation Documentation Set**

**08_Test_and_Evidence_Plan_v1.2**  
แผนทดสอบขั้นต่ำและหลักฐานที่ต้องมีสำหรับเดโมและการสอบ

| Document ID | DOC-08-TE |
| :---- | :---- |
| **Project** | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก |
| **Version** | v1.2 |
| **Status** | QA support - implementation updated |
| **Reference Date** | 06/04/2569 |

วัตถุประสงค์: เอกสารฉบับนี้ใช้เป็นฐานอ้างอิงต้นน้ำของหัวข้อ A-lite เพื่อกัน scope drift, ลดการตัดสินใจแบบเฉพาะหน้า และทำให้การคุยกับ AI / การลงมือพัฒนา / การเตรียมสอบยึดข้อมูลชุดเดียวกัน.

# **Document Control**

| Goal | ให้ทุกคำอ้างเรื่องใช้งานได้จริงมีหลักฐานรองรับ |
| :---- | :---- |
| **Testing Style** | smoke tests + manual verification + demo evidence |
| **Use With** | 03_Evaluation_Protocol_v1.1 และ 11_Implementation_Task_List_v1.0 |

# **1\. Smoke Test Set**

| Test ID | Scenario | ผ่านเมื่อ |
| :---: | ----- | ----- |
| S-01 | Login ตามบทบาท | เข้าสู่ dashboard หรือหน้าที่ได้รับอนุญาตได้ |
| S-02 | Create checklist template | template ใหม่ถูกบันทึกและแสดงในรายการ |
| S-03 | Open daily checklist | เปิด checklist ของวันแล้วระบบสร้าง run ให้อัตโนมัติได้ถ้ายังไม่มี |
| S-04 | Submit daily checklist | ผลแต่ละข้อถูกบันทึกกลับฐานข้อมูลและมี submitted_at |
| S-05 | Create incident | incident ใหม่ปรากฏใน incident list โดยไม่ต้องมี attachment ก็ได้ |
| S-06 | Update incident status (Admin/Supervisor) | Admin และ Supervisor เปลี่ยน status ได้จริง และมี activity trace |
| S-07 | Block forbidden status update (Staff) | Staff พยายามเปลี่ยน status แล้วถูกปฏิเสธอย่างถูกต้อง |
| S-08 | Open dashboard | cards/widgets ใช้ข้อมูลจริงและไม่พังเมื่อข้อมูลน้อย |

# **1.1 Current Implemented Proof Status**

* S-01 ถึง S-08 มี implementation และ automated feature tests ครบใน repo baseline ปัจจุบัน  
* WSL baseline ถูกตรวจแล้วว่าใช้งานได้จริงกับ native PHP/Composer/Node และ full test suite ผ่าน  
* dashboard ปัจจุบันใช้ summary cards + recent incidents list จากฐานข้อมูลจริง ยังไม่มี advanced analytics ตาม scope lock

# **2\. Evidence Bundle for Demo/Exam**

* screenshot ของ flow หลักทุก flow  
* screen recording ความยาวสั้นสำหรับ demo run-through  
* seed data snapshot หรือรายการ sample records ที่ใช้เดโม  
* ผล smoke tests พร้อมวันที่รัน  
* evaluation sheets และ feedback summary  
* หลักฐาน role-based access อย่างน้อย 2 ชุด (เช่น staff เข้า template management ไม่ได้, staff เปลี่ยน status incident ไม่ได้)

# **2.1 Current Day 14 Evidence Checklist**

* screenshot: Admin dashboard ที่แสดง checklist completion + incident summary จากข้อมูลจริง  
* screenshot: Staff หน้า `/checklists/runs/today` ทั้งสถานะ submitted และ non-submitted path  
* screenshot: Staff หน้า `/incidents/new` พร้อม field ที่ล็อกครบ  
* screenshot: Admin/Supervisor หน้า `/incidents` และ `/incidents/{incident}` พร้อม activity timeline  
* screenshot: access control proof อย่างน้อย 2 จุด เช่น Staff เข้า `/dashboard` ไม่ได้ และ Staff เข้า `/incidents` ไม่ได้  
* แนบผล `php artisan test` ล่าสุดพร้อมวันที่รันในหลักฐานการสอบ  
* จด demo accounts ที่ใช้จริงจาก DatabaseSeeder: `admin@example.com`, `supervisor@example.com`, `operatora@example.com`, `operatorb@example.com` (password = `password`)

# **3\. Demo Path**

1. แสดง login และ route ตามบทบาท  
2. สร้างหรือเปิด checklist template ตัวอย่าง  
3. เปิด checklist run ของวันและ submit ให้เสร็จ  
4. สร้าง incident จากกรณีผิดปกติ  
5. เปิด incident list แล้วอัปเดตสถานะ  
6. กลับมาดู dashboard ที่สะท้อนข้อมูลล่าสุด

# **3.1 Demo Reset / Readiness Routine**

ใช้ routine นี้ก่อนเดโมหรือก่อนบันทึกหลักฐาน:

1. ยืนยันว่าใช้ SQLite local database ตาม `.env`
2. รัน `php artisan migrate:fresh --seed`
3. รัน `php artisan test`
4. login ด้วย account แต่ละ role เพื่อเช็ก route landing หลัก
5. ใช้ seeded baseline เป็นจุดเริ่มเดโม:
   * Operator A มี checklist run ที่ submit แล้ว 1 ชุด
   * Operator B ยังมี checklist run ที่ยังไม่ submit 1 ชุด
   * incidents ตัวอย่างมีครบสถานะ Open / In Progress / Resolved

หมายเหตุ: routine นี้เป็น reset path สำหรับ Day 14/15 เท่านั้น ไม่ใช่คำสั่งที่ต้องรันระหว่าง feature development ทุกครั้ง

# **3.2 Smoke-Test-to-Demo Mapping**

* S-01 → login แล้วเห็น landing page ตาม role
* S-03 + S-04 → Staff checklist flow
* S-05 → Staff incident create
* S-06 + S-07 → management incident status update และ forbidden proof
* S-08 → dashboard summary หลังข้อมูลถูกแก้จริง

# **4\. QA Policy**

* ทุกครั้งที่ feature หลักเสร็จ ต้องอัปเดต Current State และเก็บหลักฐานขั้นต่ำ  
* ห้ามนับว่า feature เสร็จเพียงเพราะหน้า UI เปิดได้ แต่ยังไม่มี data path จริง  
* ถ้า demo path แตกแม้ 1 จุด ให้ถือว่ายังไม่พร้อมสำหรับการสอบ  
* browser automation เป็นตัวเสริม; ถ้า environment ไม่เสถียร ให้เก็บ manual evidence ให้ครบก่อน  
* role-based permission ต้องถูกทดสอบทั้งด้าน "อนุญาต" และ "ห้าม" ไม่ใช่เช็กแค่ happy path
* หาก incident ถูก reopen ออกจาก `Resolved` ใน v1, หลักฐานทดสอบต้องยืนยันว่า `resolved_at` ถูก clear กลับเป็น `null`
* ถ้าจำเป็นต้อง reset demo data ให้ยึด `migrate:fresh --seed` + `php artisan test` เป็น baseline recovery path เดียวกันทุกครั้ง
