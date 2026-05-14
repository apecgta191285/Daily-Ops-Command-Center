# Frontend Refactoring Blueprint: UX/UI Redesign

จากการวิเคราะห์ Codebase และข้อกำหนดของคุณ นี่คือแผนแม่บท (Blueprint) สำหรับการยกระดับ UX/UI ของ Daily Ops Command Center ให้หลุดพ้นจากกรอบ "AI Slop" และเข้าสู่มาตรฐานโลกเทียบเท่า Modern SaaS Tools (เช่น Linear, Supabase) โดยยังคงทำงานบนโครงสร้าง Laravel + Tailwind CSS + Livewire (Flux) เดิม

---

## 1. Heuristic Evaluation & Brutal Truth (ข้อบกพร่องของดีไซน์ปัจจุบัน)

จากการตรวจสอบ `resources/css/app/tokens.css` และโครงสร้าง Component ปัจจุบัน พบปัญหาใหญ่ที่ต้อง "รื้อ" ดังนี้:

- **Eye Strain in Lab Environments (สว่างเกินไป):** ปัจจุบันระบบใช้ฉากหลังและ Surface เป็นสีขาวสว่าง (`--app-content-bg: #eef2f6`, `--app-surface: #fffffd`) ซึ่งในห้องคอมพิวเตอร์ที่บางครั้งมีการหรี่ไฟ หรือเจ้าหน้าที่ต้องจ้องจอนานๆ จะทำให้เกิดอาการล้าสายตาอย่างมาก (Eye fatigue) ระบบแบบนี้ควรเป็น Dark Mode-First
- **Hierarchy ขาดมิติความลึก (Flat & Bloated):** `x-ops.hero` ปัจจุบันกินพื้นที่แนวตั้งมหาศาล (Vertical Real Estate) และกลืนไปกับการ์ดด้านล่าง การจัดการแสงเงา (Shadows) ในแบบสว่างยังดูเรียบเกินไป ไม่เกิดมิติความสำคัญ (Focus points) ที่ชัดเจน
- **Color Semantics จืดชืด:** การใช้สีแบบเก่า (เขียวพาสเทล, ส้มอ่อน) ให้ความรู้สึกเหมือนหน้าเว็บราชการยุคเก่า หรือ Template แจกฟรี ไม่สื่อถึงความเร่งด่วน หรือความเฉียบขาดระดับ "Command Center"

## 2. UI/UX Curation & Architectural Specification

เราจะเปลี่ยน Paradigm การออกแบบใหม่ทั้งหมดให้อยู่ในธีม **"Neon-Accented Modern Dark Mode"** ร่วมกับโครงสร้าง **"Bento Grid"**

### A. Layout & Structure (Bento Grid)
- **Compact & Consolidated:** จัดวาง Dashboard ให้อยู่ในกรอบ 1 หน้าจอ (No-scroll or minimal scroll) โดยใช้ระบบ Bento Grid แบ่งเป็นกล่องๆ เช่น 
  - กล่องใหญ่: ภาพรวมการตรวจ (Completion Rate)
  - กล่องยาวแนวตั้ง: คิวปัญหา (Incident Feed) ที่วิ่งแบบ Real-time
  - กล่องย่อย: สถานะห้องแบบสรุปเร็วๆ
- **Component Replacement:** ปรับปรุง `<x-ops.hero>` ให้แคบลง (Compact Header) และปรับ `<x-ops.signal-card>`, `<x-ops.trend-card>` ให้อยู่ในลักษณะ Tile ของ Bento Grid

### B. Visual Theme (Dark Mode + Glassmorphism + Neon Accents)
- **Base Canvas:** ปรับแต่ง `resources/css/app/tokens.css` ให้เป็น Dark Theme โดยสมบูรณ์ (เช่น พื้นหลัง `#030712`, พื้นการ์ด `#111827` แบบโปร่งแสง)
- **Glassmorphism Layering:** ใช้ `backdrop-blur-md` ผสมกับพื้นหลังการ์ดโปร่งแสง (เช่น `bg-white/5` หรือ `bg-zinc-900/60`) และตัดเส้นขอบบางๆ (Hairline border) ด้วย `border-white/10` ทำให้ดูเหมือนกระจกฝ้าดำ
- **Vibrant Accents (Neon Indicators):**
  - ว่าง/เรียบร้อย (Available/Done): **Cyber Green** (เขียวเรืองแสง: `text-[#39ff14]`, พร้อม Glow effect)
  - รอแก้ไข/เตือน (Pending/Warning): **Amber Glow** (ส้มสะท้อนแสง: `text-[#fbbf24]`)
  - ปัญหา/เสีย (Incident/Down): **Neon Crimson** (แดงนีออน: `text-[#ff003c]`)

### C. Responsiveness & Accessibility
- **Mobile First & Thumb Zones:** สำหรับมุมมองมือถือ Bento Grid จะเรียงตัวลงมาด้านล่าง ปุ่ม Action หลักๆ ต้องใหญ่พอสำหรับนิ้วโป้ง (min-height 44px ตามมาตรฐาน WCAG)
- **Contrast Ratios:** ข้อความหลักสีขาว/เทาอ่อน บนพื้นสีดำสนิท การันตีผ่านเกณฑ์ WCAG 2.1 AA แน่นอน

### D. Micro-interactions
- **Hover States:** การ์ดแบบ Bento จะขยับขึ้นเล็กน้อย (`-translate-y-1`) พร้อมเปลี่ยนสีกรอบเส้นบางๆ เป็นสีของ Accent ทันทีที่ Hover
- **State Transitions:** ใช้ `transition-all duration-300 ease-out` เพื่อความนุ่มนวลระดับ Premium เวลากดปุ่มหรือโหลดข้อมูล

---

## 3. Component Breakdown & State Management

เราจะเข้าไปแก้ไข Component เดิม แทนการสร้างใหม่ทั้งหมด เพื่อหลีกเลี่ยงผลกระทบกับ Backend Logic โดยต้องทำการ Overhaul ดังนี้:

#### [MODIFY] `resources/css/app/tokens.css`
- นิยามชุดสีใหม่ (Dark Canvas, Neon Colors, Glassmorphism Variables)

#### [MODIFY] Component Views
- `resources/views/components/ops/hero.blade.php`: ลดขนาดลง เปลี่ยนสไตล์เป็น Glassmorphism
- `resources/views/components/ops/signal-card.blade.php`: ปรับให้เข้ากับโครงสร้าง Bento
- `resources/views/components/ops/trend-card.blade.php`: ปรับกราฟให้เข้ากับ Dark Mode (เส้นกราฟสี Neon)
- `resources/views/components/ops/section-heading.blade.php`: ปรับตัวอักษร สี และ Hierarchy

#### [MODIFY] Main Layouts
- `resources/views/dashboard.blade.php`: จัดเรียงใหม่ในคลาส `grid`, `col-span-*`, `row-span-*` เพื่อประกอบร่าง Bento Layout

> [!IMPORTANT]  
> **การดำเนินการถัดไป (User Review Required)**
> 
> นี่คือแผนภาพรวมของ Phase 1 ที่เราเน้นดีไซน์ที่ดุดัน เฉียบคม และเป็นหน้าปัดของ Command Center อย่างแท้จริง (กำจัด AI Slop)
> 
> กรุณาตรวจสอบ Blueprint นี้ หากคุณเห็นชอบกับทิศทาง Dark Mode + Bento Grid + Glassmorphism นี้ สามารถอนุมัติเพื่อให้ผมเริ่ม "เขียนโค้ดและแกะกล่อง CSS/Components" ในรอบถัดไปได้เลยครับ
