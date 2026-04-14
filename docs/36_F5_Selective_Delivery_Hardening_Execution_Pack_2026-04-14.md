# **F5 Selective Delivery Hardening Execution Pack**

## *DOC-36-F5 | Demo runbook, browser smoke expansion, and delivery confidence*

**Version v1.0 | Feature execution pack | วันที่อ้างอิง 14/04/2569**

วัตถุประสงค์: ปิดช่องว่างสุดท้ายที่ทำให้ระบบ “ดูดีแต่ส่งมอบไม่มั่นใจ” โดยเพิ่มหลักฐานการเดโม, การตรวจ flow สำคัญ, และ runbook ที่ใช้ได้จริงใน local/demo context

# **1. Execution Goal**

ทำให้ระบบ:

* เดโมตาม role ได้โดยไม่ต้องจำ flow เองทั้งหมด
* มี browser smoke ครอบคลุม flow ที่เป็น product-critical มากขึ้น
* มี checklist การเตรียมเครื่อง/ข้อมูลแบบเบาพอใช้จริง

# **2. Chosen Scope**

รอบนี้เลือกทำ:

* เพิ่ม browser smoke สำหรับ:
  * dashboard drill-down ไป incident filters
  * checklist history/progress panel หลัง staff login
* เพิ่ม demo runbook ที่บอก:
  * account ที่ใช้
  * flow แนะนำ
  * expected outcome
  * วิธี reset local data แบบเบา
* อัปเดต canonical docs index ให้รวม F5

ไม่ทำในรอบนี้:

* deployment automation
* monitoring/alerting stack
* screenshot snapshot testing
* seeded QA environments แยกจาก local

# **3. Acceptance Criteria**

ถือว่ารอบนี้สำเร็จเมื่อ:

* browser smoke ครอบคลุม flow ใหม่ใน F1-F4 อย่างน้อยหนึ่งจุดต่อ phase
* มีเอกสาร runbook ที่ใช้เดโมได้จริงโดยไม่อ้าง feature เกินของจริง
* README และ canonical docs อ้างอิงเอกสารใหม่ครบ

# **4. Verification Surface**

* `php artisan test`
* `composer lint:check`
* `composer test:browser`
