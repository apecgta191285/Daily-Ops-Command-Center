# A-lite Daily Ops Command Center Presentation Outline

หมายเหตุ: Canva connector แจ้งว่า authentication token หมดอายุ จึงจัดทำไฟล์ PowerPoint สำรองจาก outline นี้ก่อน สามารถนำไฟล์ `.pptx` ไปเปิดนำเสนอทันที หรืออัปโหลดเข้า Canva หลัง reconnect ได้

## Slide 1: A-lite Daily Ops Command Center

- ระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย
- เว็บแอปพลิเคชันภายในสำหรับ checklist, incident และ dashboard
- ผู้ใช้งานหลัก: Staff, Supervisor และ Admin

## Slide 2: ที่มาและความสำคัญของปัญหา

- ห้องคอมพิวเตอร์เป็นพื้นที่ให้บริการด้านการเรียนการสอน การปฏิบัติการ และอุปกรณ์เทคโนโลยีสารสนเทศ
- ผู้ปฏิบัติงานต้องตรวจสอบความพร้อมของห้อง อุปกรณ์ เครือข่าย และสภาพแวดล้อมตามช่วงงาน
- ระบบงานเดิมใช้กระดาษ ไฟล์แยก หรือการสื่อสารผ่านช่องทางสนทนา ทำให้ข้อมูลกระจัดกระจาย
- การติดตามสถานะ การทบทวนย้อนหลัง และการตัดสินใจของผู้ดูแลจึงทำได้ล่าช้า

## Slide 3: ปัญหาระบบงานเดิม

- ใช้ภาพ `ch3-01-cause-effect-final.png`
- ปัญหาหลักคือข้อมูลตกหล่น ขาดศูนย์กลาง และติดตามย้อนหลังยาก
- สไลด์นี้เหมาะสำหรับเปิดเล่มประกอบหากต้องอธิบายรายละเอียดก้างปลา

## Slide 4: วัตถุประสงค์ของโครงงาน

- วิเคราะห์และออกแบบระบบจัดการงานปฏิบัติการประจำวัน
- พัฒนาเว็บแอปพลิเคชันที่รองรับ checklist, incident และ dashboard
- จัดเก็บข้อมูลให้ตรวจสอบย้อนหลังและใช้ประกอบการทบทวนงานได้
- ประเมินความเหมาะสมของระบบจากการทดสอบและความคิดเห็นของผู้เกี่ยวข้อง

## Slide 5: ขอบเขตโครงงานและบทบาทผู้ใช้

- Admin: จัดการผู้ใช้และแม่แบบรายการตรวจ
- Supervisor: ติดตามภาพรวม ตรวจสอบ incident และปรับสถานะ
- Staff: เลือกห้อง/ช่วงงาน ตรวจ checklist และแจ้งเหตุผิดปกติ
- ไม่รวม public signup, notification เต็มรูปแบบ, approval workflow, machine registry, analytics warehouse, mobile app แยก หรือ AI/copilot

## Slide 6: เครื่องมือและเทคโนโลยีที่ใช้

- PHP และ Laravel Framework
- Laravel Fortify
- Livewire และ Flux
- Tailwind CSS และ Vite
- ฐานข้อมูลเชิงสัมพันธ์และ Laravel migrations
- Composer, NPM, Laravel Pint และ Pest/Laravel Test

## Slide 7: ภาพรวมระบบงานใหม่

- ใช้ภาพ `ch3-02-system-flowchart-v2.png`
- ระบบรวม checklist, incident และ dashboard ไว้ในแหล่งข้อมูลเดียว
- Staff ทำ checklist และสร้าง incident
- Supervisor/Admin ติดตาม dashboard และอัปเดตสถานะ incident

## Slide 8: Context Diagram และผู้เกี่ยวข้อง

- ใช้ภาพ `ch3-03-context-diagram-v2.png`
- ระบบกลางคือ A-lite Daily Ops Command Center
- External entities มี Staff, Supervisor และ Admin เท่านั้น
- อธิบายข้อมูลเข้าและข้อมูลออกระดับสูง ไม่ลง subprocess ภายใน

## Slide 9: DFD Level 1

- ใช้ภาพ `ch3-04-dfd-level1-v3.png`
- กระบวนการหลัก 5 กระบวนการ ได้แก่ user management, template management, daily checklist, incident management และ dashboard/history
- หากเส้นข้อมูลแน่นเกินไปให้เปิดเล่มเอกสารประกอบ

## Slide 10: DFD Level 2 Workflow สำคัญ

- Process 3.0: ดำเนินการตรวจเช็กประจำวัน
- Process 4.0: จัดการเหตุผิดปกติ
- Process 5.0: ติดตามภาพรวมและทบทวนประวัติ
- สไลด์นี้ใช้สรุปภาพรวม และเปิดภาพ DFD Level 2 ในเล่มเมื่อกรรมการถามรายละเอียด

## Slide 11: Data Model

- ใช้ภาพ `ch3-08-erd-final.png`
- มี 8 entities หลัก ได้แก่ users, rooms, checklist_templates, checklist_items, checklist_runs, checklist_run_items, incidents และ incident_activities
- แบ่งความสัมพันธ์เป็นกลุ่ม checklist และกลุ่ม incident

## Slide 12: Data Dictionary

- สรุปว่าพจนานุกรมข้อมูลอธิบาย Attribute, Description, Type, Constraint และ Null
- กลุ่มตารางหลัก: users/rooms, checklist, incident
- ไม่ควรวาง data dictionary ทั้งหมดบนสไลด์ ให้เปิดเล่มเมื่อจำเป็นต้องดูราย attribute

## Slide 13: ผลการพัฒนาระบบที่ได้

- Login และ role-based access
- User/template management
- Daily checklist execution
- Incident create/update/history พร้อมไฟล์แนบหลักฐานประกอบเมื่อมี
- Dashboard overview
- Printable checklist recap และ incident summary สำหรับใช้เป็นหลักฐานประกอบการทบทวน

## Slide 14: สรุปผลและประโยชน์ที่ได้รับ

- รวมข้อมูลไว้ในศูนย์กลางเดียว
- ลดการพึ่งพากระดาษ ไฟล์แยก และการสื่อสารกระจัดกระจาย
- ทำให้บทบาท Staff, Supervisor และ Admin ชัดเจน
- ตรวจสอบย้อนหลังและใช้เป็นหลักฐานประกอบการทบทวนงานได้

## Slide 15: ข้อจำกัดและแนวทางพัฒนาต่อ

- พัฒนาโดยผู้พัฒนาคนเดียว จึงต้องควบคุมขอบเขต
- การทดสอบเน้น workflow หลักในสภาพแวดล้อมพัฒนา
- แนวทางพัฒนาต่อ: notification พื้นฐาน, การพิมพ์/ส่งออกสรุปรายงานตามช่วงเวลาให้สมบูรณ์ขึ้น, responsive mobile และชุดทดสอบเพิ่มเติม
