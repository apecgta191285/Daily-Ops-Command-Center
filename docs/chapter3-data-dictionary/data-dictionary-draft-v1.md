# Data Dictionary Draft v1

## users

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสผู้ใช้ | BIGINT / auto-increment | PK | N |
| 2 | name | ชื่อผู้ใช้ | VARCHAR(255) | - | N |
| 3 | email | อีเมลสำหรับเข้าสู่ระบบ | VARCHAR(255) | Unique | N |
| 4 | email_verified_at | วันและเวลาที่ยืนยันอีเมล | TIMESTAMP | - | Y |
| 5 | password | รหัสผ่านที่จัดเก็บแบบเข้ารหัส | VARCHAR(255) | hashed ใน Model | N |
| 6 | remember_token | โทเคนสำหรับจดจำการเข้าสู่ระบบ | VARCHAR(100) | Laravel authentication token | Y |
| 7 | created_at | วันและเวลาที่สร้างข้อมูลผู้ใช้ | TIMESTAMP | Laravel timestamps | Y |
| 8 | updated_at | วันและเวลาที่ปรับปรุงข้อมูลผู้ใช้ | TIMESTAMP | Laravel timestamps | Y |
| 9 | two_factor_secret | ข้อมูลลับสำหรับการยืนยันตัวตนแบบสองขั้นตอน | TEXT | Hidden ใน Model | Y |
| 10 | two_factor_recovery_codes | รหัสกู้คืนสำหรับการยืนยันตัวตนแบบสองขั้นตอน | TEXT | Hidden ใน Model | Y |
| 11 | two_factor_confirmed_at | วันและเวลาที่ยืนยันการใช้งานสองขั้นตอน | TIMESTAMP | - | Y |
| 12 | role | บทบาทของผู้ใช้ในระบบ | VARCHAR(255) | Default `staff`; canonical values: `admin`, `supervisor`, `staff` | N |
| 13 | is_active | สถานะการเปิดใช้งานบัญชีผู้ใช้ | BOOLEAN | Default `true` | N |

## rooms

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสห้อง | BIGINT / auto-increment | PK | N |
| 2 | name | ชื่อห้อง | VARCHAR(255) | - | N |
| 3 | code | รหัสประจำห้อง | VARCHAR(255) | Unique | N |
| 4 | description | รายละเอียดหรือคำอธิบายห้อง | TEXT | - | Y |
| 5 | is_active | สถานะการเปิดใช้งานห้อง | BOOLEAN | Default `true` | N |
| 6 | created_at | วันและเวลาที่สร้างข้อมูลห้อง | TIMESTAMP | Laravel timestamps | Y |
| 7 | updated_at | วันและเวลาที่ปรับปรุงข้อมูลห้อง | TIMESTAMP | Laravel timestamps | Y |

## checklist_templates

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสแม่แบบ checklist | BIGINT / auto-increment | PK | N |
| 2 | title | ชื่อแม่แบบ checklist | VARCHAR(255) | Unique: `checklist_templates_title_unique` | N |
| 3 | description | รายละเอียดของแม่แบบ checklist | TEXT | - | Y |
| 4 | scope | ขอบเขตหรือช่วงงานของแม่แบบ checklist | VARCHAR(255) | Canonical values: เปิดห้อง, ตรวจระหว่างวัน, ปิดห้อง | Y |
| 5 | is_active | สถานะการเปิดใช้งานแม่แบบ checklist | BOOLEAN | Default `true`; SQLite partial unique index: active template per `scope` | N |
| 6 | created_at | วันและเวลาที่สร้างแม่แบบ checklist | TIMESTAMP | Laravel timestamps | Y |
| 7 | updated_at | วันและเวลาที่ปรับปรุงแม่แบบ checklist | TIMESTAMP | Laravel timestamps | Y |

## checklist_items

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสรายการตรวจ | BIGINT / auto-increment | PK | N |
| 2 | checklist_template_id | รหัสแม่แบบ checklist ที่รายการตรวจสังกัด | BIGINT | FK to `checklist_templates.id`; cascadeOnDelete; Unique pair with `sort_order` | N |
| 3 | title | ชื่อรายการตรวจ | VARCHAR(255) | - | N |
| 4 | description | รายละเอียดของรายการตรวจ | TEXT | - | Y |
| 5 | group_label | ชื่อกลุ่มของรายการตรวจ | VARCHAR(255) | - | Y |
| 6 | sort_order | ลำดับการแสดงผลของรายการตรวจ | UNSIGNED INTEGER | Unique pair with `checklist_template_id` | N |
| 7 | is_required | สถานะว่ารายการตรวจเป็นรายการบังคับหรือไม่ | BOOLEAN | Default `true` | N |
| 8 | created_at | วันและเวลาที่สร้างรายการตรวจ | TIMESTAMP | Laravel timestamps | Y |
| 9 | updated_at | วันและเวลาที่ปรับปรุงรายการตรวจ | TIMESTAMP | Laravel timestamps | Y |

## checklist_runs

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสรอบการตรวจ checklist | BIGINT / auto-increment | PK | N |
| 2 | checklist_template_id | รหัสแม่แบบ checklist ที่ใช้สร้างรอบการตรวจ | BIGINT | FK to `checklist_templates.id`; restrictOnDelete; Unique with `room_id`, `run_date`, `created_by` | N |
| 3 | room_id | รหัสห้องที่เกี่ยวข้องกับรอบการตรวจ | BIGINT | FK to `rooms.id`; restrictOnDelete; final migration state is not nullable | N |
| 4 | run_date | วันที่ของรอบการตรวจ | DATE | Unique with `checklist_template_id`, `room_id`, `created_by` | N |
| 5 | assigned_team_or_scope | ทีมหรือช่วงงานที่กำหนดให้รอบการตรวจ | VARCHAR(255) | Indexed with `run_date`, `submitted_at` | Y |
| 6 | created_by | รหัสผู้ใช้ที่สร้างรอบการตรวจ | BIGINT | FK to `users.id`; restrictOnDelete; Unique with `checklist_template_id`, `room_id`, `run_date` | N |
| 7 | submitted_at | วันและเวลาที่ส่งรอบการตรวจ | TIMESTAMP | Indexed with `run_date` and scope | Y |
| 8 | submitted_by | รหัสผู้ใช้ที่ส่งรอบการตรวจ | BIGINT | FK to `users.id`; restrictOnDelete | Y |
| 9 | created_at | วันและเวลาที่สร้างรอบการตรวจ | TIMESTAMP | Laravel timestamps | Y |
| 10 | updated_at | วันและเวลาที่ปรับปรุงรอบการตรวจ | TIMESTAMP | Laravel timestamps | Y |

## checklist_run_items

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสผลการตรวจของรายการ checklist | BIGINT / auto-increment | PK | N |
| 2 | checklist_run_id | รหัสรอบการตรวจที่ผลรายการนี้สังกัด | BIGINT | FK to `checklist_runs.id`; cascadeOnDelete | N |
| 3 | checklist_item_id | รหัสรายการตรวจต้นแบบ | BIGINT | FK to `checklist_items.id`; restrictOnDelete | N |
| 4 | result | ผลการตรวจของรายการ checklist | VARCHAR(255) | Canonical values: `Done`, `Not Done` | Y |
| 5 | note | หมายเหตุประกอบผลการตรวจ | TEXT | - | Y |
| 6 | checked_by | รหัสผู้ใช้ที่ตรวจรายการ | BIGINT | FK to `users.id`; restrictOnDelete | Y |
| 7 | checked_at | วันและเวลาที่ตรวจรายการ | TIMESTAMP | - | Y |
| 8 | created_at | วันและเวลาที่สร้างผลรายการตรวจ | TIMESTAMP | Laravel timestamps | Y |
| 9 | updated_at | วันและเวลาที่ปรับปรุงผลรายการตรวจ | TIMESTAMP | Laravel timestamps | Y |

## incidents

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสเหตุผิดปกติ | BIGINT / auto-increment | PK | N |
| 2 | title | ชื่อเหตุผิดปกติ | VARCHAR(120) | Max length 120 | N |
| 3 | category | หมวดหมู่ของเหตุผิดปกติ | VARCHAR(255) | Canonical values: อุปกรณ์คอมพิวเตอร์, เครือข่าย, ความสะอาด, ความปลอดภัย, สภาพแวดล้อม, อื่น ๆ | N |
| 4 | severity | ระดับความรุนแรงของเหตุผิดปกติ | VARCHAR(255) | Canonical values: `Low`, `Medium`, `High` | N |
| 5 | room_id | รหัสห้องที่เกี่ยวข้องกับเหตุผิดปกติ | BIGINT | FK to `rooms.id`; restrictOnDelete; final migration state is not nullable | N |
| 6 | status | สถานะของเหตุผิดปกติ | VARCHAR(255) | Default `Open`; canonical values: `Open`, `In Progress`, `Resolved` | N |
| 7 | description | รายละเอียดของเหตุผิดปกติ | TEXT | - | N |
| 8 | equipment_reference | ข้อมูลอ้างอิงอุปกรณ์ที่เกี่ยวข้อง | VARCHAR(120) | Max length 120 | Y |
| 9 | attachment_path | ที่อยู่ไฟล์แนบหรือหลักฐานประกอบ | VARCHAR(255) | - | Y |
| 10 | created_by | รหัสผู้ใช้ที่แจ้งเหตุผิดปกติ | BIGINT | FK to `users.id`; restrictOnDelete | N |
| 11 | owner_id | รหัสผู้ใช้ที่รับผิดชอบเหตุผิดปกติ | BIGINT | FK to `users.id`; nullOnDelete | Y |
| 12 | follow_up_due_at | วันที่ควรติดตามเหตุผิดปกติ | DATE | Operational target date; not SLA timestamp | Y |
| 13 | resolved_at | วันและเวลาที่เหตุผิดปกติได้รับการแก้ไขเสร็จ | TIMESTAMP | Indexed | Y |
| 14 | created_at | วันและเวลาที่สร้างข้อมูลเหตุผิดปกติ | TIMESTAMP | Indexed; Laravel timestamps | Y |
| 15 | updated_at | วันและเวลาที่ปรับปรุงข้อมูลเหตุผิดปกติ | TIMESTAMP | Laravel timestamps | Y |

## incident_activities

| No | Attribute | Description | Type | Constraint | Null (Y/N) |
| -- | --------- | ----------- | ---- | ---------- | ---------- |
| 1 | id | รหัสกิจกรรมของเหตุผิดปกติ | BIGINT / auto-increment | PK | N |
| 2 | incident_id | รหัสเหตุผิดปกติที่กิจกรรมสังกัด | BIGINT | FK to `incidents.id`; cascadeOnDelete | N |
| 3 | action_type | ประเภทกิจกรรมของเหตุผิดปกติ | VARCHAR(255) | Canonical values: `created`, `status_changed` | N |
| 4 | summary | สรุปรายละเอียดของกิจกรรม | TEXT | - | Y |
| 5 | actor_id | รหัสผู้บันทึกกิจกรรมเหตุผิดปกติ | BIGINT | FK to `users.id`; restrictOnDelete | N |
| 6 | created_at | วันและเวลาที่บันทึกกิจกรรม | TIMESTAMP | Append-only log; no `updated_at` | Y |

## Notes for Review

- Fields included in this Data Dictionary but omitted from ERD for readability: `users.email_verified_at`, `users.remember_token`, `users.two_factor_secret`, `users.two_factor_recovery_codes`, and `users.two_factor_confirmed_at`.
- Enum/canonical values included from schema truth and fallback enum verification: `users.role`, `checklist_templates.scope`, `checklist_run_items.result`, `incidents.category`, `incidents.severity`, `incidents.status`, and `incident_activities.action_type`.
- `checklist_runs.room_id` and `incidents.room_id` use the final migration state: not nullable and `restrictOnDelete`.
- `incident_activities` is append-only and intentionally has no `updated_at`.
- No field in this draft is marked “ต้องตรวจสอบเพิ่ม”.
