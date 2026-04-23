# Support Responsibility Matrix
วันที่: 23 เมษายน 2026

## 1) Purpose
matrix นี้ใช้แจกแจงความรับผิดชอบเชิง support และ operations ของระบบปัจจุบัน
ให้เห็นว่าเรื่องไหนใคร `owns`, ใคร `supports`, ใคร `approves`, และใครควรถูก `informed`

## 2) Reading Guide
ใช้ความหมายนี้:
- `Owns` = รับผิดชอบหลักในการทำให้งานนั้นเกิดและปิดได้
- `Supports` = ช่วยดำเนินการหรือให้ข้อมูลที่จำเป็น
- `Approves` = ต้องรับรู้และยอมรับก่อนลงมือหรือก่อนถือว่างานจบ
- `Informed` = ควรถูกสื่อสารให้ทราบผลหรือความเสี่ยง

บทบาทใน matrix นี้เป็นบทบาทเชิงปฏิบัติการ:
- `Admin lead`
- `Backup admin`
- `Supervisor on duty`
- `Release owner`
- `Technical owner`

## 3) Support Responsibility Matrix
| Work item | Owns | Supports | Approves | Informed |
| --- | --- | --- | --- | --- |
| Prepare release checklist and release window | Release owner | Technical owner, Admin lead | Admin lead | Supervisor on duty |
| Run deploy and verify smoke checks | Release owner | Technical owner | Admin lead for risky releases | Supervisor on duty |
| Execute rollback when release is unstable | Release owner | Technical owner | Admin lead | Supervisor on duty |
| Verify daily/weekly backup health | Backup owner | Technical owner | Admin lead for policy exceptions | Release owner |
| Execute restore or drill | Restore owner | Technical owner, Admin lead | Admin lead | Release owner, Supervisor on duty |
| Review logs after user-facing failure | Technical owner | Release owner | No formal approval in baseline | Admin lead |
| Triage operational incident of the web system | Technical owner | Release owner | Admin lead when rollback/maintenance is needed | Supervisor on duty |
| Review dashboard/workboard at start of shift | Supervisor on duty | Admin lead | No formal approval in baseline | Relevant management users |
| Review unowned / overdue / stale product incidents | Supervisor on duty | Admin lead | No formal approval in baseline | Relevant management users |
| Reassign product incident ownership | Supervisor on duty | Admin lead | Admin lead when escalation crosses supervisor authority | Relevant management users |
| Create or deactivate user accounts | Admin lead | Backup admin | Admin lead or delegated governance owner | Relevant management users when access changes matter |
| Change template live state | Admin lead | Backup admin, Supervisor on duty for workflow context | Admin lead | Relevant management users |
| Review security checklist before release | Admin lead | Technical owner, Release owner | Admin lead | Supervisor on duty |

## 4) Escalation Rules
- ถ้างานกระทบ production availability ให้ `Technical owner` และ `Release owner` ถูกดึงเข้ามาเสมอ
- ถ้างานกระทบสิทธิ์ผู้ใช้หรือ template live behavior ให้ `Admin lead` เป็นจุดตัดสินใจหลัก
- ถ้างานกระทบ routine lab operation วันนี้ ให้ `Supervisor on duty` ต้องถูก informed อย่างชัดเจน
- ถ้าไม่มีคนที่ถูกตั้งชื่อไว้ในบทบาทเหล่านี้ ห้ามแกล้งทำเหมือน support model พร้อมแล้ว

## 5) Honest Limitation
matrix นี้ยังไม่ครอบคลุม:
- after-hours support
- vendor escalation
- multi-team incident command
- compliance sign-off chain

ดังนั้นนี่คือ `support responsibility baseline`
ไม่ใช่ mature support organization design
