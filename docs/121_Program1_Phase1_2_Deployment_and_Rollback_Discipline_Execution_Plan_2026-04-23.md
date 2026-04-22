# Program 1 / Phase 1.2 — Deployment and Rollback Discipline Execution Plan
วันที่: 23 เมษายน 2026

## 1) Purpose
เอกสารนี้แตก `Program 1 / Phase 1.2 — Deployment and Rollback Discipline` ให้เป็นก้อนงานที่ใช้ได้จริง
โดยยังไม่อ้างว่าระบบ production-ready และยังไม่เปิด hardening phase อื่นปนเข้ามา

## 2) Repo Truth Used For This Plan
สิ่งที่ repo มีอยู่แล้วและใช้เป็นฐานใน phase นี้:
- Laravel 13 + Vite application
- local setup ใช้ `composer install`, `php artisan storage:link`, `php artisan migrate --force`, `npm run build`
- browser QA และ feature tests มีอยู่แล้วในฐานะ release evidence บางส่วน
- production v1 baseline จาก phase ก่อนหน้าถูกล็อกเป็น:
  - MySQL 8.0
  - single-node host
  - `public` disk on same host
  - `database` queue/cache/session

## 3) Executive Decision
phase นี้ควรล็อก deployment discipline แบบ `single-node application release procedure`
โดยไม่อ้าง blue/green, zero-downtime orchestration, หรือ HA rollout

## 4) Deliverables
phase นี้ควรจบด้วย 4 deliverables:

1. [deployment_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/deployment_runbook.md)
2. [rollback_runbook.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/rollback_runbook.md)
3. [release_checklist.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/release_checklist.md)
4. [post_deploy_smoke_checklist.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/post_deploy_smoke_checklist.md)

## 5) Hard Boundaries
phase นี้ยังไม่ทำ:
- CI/CD platform implementation
- infrastructure as code
- automated rollback tooling
- backup/restore implementation
- observability integration
- security baseline implementation
- queue re-architecture
- multi-node deployment

## 6) Deployment Discipline To Lock
ลำดับ deployment ที่ phase นี้ยอมรับ:

1. pre-release validation
2. maintenance mode decision
3. backup checkpoint prerequisite
4. code update
5. dependency install
6. front-end asset build
7. safe app cache rebuild
8. migrations with explicit operator review
9. worker restart / queue process refresh
10. storage/public path verification
11. post-deploy smoke validation
12. release go/no-go decision

## 7) Migration Safety Stance
phase นี้ต้องพูดชัดว่า:
- migrations ต้องเป็น explicit reviewed step
- ถ้า release นั้นมี destructive migration risk ต้องถือเป็น elevated-risk release
- single-node baseline ไม่ได้แปลว่าควรรัน migration แบบไม่คิด rollback path

## 8) Acceptance Criteria
phase นี้จะถือว่าจบเมื่อ:
- มี deployment runbook แบบ step-by-step ที่คนอื่นเปิดแล้วทำตามได้
- มี rollback runbook ที่พูดตรงว่าทำอะไรได้และอะไรย้อนกลับไม่ได้
- มี release checklist ก่อน deploy
- มี smoke checklist หลัง deploy
- ไม่มีประโยคที่อ้างว่า deployment process นี้เป็น zero-downtime หรือ HA release flow

## 9) Recommended Next Step After This Phase
หลัง phase นี้ ควรไปต่อที่ `Program 1 / Phase 1.3 — Backup and Recovery`
เพราะ deployment discipline ที่ไม่มี backup/restore proof ยังไม่พอสำหรับ production-grade claim
