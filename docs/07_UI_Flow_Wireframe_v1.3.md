# **UI Flow Wireframe**

## *A-lite Foundation Documentation Set*

**DOC-07-UF | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก**  
**Version v1.3 | UI planning support - locked low fidelity flow | วันที่อ้างอิง 03/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้ล็อก page inventory, navigation, page purpose, CTA และ low-fidelity wireframe ของ A-lite ก่อนลงมือทำ UI จริง.

# **1\. Demo UI Context ที่ล็อก**

บริบทที่ใช้วาง flow คือ “ห้องปฏิบัติการคอมพิวเตอร์ขนาดเล็กในมหาวิทยาลัย (ใช้ข้อมูลจำลอง ไม่ผูกกับหน่วยงานจริง)” และทุกหน้าต้องรองรับทั้งการเดโมบนเดสก์ท็อปและการเปิดดูบนจอแคบโดยไม่ต้องทำ mobile app แยก.

# **2\. Access Strategy ที่ล็อก**

* Admin และ Supervisor ใช้ management surface เดียวกันสำหรับ dashboard, incident monitoring และงานจัดการข้อมูล  
* Admin และ Supervisor เปลี่ยนสถานะ incident ได้ทั้งคู่ใน v1  
* Staff ใช้ task-focused pages สำหรับ checklist และการแจ้ง incident  
* Staff ไม่มีสิทธิ์เข้า incident management surface หรืออัปเดต status incident  
* เหตุผล: เร็วกว่า, UX ชัดกว่า, และเหมาะกับ deadline 15 วันมากกว่าการพยายามยัดทุก role เข้า panel เดียว

# **3\. Sitemap / Page Inventory**

| Page | ผู้ใช้หลัก | Page Purpose | Primary CTA |
| ----- | ----- | ----- | ----- |
| Login | ทุกบทบาท | เข้าสู่ระบบและ route ไปยังหน้าที่เหมาะกับบทบาท | Sign in |
| Dashboard | Admin / Supervisor | เห็นภาพรวม checklist และ incidents ของวัน | View details |
| Checklist Templates | Admin | สร้าง/แก้ไขแม่แบบ checklist | Create template |
| Daily Checklist Run | Staff | ทำ checklist ของวันที่ระบบเตรียมให้ / สร้างให้อัตโนมัติ | Submit checklist |
| Incident Create | Staff | แจ้งเหตุผิดปกติแบบมีโครงสร้าง | Create incident |
| Incident List / Detail | Supervisor / Admin | ดูรายการปัญหา กรอง และอัปเดตสถานะ | Update status |

# **4\. Route Structure ที่แนะนำ**

* /login  
* /dashboard  
* /templates  
* /templates/new  
* /checklists/runs/today  
* /incidents/new  
* /incidents  
* /incidents/{id}

# **5\. Navigation Rules**

* Staff ควรเห็นทางลัดไปที่ “Checklist ของวันนี้” และ “แจ้ง Incident” ทันทีหลัง login  
* Supervisor ควรเห็นทางลัดไปที่ “Incident List” และ “Dashboard”  
* Admin ควรเห็นทางลัดไปที่ “Checklist Templates”, “Incident List” และ “Dashboard”  
* ห้ามให้ dashboard เป็นศูนย์รวมทุกอย่างใน MVP; หน้านี้ต้องเป็น summary + entry points เท่านั้น

# **6\. Primary Flows ที่ล็อก**

## **6.1 Admin Flow**

Login → Dashboard → Checklist Templates → Create/Edit Template → Save

ผลลัพธ์ที่คาดหวัง: admin สามารถสร้าง template และ checklist item ได้โดยไม่ต้องเจอหน้าจอที่มีหลายงานหลักปนกัน

## **6.2 Staff Checklist Flow**

Login → เปิด `/checklists/runs/today` → ถ้ายังไม่มี run ระบบสร้างให้อัตโนมัติ → ติ๊ก Done/Not Done → ใส่ note ถ้าจำเป็น → Submit

ผลลัพธ์ที่คาดหวัง: ผู้ใช้ทำ checklist ได้จบในหน้าเดียวหรือ flow เดียว โดยไม่ต้องกระโดดหลายหน้า

## **6.3 Staff Incident Flow**

ระหว่างทำ checklist หรือจากเมนูหลัก → Incident Create → กรอก title, category, severity, description, optional attachment → Save

ผลลัพธ์ที่คาดหวัง: การแจ้งปัญหาทำได้เร็วและเก็บข้อมูลขั้นต่ำครบ

## **6.4 Management Incident Flow (Admin / Supervisor)**

Login → Dashboard หรือ Incident List → Incident Detail → Update Status

ผลลัพธ์ที่คาดหวัง: ผู้ใช้ฝั่ง management ตรวจ incident ค้างและเปลี่ยนสถานะได้ชัดเจนโดยไม่ต้องแยก flow คนละแบบสำหรับ Admin กับ Supervisor

# **7\. Page Blueprint รายหน้า**

## **7.1 Login**

| องค์ประกอบ | รายละเอียดที่ล็อก |
| ----- | ----- |
| Header | ชื่อระบบ + subtitle สั้น |
| Body | ฟอร์ม email/password |
| CTA หลัก | Sign in |
| UX Note | ถ้า login สำเร็จให้ route ตามบทบาท |

## **7.2 Dashboard**

| องค์ประกอบ | รายละเอียดที่ล็อก |
| ----- | ----- |
| Summary Cards | Checklist completion วันนี้ / Open incidents / In-progress / Resolved |
| Secondary Block | รายการ incident ล่าสุด 5 รายการ |
| CTA หลัก | View checklist / View incidents |
| UX Note | ห้ามยัด chart ใหญ่เกินจำเป็นใน MVP |

## **7.3 Checklist Templates**

| องค์ประกอบ | รายละเอียดที่ล็อก |
| ----- | ----- |
| List View | รายชื่อ template ที่มีอยู่ |
| Form Area | ชื่อ template + description + item list |
| CTA หลัก | Save template |
| UX Note | ใช้ add item แบบ repeatable row ง่าย ๆ |

## **7.4 Daily Checklist Run**

| องค์ประกอบ | รายละเอียดที่ล็อก |
| ----- | ----- |
| Header | ชื่อ template + วันที่ |
| Body | รายการ checklist items แบบติ๊ก Done/Not Done พร้อม note |
| CTA หลัก | Submit checklist |
| UX Note | ถ้าพบปัญหาให้มีปุ่มลัด “แจ้ง Incident”; v1 ไม่มี Save Draft; ก่อน submit ต้องตอบทุกข้อ |

## **7.5 Incident Create**

| องค์ประกอบ | รายละเอียดที่ล็อก |
| ----- | ----- |
| Form Fields | title, category, severity, description, optional attachment |
| CTA หลัก | Create incident |
| Validation | field สำคัญห้ามว่าง |
| UX Note | ควรทำเป็นฟอร์มสั้น ไม่ใช่หน้ารายงานยาว |

## **7.6 Incident List / Detail**

| องค์ประกอบ | รายละเอียดที่ล็อก |
| ----- | ----- |
| List | filter by status/category/severity แบบง่าย |
| Detail | ข้อมูล incident + activity timeline แบบง่าย |
| CTA หลัก | Update status |
| UX Note | timeline ใช้ข้อความสั้น ๆ เช่น created / status changed; เฉพาะ Admin และ Supervisor เท่านั้นที่เข้าหน้านี้และเปลี่ยน status ได้ |

# **8\. Low-Fidelity Sketch (ข้อความแทนภาพ)**

**Dashboard**

[Top nav]  
[Card: Completion วันนี้] [Card: Open] [Card: In Progress] [Card: Resolved]  
[Section: วันนี้ต้องทำอะไรต่อ] -> ปุ่มไป Checklist ของวันนี้  
[Section: Incident ล่าสุด] -> ตารางสั้น 5 แถว

**Daily Checklist Run**

[Header: ชื่อ template + วันที่]  
[Item 1] ( ) Done  ( ) Not Done   [Note]  
[Item 2] ( ) Done  ( ) Not Done   [Note]  
...  
[Create Incident] [Submit]

**Incident Detail**

[Title / Category / Severity / Status]  
[Description]  
[Attachment preview optional]  
[Timeline]  
- created by Staff A at 09:10  
- status changed to In Progress at 09:30  
[Update status]

# **9\. UI Rules ที่ต้องยึด**

* ทุกหน้าต้องมีงานหลักเพียง 1 งานที่เด่นที่สุด  
* ฟอร์มต้องใช้ label ชัด, error message ชัด และไม่ซ่อน field สำคัญ  
* หน้า Checklist Run ต้องเร็วและไม่บังคับให้ผู้ใช้เปิดหลาย modal ซ้อนกัน  
* หน้า Incident List ต้องอ่านเร็วและกรองได้อย่างน้อยตาม status และ category  
* Dashboard ต้องเป็นหน้าสรุป ไม่ใช่หน้าปฏิบัติงานแทน checklist หรือ incident form  
* สิทธิ์ของ Admin กับ Supervisor ใน incident status update ต้องเหมือนกันใน v1

# **10\. Ready-to-Build Checklist**

* Page inventory ถูกล็อกแล้ว  
* Route structure ถูกกำหนดแล้ว  
* Primary flows ถูกกำหนดแล้ว  
* Access strategy ระหว่าง management surface กับ task-focused pages ถูกกำหนดแล้ว  
* Permission เรื่อง incident status update ถูกล็อกแล้ว  
* Page purpose และ CTA หลักของทุกหน้าถูกกำหนดแล้ว  
* เอกสารนี้พร้อมใช้แตกงาน UI, component list และ page implementation plan
