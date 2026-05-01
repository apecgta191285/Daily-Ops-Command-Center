3.5 คำอธิบายการประมวลผลข้อมูล (Process Description)

จากแผนภาพกระแสข้อมูล สามารถนำมาบรรยายภาพรวมของระบบโดยใช้คำอธิบายการประมวลผลข้อมูลได้ดังตาราง 3.1 - 3.5

ตาราง 3.1 Process Description ของ Process 1 จัดการผู้ใช้และสิทธิ์

Process 1 : จัดการผู้ใช้และสิทธิ์

Description : เป็นกระบวนการที่ผู้ดูแลระบบใช้ในการจัดการข้อมูลบัญชีผู้ใช้ บทบาท และสถานะการใช้งานของผู้ใช้ภายในระบบ เพื่อให้ผู้ใช้แต่ละกลุ่มสามารถเข้าถึงการทำงานที่สอดคล้องกับสิทธิ์ของตน

Input
- ข้อมูลจัดการผู้ใช้จาก Admin ได้แก่ ชื่อผู้ใช้ อีเมล บทบาท และสถานะการใช้งาน
- D1 ข้อมูลผู้ใช้ ได้แก่ id, name, email, role, is_active

Output
- ผลการจัดการผู้ใช้กลับไปยัง Admin
- ข้อมูลผู้ใช้ที่ถูกสร้างหรือปรับปรุงใน D1 ข้อมูลผู้ใช้ ได้แก่ id, name, email, role, is_active

Actor : Admin

Task :
1. รับข้อมูลการจัดการผู้ใช้จาก Admin
2. ตรวจสอบข้อมูลบัญชีผู้ใช้ บทบาท และสถานะการใช้งานที่เกี่ยวข้อง
3. บันทึกหรือปรับปรุงข้อมูลผู้ใช้ใน D1 ข้อมูลผู้ใช้
4. ส่งผลการจัดการผู้ใช้กลับให้ Admin

ตาราง 3.2 Process Description ของ Process 2 จัดการแม่แบบรายการตรวจ

Process 2 : จัดการแม่แบบรายการตรวจ

Description : เป็นกระบวนการที่ผู้ดูแลระบบใช้ในการจัดการแม่แบบ checklist และรายการตรวจที่ใช้เป็นต้นแบบสำหรับการตรวจเช็กประจำวันของระบบ

Input
- ข้อมูลแม่แบบรายการตรวจจาก Admin ได้แก่ รายละเอียดแม่แบบ ขอบเขตงาน สถานะการใช้งาน และรายการตรวจภายใต้แม่แบบ
- D3 ข้อมูลแม่แบบรายการตรวจ ได้แก่ checklist_templates.id, title, description, scope, is_active และ checklist_items.id, checklist_template_id, title, group_label, sort_order, is_required

Output
- ผลการจัดการแม่แบบกลับไปยัง Admin
- ข้อมูลแม่แบบ checklist และรายการตรวจที่ถูกสร้างหรือปรับปรุงใน D3 ข้อมูลแม่แบบรายการตรวจ ได้แก่ checklist_templates.id, title, description, scope, is_active และ checklist_items.id, checklist_template_id, title, group_label, sort_order, is_required

Actor : Admin

Task :
1. รับข้อมูลการสร้างหรือปรับปรุงแม่แบบ checklist จาก Admin
2. จัดการรายละเอียดแม่แบบ เช่น ชื่อ รายละเอียด ขอบเขตงาน และสถานะการใช้งาน
3. จัดการรายการตรวจภายใต้แม่แบบ เช่น ชื่อรายการ กลุ่มรายการ ลำดับการแสดงผล และสถานะรายการบังคับ
4. บันทึกหรือปรับปรุงข้อมูลใน D3 ข้อมูลแม่แบบรายการตรวจ
5. ส่งผลการจัดการแม่แบบกลับให้ Admin

ตาราง 3.3 Process Description ของ Process 3 ดำเนินการตรวจเช็กประจำวัน

Process 3 : ดำเนินการตรวจเช็กประจำวัน

Description : เป็นกระบวนการที่ Staff ใช้ดำเนินการตรวจเช็กประจำวันตามห้องและช่วงงานที่เลือก โดยระบบตรวจสอบผู้ปฏิบัติงาน ตรวจสอบข้อมูลห้อง ค้นหาแม่แบบ checklist ที่พร้อมใช้งาน ตรวจสอบหรือสร้างรอบการตรวจของวัน และบันทึกผลการตรวจเช็ก

Input
- ข้อมูลเข้าสู่ระบบจาก Staff
- ข้อมูลการเลือกห้องและช่วงงานจาก Staff
- ข้อมูลผลการตรวจเช็กจาก Staff
- D1 ข้อมูลผู้ใช้ ได้แก่ users.id, role, is_active
- D2 ข้อมูลห้อง ได้แก่ rooms.id, name, code, is_active
- D3 ข้อมูลแม่แบบรายการตรวจ ได้แก่ checklist_templates.id, title, scope, is_active และ checklist_items.id, title, group_label, sort_order, is_required
- D4 ข้อมูลการตรวจเช็กประจำวัน ได้แก่ checklist_runs.id, checklist_template_id, room_id, run_date, assigned_team_or_scope, created_by, submitted_at, submitted_by และ checklist_run_items.id, checklist_run_id, checklist_item_id, result, note, checked_by, checked_at

Output
- รายการตรวจเช็กที่เกี่ยวข้อง สถานะ checklist ของวัน และผลการบันทึกข้อมูลกลับไปยัง Staff
- ข้อมูล checklist run และผลการตรวจเช็กที่ถูกสร้างหรือปรับปรุงใน D4 ข้อมูลการตรวจเช็กประจำวัน ได้แก่ checklist_runs.id, checklist_template_id, room_id, run_date, assigned_team_or_scope, created_by, submitted_at, submitted_by และ checklist_run_items.id, checklist_run_id, checklist_item_id, result, note, checked_by, checked_at

Actor : Staff

Task :
1. ตรวจสอบข้อมูลเข้าสู่ระบบและสิทธิ์ของผู้ปฏิบัติงานจาก D1 ข้อมูลผู้ใช้
2. รับข้อมูลห้องและช่วงงานที่ Staff เลือก และตรวจสอบข้อมูลห้องจาก D2 ข้อมูลห้อง
3. ค้นหาแม่แบบ checklist และรายการตรวจที่พร้อมใช้งานจาก D3 ข้อมูลแม่แบบรายการตรวจ
4. ตรวจสอบสถานะ checklist ของวันจาก D4 ข้อมูลการตรวจเช็กประจำวัน
5. สร้าง checklist run ใหม่หรือใช้ checklist run ที่มีอยู่ตามเงื่อนไขของระบบ
6. บันทึกผลการตรวจเช็กของแต่ละรายการลงใน D4 ข้อมูลการตรวจเช็กประจำวัน
7. ส่งรายการตรวจเช็ก สถานะของวัน และผลการบันทึกกลับให้ Staff

ตาราง 3.4 Process Description ของ Process 4 จัดการเหตุผิดปกติ

Process 4 : จัดการเหตุผิดปกติ

Description : เป็นกระบวนการสำหรับรับและบันทึกข้อมูลเหตุผิดปกติจาก Staff รวมถึงปรับปรุงสถานะหรือข้อมูลการติดตามโดย Supervisor หรือ Admin โดยระบบบันทึกกิจกรรมของเหตุผิดปกติเป็นประวัติการดำเนินงาน

Input
- ข้อมูลเหตุผิดปกติจาก Staff ได้แก่ ห้อง หมวดหมู่ ระดับความรุนแรง รายละเอียด และหลักฐานประกอบเมื่อมี
- ข้อมูลอัปเดตสถานะเหตุผิดปกติและข้อมูลการติดตามจาก Supervisor หรือ Admin
- D2 ข้อมูลห้อง ได้แก่ rooms.id, name, code
- D5 ข้อมูลเหตุผิดปกติ ได้แก่ incidents.id, room_id, created_by, owner_id, title, category, severity, status, description, equipment_reference, attachment_path, follow_up_due_at, resolved_at
- D6 ข้อมูลกิจกรรมเหตุผิดปกติ ได้แก่ incident_activities.id, incident_id, action_type, summary, actor_id, created_at

Output
- ผลการบันทึกเหตุผิดปกติกลับไปยัง Staff
- ผลการอัปเดตสถานะกลับไปยัง Supervisor หรือ Admin
- ข้อมูลเหตุผิดปกติที่ถูกสร้างหรือปรับปรุงใน D5 ข้อมูลเหตุผิดปกติ ได้แก่ incidents.id, room_id, created_by, owner_id, title, category, severity, status, description, equipment_reference, attachment_path, follow_up_due_at, resolved_at
- ข้อมูลกิจกรรมเหตุผิดปกติที่ถูกบันทึกใน D6 ข้อมูลกิจกรรมเหตุผิดปกติ ได้แก่ incident_activities.id, incident_id, action_type, summary, actor_id, created_at

Actor : Staff, Supervisor, Admin

Task :
1. รับข้อมูลเหตุผิดปกติจาก Staff เพื่อสร้างรายการเหตุผิดปกติในระบบ
2. ตรวจสอบข้อมูลห้องที่เกี่ยวข้องจาก D2 ข้อมูลห้อง และตรวจสอบรายละเอียดเหตุผิดปกติที่รับเข้าระบบ
3. บันทึกรายงานเหตุผิดปกติลงใน D5 ข้อมูลเหตุผิดปกติ
4. บันทึกกิจกรรมการสร้างเหตุผิดปกติลงใน D6 ข้อมูลกิจกรรมเหตุผิดปกติ
5. รับข้อมูลอัปเดตสถานะหรือข้อมูลการติดตามจาก Supervisor หรือ Admin
6. อ่านข้อมูลเหตุผิดปกติเดิมจาก D5 และปรับปรุงสถานะ ผู้รับผิดชอบ วันติดตาม หรือข้อมูลการแก้ไขตามที่เกี่ยวข้อง
7. บันทึกกิจกรรมการเปลี่ยนแปลงลงใน D6 ข้อมูลกิจกรรมเหตุผิดปกติ
8. ส่งผลการบันทึกหรือผลการอัปเดตกลับไปยัง Staff, Supervisor หรือ Admin ตามบทบาทของผู้เกี่ยวข้อง

ตาราง 3.5 Process Description ของ Process 5 ติดตามภาพรวมและทบทวนประวัติ

Process 5 : ติดตามภาพรวมและทบทวนประวัติ

Description : เป็นกระบวนการสำหรับ Supervisor และ Admin ในการติดตามภาพรวมการทำงาน ตรวจสอบรายการเหตุผิดปกติ ทบทวนประวัติ checklist และ incident activity รวมถึงจัดเตรียมข้อมูลสรุปหรือเอกสารประกอบการทบทวน

Input
- คำขอติดตามสถานะงานจาก Supervisor
- คำขอดูภาพรวมระบบจาก Admin
- D4 ข้อมูลการตรวจเช็กประจำวัน ได้แก่ checklist_runs.id, room_id, run_date, assigned_team_or_scope, submitted_at, submitted_by และ checklist_run_items.id, result, note, checked_by, checked_at
- D5 ข้อมูลเหตุผิดปกติ ได้แก่ incidents.id, room_id, status, severity, owner_id, follow_up_due_at, resolved_at, created_at
- D6 ข้อมูลกิจกรรมเหตุผิดปกติ ได้แก่ incident_activities.id, incident_id, action_type, summary, actor_id, created_at

Output
- ข้อมูลแดชบอร์ด รายการเหตุผิดปกติ ประวัติ และเอกสารสรุปกลับไปยัง Supervisor
- ข้อมูลภาพรวมระบบกลับไปยัง Admin
- ข้อมูลสรุปที่ประมวลจาก D4 ข้อมูลการตรวจเช็กประจำวัน D5 ข้อมูลเหตุผิดปกติ และ D6 ข้อมูลกิจกรรมเหตุผิดปกติ

Actor : Supervisor, Admin

Task :
1. รับคำขอติดตามสถานะงานจาก Supervisor หรือคำขอดูภาพรวมระบบจาก Admin
2. อ่านข้อมูล checklist history และผลการตรวจเช็กจาก D4 ข้อมูลการตรวจเช็กประจำวัน
3. อ่านข้อมูลเหตุผิดปกติจาก D5 ข้อมูลเหตุผิดปกติ เพื่อจัดทำข้อมูลสรุปและรายการเหตุผิดปกติ
4. อ่านข้อมูลกิจกรรมเหตุผิดปกติจาก D6 ข้อมูลกิจกรรมเหตุผิดปกติ เพื่อใช้เป็นประวัติการดำเนินงาน
5. ประมวลผลภาพรวม dashboard จากข้อมูล checklist และ incident
6. จัดเตรียมประวัติและเอกสารสรุปที่เกี่ยวข้อง
7. ส่งข้อมูลภาพรวม รายการเหตุผิดปกติ ประวัติ และเอกสารสรุปกลับให้ Supervisor หรือ Admin ตามบทบาท

## Review Notes

- ไม่พบจุดที่ต้องระบุ “ต้องตรวจสอบเพิ่ม” จากข้อมูลที่ใช้ในร่างนี้
- Notes จาก `process-description-draft-v1.md` ถูกถอดออกจากเนื้อหาหลักของแต่ละ process โดยตั้งใจ เพื่อให้รูปแบบสอดคล้องกับตัวอย่างและรูปแบบทางการของบทที่ 3
- ขอบเขตของเนื้อหายังคงตัด public signup, notification, approval workflow, machine registry, external API, analytics warehouse, report builder และ AI/copilot ออกจากคำอธิบายกระบวนการตาม DFD ที่อนุมัติ
