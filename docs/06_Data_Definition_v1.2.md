# **Data Definition**

## *A-lite Foundation Documentation Set*

**DOC-06-DD | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก**  
**Version v1.2 | Implementation support - locked demo data | วันที่อ้างอิง 02/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้ล็อก taxonomy, demo seed data, sample records และข้อกำหนดข้อมูลขั้นต่ำของ A-lite เพื่อให้ schema, local demo bootstrap และ demo evidence ใช้คำศัพท์และ narrative ชุดเดียวกัน โดย automated tests ไม่ควรผูก correctness เข้ากับ seeded narrative records เหล่านี้อีกต่อไป.

# **1\. Demo Domain ที่ล็อก**

บริบท demo ที่ใช้จริงใน v1 คือ “ห้องปฏิบัติการคอมพิวเตอร์ขนาดเล็กในมหาวิทยาลัย (ใช้ข้อมูลจำลอง ไม่ผูกกับหน่วยงานจริง)” โดยใช้ข้อมูลจำลองทั้งหมด เช่น ชื่อห้อง, ชื่อผู้ใช้, รายการ incident และหลักฐานแนบ เพื่อหลีกเลี่ยงข้อมูลส่วนบุคคลและข้อมูลหน่วยงานจริง.

เหตุผลที่เลือกบริบทนี้: ใกล้ตัวนักศึกษา, เข้าใจ use case ได้เร็ว, ไม่ต้องแตะสัญญา/บัญชี/ข้อมูลราชการ, และมี routine checklist + incident ที่สมเหตุสมผลสำหรับการสาธิตระบบ.

# **2\. Demo Scope และหน่วยงานจำลอง**

| หัวข้อ | ค่าที่ล็อก |
| ----- | ----- |
| ชื่อบริบทจำลอง | Computer Lab A (ห้องปฏิบัติการคอมพิวเตอร์ตัวอย่าง) |
| ทีมผู้ใช้ | Admin 1 คน, Supervisor 1 คน, Staff 2 คน |
| ช่วงเวลาการใช้งาน | งานเปิดห้อง / ตรวจระหว่างวัน / ปิดห้อง ใน 1 วันทำการ |
| สิ่งที่อยู่ในขอบเขต | เครื่องคอมพิวเตอร์, โปรเจกเตอร์, เครือข่าย, ความสะอาด, ความปลอดภัยพื้นฐาน |

# **3\. Taxonomy ที่ล็อกสำหรับ v1**

| หมวด | ค่าที่ล็อก | คำอธิบาย/กติกา |
| ----- | :---: | ----- |
| Checklist Scope | เปิดห้อง / ตรวจระหว่างวัน / ปิดห้อง | ใช้เป็น classification metadata สำหรับ template administration และ reporting; baseline ปัจจุบันยังไม่ใช้เพื่อสร้าง parallel daily execution flows |
| Incident Category | อุปกรณ์คอมพิวเตอร์ / เครือข่าย / ความสะอาด / ความปลอดภัย / สภาพแวดล้อม / อื่น ๆ | ใช้ค่าชุดนี้เท่านั้นใน v1 เพื่อกัน category กระจัดกระจาย |
| Severity | Low / Medium / High | Low = รบกวนเล็กน้อย, Medium = กระทบการใช้งานบางส่วน, High = กระทบการใช้งานหลักหรือความปลอดภัย |
| Incident Status | Open / In Progress / Resolved | ไม่มี approval หรือ reassignment หลายชั้นใน v1 |
| Checklist Result | Done / Not Done | ถ้าทำไม่ได้ให้ใช้ Not Done พร้อม note อธิบาย |

# **4\. Severity Definition แบบใช้งานจริง**

| ระดับ | เกณฑ์ | ตัวอย่างในบริบทห้องปฏิบัติการ |
| :---: | ----- | ----- |
| Low | ไม่หยุดการใช้งานหลัก แต่ควรแก้ | โต๊ะมีฝุ่น, สายเมาส์เริ่มหลวม, เก้าอี้วางไม่เรียบร้อย |
| Medium | กระทบการใช้งานบางส่วนหรือบางเครื่อง | เครื่อง 1 เครื่องเปิดไม่ติด, อินเทอร์เน็ตช้าผิดปกติ, โปรเจกเตอร์ภาพไม่ชัด |
| High | กระทบการใช้งานหลักหรือมีความเสี่ยงด้านความปลอดภัย | ไฟห้องบางจุดไม่ทำงาน, สายไฟชำรุด, อินเทอร์เน็ตล่มทั้งห้อง |

# **5\. ผู้ใช้และบัญชีตัวอย่าง**

| รหัส | บทบาท | ชื่อแสดงผล | หน้าที่ใช้งานหลัก |
| :---: | :---: | ----- | ----- |
| U-001 | Admin | Lab Admin | จัดการ template และดูภาพรวมระบบ |
| U-002 | Supervisor | Lab Supervisor | ตรวจ incident และดู dashboard |
| U-003 | Staff | Operator A | ทำ checklist และแจ้ง incident |
| U-004 | Staff | Operator B | ทำ checklist และแจ้ง incident |

# **6\. Checklist Template ที่ล็อกสำหรับ demo seed data ชุดแรก**

Template ชุดแรกของ v1 ใช้ 2 template เพื่อให้เดโม taxonomy ชัดและทดสอบ flow ได้ครบ โดยตั้งใจไม่ทำเกินจำเป็นในรอบแรก ปัจจุบันระบบยังรองรับ active daily execution template เพียง 1 อันทั้งระบบในแต่ละครั้ง

| รหัส | Template | Scope | จุดประสงค์ |
| :---: | ----- | :---: | ----- |
| T-001 | เปิดห้องปฏิบัติการ | เปิดห้อง | ตรวจความพร้อมก่อนเริ่มใช้งาน |
| T-002 | ปิดห้องปฏิบัติการ | ปิดห้อง | ตรวจความเรียบร้อยก่อนปิดพื้นที่ |

# **7\. Checklist Items ที่ใช้จริงชุดแรก**

Template T-001: เปิดห้องปฏิบัติการ

| ลำดับ | Checklist Item | Required | เหตุผล/หมายเหตุ |
| :---: | ----- | :---: | ----- |
| 1 | เปิดไฟและตรวจสภาพไฟส่องสว่าง | Yes | ต้องมั่นใจว่าพื้นที่พร้อมใช้งาน |
| 2 | เปิดเครื่องคอมพิวเตอร์ตัวอย่าง 1 เครื่อง | Yes | ตรวจว่าอุปกรณ์หลักใช้งานได้ |
| 3 | ตรวจการเชื่อมต่ออินเทอร์เน็ต | Yes | ใช้ยืนยันความพร้อมของเครือข่าย |
| 4 | ตรวจโปรเจกเตอร์หรือจอแสดงผลหลัก | Yes | ใช้ในกรณีมีการสอนหรือสาธิต |
| 5 | ตรวจความสะอาดโต๊ะและทางเดิน | Yes | ป้องกันสภาพพื้นที่ไม่พร้อม |
| 6 | ตรวจว่าพื้นที่ไม่มีสายไฟหรือสิ่งกีดขวางผิดปกติ | Yes | เป็น checklist ด้านความปลอดภัยพื้นฐาน |

Template T-002: ปิดห้องปฏิบัติการ

| ลำดับ | Checklist Item | Required | เหตุผล/หมายเหตุ |
| :---: | ----- | :---: | ----- |
| 1 | ตรวจว่าผู้ใช้งานออกจากพื้นที่แล้ว | Yes | ป้องกันการปิดพื้นที่ขณะยังมีผู้ใช้งาน |
| 2 | ปิดเครื่องคอมพิวเตอร์และอุปกรณ์หลัก | Yes | ลดการใช้พลังงานและความเสี่ยง |
| 3 | เก็บอุปกรณ์ที่วางผิดที่ให้เรียบร้อย | Yes | รักษาความพร้อมของพื้นที่วันถัดไป |
| 4 | ตรวจความสะอาดพื้นฐานของโต๊ะและทางเดิน | Yes | ลดงานค้างสะสม |
| 5 | ปิดไฟ/แอร์/อุปกรณ์ที่ไม่จำเป็น | Yes | ใช้เป็นขั้นตอนปิดพื้นที่ |
| 6 | ตรวจและล็อกประตูหรือแจ้งผู้รับผิดชอบ | Yes | เป็นขั้นตอนความปลอดภัยพื้นฐาน |

# **8\. Checklist Run Policy ที่ล็อกสำหรับ v1**

* Staff เปิดหน้า `/checklists/runs/today` แล้วระบบต้องค้นหา run ของวันนั้นก่อน  
* ถ้ายังไม่มี run ระบบต้องสร้างใหม่ให้อัตโนมัติ  
* ใน MVP ใช้หลัก 1 run ต่อ 1 template ต่อ 1 วัน ต่อ 1 staff owner  
* current runtime รองรับ active daily checklist template เพียง 1 อันทั้งระบบ; `scope` ยังไม่ทำหน้าที่แยก execution flow หลายสาย  
* `created_by` ใช้เก็บผู้ที่เปิด run ครั้งแรกและทำหน้าที่เป็น owner ของ run ในบริบท demo  
* `submitted_at` และ `submitted_by` ใช้บอกว่ารันถูก submit แล้ว  
* v1 ไม่มี draft state อย่างเป็นทางการ; ค่าที่ยังไม่ submit คือ in-progress ตามสภาพจริง ไม่ใช่ฟีเจอร์ draft แยกต่างหาก

# **9\. Incident Sample Records ที่ใช้จริงชุดแรกสำหรับ demo narrative**

ใช้ incident ตัวอย่างอย่างน้อย 10 รายการ เพื่อให้หน้า list/detail/filter/dashboard มีข้อมูลพอสำหรับทดสอบและเดโม.

| รหัส | หัวข้อ | Category | Severity | Status |
| :---: | ----- | :---: | :---: | :---: |
| I-001 | เครื่อง PC-03 เปิดไม่ติด | อุปกรณ์คอมพิวเตอร์ | Medium | Open |
| I-002 | อินเทอร์เน็ตใช้งานไม่ได้ทั้งห้อง | เครือข่าย | High | In Progress |
| I-003 | โปรเจกเตอร์ภาพเบลอ | อุปกรณ์คอมพิวเตอร์ | Medium | Resolved |
| I-004 | โต๊ะด้านหลังมีฝุ่นมาก | ความสะอาด | Low | Resolved |
| I-005 | สายไฟใต้โต๊ะวางระเกะระกะ | ความปลอดภัย | High | Open |
| I-006 | แอร์ห้องไม่เย็น | สภาพแวดล้อม | Medium | In Progress |
| I-007 | เมาส์เครื่อง PC-07 ขัดข้อง | อุปกรณ์คอมพิวเตอร์ | Low | Resolved |
| I-008 | ปลั๊กพ่วงใกล้หน้าห้องมีรอยไหม้ | ความปลอดภัย | High | Open |
| I-009 | พื้นทางเดินมีขยะและสาย LAN พาด | ความสะอาด | Medium | Open |
| I-010 | เสียงพัดลมเครื่อง PC-02 ดังผิดปกติ | อุปกรณ์คอมพิวเตอร์ | Low | In Progress |

# **10\. Implementation-Critical Field Notes**

| Entity / Field | คำอธิบายที่ล็อก |
| ----- | ----- |
| ChecklistRun.created_by | staff owner ของ run ใน MVP |
| ChecklistRun.submitted_at | เวลาที่ผู้ใช้ submit checklist เสร็จ |
| ChecklistRun.submitted_by | ผู้ที่กด submit; ปกติจะเป็น staff owner ใน MVP |
| ChecklistRunItem.result | ใช้ค่า Done หรือ Not Done เท่านั้น |
| Incident.attachment_path | path ของไฟล์บน local public disk; optional |
| IncidentActivity.action_type | อย่างน้อยต้องรองรับ `created` และ `status_changed` |

# **11\. Data Constraints และกติกาการตั้งชื่อ**

* Checklist Template ต้องมีชื่อไม่ซ้ำกันภายใน repository baseline ปัจจุบัน  
* Checklist Item ต้องมี `sort_order` ชัดและไม่ซ้ำใน template เดียวกัน  
* Checklist Run ต้องไม่ถูกสร้างซ้ำสำหรับ `(template_id, run_date, created_by)` ชุดเดียวกัน  
* ทุก ChecklistRunItem ต้องมี result ก่อน submit  
* Incident title ควรยาวไม่เกิน 120 ตัวอักษร เพื่อให้ list view อ่านง่าย  
* Description และ note ใช้ข้อความสั้น กระชับ และหลีกเลี่ยงข้อมูลส่วนบุคคลจริง  
* หลักฐานแนบใช้ไฟล์ตัวอย่าง/ภาพจำลองเท่านั้น ห้ามใช้รูปที่เปิดเผยข้อมูลอ่อนไหว  
* attachment_path ต้องอ้างถึง local public disk เท่านั้นใน v1

# **12\. Seed Data Minimum Count**

| ข้อมูล | ขั้นต่ำที่ต้องมี |
| ----- | :---: |
| Users | 4 records |
| Checklist Templates | 2 records |
| Checklist Items | 12 records |
| Checklist Runs | อย่างน้อย 2 records สำหรับคนละวันหรือคนละ template |
| Incidents | 10 records |

# **13\. Ready-to-Implement Checklist**

* Domain demo ถูกล็อกแล้ว: ห้องปฏิบัติการคอมพิวเตอร์ขนาดเล็กในมหาวิทยาลัย  
* Taxonomy หลักถูกล็อกแล้ว: category, severity, status, checklist scope, checklist result  
* Template ชุดแรกและ incident seed data ชุดแรกถูกกำหนดแล้ว  
* Checklist run creation policy ถูกล็อกแล้ว  
* Attachment policy ถูกล็อกแล้ว  
* เอกสารนี้พร้อมใช้เป็นฐานทำ migration, demo seed script และ demo data  
* automated tests ควรใช้ factory/scenario helper ของตัวเองเป็นหลัก ไม่อ้าง seeded narrative records โดยตรง
