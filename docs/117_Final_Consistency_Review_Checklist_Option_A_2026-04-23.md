# Final Consistency Review Checklist for Option A Submission
วันที่: 23 เมษายน 2026

## 1) Project Identity
- [ ] README อธิบายตรงว่าเป็น internal web app สำหรับหลายห้องคอมในมหาวิทยาลัยเดียว
- [ ] Project Lock, Product Brief, System Spec, และ Current State ใช้ story เดียวกัน
- [ ] ไม่มีเอกสารหลักไหนพูดเหมือน room-centered operations ยังเป็น future only

## 2) Actor Mapping
- [ ] Admin = อาจารย์ผู้รับผิดชอบ / ผู้ได้รับมอบหมายดูแลระบบ
- [ ] Supervisor = lab boy / เจ้าหน้าที่แล็บ / ผู้ดูแลห้อง
- [ ] Staff = นักศึกษาที่เข้าเวรตรวจห้องตามรอบ
- [ ] สไลด์และคำพูดหน้าห้องไม่ใช้คำเรียก role ที่หลุดจาก mapping นี้

## 3) Product Scope
- [ ] ทุกเอกสารพูดตรงกันว่าระบบใช้ `room + time scope`
- [ ] ทุกเอกสารพูดตรงกันว่า `opening / during-day / closing` ยังเป็นมิติของเวลา
- [ ] ทุกเอกสารพูดตรงกันว่า `equipment_reference` เป็น optional free text แบบ lightweight
- [ ] ทุกเอกสารพูดตรงกันว่ายังไม่มี machine registry

## 4) Claim Discipline
- [ ] ไม่มีเอกสารไหนอ้างว่า production-grade
- [ ] ไม่มีเอกสารไหนอ้างว่า enterprise platform
- [ ] ไม่มีเอกสารไหนอ้างว่า machine management system
- [ ] limitation statement ใช้แนวเดียวกันทุกไฟล์

## 5) Demo Consistency
- [ ] demo accounts อธิบาย role ตรงกับ case study
- [ ] demo script ใช้ห้อง `Lab 1–Lab 5` ตรงกับ seeded data
- [ ] scenario หลัก staff → incident → supervisor → admin ตรงกันใน runbook และ defense notes
- [ ] ไม่มีคำพูดเดโมที่ทำให้เข้าใจว่า room context = machine registry

## 6) Page Explanation Consistency
- [ ] login พูดเรื่อง internal-only และ demo roles
- [ ] checklist runtime พูดเรื่องเลือกห้องก่อน
- [ ] incident create พูดเรื่อง room + optional equipment reference
- [ ] dashboard / queue / detail / history พูดเรื่อง room-aware follow-up
- [ ] admin pages พูดเรื่อง governance ใน product เดียวกัน

## 7) Oral Defense Consistency
- [ ] อธิบาย role เป็นภาษาไทยได้เอง
- [ ] อธิบาย flow หลักเป็นภาษาไทยได้เอง
- [ ] ตอบได้ว่าถ้าเครื่องเสียทำอย่างไรใน scope ปัจจุบัน
- [ ] ตอบได้ว่าทำไมยังไม่ทำ machine registry
- [ ] ตอบได้ว่าทำไมระบบนี้ยังไม่ production-grade แต่ก็ไม่ใช่งานเผา

## 8) Final Go / No-Go Rule
ให้ถือว่า submission docs พร้อม เมื่อ:
- [ ] canonical docs ตรงกัน
- [ ] support docs ตรงกัน
- [ ] limitation statement ตรงกัน
- [ ] demo story ตรงกับ seeded reality
- [ ] ไม่มีคำอธิบายที่อ้างเกิน capability จริง

ถ้ามีข้อใดข้อหนึ่งยังไม่ผ่าน:
- [ ] แก้ wording ก่อน
- [ ] ห้ามแก้ scope เพื่อปิดช่องว่างของคำพูด

