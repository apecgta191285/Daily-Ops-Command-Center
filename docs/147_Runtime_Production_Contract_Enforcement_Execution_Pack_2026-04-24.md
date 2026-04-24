# Runtime Production Contract Enforcement Execution Pack

Date: 2026-04-24  
Scope: `Priority 2 — Enforce Runtime Production Contract in Code`

## 1. Why This Round Exists

หลังจากรอบ secure attachment handling ระบบพูดความจริงเรื่อง security/privacy ดีขึ้นแล้ว
แต่ production baseline ยังถูกล็อกไว้ใน runbook มากกว่าใน runtime จริง

Brutal truth:

- ถ้า production env ถูกตั้งค่าแบบ local-ish ระบบยังสามารถ boot ได้
- นั่นแปลว่า repo มีเอกสารที่ดี แต่ยัง fail-fast ไม่พอ
- รอบนี้จึงมีหน้าที่ทำให้ `production contract` กลายเป็นสิ่งที่ app ตรวจเองได้

## 2. Changes Landed

### 2.1 Added a runtime contract owner

เพิ่ม [ProductionEnvironmentContract.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Support/ProductionEnvironmentContract.php)
เพื่อรวม production baseline checks ไว้จุดเดียว

สิ่งที่ class นี้ตรวจ:

- `APP_DEBUG=false`
- `DB_CONNECTION=mysql`
- `QUEUE_CONNECTION=database`
- `CACHE_STORE=database`
- `SESSION_DRIVER=database`
- `SESSION_SECURE_COOKIE=true`
- `MAIL_MAILER=smtp`
- logging ต้องเป็น `daily` หรือ `stack` ที่ include `daily`
- log level ต้องเป็น `info` หรือเข้มกว่านั้น
- `APP_URL` ต้องเป็น `https` และต้องไม่ชี้ไป `localhost` หรือ loopback

### 2.2 Wired enforcement into application bootstrap

เพิ่มการเรียก runtime contract ใน [AppServiceProvider.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Providers/AppServiceProvider.php)

หลักการ:

- local/testing/staging style flow ยังไม่ถูกบีบแบบ production
- แต่ถ้า app boot ใน `production` และ contract ไม่ครบ จะ fail fast ทันที

### 2.3 Added focused regression proof

เพิ่ม [ProductionEnvironmentContractTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Unit/ProductionEnvironmentContractTest.php)
เพื่อยืนยันทั้ง 3 แบบ:

- baseline ที่ถูกต้องผ่าน
- misconfiguration หลายจุดถูกรายงานครบ
- thrown exception อ่านแล้วรู้สาเหตุจริง

### 2.4 Realigned contract docs

อัปเดต runbook owner docs:

- [production_env_contract.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/production_env_contract.md)
- [environment_matrix.md](/home/home_pc/projects/Daily%20Ops%20Command%20Center/docs/runbooks/environment_matrix.md)

สิ่งที่แก้:

- เปลี่ยน attachment storage truth จาก `public` disk ไปเป็น host-local private storage
- เพิ่ม secure session cookie และ daily logging เข้า baseline ชัดเจน
- ล็อกว่า attachment access ต้องผ่าน authenticated route

## 3. What Was Intentionally Left Untouched

- ไม่แก้ `.env.example` local defaults
- ไม่เปลี่ยน config defaults สำหรับ local convenience
- ไม่เปิด deployment/rollback/backup/observability/security wave ใหม่
- ไม่แตะ product workflow
- ไม่แตะ browser QA

## 4. Risks Still Remaining

- contract นี้ยังเป็น `startup guard`, ไม่ใช่ full production automation
- ยังไม่ได้ยืนยันว่า host จริงถูก provision ตาม contract แล้ว
- ยังไม่ได้ลง external monitoring, restore drill proof, หรือ security tooling integration

## 5. Why This Is the Right Level of Change

รอบนี้ไม่ใช่ quick fix และไม่ใช่ docs-only closure

เราแก้แบบมี owner และมี regression proof:

- contract ถูกนิยามใน code
- provider เป็นแค่จุด bootstrap
- tests จับ misconfiguration ได้ตรง
- runbook owner docs พูดตรงกับ runtime truth มากขึ้น
