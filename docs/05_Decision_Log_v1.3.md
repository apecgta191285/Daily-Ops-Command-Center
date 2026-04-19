# **Decision Log**

> Historical note: This document contains append-only decisions across multiple project phases. For the current repository baseline, use this log together with [22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md](./22_Architecture_Boundary_and_Execution_Standards_2026-04-11.md), [24_Domain_Normalization_Design_2026-04-11.md](./24_Domain_Normalization_Design_2026-04-11.md), and [26_Architecture_Debt_Roadmap_2026-04-11.md](./26_Architecture_Debt_Roadmap_2026-04-11.md) as the active engineering reference.
>
> Historical impact labels below may still mention older working documents that were intentionally removed from the canonical repo set. Treat those names as historical context, not active source-of-truth files.

## *A-lite Foundation Documentation Set*

**DOC-05-DL | ระบบเช็กลิสต์งานประจำวันและติดตามเหตุการณ์ผิดปกติสำหรับทีมงานขนาดเล็ก**  
**Version v1.3 | Live working document - append only | วันที่อ้างอิง 03/04/2569**

วัตถุประสงค์: เอกสารฉบับนี้ใช้บันทึกการตัดสินใจเชิงวิศวกรรมของหัวข้อ A-lite เพื่อกันการย้อนเถียงจากความทรงจำหรือข้อความแชต และบังคับให้เอกสารอื่นสะท้อนการตัดสินใจที่สำคัญ.

# **1\. Policy**

* append only / ไม่ย้อนแก้ประวัติแบบไร้ร่องรอย  
* ใช้กับการเปลี่ยน scope, baseline, stack, schema, evaluation และ demo context  
* ถ้าตัดสินใจใหม่กระทบเอกสารอื่น ต้องอัปเดตเอกสารนั้นด้วย

# **2\. Initial Decisions**

**D-001 | Locked**

Decision: ล็อกหัวข้อโครงงานเป็น A-lite แทน Daily Ops Command Center แบบเต็ม

Rationale: เพื่อให้ขอบเขตพอดีกับผู้พัฒนาคนเดียวและทำ MVP จบจริงได้

Impact: Project Lock, Product Brief, System Spec ต้องสะท้อนขอบเขตใหม่

**D-002 | Locked**

Decision: กำหนดแกนระบบเป็น checklist รายวัน + incident tracking + dashboard พื้นฐาน

Rationale: เพื่อให้ระบบไม่กลายเป็น dashboard ลอย ๆ และยังไม่หนักเท่า operations suite เต็มรูปแบบ

Impact: ตัด feature พวก approval, notification, analytics ขั้นสูง ออกจาก v1

**D-003 | Locked**

Decision: เลือก baseline เป็น manual checklist / chat reporting เทียบกับระบบ A-lite

Rationale: เพราะเป็น baseline ที่อธิบายง่าย เหมาะกับการประเมินเชิงวิชาการ และใช้ข้อมูลชุดเดียวกันได้

Impact: Evaluation Protocol ต้องล็อก task set และ metrics ตาม comparison นี้

**D-004 | Locked**

Decision: ใช้ modular monolith เป็นสถาปัตยกรรมตั้งต้น

Rationale: ลด moving parts และเพิ่ม maintainability สำหรับ solo dev

Impact: repo structure และ coding plan ต้องไม่แตกเป็นหลาย service

**D-005 | Locked**

Decision: ล็อก demo domain เป็นห้องปฏิบัติการคอมพิวเตอร์ขนาดเล็กในมหาวิทยาลัย (ใช้ข้อมูลจำลอง)

Rationale: ทำให้ Data Definition, Wireframe, Seed Data และ Evaluation มีบริบทเดียวกัน และยังไม่แตะข้อมูลจริงที่อ่อนไหว

Impact: 06_Data_Definition, 07_UI_Flow_Wireframe, 04_Current_State ต้องสะท้อนบริบทนี้

# **3\. Added Decisions (02/04/2569)**

**D-006 | Locked**

Decision: ล็อก technology stack หลักเป็น Laravel 13 + PHP 8.3+, Livewire 4, Filament 5, Tailwind CSS v4+, MySQL local, Laravel local public disk, Pest เป็นแกนการทดสอบ และใช้ Laravel Dusk เฉพาะเมื่อ environment พร้อมโดยไม่กินเวลาเกิน scope

Rationale: ต้องใช้ stack ที่ build ได้เร็ว, มี ecosystem รองรับ, และไม่บังคับให้เสียเวลากับ infra เกินจำเป็น

Impact: 04_Current_State, 08_Test_and_Evidence_Plan, 09_Implementation_Foundation_Plan ต้องอัปเดตให้ตรงกัน

**D-007 | Locked**

Decision: ล็อก access strategy เป็น Admin/Supervisor ใช้ management surface เดียวกัน และ Staff ใช้ task-focused pages แยกจาก panel

Rationale: เร็วกว่า, แยก UX ของคนตั้งค่าออกจากคนปฏิบัติงาน, และเหมาะกับ deadline 15 วันมากกว่าการพยายามยัดทุก role เข้า panel เดียว

Impact: 07_UI_Flow_Wireframe และ 09_Implementation_Foundation_Plan ต้องสะท้อน strategy นี้

**D-008 | Locked**

Decision: ล็อก checklist run creation policy เป็น Staff เปิดหน้า checklist ของวันแล้วระบบสร้าง run ให้อัตโนมัติถ้ายังไม่มี โดยใช้ 1 run ต่อ 1 template ต่อ 1 วัน ต่อ 1 staff owner ใน MVP

Rationale: ตัด assignment workflow ที่ยังไม่จำเป็น และลดความซับซ้อนของ route, policy และ seed logic

Impact: 02_System_Spec, 06_Data_Definition, 07_UI_Flow_Wireframe, 09_Implementation_Foundation_Plan ต้องสะท้อน rule นี้

**D-009 | Locked**

Decision: ล็อก checklist submission model เป็นไม่มี draft state อย่างเป็นทางการใน v1; ใช้ `submitted_at` เป็นตัวบอกว่ารันถูก submit แล้วหรือยัง และทุก item ต้องถูกตอบก่อน submit

Rationale: ลดสถานะที่ต้องดูแล, ลด schema complexity และตรงกับเวลาพัฒนาแบบ solo dev

Impact: 02_System_Spec, 06_Data_Definition, 07_UI_Flow_Wireframe, 08_Test_and_Evidence_Plan ต้องสะท้อน rule นี้

**D-010 | Locked**

Decision: ล็อก incident attachment policy เป็น optional และใช้ Laravel local public disk เท่านั้น; ไม่ใช้ Supabase หรือ external storage ใน v1

Rationale: ตรงกับข้อจำกัดเรื่องเวลาและช่วยให้ feature path จบจริงโดยไม่เพิ่ม infra เกินจำเป็น

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 07_UI_Flow_Wireframe, 09_Implementation_Foundation_Plan ต้องสะท้อน rule นี้

**D-011 | Locked**

Decision: ล็อก incident workflow ใน v1 เป็น Create + Update Status + Resolve เท่านั้น; ไม่มี assign / reassign

Rationale: ลด state และ action ที่ไม่จำเป็นต่อการเดโม และกัน scope leak จากคำว่า command center เต็มรูปแบบ

Impact: 00_Project_Lock, 02_System_Spec, 07_UI_Flow_Wireframe, 09_Implementation_Foundation_Plan ต้องสะท้อนขอบเขตนี้

**D-012 | Locked**

Decision: ห้ามใช้คำอธิบายว่า production-ready system กับสถานะปัจจุบัน; คำอธิบายที่ถูกต้องคือ demo-ready MVP foundation

Rationale: เพื่อให้ narrative ตรงกับหลักฐานจริงและลดความเสี่ยงในการอ้างเกินสิ่งที่ build ได้

Impact: 00_Project_Lock, 01_Product_Brief, 03_Evaluation_Protocol, 09_Implementation_Foundation_Plan ต้องใช้ wording ให้สอดคล้องกัน

**D-013 | Locked**

Decision: ล็อก repo policy เป็น single repository / single Laravel app และยอมใช้ short-lived feature branches โดยไม่บังคับ CI ก่อน MVP

Rationale: ลด overhead ของ workflow และให้โฟกัสกับ end-to-end working path มากกว่าการตั้ง infra เกินตัว

Impact: 04_Current_State และ 09_Implementation_Foundation_Plan ต้องสะท้อนนโยบายนี้

# **4\. Added Decisions (03/04/2569)**

**D-014 | Locked**

Decision: ล็อก incident status permission ใน v1 เป็น Admin และ Supervisor เปลี่ยน status ได้ทั้งคู่ ส่วน Staff สร้าง incident ได้แต่ไม่มีสิทธิ์เปลี่ยน status

Rationale: ตัด conflict ระหว่าง System Spec กับ UI/Implementation docs, ลดการทำ policy ซ้ำซ้อนโดยไม่จำเป็น, และทำให้ management surface ของ Admin/Supervisor มีขอบเขตชัดเจน

Impact: 02_System_Spec, 04_Current_State, 07_UI_Flow_Wireframe, 08_Test_and_Evidence_Plan และ 09_Implementation_Foundation_Plan ต้องสะท้อน permission นี้ให้ตรงกัน

# **5\. Added Decisions (04/04/2569)**

**D-015 | Locked**

Decision: ปรับฐานข้อมูลสำหรับการพัฒนา local MVP จาก MySQL เป็น SQLite (Controlled pivot)

Rationale: เครื่อง local ไม่มี MySQL/XAMPP ติดตั้งอยู่ และโครงงานต้องการฐานข้อมูลแบบ standalone เพื่อให้การพัฒนาเดินหน้าสู่เดโมได้ฉับไวที่สุด การใช้ SQLite รองรับ natively โดย Laravel และตรงตามหลัก demo-first progress. โครงสร้างสถาปัตยกรรมยังคงเป็น modular monolith จบในที่เดียวเช่นเดิม

Impact: 04_Current_State และ 09_Implementation_Foundation_Plan ถูกปรับให้ระบุว่าใช้ SQLite เป็น Local Database แทน MySQL

# **6\. Added Decisions (05/04/2569)**

**D-016 | Locked**

Decision: ล็อก Daily Checklist Run singular resolution rule. `/checklists/runs/today` จะต้อง resolve template ที่ active เพียงอันเดียวเท่านั้น เพื่อลดความซับซ้อนของ flow ตาม UI แบบ singular. หากมี template ที่ active มากกว่า 1 อัน ระบบจะแสดงข้อความ error. การ demo จะคง template ปิดห้องให้เป็น inactive ไว้ชั่วคราวเพื่อให้มี 1 active template ใช้งานได้.

Rationale: ตัดปัญหาความคลุมเครือจากการมีหลาย template แข่งกันให้ staff เปิดทำ checklist ประจำวัน โดยไม่ซ้ำซ้อนกับ template selection UI ที่ไม่ได้วางแผนไว้ในกรอบ MVP 15 วัน

Impact: 02_System_Spec, 07_UI_Flow_Wireframe และ 11_Implementation_Task_List จะต้องอนุมาน rule ว่ามี 1 active template ตลอด flow ของ /checklists/runs/today. ปรับแก้ DatabaseSeeder ให้มี template แบบ active เพียง 1 อัน.

# **7\. Added Decisions (06/04/2569)**

**D-017 | Locked**

Decision: ล็อก `resolved_at` convention สำหรับ incident reopening ใน v1. เมื่อ incident ถูกเปลี่ยนสถานะเป็น `Resolved` ให้ set `resolved_at` เป็นเวลาปัจจุบัน; หากถูกเปลี่ยนกลับออกจาก `Resolved` ไปเป็น `Open` หรือ `In Progress` ให้ clear `resolved_at` กลับเป็น `null`

Rationale: เอกสารเดิมล็อกเพียงว่า resolved case ต้อง set timestamp แต่ยังไม่ล็อกพฤติกรรมตอน reopen. การ clear ค่ากลับเป็น null ทำให้ field นี้สะท้อนสถานะปัจจุบันตรงที่สุดและตรงกับ implementation ที่ถูกทดสอบแล้ว

Impact: 04_Current_State, 08_Test_and_Evidence_Plan, 09_Implementation_Foundation_Plan และ 11_Implementation_Task_List ต้องสะท้อน convention นี้ให้ตรงกับ implementation และ tests

# **8\. Added Decisions (11/04/2569)**

**D-018 | Locked**

Decision: ปิด foundation remediation และล็อก engineering baseline ใหม่ของ repository เป็น PHP 8.4 local/runtime baseline, SQLite local development profile, internal-account-only authentication, active-user enforcement, application-layer workflow orchestration สำหรับ core use cases, และ source-only repository policy ที่ไม่ track vendor-generated Filament assets

Rationale: baseline เดิมมี truth mismatch ระหว่าง docs, runtime, CI, auth policy, workflow placement และ repository artifacts ทำให้การพัฒนาต่อมีความเสี่ยงสูงต่อ drift และการแก้แบบเฉพาะหน้า

Impact: 04_Current_State, README, architecture boundary documentation, domain normalization references และ repository hygiene policy ต้องสะท้อน baseline นี้ให้ตรงกับ code ปัจจุบัน

**D-019 | Locked**

Decision: จำกัด Filament panel `/admin` เป็น admin-only สำหรับ current repository baseline และไม่ให้ supervisor เข้า panel จนกว่าจะมี admin-surface use case ที่ supervisor ใช้งานได้จริง

Rationale: ของจริงใน panel ปัจจุบันมี resource เชิงธุรกิจหลักเฉพาะ checklist template administration เท่านั้น การเปิด panel ให้ supervisor เข้าได้แต่ไม่มี resource ที่มีความหมายสร้างทั้ง authorization mismatch และ UX confusion

Impact: User panel-access policy, admin surface navigation, current-state summary และ architecture boundary docs ต้องสะท้อนขอบเขตนี้ให้ตรงกัน

**D-020 | Locked**

Decision: ยกเลิก Filament panel ในฐานะ active presentation surface และย้าย checklist template administration เข้ามาอยู่ใน main application shell ผ่าน custom Livewire/admin-only routes แทน

Rationale: หลัง remediation พบว่า admin surface ของจริงมีเพียง checklist template CRUD หนึ่งชุด ซึ่งเล็กเกินกว่าจะคุ้มค่ากับการแบกอีกหนึ่ง UI system, auth entry, theme contract, และ maintenance surface แยกต่างหาก การรวมกลับเข้า shell หลักให้ความสอดคล้องของ UX สูงกว่าและลด long-term presentation debt

Impact: 04_Current_State, 22_Architecture_Boundary_and_Execution_Standards, README, routes/web.php, navigation, tests และ checklist-template implementation ต้องสะท้อน single-surface admin strategy นี้ให้ตรงกัน

**D-021 | Locked**

Decision: เปิด master refactor program อย่างเป็นทางการตาม 27_Full_Codebase_Audit_2026-04-11, 28_Master_Refactor_Plan_2026-04-11 และ execution artifact ที่ตามมา โดยห้าม scope expansion ระหว่างรอบ refactor นี้

Rationale: repository ดีขึ้นพอที่จะเริ่ม refactor รอบใหญ่ได้แล้ว แต่ยังมี contract mismatch หลายชั้น การ refactor ต้องถูกควบคุมเป็น phase และยึด contract เดียว มิฉะนั้นจะกลับไปสู่สภาพ drift แบบเดิม

Impact: ทุก phase ของ refactor ต้องอัปเดต code, tests และ canonical docs พร้อมกัน และห้ามเพิ่ม feature family ใหม่โดยไม่ผ่าน Project Lock และ Decision Log ก่อน

**D-022 | Locked**

Decision: ล็อกความจริงของ checklist execution ใน baseline ปัจจุบันว่า `/checklists/runs/today` รองรับ daily checklist runtime แบบ singular flow เท่านั้น โดย `ChecklistScope` ถูกใช้เป็น classification metadata สำหรับ template administration และ reporting เท่านั้น ยังไม่ใช่ dimension ที่สร้าง parallel execution flows

Rationale: code ปัจจุบันมี `scope` ใน model และ admin UI แต่ runtime ยัง resolve active template เพียงหนึ่งเดียวทั้งระบบ หากไม่ล็อกความจริงนี้ให้ชัด จะเกิดการตีความผิดว่าระบบรองรับหลาย daily flows ทั้งที่ของจริงยังไม่รองรับ

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 22_Architecture_Boundary_and_Execution_Standards, 24_Domain_Normalization_Design, admin template copy, และ daily checklist copy ต้องสะท้อน singular execution truth นี้ให้ตรงกัน

**D-023 | Locked**

Decision: ใน Phase 2 ของ master refactor program ให้ harden persistence contract เฉพาะ invariant ที่มีความเสี่ยงสูงและคุ้มค่าที่สุดก่อน ได้แก่ `checklist_templates.title` ต้องไม่ซ้ำ และระบบต้องมี active checklist template ได้เพียง 1 อันทั่วทั้งตาราง โดยใช้ database-backed enforcement ร่วมกับ application transaction ordering

Rationale: สอง invariant นี้เป็นจุดที่เสี่ยงต่อ state corruption สูงและมีผลต่อ daily runtime โดยตรง ขณะเดียวกันการ rebuild ตารางทั้งหมดเพื่อใส่ CHECK constraints ให้ทุก string taxonomy ใน SQLite ตอนนี้จะ churn สูงเกินประโยชน์และเสี่ยงทำให้ phase ยืดเกินขอบเขต

Impact: migration, SaveChecklistTemplate action, AdminSurfaceBoundary tests, และ persistence-hardening notes ต้องสะท้อน strategy นี้ให้ตรงกัน

**D-024 | Locked**

Decision: canonical value families เช่น role, scope, incident status, severity, category, และ checklist result ยังคง enforce หลักที่ application/domain layer ไปก่อนในรอบนี้ โดยยังไม่บังคับ full schema rebuild เพื่อแปลงทุกตารางเป็น DB CHECK constraints ทันที

Rationale: repository baseline ปัจจุบันรองรับ SQLite เป็น primary development/runtime profile และการ rebuild หลายตารางพร้อมกันในรอบนี้ไม่คุ้มเมื่อเทียบกับความเสี่ยง การ refactor จะเดินแบบ incremental โดย harden จุดที่คุ้มที่สุดก่อน แล้วค่อยตัดสินใจเรื่อง schema-wide constraints ในรอบต่อไปหากยังจำเป็น

Impact: 24_Domain_Normalization_Design, 26_Architecture_Debt_Roadmap, 28_Master_Refactor_Plan และ 29_Refactor_Execution_Pack ต้องสะท้อนว่า persistence hardening รอบนี้เป็น selective hardening ไม่ใช่ full schema rewrite

**D-025 | Locked**

Decision: ยกเลิก legacy `/admin/*` compatibility routes สำหรับ checklist template administration และให้ `/templates`, `/templates/create`, `/templates/{template}/edit` เป็น route contract เดียวที่รองรับอย่างเป็นทางการ

Rationale: หลังจาก checklist template administration ถูกย้ายเข้ามาใน main application shell แล้ว การคง redirect compatibility routes ไว้ต่อจะทำให้ route truth มีสองชุดโดยไม่จำเป็น และเปิดโอกาสให้ docs, tests, bookmarks, และ implementation drift กลับไปสู่ความกำกวมเดิม

Impact: routes/web.php, AdminSurfaceBoundary tests, README, 04_Current_State, 22_Architecture_Boundary_and_Execution_Standards, 26_Architecture_Debt_Roadmap และ 29_Refactor_Execution_Pack ต้องสะท้อน route family เดียวนี้ให้ตรงกัน

**D-026 | Locked**

Decision: สำหรับ checklist template administration ใน baseline ปัจจุบัน ให้ใช้ route-level role middleware เป็น authorization contract หลักต่อไป และยังไม่เพิ่ม object-level policy layer จนกว่าจะมี per-record ownership, per-action differentiation, หรือเงื่อนไข authorization ที่แยกระหว่าง admin ด้วยกันเอง

Rationale: use case ปัจจุบันเป็น admin-only ทั้งชุด และทุก template action ใช้สิทธิ์ระดับเดียวกัน การเพิ่ม policy layer ตอนนี้จะเพิ่ม surface area โดยยังไม่ลดความซับซ้อนหรือเพิ่มความปลอดภัยอย่างมีนัยสำคัญ

Impact: routes/web.php, EnsureUserHasRole middleware, checklist-template Livewire screens, tests, และ 22_Architecture_Boundary_and_Execution_Standards ต้องสะท้อน coarse route-level authorization truth นี้ให้ตรงกัน

**D-027 | Locked**

Decision: ในรอบ Phase 4 ปัจจุบันให้คง settings surface ไว้ใน page-owned Volt/Flux pattern ต่อไปก่อน และลงทุนกับการทำ frontend contract ให้เป็น app-owned system ผ่าน modular CSS, shared layout rules, และ Flux restyling แทนการย้าย settings ทุกหน้ามาเป็น explicit Livewire classes ทันที

Rationale: settings pages ปัจจุบันมีขอบเขตจำกัดและผูกกับ Fortify/Flux behavior อยู่พอสมควร การย้ายทั้งหมดตอนนี้จะ churn สูงกว่า value ที่ได้ ขณะที่ปัญหาเร่งด่วนจริงคือ frontend contract ยังไม่ modular และ Flux-specific styling leakage ยังทำให้หน้าตา drift ได้ง่ายกว่า

Impact: app.css, resources/css/app/**, settings/auth views, 22_Architecture_Boundary_and_Execution_Standards, 26_Architecture_Debt_Roadmap และ 29_Refactor_Execution_Pack ต้องสะท้อนว่า Phase 4 รอบนี้โฟกัส modular CSS + shared presentation contract ก่อน ไม่ใช่ settings class migration เต็มรูปแบบ

**D-028 | Locked**

Decision: ใน Phase 5 ของ master refactor program ให้แยก `demo seed data` ออกจาก `automated test correctness` อย่างชัดเจน โดยถือว่า `DatabaseSeeder` มีหน้าที่สร้างข้อมูลสาธิตและ local bootstrap narrative ส่วน feature/application tests ต้องสร้าง state ที่ต้องใช้ผ่าน factory และ scenario helper ของตัวเองเป็นหลัก

Rationale: การผูก tests เข้ากับชื่อผู้ใช้, title, และ narrative demo records จาก DatabaseSeeder ทำให้ test suite เปราะและทำให้การแก้หรือขยาย demo data กระทบ regression โดยไม่จำเป็น การแยกสองหน้าที่นี้ทำให้ seed data เปลี่ยนได้โดยไม่ทำให้ behavior tests พังตาม

Impact: model factories, test helpers, Pest bootstrap, feature/application tests, 06_Data_Definition, 26_Architecture_Debt_Roadmap และ 29_Refactor_Execution_Pack ต้องสะท้อนว่า test correctness ไม่ได้ขึ้นกับ seeded narrative records อีกต่อไป

**D-029 | Locked**

Decision: สำหรับ Phase 7 ให้ใช้ `Pest Browser + Playwright` เป็น browser-regression stack หลักของ repository แทน Laravel Dusk และเริ่มต้นด้วย smoke suite ขนาดเล็กสำหรับ guest, admin, และ staff flows ก่อน

Rationale: repository ใช้ Pest เป็นแกนอยู่แล้ว การเลือก Pest Browser ทำให้ browser-level regression อยู่ใน test vocabulary เดียวกับ feature/application tests, ใช้ factory/scenario helpers เดิมได้, และมี churn ต่ำกว่าการนำ Dusk เข้ามาเพิ่มอีกหนึ่ง test framework

Impact: composer/package dependencies, Pest bootstrap, `.gitignore`, GitHub Actions workflow, README, 26_Architecture_Debt_Roadmap และ 29_Refactor_Execution_Pack ต้องสะท้อน stack นี้ให้ตรงกัน รวมถึงต้องระบุอย่างตรงไปตรงมาว่า local execution บน Linux/WSL ยังต้องมี Playwright system dependencies ครบก่อนจึงจะรัน smoke suite ได้

**D-030 | Locked**

Decision: หลังการส่งมอบ `WF1 Scoped Daily Operations Runtime` ความจริงปัจจุบันของ repository คือ `/checklists/runs/today` รองรับ scope-aware runtime แล้ว และ `ChecklistScope` ไม่ใช่ metadata-only อีกต่อไป แต่เป็น operational runtime dimension สำหรับ opening / midday / closing lanes

Rationale: baseline เดิมแบบ singular runtime ถูกต้องในช่วงก่อน WF1 เท่านั้น แต่หลังจาก persistence, staff runtime entry, dashboard signals, และ template administration ถูกย้ายมาอยู่บน per-scope truth แล้ว การปล่อยให้ canonical docs ยังพูดว่า scope เป็นแค่ metadata จะทำให้ repository truth แตกอีกครั้ง

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 22_Architecture_Boundary_and_Execution_Standards, 24_Domain_Normalization_Design, README, และเอกสาร execution packs ของ WF1 ต้องสะท้อน runtime truth ใหม่นี้ให้ตรงกัน

**D-031 | Locked**

Decision: invariant ของ checklist template activation ถูกย้ายจาก `exactly one active template globally` ไปเป็น `at most one active template per scope` และ admin/dashboard/staff surfaces ต้องอธิบายผลกระทบในภาษาเดียวกันทั้งหมด

Rationale: ถ้า persistence เปลี่ยนแล้วแต่ presentation contract ยังพูดเหมือน activation เป็น global replacement หรือ daily runtime ยังพูดเหมือนมี checklist lane เดียว ระบบจะกลายเป็น half-migrated truth ซึ่งอันตรายกว่าการยังไม่เริ่มเปลี่ยนตั้งแต่แรก

Impact: SaveChecklistTemplate action, daily runtime entry, dashboard assembly, template governance surfaces, tests, และ canonical docs ทุกฉบับที่กล่าวถึง active template rule ต้องสะท้อน per-scope ownership model นี้ให้ตรงกัน

**D-032 | Locked**

Decision: หลังการส่งมอบ `WF2 Incident Ownership Lite` ความจริงปัจจุบันของ repository คือ incident สามารถมี owner แบบ optional และ `follow_up_due_at` แบบ optional ได้แล้ว โดย accountability model นี้เป็น lightweight coordination truth สำหรับ management surfaces ไม่ใช่ enterprise assignment system

Rationale: หลังจาก persistence, queue filters, incident detail lane, และ dashboard ownership pressure ถูกส่งลงระบบแล้ว การปล่อยให้ canonical docs ยังพูดว่า incident มีเพียง status กับ activity trail จะทำให้ product truth กลับไปแตกอีกครั้ง และเปิดทางให้ทีมตีความเกิน scope ไปถึง reassignment, SLA, หรือ notification โดยไม่มี decision รองรับ

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 22_Architecture_Boundary_and_Execution_Standards, 24_Domain_Normalization_Design, 26_Architecture_Debt_Roadmap, README, และ execution packs ของ WF2 ต้องสะท้อนว่า incident accountability ตอนนี้เป็น first-class product truth แต่ยังคง intentionally lightweight

**D-033 | Locked**

Decision: หลังการส่งมอบ `WF3 User Administration Lite` ความจริงปัจจุบันของ repository คือ user lifecycle เป็น admin-only product capability ภายใน app shell แล้ว ผ่าน route family `/users`, `/users/create`, และ `/users/{user}/edit` โดยยังคง intentionally lightweight และไม่ขยายไปเป็น IAM platform

Rationale: หลังจาก WF3-A/B land แล้ว product สามารถ provision และ update accounts จากในระบบจริงได้ การปล่อยให้ canonical docs ยังพูดว่าระบบ “มี role/is_active อยู่ใน code แต่ยังไม่มี user administration surface” จะทำให้ repository truth แตกอีกครั้ง

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 22_Architecture_Boundary_and_Execution_Standards, 24_Domain_Normalization_Design, 26_Architecture_Debt_Roadmap, README, และ execution packs ของ WF3 ต้องสะท้อนว่า user lifecycle ตอนนี้เป็น first-class product truth แล้ว

**D-034 | Locked**

Decision: strategy ของ WF3 สำหรับ password handling ให้คงเป็น explicit internal set/reset โดย admin ต่อไป และห้ามตีความ flow นี้เป็น invitation, email onboarding, หรือ external-reset orchestration โดยปริยาย

Rationale: deterministic กว่า, ไม่พึ่ง delivery infra, และสอดคล้องกับ A-lite scope ที่ต้องจบจริงโดยไม่ over-engineer

Impact: user-administration surfaces, README, Current State, และ canonical docs ทั้งชุดต้องพูดตรงกันว่า password handling ของ WF3 เป็น internal explicit path

**D-035 | Locked**

Decision: guard rails ของ WF3 ต้องอยู่ใน application layer และล็อกอย่างน้อย 3 เรื่อง:

- self-deactivation ต้องถูกปฏิเสธ
- self-demotion ออกจาก admin role ต้องถูกปฏิเสธ
- ระบบต้องมี active admin เหลืออยู่เสมออย่างน้อย 1 account

Rationale: เมื่อ user administration กลายเป็น capability จริงใน app shell แล้ว ความเสี่ยงหลักคือ accidental self-lockout หรือ admin coverage collapse ซึ่งไม่ควรถูกปล่อยให้เป็นแค่ Blade-level affordance problem

Impact: UpdateManagedUser action, admin user surface, regression tests, System Spec, Architecture Boundary, Domain Normalization, Current State, และ README ต้องสะท้อน lifecycle safety contract เดียวกัน

**D-036 | Locked**

Decision: หลังการส่งมอบ `WF4 Operational History and Run Archive` ความจริงปัจจุบันของ repository คือ product มี lightweight operational history layer แล้วผ่าน `/checklists/history` และ `/incidents/history` โดยตั้งใจให้เป็น review surface ไม่ใช่ analytics/reporting subsystem

Rationale: หลังจาก WF4-A/B/C ลงระบบแล้ว repository สามารถ review งานที่เกิดขึ้นจริงในช่วงที่ผ่านมาได้แล้ว หาก canonical docs ยังมอง history เป็นเพียง planning artifact หรือปล่อยให้คนตีความต่อไปเป็น exports/KPIs/reassignment reporting จะทำให้ truth แตกอีกครั้งและเสี่ยงต่อ scope drift

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 22_Architecture_Boundary_and_Execution_Standards, 24_Domain_Normalization_Design, 26_Architecture_Debt_Roadmap, README, และ WF4 execution packs ต้องสะท้อน operational-history-as-review truth นี้ให้ตรงกัน

**D-037 | Locked**

Decision: หลังการส่งมอบ `WF5 Dashboard Workboard Upgrade` ความจริงปัจจุบันของ repository คือ `/dashboard` ไม่ใช่เพียง summary surface แล้ว แต่เป็น today-first management workboard ที่ประกอบจาก scope lane truth, incident ownership pressure, และ lightweight recent operational context ที่มีอยู่จริงในระบบ

Rationale: หลังจาก WF5-A/B/C ลงระบบแล้ว dashboard สามารถตอบคำถามเชิงปฏิบัติการได้ว่า "วันนี้ควรดูตรงไหนก่อน", "งานยังค้างใน lane ไหน", "ownership pressure อยู่ตรงไหน", และ "ช่วงล่าสุดดูนิ่งหรือยังมี carryover" หาก canonical docs ยังเรียก dashboard ว่าเป็นเพียง summary หรือ overview จะทำให้ repository truth แตกอีกครั้ง และเปิดทางให้ทีมตีความต่อไปเป็น analytics product โดยไม่มี decision รองรับ

Impact: 02_System_Spec, 04_Current_State, 06_Data_Definition, 22_Architecture_Boundary_and_Execution_Standards, 24_Domain_Normalization_Design, 26_Architecture_Debt_Roadmap, README, และ WF5 execution packs ต้องสะท้อน dashboard-workboard truth นี้ให้ตรงกัน
