# **14_Spoken_Demo_Script**

## **Purpose**

script สั้นสำหรับพูดระหว่างเดโมสด โดยอ้างอิงระบบที่ freeze แล้วเท่านั้น

## **1. Opening (20–30 seconds)**

“โครงงานนี้คือ A-lite เป็นระบบเว็บสำหรับงานเช็กลิสต์ประจำวันและติดตาม incident สำหรับทีมขนาดเล็ก โดยใช้บริบทห้องปฏิบัติการคอมพิวเตอร์ในมหาวิทยาลัย จุดที่ต้องการแก้คือการทำ checklist แบบ manual และการรายงานปัญหาผ่านแชตที่ติดตามย้อนหลังยาก ระบบนี้รวม 3 ส่วนหลักคือ daily checklist, incident tracking, และ dashboard summary โดยใช้ข้อมูลจริงจากฐานข้อมูล ไม่ใช่ mock UI”

## **2. Staff Flow (45–60 seconds)**

“เริ่มจากบทบาท Staff ผม login ด้วย `operatorb@example.com` ระบบจะพามาที่ checklist ของวันนี้โดยตรง ที่หน้านี้ระบบสร้าง run ให้อัตโนมัติถ้ายังไม่มี จากนั้น Staff ตอบรายการตรวจเช็กและ submit ได้ในหน้าเดียว ถ้าพบปัญหา Staff สามารถไปที่หน้าแจ้ง incident กรอก title, category, severity, description และส่งได้ทันที โดย attachment เป็น optional”

## **3. Supervisor Flow (45–60 seconds)**

“ต่อไปเป็น Supervisor ผม login ด้วย `supervisor@example.com` แล้วเข้าหน้า incident list เพื่อดูรายการปัญหา จากนั้นเข้า incident detail เลือกอัปเดตสถานะเป็น In Progress หรือ Resolved ระบบจะบันทึกการเปลี่ยนสถานะลงฐานข้อมูลจริงและเพิ่ม activity timeline เพื่อ trace ว่าใครเปลี่ยนอะไรเมื่อไร”

## **4. Dashboard Flow (30–45 seconds)**

“หลังจากอัปเดต incident แล้ว ผมกลับมาที่ dashboard หน้านี้จะแสดง summary จากข้อมูลจริง เช่น checklist completion วันนี้, จำนวน incident ตามสถานะ, และ recent incidents เพื่อให้ฝ่าย management เห็นภาพรวมได้เร็ว”

## **5. Admin Flow (20–30 seconds)**

“สุดท้ายในบทบาท Admin ระบบยังมี template management สำหรับจัดการ checklist templates ซึ่งถูกแยกสิทธิ์ไว้เฉพาะ admin เท่านั้น”

## **6. Access Control / Proof Close (20–30 seconds)**

“เรื่องสำคัญอีกจุดคือ role-based access control: Staff เข้า dashboard หรือ incident management ไม่ได้ ขณะที่ Supervisor และ Admin เข้าหน้า management ได้ ซึ่งจุดนี้มีทั้ง implementation จริงและ automated tests รองรับ”

## **7. Closing (15–20 seconds)**

“สรุปคือระบบนี้ปิด happy path หลักได้ครบสำหรับ MVP: login ตามบทบาท, ทำ checklist, สร้าง incident, อัปเดตสถานะ, และดู dashboard summary พร้อมหลักฐานจาก test suite และ seeded demo data”
