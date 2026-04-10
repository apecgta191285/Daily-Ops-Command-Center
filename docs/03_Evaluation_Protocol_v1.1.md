**A-lite Foundation Documentation Set**

**03_Evaluation_Protocol_v1.1**  
วิธีวัดผล เปรียบเทียบ baseline และเก็บหลักฐานสำหรับ A-lite

| Document ID | DOC-03-EP |
| :---- | :---- |
| **Project** | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก |
| **Version** | v1.1 |
| **Status** | Evaluation baseline |
| **Reference Date** | 02/04/2569 |

วัตถุประสงค์: เอกสารฉบับนี้ใช้เป็นฐานอ้างอิงต้นน้ำของหัวข้อ A-lite เพื่อกัน scope drift, ลดการตัดสินใจแบบเฉพาะหน้า และทำให้การคุยกับ AI / การลงมือพัฒนา / การเตรียมสอบยึดข้อมูลชุดเดียวกัน.

# **Document Control**

| Comparison | Baseline A vs System B (A-lite) |
| :---- | :---- |
| **Evidence Goal** | ใช้สื่อสารกับอาจารย์/กรรมการได้ |
| **Minimum Sample** | ผู้ทดลอง 5-8 คน หรือ replay task set แบบมีผู้ประเมิน |
| **Claim Boundary** | ใช้ยืนยันว่า A-lite เป็น demo-ready MVP ที่ใช้งานได้จริงในบริบทจำลอง ไม่ใช่ production benchmark |

# **1\. Evaluation Objective**

* ประเมินว่า A-lite ทำให้งานประจำวันเป็นระบบกว่าวิธี baseline หรือไม่  
* ประเมินว่าการแจ้งและติดตาม incident ชัดเจนขึ้นหรือไม่  
* ประเมินว่าหัวหน้าทีมมองภาพรวมได้เร็วขึ้นหรือไม่

# **2\. Locked Comparison**

| ระบบ | คำอธิบาย |
| :---: | ----- |
| Baseline A | เช็กลิสต์บนกระดาษ/Google Form และแจ้งปัญหาผ่านแชตทั่วไป |
| System B | เว็บแอป A-lite ที่รวม checklist รายวัน, incident tracking และ dashboard |

# **3\. Task Set**

| Task | สิ่งที่ให้ทำ | ผลลัพธ์คาดหวัง |
| :---: | ----- | ----- |
| T1 | ทำ checklist ประจำวันให้ครบ | บันทึกผลแต่ละข้อได้ครบและตรวจย้อนหลังได้ |
| T2 | แจ้งเหตุผิดปกติ 1 กรณี | incident ถูกสร้างพร้อมรายละเอียดขั้นต่ำ |
| T3 | หัวหน้าทีมตรวจ incident และอัปเดตสถานะ | สถานะเปลี่ยนและมีร่องรอยกิจกรรม |
| T4 | เปิด dashboard เพื่อดูภาพรวม | เห็น completion และ incident overview |

# **4\. Metrics**

| Metric | นิยาม | เป้าหมายขั้นต่ำ |
| :---: | ----- | ----- |
| Task completion success | ทำ task สำเร็จตาม flow หรือไม่ | System B สำเร็จมากกว่า A |
| Time to complete | เวลาที่ใช้ต่อ task | System B ใช้เวลาน้อยกว่าหรือใกล้เคียงแต่มีหลักฐานดีกว่า |
| Data completeness | ข้อมูล checklist/incident ครบตามขั้นต่ำหรือไม่ | System B ครบกว่าอย่างมีนัยเชิงปฏิบัติ |
| Supervisor visibility | หัวหน้าทีมเข้าใจสถานะรวมได้หรือไม่ | คะแนนความเข้าใจ/ความสะดวกสูงกว่า A |
| User satisfaction | ความพึงพอใจ Likert 1-5 | System B เฉลี่ย >= 4 หรือสูงกว่า A |

# **5\. Evidence Required**

* task sheets ที่ล็อกก่อนทดลอง  
* timestamps หรือจับเวลาแต่ละ task  
* screenshots / screen recordings ของ baseline และ system B  
* แบบฟอร์ม rubric และแบบสอบถามความพึงพอใจ  
* failure cases หรือสิ่งที่ผู้ใช้สับสนระหว่างการใช้งาน

# **6\. Threats to Validity**

* จำนวนผู้ใช้ทดลองอาจน้อยและไม่สามารถ generalize กว้าง  
* บริบท demo เป็นทีมเล็กและข้อมูลจำลอง ไม่ใช่ production deployment  
* ผู้ทดลองอาจคุ้นกับวิธีเดิมมากกว่า/น้อยกว่า ส่งผลต่อเวลาและความพึงพอใจ  
* หากใช้ manual verification แทน browser automation บางส่วน ต้องอธิบายขอบเขตอย่างตรงไปตรงมา
