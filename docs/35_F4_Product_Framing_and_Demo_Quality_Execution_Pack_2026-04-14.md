# **F4 Product Framing and Demo Quality Execution Pack**

## *DOC-35-F4 | Demo-ready framing, local demo guidance, and narrative seed quality*

**Version v1.0 | Feature execution pack | วันที่อ้างอิง 14/04/2569**

วัตถุประสงค์: ทำให้ระบบเปิดมาแล้วเข้าใจเร็ว, เดโมได้ลื่น, และสื่อคุณค่าแบบ “โปรเจกต์จบที่พร้อมโชว์” โดยไม่เพิ่ม feature ที่เกิน A-lite scope

# **1. Execution Goal**

ยกระดับ perception ของระบบจาก:

* “มี flow หลักครบ แต่ยังต้องอธิบายเยอะ”

ไปสู่:

* “เปิดมาก็เข้าใจว่าใครใช้ทำอะไร และเดโมตาม role ได้ทันที”

# **2. Chosen Scope**

รอบนี้เลือกทำเฉพาะสิ่งที่คุ้มและเชื่อมกับของจริง:

* เพิ่ม role-based demo walkthrough framing บน landing page
* เพิ่ม local/testing demo account guidance บน login page
* ปรับ seeded narrative data ให้มี timeline ที่เดโมแล้วเล่า story ได้ชัดขึ้น
* อัปเดต canonical docs และ README ให้สะท้อน demo flow ปัจจุบัน

ไม่ทำในรอบนี้:

* หน้า help center แยก
* guided onboarding flow แบบหลาย step
* fixture/demo mode แยกจาก runtime จริง
* fake analytics หรือ fake notifications

# **3. Architectural Placement**

* framing อยู่ที่ presentation layer (`welcome`, `login`)
* demo account guidance ถูกจำกัดไว้ที่ `local/testing` environment เท่านั้น
* narrative demo story ใช้ `DatabaseSeeder` ที่มีอยู่แล้ว ไม่เพิ่ม test dependency ใหม่

# **4. Acceptance Criteria**

ถือว่ารอบนี้สำเร็จเมื่อ:

* หน้าแรกเล่า role-specific walkthrough ได้ชัด
* local/testing login surface มีตัวอย่างบัญชีเดโมและสิ่งที่แต่ละ role จะเห็น
* seeded incidents/checklist history มี timeline ที่ใช้เล่า demo ได้จริง
* browser smoke ยังผ่าน
* tests ไม่พึ่ง seeded narrative detail เกินสิ่งที่ contract จริงควรรองรับ

# **5. Risk Notes**

* ห้ามเปิดเผย demo credentials ใน production surface
* ห้ามทำให้ seeder กลายเป็น hard dependency ของ automated tests
* ห้ามเพิ่ม copy ที่อ้าง feature เกินกว่าที่ระบบทำได้จริง

# **6. Verification Surface**

* `php artisan test`
* `composer lint:check`
* `composer test:browser`
