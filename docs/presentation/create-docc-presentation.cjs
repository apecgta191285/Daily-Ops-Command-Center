const pptxgen = require('pptxgenjs');
const path = require('path');
const fs = require('fs');

const root = '/home/home_pc/projects/Daily Ops Command Center';
const diagramDir = path.join(root, 'docs/chapter3-diagrams');
const outDir = path.join(root, 'docs/presentation');

const pptx = new pptxgen();
pptx.layout = 'LAYOUT_WIDE';
pptx.author = 'A-lite Daily Ops Command Center';
pptx.subject = 'Student project presentation';
pptx.title = 'A-lite Daily Ops Command Center';
pptx.company = 'A-lite Daily Ops Command Center';
pptx.lang = 'th-TH';
pptx.theme = {
  headFontFace: 'Noto Sans Thai',
  bodyFontFace: 'Noto Sans Thai',
  lang: 'th-TH',
};
pptx.defineLayout({ name: 'LAYOUT_WIDE', width: 13.333, height: 7.5 });

const C = {
  ink: '1F2933',
  muted: '5B6673',
  line: '111827',
  soft: 'F3F4F6',
  panel: 'FFFFFF',
  accent: '374151',
};

function addFrame(slide) {
  slide.background = { color: 'FFFFFF' };
  slide.addShape(pptx.ShapeType.rect, {
    x: 0.28, y: 0.22, w: 12.78, h: 7.06,
    fill: { color: 'FFFFFF', transparency: 100 },
    line: { color: 'D1D5DB', width: 0.75 },
  });
}

function addHeader(slide, title, section = '') {
  addFrame(slide);
  slide.addText(title, {
    x: 0.62, y: 0.38, w: 9.4, h: 0.48,
    fontFace: 'Noto Sans Thai', fontSize: 20, bold: true, color: C.ink,
    margin: 0,
  });
  if (section) {
    slide.addText(section, {
      x: 10.05, y: 0.45, w: 2.55, h: 0.28,
      fontFace: 'Noto Sans Thai', fontSize: 9, color: C.muted,
      align: 'right', margin: 0,
    });
  }
  slide.addShape(pptx.ShapeType.line, {
    x: 0.62, y: 0.98, w: 12.08, h: 0,
    line: { color: 'D1D5DB', width: 1 },
  });
}

function addBullets(slide, bullets, x, y, w, h, fontSize = 15) {
  slide.addText(bullets.map(t => ({
    text: t,
    options: { bullet: { type: 'bullet' }, hanging: 4 },
  })), {
    x, y, w, h,
    fontFace: 'Noto Sans Thai',
    fontSize,
    color: C.ink,
    breakLine: false,
    fit: 'shrink',
    valign: 'top',
    paraSpaceAfterPt: 8,
    margin: 0.03,
  });
}

function addKpi(slide, label, value, x, y, w = 2.6) {
  slide.addShape(pptx.ShapeType.roundRect, {
    x, y, w, h: 0.84,
    rectRadius: 0.04,
    fill: { color: C.soft },
    line: { color: '9CA3AF', width: 0.75 },
  });
  slide.addText(value, {
    x: x + 0.1, y: y + 0.1, w: w - 0.2, h: 0.26,
    fontFace: 'Noto Sans Thai', fontSize: 13, bold: true, color: C.ink,
    margin: 0, align: 'center',
  });
  slide.addText(label, {
    x: x + 0.1, y: y + 0.47, w: w - 0.2, h: 0.22,
    fontFace: 'Noto Sans Thai', fontSize: 8.5, color: C.muted,
    margin: 0, align: 'center',
  });
}

function addDiagram(slide, filename, x, y, w, h, label = '') {
  const p = path.join(diagramDir, filename);
  if (fs.existsSync(p)) {
    slide.addImage({ path: p, x, y, w, h });
  } else {
    slide.addShape(pptx.ShapeType.rect, {
      x, y, w, h,
      fill: { color: 'FFFFFF' },
      line: { color: '9CA3AF', dash: 'dash' },
    });
    slide.addText(`ไม่พบไฟล์ภาพ: ${filename}`, {
      x: x + 0.1, y: y + h / 2 - 0.15, w: w - 0.2, h: 0.3,
      fontFace: 'Noto Sans Thai', fontSize: 12, color: C.muted,
      align: 'center', margin: 0,
    });
  }
  if (label) {
    slide.addText(label, {
      x, y: y + h + 0.05, w, h: 0.22,
      fontFace: 'Noto Sans Thai', fontSize: 8.5, color: C.muted,
      margin: 0, align: 'center',
    });
  }
}

function titleSlide() {
  const slide = pptx.addSlide();
  addFrame(slide);
  slide.addText('A-lite Daily Ops Command Center', {
    x: 0.86, y: 1.05, w: 11.6, h: 0.72,
    fontFace: 'Noto Sans Thai', fontSize: 32, bold: true,
    color: C.ink, align: 'center', margin: 0,
  });
  slide.addText('ระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย', {
    x: 1.25, y: 1.88, w: 10.85, h: 0.44,
    fontFace: 'Noto Sans Thai', fontSize: 18,
    color: C.accent, align: 'center', margin: 0,
  });
  slide.addShape(pptx.ShapeType.line, {
    x: 2.2, y: 2.65, w: 8.95, h: 0,
    line: { color: '9CA3AF', width: 1 },
  });
  addKpi(slide, 'บทบาทผู้ใช้', 'Staff / Supervisor / Admin', 1.3, 3.18, 3.4);
  addKpi(slide, 'โมดูลหลัก', 'Checklist / Incident / Dashboard', 4.97, 3.18, 3.4);
  addKpi(slide, 'รูปแบบระบบ', 'Internal Web Application', 8.65, 3.18, 3.4);
  addBullets(slide, [
    'เป้าหมาย: รวมข้อมูลการตรวจเช็ก เหตุผิดปกติ และภาพรวมงานไว้ในระบบเดียว',
    'การนำเสนอ: ที่มาและความสำคัญ, ขอบเขต, เครื่องมือ, การออกแบบระบบ, data model และ data dictionary',
  ], 1.6, 4.35, 10.2, 1.1, 15);
}

const slides = [
  {
    title: 'ที่มาและความสำคัญของปัญหา',
    section: 'Background',
    bullets: [
      'ห้องคอมพิวเตอร์เป็นพื้นที่ให้บริการด้านการเรียนการสอน การปฏิบัติการ และอุปกรณ์เทคโนโลยีสารสนเทศ',
      'ผู้ปฏิบัติงานต้องตรวจสอบความพร้อมของห้อง อุปกรณ์ เครือข่าย และสภาพแวดล้อมตามช่วงงาน',
      'ระบบงานเดิมอาศัยกระดาษ ไฟล์แยก หรือการสื่อสารผ่านช่องทางสนทนา ทำให้ข้อมูลกระจัดกระจาย',
      'เมื่อข้อมูลไม่รวมศูนย์ การติดตามสถานะ การทบทวนย้อนหลัง และการตัดสินใจของผู้ดูแลจึงล่าช้า',
    ],
  },
  {
    title: 'ปัญหาระบบงานเดิม',
    section: 'Cause and Effect',
    image: ['ch3-01-cause-effect-final.png', 6.1, 1.25, 6.55, 4.9, 'ภาพก้างปลา Cause and Effect ของระบบงานเดิม'],
    bullets: [
      'วิธีทำงานเดิม: กระดาษ ไฟล์แยก และข้อมูลจากแต่ละคน',
      'เครื่องมือและข้อมูล: ไม่มีศูนย์กลางของสถานะงานและประวัติเหตุผิดปกติ',
      'ห้องและบริบทหน้างาน: เห็นไม่ชัดว่าปัญหาเกิดที่ห้องใดและเวลาใด',
      'ผลกระทบ: ข้อมูลตกหล่น ติดตามย้อนหลังยาก และการตัดสินใจล่าช้า',
    ],
  },
  {
    title: 'วัตถุประสงค์ของโครงงาน',
    section: 'Objectives',
    bullets: [
      'วิเคราะห์และออกแบบระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย',
      'พัฒนาเว็บแอปพลิเคชันที่รองรับการตรวจเช็กรายการประจำวัน การแจ้งเหตุผิดปกติ และการติดตามภาพรวมในระบบเดียว',
      'จัดเก็บข้อมูลการปฏิบัติงานและเหตุผิดปกติให้ตรวจสอบย้อนหลังและใช้ประกอบการทบทวนงานได้',
      'ประเมินความเหมาะสมของระบบจากการทดสอบการทำงานและความคิดเห็นของผู้เกี่ยวข้อง',
    ],
  },
  {
    title: 'ขอบเขตโครงงานและบทบาทผู้ใช้',
    section: 'Scope',
    bullets: [
      'Admin: จัดการบัญชีผู้ใช้ บทบาท สถานะการใช้งาน และแม่แบบรายการตรวจเช็ก',
      'Supervisor: ติดตามภาพรวมงาน ตรวจสอบรายการเหตุผิดปกติ และปรับปรุงสถานะการดำเนินงาน',
      'Staff: เลือกห้องและช่วงงาน ดำเนินการตรวจเช็กประจำวัน และแจ้งเหตุผิดปกติเมื่อพบปัญหา',
      'ระบบไม่รวม public signup, notification เต็มรูปแบบ, approval workflow, machine registry, analytics warehouse, mobile app แยก หรือ AI/copilot',
    ],
  },
  {
    title: 'เครื่องมือและเทคโนโลยีที่ใช้',
    section: 'Tools',
    bullets: [
      'PHP และ Laravel Framework: ใช้พัฒนาโครงสร้างฝั่งเซิร์ฟเวอร์ routing, middleware, Eloquent ORM และ migrations',
      'Laravel Fortify: ใช้สนับสนุนระบบ authentication สำหรับผู้ใช้ภายใน',
      'Livewire และ Flux: ใช้สร้างหน้าจอโต้ตอบ เช่น checklist, incident และ dashboard',
      'Tailwind CSS และ Vite: ใช้จัดรูปแบบหน้าจอและ build frontend assets',
      'ฐานข้อมูลเชิงสัมพันธ์, Composer, NPM, Laravel Pint และ Pest/Laravel Test: ใช้จัดเก็บข้อมูล จัดการ dependency ตรวจรูปแบบโค้ด และทดสอบ workflow หลัก',
    ],
  },
  {
    title: 'ภาพรวมระบบงานใหม่',
    section: 'System Flowchart',
    image: ['ch3-02-system-flowchart-v2.png', 5.9, 1.22, 6.75, 5.25, 'ภาพรวม flow ของระบบงานใหม่'],
    bullets: [
      'ระบบรวม checklist, incident และ dashboard ไว้ในแหล่งข้อมูลเดียว',
      'Staff เลือกห้องและช่วงงาน จากนั้นเปิดหน้า checklist ของวันและบันทึกผล',
      'เมื่อพบเหตุผิดปกติ Staff สร้าง incident พร้อมรายละเอียดและหลักฐานเมื่อมี',
      'Supervisor และ Admin ติดตามผ่านแดชบอร์ดหรือรายการเหตุผิดปกติ และอัปเดตสถานะเมื่อจำเป็น',
    ],
  },
  {
    title: 'Context Diagram และผู้เกี่ยวข้อง',
    section: 'Context Diagram',
    image: ['ch3-03-context-diagram-v2.png', 5.92, 1.28, 6.6, 4.95, 'Context Diagram Level 0'],
    bullets: [
      'ระบบกลางคือ A-lite Daily Ops Command Center',
      'External entities มี 3 กลุ่ม: Staff, Supervisor และ Admin',
      'Staff ส่งข้อมูลเลือกห้อง/ช่วงงาน ข้อมูลตรวจเช็ก และข้อมูลเหตุผิดปกติ',
      'Supervisor และ Admin ติดตามภาพรวม อัปเดตสถานะ และรับข้อมูล dashboard/history จากระบบ',
    ],
  },
  {
    title: 'DFD Level 1: กระบวนการหลักของระบบ',
    section: 'DFD Level 1',
    image: ['ch3-04-dfd-level1-v3.png', 5.55, 1.18, 7.0, 5.25, 'DFD Level 1 ของระบบ'],
    bullets: [
      '1.0 จัดการผู้ใช้และสิทธิ์',
      '2.0 จัดการแม่แบบรายการตรวจ',
      '3.0 ดำเนินการตรวจเช็กประจำวัน',
      '4.0 จัดการเหตุผิดปกติ',
      '5.0 ติดตามภาพรวมและทบทวนประวัติ',
    ],
  },
  {
    title: 'DFD Level 2: Workflow สำคัญที่ควรเล่า',
    section: 'DFD Level 2',
    bullets: [
      'Process 3.0: ตรวจสอบผู้ปฏิบัติงาน รับข้อมูลห้องและช่วงงาน ค้นหาแม่แบบ checklist สร้าง/ใช้รอบการตรวจ และบันทึกผล',
      'Process 4.0: Staff สร้าง incident, ระบบบันทึก incident/activity, Supervisor หรือ Admin อัปเดตสถานะและข้อมูลติดตาม',
      'Process 5.0: Supervisor หรือ Admin ขอข้อมูลภาพรวม ระบบอ่าน D4, D5 และ D6 เพื่อสร้าง dashboard, history และเอกสารสรุป',
      'หากกรรมการถามรายละเอียดเส้นข้อมูล ให้เปิด DFD Level 2 ในเล่มประกอบการอธิบาย',
    ],
  },
  {
    title: 'Data Model: ERD ของระบบ',
    section: 'ERD',
    image: ['ch3-08-erd-final.png', 5.25, 1.15, 7.25, 5.2, 'ERD ของระบบ A-lite Daily Ops Command Center'],
    bullets: [
      'ระบบมี 8 entities หลัก: users, rooms, checklist_templates, checklist_items, checklist_runs, checklist_run_items, incidents และ incident_activities',
      'ฝั่ง checklist เชื่อมแม่แบบ รายการตรวจ รอบการตรวจ และผลการตรวจ',
      'ฝั่ง incident เชื่อมห้อง เหตุผิดปกติ ผู้รับผิดชอบ และกิจกรรมของเหตุผิดปกติ',
      'users เชื่อมกับการสร้าง/ส่งรอบการตรวจ การตรวจรายการ การแจ้งเหตุ และ activity log',
    ],
  },
  {
    title: 'Data Dictionary: ตารางข้อมูลสำคัญ',
    section: 'Data Dictionary',
    bullets: [
      'Data Dictionary อธิบาย Attribute, Description, Type, Constraint และ Null ของแต่ละตาราง',
      'กลุ่มผู้ใช้และพื้นที่: users, rooms',
      'กลุ่ม checklist: checklist_templates, checklist_items, checklist_runs, checklist_run_items',
      'กลุ่ม incident: incidents, incident_activities',
      'รายละเอียดบางฟิลด์เกี่ยวกับ authentication แสดงใน Data Dictionary แม้ไม่ได้แสดงใน ERD เพื่อให้แผนภาพอ่านง่าย',
      'ไม่ควรวาง data dictionary ทั้งหมดบนสไลด์ ให้เปิดเล่มเมื่อจำเป็นต้องดูราย attribute',
    ],
  },
  {
    title: 'ผลการพัฒนาระบบที่ได้',
    section: 'Implementation',
    bullets: [
      'รองรับการเข้าสู่ระบบและแยกบทบาทผู้ใช้ตาม Admin, Supervisor และ Staff',
      'รองรับการจัดการผู้ใช้และแม่แบบรายการตรวจสำหรับผู้ดูแลระบบ',
      'รองรับการเลือกห้อง ดำเนินการตรวจเช็กประจำวัน และบันทึกผลรายการตรวจ',
      'รองรับการแจ้งเหตุผิดปกติ การแนบหลักฐาน การติดตามคิวปัญหา การปรับสถานะ และการดูประวัติ',
      'มีแดชบอร์ดสำหรับแสดงสถานะงานประจำวัน รายการที่ต้องติดตาม และภาพรวมของข้อมูลที่เกี่ยวข้อง',
      'มีหน้า printable checklist recap และ incident summary สำหรับใช้เป็นหลักฐานประกอบการทบทวน',
    ],
  },
  {
    title: 'สรุปผลและประโยชน์ที่ได้รับ',
    section: 'Summary',
    bullets: [
      'ระบบช่วยรวมข้อมูลการตรวจเช็ก เหตุผิดปกติ และภาพรวมงานไว้ในศูนย์กลางเดียว',
      'ช่วยลดการพึ่งพากระดาษ ไฟล์แยก และการสื่อสารแบบกระจัดกระจาย',
      'ทำให้ Staff, Supervisor และ Admin มีหน้าที่ชัดเจนตามบทบาทของตน',
      'ช่วยให้ตรวจสอบย้อนหลังและใช้ข้อมูลเป็นหลักฐานประกอบการทบทวนงานได้ดีขึ้น',
      'ระบบอยู่ในระดับต้นแบบพร้อมสาธิต และเหมาะกับขอบเขตโครงงานจบระดับนักศึกษา',
    ],
  },
  {
    title: 'ข้อจำกัดและแนวทางพัฒนาต่อ',
    section: 'Next Steps',
    bullets: [
      'ข้อจำกัดหลักคือการพัฒนาโดยผู้พัฒนาคนเดียว จึงต้องควบคุมขอบเขตให้พัฒนาและตรวจสอบได้จริง',
      'การทดสอบเน้น workflow หลักในสภาพแวดล้อมการพัฒนา มากกว่าการใช้งานจริงพร้อมกันจำนวนมาก',
      'แนวทางพัฒนาต่อ: เพิ่มระบบแจ้งเตือนพื้นฐานสำหรับเหตุผิดปกติใหม่หรือใกล้ครบกำหนดติดตาม',
      'ต่อยอดการพิมพ์หรือส่งออกสรุปรายงานตามช่วงเวลาให้สมบูรณ์ขึ้น',
      'ปรับปรุงการแสดงผลบนหน้าจอขนาดเล็กเพื่อให้ Staff ใช้ระหว่างเดินตรวจห้องได้สะดวกขึ้น',
    ],
  },
];

titleSlide();

slides.forEach((data, index) => {
  const slide = pptx.addSlide();
  addHeader(slide, data.title, data.section);
  const image = data.image;
  if (image) {
    addBullets(slide, data.bullets, 0.75, 1.35, 4.75, 5.35, index === 7 ? 13.5 : 14);
    addDiagram(slide, ...image);
  } else {
    addBullets(slide, data.bullets, 1.02, 1.45, 11.15, 4.95, 16);
  }
  slide.addText(`${index + 2}`, {
    x: 12.15, y: 6.82, w: 0.45, h: 0.22,
    fontFace: 'Noto Sans Thai', fontSize: 8, color: C.muted,
    align: 'right', margin: 0,
  });
});

pptx.writeFile({ fileName: path.join(outDir, 'A-lite-Daily-Ops-Command-Center-presentation.pptx') });
