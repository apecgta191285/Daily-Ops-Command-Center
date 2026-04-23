# Ownership Matrix
วันที่: 23 เมษายน 2026

## 1) Purpose
matrix นี้ใช้ระบุ `primary owner` ของงาน operational baseline ใน Daily Ops Command Center
เพื่อให้ runbooks และ SOP ที่มีอยู่แล้วมีเจ้าภาพขั้นต่ำที่ชัดเจน

## 2) Ownership Model Boundary
baseline ปัจจุบันสมมติรูปแบบทีมแบบ pragmatic:
- เป็น `single-team operational model`
- คนหนึ่งอาจถือหลายหน้าที่ได้
- product roles (`Admin`, `Supervisor`, `Staff`) ไม่ได้บอก ownership ครบทุกอย่างเอง

ดังนั้น matrix นี้ใช้ภาษางานแบบ:
- `Primary owner`
- `Backup owner`
- `Approval needed`

ไม่ใช่ enterprise RACI เต็มรูปแบบ

## 3) Ownership Matrix
| Operational area | Primary owner | Backup owner | Approval needed | Source baseline |
| --- | --- | --- | --- | --- |
| Platform environment contract | Technical owner / system maintainer | Admin lead | Yes, before production changes | `production_env_contract.md` |
| Release execution | Release owner | Technical owner / system maintainer | Yes, per release window | `release_checklist.md`, `deployment_runbook.md` |
| Rollback decision and execution | Release owner | Technical owner / system maintainer | Yes, when release risk is real | `rollback_runbook.md` |
| Backup execution and verification | Backup owner | Technical owner / system maintainer | Yes, for policy changes | `backup_policy.md` |
| Restore execution | Restore owner | Technical owner / system maintainer | Yes, always | `restore_runbook.md` |
| Observability review | Technical owner / system maintainer | Release owner | No, but cadence must be agreed | `observability_baseline.md`, `alerting_baseline.md` |
| Security baseline sign-off | Admin lead | Technical owner / system maintainer | Yes, for release or policy exceptions | `security_baseline.md`, `release_security_checklist.md` |
| User administration | Admin lead | Named backup admin | Yes, for role/active changes with governance impact | `admin_sop.md`, `user_lifecycle_sop.md` |
| Template governance | Admin lead | Named backup admin | Yes, for major template changes | `template_governance_sop.md`, `template_change_review_checklist.md` |
| Dashboard/workboard daily review | Supervisor on duty | Admin lead | No, but daily routine is expected | `supervisor_sop.md`, `workboard_usage_sop.md` |
| Incident queue ownership and follow-up | Supervisor on duty | Admin lead | Escalate when risk crosses supervisor authority | `incident_triage_sop.md`, `supervisor_sop.md` |
| Operational incident triage of the web system | Technical owner / system maintainer | Release owner | Yes, for rollback or maintenance mode | `incident_triage_runbook.md` |

## 4) Minimum Named Roles To Fill
ก่อนใช้ matrix นี้จริง ทีมควรระบุชื่อคนอย่างน้อยสำหรับ:
- `Admin lead`
- `Named backup admin`
- `Supervisor on duty`
- `Release owner`
- `Technical owner / system maintainer`
- `Backup owner`
- `Restore owner`

ถ้ายังตั้งชื่อไม่ได้ แปลว่า ownership model ยังไม่ลงดินจริง

## 5) Practical Rules
- งานที่มีผลต่อ production state ต้องมี primary owner ชัดก่อนลงมือ
- งานที่เกี่ยวกับ restore หรือ security exception ต้องไม่ทำแบบไม่มี approval
- งานที่เป็น routine daily review ต้องไม่ถูกปล่อยเป็น “ทุกคนดูได้” โดยไม่มีคนรับผิดชอบจริง
- ถ้าคนเดียวถือหลายบทบาท ต้องประกาศให้ชัด ไม่ใช่ปล่อยให้คนอื่นเดา

## 6) Honest Limitation
matrix นี้ยังไม่ใช่:
- org chart
- HR responsibility model
- 24/7 support roster
- formal enterprise RACI

มันคือ `operational ownership baseline`
สำหรับทีมขนาดเล็กที่ต้องการหยุดสภาพ “มีเอกสารแต่ไม่มีเจ้าภาพ”
