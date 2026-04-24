# Incident History Query Hardening Execution Pack

Date: 2026-04-24  
Scope: `Priority 3 — Harden Incident History Query Path`

## 1. Why This Round Exists

จาก audit ล่าสุด จุดที่ยังไม่คมคือ incident history ยังใช้วิธี:

- ดึง incident ทั้งก้อนใน selected window
- แล้วค่อย filter opened/resolved แยกใน memory ทีละวัน

Brutal truth:

- สำหรับ current scale มันยังพอได้
- แต่ในมุม Senior Engineer มันยังไม่ใช่ query hygiene ที่ดีพอ
- และมันทำให้ read path นี้โตยากโดยไม่จำเป็น

## 2. What Changed

### 2.1 Split the query path by event type

ใน [ListIncidentHistorySlices.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Incidents/Queries/ListIncidentHistorySlices.php)

จากเดิม:

- query เดียว ดึง incident ที่ created หรือ resolved อยู่ในช่วง
- hydrate ทั้งก้อน
- ให้ builder filter ซ้ำ

เป็น:

- query ชุด `openedIncidents()` สำหรับ `created_at` ในช่วง
- query ชุด `resolvedIncidents()` สำหรับ `resolved_at` ในช่วง
- ทั้งสองชุด eager-load relations เท่าที่ surface ต้องใช้
- ทั้งสองชุดมี deterministic ordering

### 2.2 Reduced in-memory filtering inside the slice builder

ใน [IncidentHistorySliceBuilder.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/app/Application/Incidents/Support/IncidentHistorySliceBuilder.php)

จากเดิม:

- filter collection เดิมซ้ำ 2 รอบต่อวัน (`opened` และ `resolved`)

เป็น:

- group opened incidents ตาม `created_at` date ก่อน
- group resolved incidents ตาม `resolved_at` date ก่อน
- slice loop ดึง collection ตาม date key โดยตรง

ผลคือ:

- logic อ่านง่ายขึ้น
- owner ของ query กับ owner ของ shaping แยกหน้าที่ชัดขึ้น
- ลด work ที่ทำซ้ำใน memory

## 3. Regression Proof Added

เพิ่ม [ListIncidentHistorySlicesQueryTest.php](/home/home_pc/projects/Daily%20Ops%20Command%20Center/tests/Feature/Application/ListIncidentHistorySlicesQueryTest.php)

สิ่งที่ test ใหม่ยืนยัน:

- selected day window ยังดึงเฉพาะ incident ที่อยู่ในช่วงจริง
- record นอกช่วงไม่หลุดเข้ามา
- incident ที่เปิดและปิดในวันเดียวกันยังถูกจัดเข้า slice เดียวอย่างซื่อสัตย์

## 4. What Was Intentionally Left Untouched

- ไม่แตะ dashboard hotspot รอบนี้
- ไม่เปิด caching wave
- ไม่เปลี่ยน UI/history copy
- ไม่เปลี่ยน route หรือ Livewire contract
- ไม่เปิด broad performance pass ทั้ง repo

## 5. Remaining Truth After This Round

- incident history read path ดีขึ้นชัดเจน แต่ยังไม่ใช่ infinite-scale architecture
- dashboard query object ยังเป็น hotspot แยกต่างหาก
- ถ้าจะเดินต่ออย่างถูกลำดับที่สุด รอบถัดไปควรเป็น selective dashboard query hardening เท่านั้น
