# Schema Truth Report for Chapter 3 Data Dictionary

Source priority used:
1. `database/migrations` = highest authority
2. `app/Models` = supporting evidence for fillable, casts, and relationships
3. Current ERD files = visual reference only, not schema authority

Scope tables:
- `users`
- `rooms`
- `checklist_templates`
- `checklist_items`
- `checklist_runs`
- `checklist_run_items`
- `incidents`
- `incident_activities`

## users

### Migration evidence
- migration file name:
  - `0001_01_01_000000_create_users_table.php`
  - `2025_08_14_170933_add_two_factor_columns_to_users_table.php`
  - `2026_04_05_000001_add_role_and_is_active_to_users_table.php`
- schema summary:
  - Base Laravel `users` table with authentication fields.
  - Adds Fortify two-factor fields.
  - Adds application role and active-status fields.
  - `email` is unique.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสผู้ใช้ | Primary key |
| name | string | no | - | normal | - | Fillable | ชื่อผู้ใช้ | - |
| email | string | no | - | normal | - | Fillable | อีเมล | Unique |
| email_verified_at | timestamp | yes | - | normal | - | Cast: `datetime` | วันที่ยืนยันอีเมล | Not shown in current ERD |
| password | string | no | - | normal | - | Fillable; cast: `hashed`; hidden | รหัสผ่าน | Stored as hashed password |
| remember_token | rememberToken | yes | - | normal | - | Hidden | โทเคนจดจำการเข้าสู่ระบบ | Not shown in current ERD |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Created by Laravel timestamps |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |
| two_factor_secret | text | yes | - | normal | - | Hidden | ข้อมูลลับสำหรับยืนยันสองขั้นตอน | Not shown in current ERD |
| two_factor_recovery_codes | text | yes | - | normal | - | Hidden | รหัสกู้คืนการยืนยันสองขั้นตอน | Not shown in current ERD |
| two_factor_confirmed_at | timestamp | yes | - | normal | - | Not fillable; no explicit cast | วันที่ยืนยันการใช้สองขั้นตอน | Not shown in current ERD |
| role | string | no | `'staff'` | normal | - | Fillable; cast: `UserRole::class` | บทบาทผู้ใช้ | Canonical role values noted in migration: `admin`, `supervisor`, `staff` |
| is_active | boolean | no | `true` | normal | - | Fillable; cast: `boolean` | สถานะการใช้งาน | - |

## rooms

### Migration evidence
- migration file name:
  - `2026_04_22_000002_create_rooms_table.php`
- schema summary:
  - Room master table for room context.
  - `code` is unique.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสห้อง | Primary key |
| name | string | no | - | normal | - | Fillable | ชื่อห้อง | - |
| code | string | no | - | normal | - | Fillable | รหัสห้อง | Unique |
| description | text | yes | - | normal | - | Fillable | รายละเอียดห้อง | - |
| is_active | boolean | no | `true` | normal | - | Fillable; cast: `boolean` | สถานะการใช้งานห้อง | - |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Created by Laravel timestamps |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |

## checklist_templates

### Migration evidence
- migration file name:
  - `2026_04_05_000002_create_checklist_templates_table.php`
  - `2026_04_11_000008_harden_checklist_template_invariants.php`
  - `2026_04_18_000010_scope_active_checklist_templates.php`
- schema summary:
  - Checklist template master table.
  - `title` is unique.
  - SQLite-only partial unique index enforces one active template per `scope`.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสแม่แบบรายการตรวจ | Primary key |
| title | string | no | - | normal | - | Fillable | ชื่อแม่แบบรายการตรวจ | Unique: `checklist_templates_title_unique` |
| description | text | yes | - | normal | - | Fillable | รายละเอียดแม่แบบ | - |
| scope | string | yes | - | normal | - | Fillable; cast: `ChecklistScope::class` | ช่วงงานหรือขอบเขตของแม่แบบ | Migration comment indicates fixed taxonomy; exact values should be taken from enum/spec if needed |
| is_active | boolean | no | `true` | normal | - | Fillable; cast: `boolean` | สถานะแม่แบบที่ใช้งาน | SQLite partial unique index on `scope` where `is_active = 1` |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Created by Laravel timestamps |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |

## checklist_items

### Migration evidence
- migration file name:
  - `2026_04_05_000003_create_checklist_items_table.php`
  - `2026_04_16_000009_add_group_label_to_checklist_items_table.php`
- schema summary:
  - Checklist item rows under a checklist template.
  - `checklist_template_id` references `checklist_templates.id`.
  - Unique constraint on `checklist_template_id` + `sort_order`.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสรายการตรวจ | Primary key |
| checklist_template_id | foreignId | no | - | FK | `checklist_templates.id` | Fillable | รหัสแม่แบบรายการตรวจ | `cascadeOnDelete()` |
| title | string | no | - | normal | - | Fillable | ชื่อรายการตรวจ | - |
| description | text | yes | - | normal | - | Fillable | รายละเอียดรายการตรวจ | - |
| group_label | string | yes | - | normal | - | Fillable | กลุ่มรายการตรวจ | Added after `description` |
| sort_order | unsignedInteger | no | - | normal | - | Fillable; cast: `integer` | ลำดับการแสดงผล | Unique with `checklist_template_id` |
| is_required | boolean | no | `true` | normal | - | Fillable; cast: `boolean` | สถานะรายการบังคับ | - |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Created by Laravel timestamps |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |

## checklist_runs

### Migration evidence
- migration file name:
  - `2026_04_05_000004_create_checklist_runs_table.php`
  - `2026_04_22_000003_add_room_context_to_runs_and_incidents.php`
  - `2026_04_22_000004_adjust_checklist_run_uniqueness_for_room_context.php`
  - `2026_04_23_000005_add_selective_query_hygiene_indexes.php`
  - `2026_04_23_000006_add_history_and_scope_board_indexes.php`
  - `2026_04_24_000001_restore_room_aware_checklist_run_uniqueness.php`
  - `2026_04_24_000002_enforce_room_context_not_null.php`
  - `2026_04_24_000003_align_room_delete_semantics.php`
- schema summary:
  - Daily checklist run table.
  - References checklist template, room, creator user, and optional submitter user.
  - Final uniqueness is `checklist_template_id`, `room_id`, `run_date`, `created_by`.
  - `room_id` was introduced nullable, later enforced non-null, and final delete behavior is `restrictOnDelete()`.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสรอบการตรวจ | Primary key |
| checklist_template_id | foreignId | no | - | FK | `checklist_templates.id` | Fillable | รหัสแม่แบบรายการตรวจ | `restrictOnDelete()` |
| room_id | foreignId | no | - | FK | `rooms.id` | Fillable | รหัสห้อง | Added later; final state is non-null and `restrictOnDelete()` |
| run_date | date | no | - | normal | - | Fillable; cast: `date` | วันที่ของรอบการตรวจ | Part of final unique constraint |
| assigned_team_or_scope | string | yes | - | normal | - | Fillable | ทีมหรือช่วงงานที่กำหนด | Indexed with `run_date`, `submitted_at` |
| created_by | foreignId | no | - | FK | `users.id` | Fillable | ผู้สร้างรอบการตรวจ | `restrictOnDelete()`; part of final unique constraint |
| submitted_at | timestamp | yes | - | normal | - | Fillable; cast: `datetime` | วันที่ส่งรอบการตรวจ | Indexed with `run_date` and scope |
| submitted_by | foreignId | yes | - | FK | `users.id` | Fillable | ผู้ส่งรอบการตรวจ | `restrictOnDelete()` |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Created by Laravel timestamps |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |

## checklist_run_items

### Migration evidence
- migration file name:
  - `2026_04_05_000005_create_checklist_run_items_table.php`
- schema summary:
  - Result rows for checklist items within a checklist run.
  - References checklist run, checklist item, and optional checker user.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสผลรายการตรวจ | Primary key |
| checklist_run_id | foreignId | no | - | FK | `checklist_runs.id` | Fillable | รหัสรอบการตรวจ | `cascadeOnDelete()` |
| checklist_item_id | foreignId | no | - | FK | `checklist_items.id` | Fillable | รหัสรายการตรวจ | `restrictOnDelete()` |
| result | string | yes | - | normal | - | Fillable | ผลการตรวจ | Migration comment notes canonical values: `Done`, `Not Done` |
| note | text | yes | - | normal | - | Fillable | หมายเหตุผลการตรวจ | - |
| checked_by | foreignId | yes | - | FK | `users.id` | Fillable | ผู้ตรวจรายการ | `restrictOnDelete()` |
| checked_at | timestamp | yes | - | normal | - | Fillable; cast: `datetime` | วันที่ตรวจรายการ | - |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Created by Laravel timestamps |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |

## incidents

### Migration evidence
- migration file name:
  - `2026_04_05_000006_create_incidents_table.php`
  - `2026_04_19_000001_add_owner_and_follow_up_to_incidents_table.php`
  - `2026_04_22_000003_add_room_context_to_runs_and_incidents.php`
  - `2026_04_23_000005_add_selective_query_hygiene_indexes.php`
  - `2026_04_23_000006_add_history_and_scope_board_indexes.php`
  - `2026_04_24_000002_enforce_room_context_not_null.php`
  - `2026_04_24_000003_align_room_delete_semantics.php`
- schema summary:
  - Incident report table.
  - References room, creator user, and optional owner user.
  - `room_id` was introduced nullable, later enforced non-null, and final delete behavior is `restrictOnDelete()`.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสเหตุผิดปกติ | Primary key |
| title | string(120) | no | - | normal | - | Fillable | ชื่อเหตุผิดปกติ | Max length 120 |
| category | string | no | - | normal | - | Fillable; cast: `IncidentCategory::class` | หมวดหมู่เหตุผิดปกติ | Canonical values are noted in migration comments; exact Thai enum values should be verified from enum/spec if needed |
| severity | string | no | - | normal | - | Fillable; cast: `IncidentSeverity::class` | ระดับความรุนแรง | Migration comment notes `Low`, `Medium`, `High` |
| room_id | foreignId | no | - | FK | `rooms.id` | Fillable | รหัสห้องที่เกี่ยวข้อง | Added later; final state is non-null and `restrictOnDelete()` |
| status | string | no | `'Open'` | normal | - | Fillable; cast: `IncidentStatus::class` | สถานะเหตุผิดปกติ | Migration comment notes `Open`, `In Progress`, `Resolved` |
| description | text | no | - | normal | - | Fillable | รายละเอียดเหตุผิดปกติ | - |
| equipment_reference | string(120) | yes | - | normal | - | Fillable | ข้อมูลอ้างอิงอุปกรณ์ | Added later |
| attachment_path | string | yes | - | normal | - | Fillable | ที่อยู่ไฟล์แนบ | - |
| created_by | foreignId | no | - | FK | `users.id` | Fillable | ผู้แจ้งเหตุผิดปกติ | `restrictOnDelete()` |
| owner_id | foreignId | yes | - | FK | `users.id` | Fillable | ผู้รับผิดชอบเหตุผิดปกติ | `nullOnDelete()` |
| follow_up_due_at | date | yes | - | normal | - | Fillable; cast: `date` | วันที่ควรติดตาม | Migration note says operational target date, not SLA timestamp |
| resolved_at | timestamp | yes | - | normal | - | Fillable; cast: `datetime` | วันที่ปิดหรือแก้ไขเสร็จ | Indexed |
| created_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่สร้างข้อมูล | Indexed |
| updated_at | timestamp from `timestamps()` | yes | - | normal | - | Not fillable; no explicit cast | วันที่ปรับปรุงข้อมูล | Created by Laravel timestamps |

## incident_activities

### Migration evidence
- migration file name:
  - `2026_04_05_000007_create_incident_activities_table.php`
- schema summary:
  - Append-only incident activity log.
  - Has `created_at` only and no `updated_at`.
  - References incident and actor user.

### Columns
| Column | Type from migration | Nullable | Default | Key | FK reference | Model fillable/cast evidence | Thai meaning | Notes |
|---|---|---|---|---|---|---|---|---|
| id | id | no | - | PK | - | Not fillable; no cast | รหัสกิจกรรมเหตุผิดปกติ | Primary key |
| incident_id | foreignId | no | - | FK | `incidents.id` | Fillable | รหัสเหตุผิดปกติ | `cascadeOnDelete()` |
| action_type | string | no | - | normal | - | Fillable | ประเภทกิจกรรม | Migration comment notes canonical values: `created`, `status_changed` |
| summary | text | yes | - | normal | - | Fillable | สรุปกิจกรรม | - |
| actor_id | foreignId | no | - | FK | `users.id` | Fillable | ผู้บันทึกกิจกรรม | `restrictOnDelete()` |
| created_at | timestamp | yes | - | normal | - | Fillable; cast: `datetime` | วันที่บันทึกกิจกรรม | Append-only log; model sets `$timestamps = false` |

## Relationship Summary

Only schema/model-supported relationships:

- `users -> checklist_runs via created_by` — supported by migration FK and `User::checklistRuns()`, `ChecklistRun::creator()`.
- `users -> checklist_runs via submitted_by` — supported by migration FK and `ChecklistRun::submitter()`; inverse `User` relation is not defined but child-side model evidence exists.
- `users -> checklist_run_items via checked_by` — supported by migration FK and `ChecklistRunItem::checker()`; inverse `User` relation is not defined but child-side model evidence exists.
- `rooms -> checklist_runs via room_id` — supported by migration FK and `Room::checklistRuns()`, `ChecklistRun::room()`.
- `checklist_templates -> checklist_items via checklist_template_id` — supported by migration FK and `ChecklistTemplate::items()`, `ChecklistItem::template()`.
- `checklist_templates -> checklist_runs via checklist_template_id` — supported by migration FK and `ChecklistTemplate::runs()`, `ChecklistRun::template()`.
- `checklist_runs -> checklist_run_items via checklist_run_id` — supported by migration FK and `ChecklistRun::items()`, `ChecklistRunItem::run()`.
- `checklist_items -> checklist_run_items via checklist_item_id` — supported by migration FK and `ChecklistRunItem::checklistItem()`; inverse `ChecklistItem` relation is not defined but child-side model evidence exists.
- `rooms -> incidents via room_id` — supported by migration FK and `Room::incidents()`, `Incident::room()`.
- `users -> incidents via created_by` — supported by migration FK and `User::incidents()`, `Incident::creator()`.
- `users -> incidents via owner_id` — supported by migration FK and `User::ownedIncidents()`, `Incident::owner()`.
- `incidents -> incident_activities via incident_id` — supported by migration FK and `Incident::activities()`, `IncidentActivity::incident()`.
- `users -> incident_activities via actor_id` — supported by migration FK and `IncidentActivity::actor()`; inverse `User` relation is not defined but child-side model evidence exists.

## Mismatch Report

| Area | Expected from migration/model | Found in ERD/model | Status | Required action |
|---|---|---|---|---|
| `users.email_verified_at` | Migration includes nullable `timestamp`; User model casts it to `datetime`. | Current ERD does not show this column. | Mismatch: ERD incomplete | Data Dictionary should include this column from migration truth, or explicitly decide to omit auth-support fields from thesis scope. |
| `users.remember_token` | Migration includes `rememberToken`; User model hides it. | Current ERD does not show this column. | Mismatch: ERD incomplete | Data Dictionary should include this column if representing full `users` schema. |
| `users.two_factor_secret` | Migration includes nullable `text`; User model hides it. | Current ERD does not show this column. | Mismatch: ERD incomplete | Data Dictionary should include this column if representing full `users` schema. |
| `users.two_factor_recovery_codes` | Migration includes nullable `text`; User model hides it. | Current ERD does not show this column. | Mismatch: ERD incomplete | Data Dictionary should include this column if representing full `users` schema. |
| `users.two_factor_confirmed_at` | Migration includes nullable `timestamp`. | Current ERD does not show this column. | Mismatch: ERD incomplete | Data Dictionary should include this column if representing full `users` schema. |
| `incident_activities.actor_id` cardinality | Migration defines non-null `foreignId('actor_id')->constrained('users')->restrictOnDelete()`. | Current ERD visual uses `0..1` on the users side for actor relationship. | Mismatch: ERD cardinality likely inaccurate | ERD should represent each activity as requiring one actor, unless the notation is intentionally parent-side optionality; clarify before final thesis figure/table. |
| `checklist_runs.room_id` nullability/delete behavior | Final migrations make `room_id` non-null and `restrictOnDelete()`. | Earlier migration introduced `room_id` nullable with `nullOnDelete()`, but later migrations change it; ERD only shows the relationship visually. | No ERD column mismatch; migration history needs final-state interpretation | Data Dictionary should use final migration state: non-null, restrict delete. |
| `incidents.room_id` nullability/delete behavior | Final migrations make `room_id` non-null and `restrictOnDelete()`. | Earlier migration introduced `room_id` nullable with `nullOnDelete()`, but later migrations change it; ERD only shows the relationship visually. | No ERD column mismatch; migration history needs final-state interpretation | Data Dictionary should use final migration state: non-null, restrict delete. |
| `checklist_runs` uniqueness | Final migration defines unique key on `checklist_template_id`, `room_id`, `run_date`, `created_by`. | ERD does not show this unique constraint. | ERD omits constraint detail | Include in Notes if the Data Dictionary includes constraints. |
| `checklist_templates` uniqueness | Migration defines unique `title`; SQLite partial unique index on active `scope`. | ERD does not show these unique constraints. | ERD omits constraint detail | Include in Notes if the Data Dictionary includes constraints. |
| `checklist_items` uniqueness | Migration defines unique `checklist_template_id` + `sort_order`. | ERD does not show this unique constraint. | ERD omits constraint detail | Include in Notes if the Data Dictionary includes constraints. |
| Model inverse relation for `users -> checklist_runs via submitted_by` | Migration and `ChecklistRun::submitter()` support relationship. | `User` model has no inverse hasMany relation for submitted runs. | Model partial evidence, not schema mismatch | No schema change required; use child-side model evidence. |
| Model inverse relation for `users -> checklist_run_items via checked_by` | Migration and `ChecklistRunItem::checker()` support relationship. | `User` model has no inverse hasMany relation for checked run items. | Model partial evidence, not schema mismatch | No schema change required; use child-side model evidence. |
| Model inverse relation for `users -> incident_activities via actor_id` | Migration and `IncidentActivity::actor()` support relationship. | `User` model has no inverse hasMany relation for incident activities. | Model partial evidence, not schema mismatch | No schema change required; use child-side model evidence. |
| `incident_activities.updated_at` | Migration intentionally has no `updated_at`; model sets `$timestamps = false`. | Current ERD does not show `updated_at`. | Match | No action. |
| Direct `users -> rooms` relationship | No FK or model relationship supports direct `users -> rooms`. | Current ERD final does not show direct relationship. | Match | No action. |
| Direct `rooms -> checklist_templates` relationship | No FK or model relationship supports direct `rooms -> checklist_templates`. | Current ERD final does not show direct relationship. | Match | No action. |
| Direct `checklist_runs -> incidents` relationship | No FK or model relationship supports direct `checklist_runs -> incidents`. | Current ERD final does not show direct relationship. | Match | No action. |
