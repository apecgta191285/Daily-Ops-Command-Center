const pptxgen = require('pptxgenjs');
const fs = require('fs');
const path = require('path');

const root = '/home/home_pc/projects/Daily Ops Command Center';
const diagramDir = path.join(root, 'docs/chapter3-diagrams');
const outDir = path.join(root, 'docs/presentation');

const pptx = new pptxgen();
pptx.layout = 'LAYOUT_WIDE';
pptx.author = 'A-lite Daily Ops Command Center';
pptx.subject = 'Codebase-first thesis presentation';
pptx.title = 'A-lite Daily Ops Command Center Presentation Redesign';
pptx.company = 'Daily Ops Command Center';
pptx.lang = 'th-TH';
pptx.theme = {
  headFontFace: 'Aptos Display',
  bodyFontFace: 'Noto Sans Thai',
  lang: 'th-TH',
};
pptx.defineLayout({ name: 'LAYOUT_WIDE', width: 13.333, height: 7.5 });

const C = {
  ink: '17212B',
  navy: '172A3A',
  teal: '0F766E',
  teal2: '14B8A6',
  sky: 'E6F6F4',
  amber: 'F59E0B',
  amberSoft: 'FFF7E6',
  red: 'B91C1C',
  redSoft: 'FEE2E2',
  blue: '2563EB',
  blueSoft: 'EFF6FF',
  green: '16A34A',
  greenSoft: 'ECFDF3',
  violet: '6D28D9',
  violetSoft: 'F4F0FF',
  paper: 'FFFFFF',
  wash: 'F5F7FA',
  line: 'D9E2EC',
  muted: '5D6B7A',
  darkMuted: 'A7B2C1',
};

function pngSize(file) {
  const b = fs.readFileSync(file);
  if (b.readUInt32BE(12) !== 0x49484452) return { width: 16, height: 9 };
  return { width: b.readUInt32BE(16), height: b.readUInt32BE(20) };
}

function contain(file, x, y, w, h) {
  const { width, height } = pngSize(file);
  const r = Math.min(w / width, h / height);
  const iw = width * r;
  const ih = height * r;
  return { x: x + (w - iw) / 2, y: y + (h - ih) / 2, w: iw, h: ih };
}

function bg(slide, dark = false) {
  slide.background = { color: dark ? C.navy : C.wash };
  if (dark) {
    slide.addShape(pptx.ShapeType.arc, {
      x: 8.3, y: -0.7, w: 5.6, h: 5.6,
      adjustPoint: 0.23,
      fill: { color: '1F7A75', transparency: 28 },
      line: { color: '1F7A75', transparency: 100 },
      rotate: 20,
    });
    slide.addShape(pptx.ShapeType.arc, {
      x: -1.4, y: 4.75, w: 4.2, h: 4.2,
      adjustPoint: 0.32,
      fill: { color: 'F59E0B', transparency: 36 },
      line: { color: 'F59E0B', transparency: 100 },
      rotate: 160,
    });
  } else {
    slide.addShape(pptx.ShapeType.rect, {
      x: 0, y: 0, w: 13.333, h: 0.13,
      fill: { color: C.teal },
      line: { color: C.teal },
    });
    slide.addShape(pptx.ShapeType.rect, {
      x: 0, y: 0.13, w: 13.333, h: 0.035,
      fill: { color: C.amber },
      line: { color: C.amber },
    });
  }
}

function header(slide, title, kicker) {
  bg(slide);
  slide.addText(kicker, {
    x: 0.62, y: 0.38, w: 3.7, h: 0.25,
    fontFace: 'Aptos', fontSize: 8.5, color: C.teal, bold: true,
    charSpace: 1.1, margin: 0,
  });
  slide.addText(title, {
    x: 0.62, y: 0.68, w: 8.6, h: 0.52,
    fontFace: 'Noto Sans Thai', fontSize: 24, bold: true, color: C.ink,
    margin: 0,
  });
  slide.addText('A-lite Daily Ops Command Center', {
    x: 9.35, y: 0.5, w: 3.35, h: 0.25,
    fontFace: 'Aptos', fontSize: 8.5, color: C.muted,
    align: 'right', margin: 0,
  });
}

function footer(slide, no) {
  slide.addText(String(no).padStart(2, '0'), {
    x: 12.28, y: 6.95, w: 0.42, h: 0.22,
    fontFace: 'Aptos', fontSize: 8.5, color: C.muted,
    align: 'right', margin: 0,
  });
}

function card(slide, x, y, w, h, opts = {}) {
  slide.addShape(pptx.ShapeType.roundRect, {
    x, y, w, h,
    rectRadius: 0.055,
    fill: { color: opts.fill || C.paper },
    line: { color: opts.line || C.line, width: opts.width || 0.75 },
    shadow: opts.shadow === false ? undefined : { type: 'outer', color: '000000', opacity: 0.09, blur: 1.2, angle: 45, distance: 1 },
  });
}

function pill(slide, text, x, y, w, color = C.teal, fill = C.sky) {
  slide.addShape(pptx.ShapeType.roundRect, {
    x, y, w, h: 0.34,
    rectRadius: 0.08,
    fill: { color: fill },
    line: { color: fill },
  });
  slide.addText(text, {
    x: x + 0.12, y: y + 0.075, w: w - 0.24, h: 0.16,
    fontFace: 'Noto Sans Thai', fontSize: 8.5, bold: true, color,
    align: 'center', margin: 0,
  });
}

function bulletList(slide, items, x, y, w, h, size = 14, color = C.ink) {
  const runs = [];
  items.forEach((item) => runs.push({ text: item, options: { bullet: { type: 'bullet' }, hanging: 4 } }));
  slide.addText(runs, {
    x, y, w, h,
    fontFace: 'Noto Sans Thai',
    fontSize: size,
    color,
    fit: 'shrink',
    valign: 'top',
    paraSpaceAfterPt: 7,
    breakLine: false,
    margin: 0.02,
  });
}

function label(slide, text, x, y, w, color = C.muted, size = 8.2, align = 'left') {
  slide.addText(text, {
    x, y, w, h: 0.22,
    fontFace: 'Noto Sans Thai', fontSize: size, color, margin: 0, align,
  });
}

function addDiagram(slide, filename, x, y, w, h, caption) {
  const file = path.join(diagramDir, filename);
  card(slide, x, y, w, h, { shadow: true });
  if (fs.existsSync(file)) {
    slide.addImage({ path: file, ...contain(file, x + 0.12, y + 0.12, w - 0.24, h - 0.44) });
  } else {
    slide.addText(`ไม่พบไฟล์ ${filename}`, {
      x: x + 0.2, y: y + h / 2 - 0.15, w: w - 0.4, h: 0.3,
      fontFace: 'Noto Sans Thai', fontSize: 11, color: C.red, align: 'center', margin: 0,
    });
  }
  label(slide, caption, x + 0.2, y + h - 0.27, w - 0.4, C.muted, 8, 'center');
}

function iconBubble(slide, txt, x, y, fill, color = C.paper) {
  slide.addShape(pptx.ShapeType.ellipse, {
    x, y, w: 0.48, h: 0.48,
    fill: { color: fill },
    line: { color: fill },
  });
  slide.addText(txt, {
    x, y: y + 0.085, w: 0.48, h: 0.22,
    fontFace: 'Aptos', fontSize: 11, bold: true, color,
    align: 'center', margin: 0,
  });
}

function titleSlide() {
  const s = pptx.addSlide();
  bg(s, true);
  pill(s, 'CODEBASE-FIRST PRESENTATION', 0.78, 0.7, 2.5, C.teal2, 'D9FFFA');
  s.addText('A-lite Daily Ops\nCommand Center', {
    x: 0.78, y: 1.28, w: 7.1, h: 1.45,
    fontFace: 'Aptos Display', fontSize: 38, bold: true, color: C.paper,
    breakLine: false, fit: 'shrink', margin: 0,
  });
  s.addText('ระบบจัดการงานปฏิบัติการประจำวันสำหรับทีมดูแลห้องคอมของมหาวิทยาลัย', {
    x: 0.82, y: 2.92, w: 7.35, h: 0.38,
    fontFace: 'Noto Sans Thai', fontSize: 17, color: 'E6EEF7', margin: 0,
  });
  const stats = [
    ['3', 'บทบาทผู้ใช้', 'Staff / Supervisor / Admin'],
    ['5', 'กระบวนการหลัก', 'User, Template, Checklist, Incident, Dashboard'],
    ['8', 'ตารางหลัก', 'Schema truth จาก migrations/models'],
  ];
  stats.forEach(([num, title, body], i) => {
    const x = 0.82 + i * 3.95;
    card(s, x, 4.3, 3.45, 1.15, { fill: '20384D', line: '2F4B63', shadow: false });
    s.addText(num, { x: x + 0.18, y: 4.53, w: 0.55, h: 0.4, fontFace: 'Aptos Display', fontSize: 26, bold: true, color: C.teal2, margin: 0 });
    s.addText(title, { x: x + 0.82, y: 4.46, w: 2.35, h: 0.25, fontFace: 'Noto Sans Thai', fontSize: 11, bold: true, color: C.paper, margin: 0 });
    s.addText(body, { x: x + 0.82, y: 4.78, w: 2.35, h: 0.35, fontFace: 'Noto Sans Thai', fontSize: 8.5, color: C.darkMuted, margin: 0, fit: 'shrink' });
  });
  s.addText('ที่มาและความสำคัญ | ขอบเขต | เครื่องมือ | Fishbone & Flowchart | DFD | Data Model | Data Dictionary', {
    x: 0.82, y: 6.25, w: 11.7, h: 0.28,
    fontFace: 'Noto Sans Thai', fontSize: 10.5, color: 'DDE8F2', margin: 0,
  });
}

function backgroundSlide(no) {
  const s = pptx.addSlide();
  header(s, 'ที่มาและความสำคัญ', '01 BACKGROUND');
  card(s, 0.72, 1.45, 4.05, 4.95, { fill: C.paper });
  s.addText('ปัญหาเริ่มจากข้อมูลกระจัดกระจาย', {
    x: 1.02, y: 1.82, w: 3.3, h: 0.5,
    fontFace: 'Noto Sans Thai', fontSize: 20, bold: true, color: C.ink,
    fit: 'shrink', margin: 0,
  });
  bulletList(s, [
    'งานตรวจห้องคอมเกิดซ้ำทุกวันตามช่วงเปิดห้อง ตรวจระหว่างวัน และปิดห้อง',
    'ข้อมูลเดิมอยู่ในกระดาษ ไฟล์แยก หรือข้อความสนทนา',
    'ผู้ดูแลต้องรวบรวมข้อมูลเองก่อนเห็นสถานะของวัน',
  ], 1.02, 2.65, 3.38, 2.1, 13);
  iconBubble(s, '!', 1.02, 5.35, C.amber);
  s.addText('ผลกระทบ: ติดตามย้อนหลังยาก ตัดสินใจช้า และเสี่ยงต่อการตกหล่นของงาน', {
    x: 1.62, y: 5.37, w: 2.75, h: 0.46,
    fontFace: 'Noto Sans Thai', fontSize: 10.5, color: C.ink,
    margin: 0, fit: 'shrink',
  });

  const flowX = 5.25;
  const blocks = [
    ['กระดาษ / ไฟล์แยก', 'ข้อมูลไม่รวมศูนย์', C.redSoft, C.red],
    ['บริบทหน้างานไม่ชัด', 'ห้อง เวลา ผู้รับผิดชอบ', C.amberSoft, C.amber],
    ['ทบทวนย้อนหลังยาก', 'หลักฐานไม่ครบถ้วน', C.blueSoft, C.blue],
  ];
  blocks.forEach(([t, b, fill, color], i) => {
    const y = 1.55 + i * 1.55;
    card(s, flowX, y, 3.0, 0.95, { fill, line: fill, shadow: false });
    iconBubble(s, String(i + 1), flowX + 0.18, y + 0.23, color);
    s.addText(t, { x: flowX + 0.82, y: y + 0.18, w: 1.9, h: 0.22, fontFace: 'Noto Sans Thai', fontSize: 11.2, bold: true, color: C.ink, margin: 0 });
    s.addText(b, { x: flowX + 0.82, y: y + 0.48, w: 1.9, h: 0.2, fontFace: 'Noto Sans Thai', fontSize: 8.5, color: C.muted, margin: 0 });
    if (i < blocks.length - 1) {
      s.addShape(pptx.ShapeType.downArrow, { x: flowX + 1.35, y: y + 1.0, w: 0.28, h: 0.32, fill: { color }, line: { color } });
    }
  });
  card(s, 8.75, 1.55, 3.7, 4.02, { fill: C.navy, line: C.navy });
  s.addText('เป้าหมายของระบบใหม่', { x: 9.1, y: 1.95, w: 2.7, h: 0.32, fontFace: 'Noto Sans Thai', fontSize: 17, bold: true, color: C.paper, margin: 0 });
  bulletList(s, [
    'รวม checklist, incident และ dashboard ในระบบเดียว',
    'ทำให้บทบาท Staff, Supervisor และ Admin ชัดเจน',
    'เก็บข้อมูลให้ตรวจสอบย้อนหลังและใช้เป็นหลักฐานได้',
  ], 9.08, 2.58, 2.85, 1.85, 12.2, 'E8F1FA');
  pill(s, 'Internal web application', 9.1, 4.75, 2.55, C.teal2, 'D9FFFA');
  footer(s, no);
}

function scopeSlide(no) {
  const s = pptx.addSlide();
  header(s, 'ขอบเขตโครงงาน', '02 SCOPE');
  const roles = [
    ['Staff', 'ตรวจเช็กประจำวัน\nแจ้งเหตุผิดปกติ', C.green, C.greenSoft],
    ['Supervisor', 'ติดตามแดชบอร์ด\nอัปเดตสถานะ incident', C.blue, C.blueSoft],
    ['Admin', 'จัดการผู้ใช้\nจัดการแม่แบบ checklist', C.violet, C.violetSoft],
  ];
  roles.forEach(([role, body, color, fill], i) => {
    const x = 0.72 + i * 4.18;
    card(s, x, 1.45, 3.62, 1.75, { fill, line: fill });
    iconBubble(s, role[0], x + 0.28, 1.79, color);
    s.addText(role, { x: x + 0.92, y: 1.75, w: 2.2, h: 0.28, fontFace: 'Aptos Display', fontSize: 17, bold: true, color, margin: 0 });
    s.addText(body, { x: x + 0.92, y: 2.12, w: 2.25, h: 0.52, fontFace: 'Noto Sans Thai', fontSize: 11.3, color: C.ink, margin: 0, breakLine: false, fit: 'shrink' });
  });
  s.addShape(pptx.ShapeType.chevron, { x: 2.0, y: 3.72, w: 1.0, h: 0.55, fill: { color: C.teal }, line: { color: C.teal }, rotate: 90 });
  s.addShape(pptx.ShapeType.chevron, { x: 6.17, y: 3.72, w: 1.0, h: 0.55, fill: { color: C.teal }, line: { color: C.teal }, rotate: 90 });
  s.addShape(pptx.ShapeType.chevron, { x: 10.35, y: 3.72, w: 1.0, h: 0.55, fill: { color: C.teal }, line: { color: C.teal }, rotate: 90 });
  card(s, 1.05, 4.42, 11.25, 1.24, { fill: C.paper });
  s.addText('ขอบเขตที่ระบบรองรับ', { x: 1.35, y: 4.73, w: 2.0, h: 0.25, fontFace: 'Noto Sans Thai', fontSize: 13.2, bold: true, color: C.ink, margin: 0 });
  s.addText('Daily checklist • Incident queue/history • Dashboard • User management • Checklist template management • Printable recap/summary', {
    x: 3.45, y: 4.72, w: 8.25, h: 0.32,
    fontFace: 'Aptos', fontSize: 12, color: C.teal, bold: true, margin: 0,
    fit: 'shrink',
  });
  s.addText('ไม่รวม: public signup, notification เต็มรูปแบบ, approval workflow, machine registry, analytics warehouse, mobile app แยก, AI/copilot', {
    x: 1.35, y: 5.21, w: 10.7, h: 0.28,
    fontFace: 'Noto Sans Thai', fontSize: 9.5, color: C.muted, margin: 0,
  });
  footer(s, no);
}

function toolsSlide(no) {
  const s = pptx.addSlide();
  header(s, 'เครื่องมือที่ใช้จาก Codebase', '03 TOOLS');
  const tools = [
    ['Backend', 'PHP 8.4\\nLaravel 13\\nLaravel Fortify', C.navy, 'E8EEF5'],
    ['Frontend', 'Livewire 4\\nFlux 2\\nTailwind CSS + Vite', C.teal, C.sky],
    ['Data', 'Laravel migrations\\nEloquent models\\nSQLite dev database', C.violet, C.violetSoft],
    ['Quality', 'Pest / Browser tests\\nLaravel Pint\\nComposer + NPM', C.amber, C.amberSoft],
  ];
  tools.forEach(([title, body, color, fill], i) => {
    const x = 0.72 + (i % 2) * 6.1;
    const y = 1.45 + Math.floor(i / 2) * 2.25;
    card(s, x, y, 5.55, 1.72, { fill });
    s.addShape(pptx.ShapeType.rect, { x, y, w: 0.12, h: 1.72, fill: { color }, line: { color } });
    s.addText(title, { x: x + 0.35, y: y + 0.28, w: 1.8, h: 0.28, fontFace: 'Aptos Display', fontSize: 16, bold: true, color, margin: 0 });
    s.addText(body, { x: x + 0.35, y: y + 0.72, w: 4.75, h: 0.68, fontFace: 'Noto Sans Thai', fontSize: 12.2, color: C.ink, margin: 0, fit: 'shrink', breakLine: false });
  });
  card(s, 0.92, 6.03, 11.5, 0.53, { fill: C.paper, shadow: false });
  s.addText('Source evidence: composer.json, package.json, routes/web.php, app/Livewire, app/Models, database/migrations, tests', {
    x: 1.18, y: 6.21, w: 10.95, h: 0.18,
    fontFace: 'Aptos', fontSize: 9.5, color: C.muted, margin: 0,
  });
  footer(s, no);
}

function diagramSlide(no, title, kicker, file, caption, callouts) {
  const s = pptx.addSlide();
  header(s, title, kicker);
  addDiagram(s, file, 0.72, 1.32, 8.15, 5.58, caption);
  card(s, 9.15, 1.45, 3.35, 5.28, { fill: C.paper });
  s.addText('จุดที่ควรเล่า', { x: 9.48, y: 1.78, w: 2.2, h: 0.3, fontFace: 'Noto Sans Thai', fontSize: 16, bold: true, color: C.ink, margin: 0 });
  bulletList(s, callouts, 9.5, 2.32, 2.55, 3.4, 11.5);
  footer(s, no);
}

function dfdLevel2Slide(no) {
  const s = pptx.addSlide();
  header(s, 'DFD Level 2: กระบวนการที่ควรเปิดประกอบ', '07 DFD LEVEL 2');
  const items = [
    ['3.0 ตรวจเช็กประจำวัน', 'ch3-05-dfd-level2-process-3.png', 'ตรวจสอบผู้ใช้ เลือกห้อง/ช่วงงาน สร้างหรือใช้ checklist run และบันทึกผล'],
    ['4.0 จัดการเหตุผิดปกติ', 'ch3-06-dfd-level2-process-4.png', 'Staff สร้าง incident ส่วน Supervisor/Admin อัปเดตสถานะและติดตาม'],
    ['5.0 ติดตามภาพรวม', 'ch3-07-dfd-level2-process-5.png', 'อ่าน D4/D5/D6 เพื่อสร้าง dashboard, history และ summary'],
  ];
  items.forEach(([title, file, body], i) => {
    const x = 0.68 + i * 4.23;
    card(s, x, 1.45, 3.76, 4.92, { fill: C.paper });
    s.addText(title, { x: x + 0.28, y: 1.75, w: 3.15, h: 0.28, fontFace: 'Noto Sans Thai', fontSize: 13.2, bold: true, color: C.teal, margin: 0 });
    const p = path.join(diagramDir, file);
    if (fs.existsSync(p)) s.addImage({ path: p, ...contain(p, x + 0.18, 2.16, 3.4, 2.48) });
    s.addText(body, { x: x + 0.32, y: 4.9, w: 3.08, h: 0.66, fontFace: 'Noto Sans Thai', fontSize: 9.4, color: C.ink, margin: 0, fit: 'shrink' });
    pill(s, 'เปิดเล่มเมื่อถามเส้นข้อมูลละเอียด', x + 0.45, 5.7, 2.8, C.muted, C.wash);
  });
  footer(s, no);
}

function dataDictionarySlide(no) {
  const s = pptx.addSlide();
  header(s, 'Data Dictionary: สรุปจาก schema truth', '09 DATA DICTIONARY');
  const groups = [
    ['ผู้ใช้และพื้นที่', ['users', 'rooms'], C.blue, C.blueSoft],
    ['Checklist', ['checklist_templates', 'checklist_items', 'checklist_runs', 'checklist_run_items'], C.teal, C.sky],
    ['Incident', ['incidents', 'incident_activities'], C.amber, C.amberSoft],
  ];
  groups.forEach(([title, rows, color, fill], i) => {
    const x = 0.82 + i * 4.08;
    card(s, x, 1.52, 3.55, 3.95, { fill });
    s.addText(title, { x: x + 0.25, y: 1.86, w: 2.55, h: 0.25, fontFace: 'Noto Sans Thai', fontSize: 14.3, bold: true, color, margin: 0 });
    rows.forEach((r, j) => {
      const y = 2.38 + j * 0.48;
      s.addShape(pptx.ShapeType.roundRect, { x: x + 0.25, y, w: 3.0, h: 0.32, rectRadius: 0.035, fill: { color: C.paper }, line: { color: C.paper } });
      s.addText(r, { x: x + 0.43, y: y + 0.075, w: 2.65, h: 0.14, fontFace: 'Aptos', fontSize: 9.2, bold: true, color: C.ink, margin: 0 });
    });
  });
  card(s, 1.02, 5.9, 11.15, 0.68, { fill: C.navy, line: C.navy, shadow: false });
  s.addText('Data Dictionary ในเล่มแสดง Attribute, Description, Type, Constraint และ Null ครบทุกตารางตาม migrations/models', {
    x: 1.42, y: 6.11, w: 10.35, h: 0.22,
    fontFace: 'Noto Sans Thai', fontSize: 11.2, color: C.paper, align: 'center', margin: 0,
  });
  footer(s, no);
}

function resultsSlide(no) {
  const s = pptx.addSlide();
  header(s, 'ผลลัพธ์ที่ยืนยันจากระบบจริง', '10 IMPLEMENTATION');
  const rows = [
    ['Authentication + Roles', 'แยก route ตาม Staff, Supervisor และ Admin', C.violet],
    ['Daily Checklist', 'เลือกห้อง/ช่วงงาน สร้างหรือใช้ checklist run และบันทึกผล', C.teal],
    ['Incident Management', 'สร้าง incident แนบหลักฐาน อัปเดตสถานะ เจ้าของ และวันติดตาม', C.amber],
    ['Dashboard + History', 'สรุปสถานะงาน คิวปัญหา ประวัติ checklist/incident', C.blue],
    ['Printable Evidence', 'Checklist recap และ incident summary สำหรับทบทวน', C.green],
  ];
  rows.forEach(([title, body, color], i) => {
    const y = 1.38 + i * 0.98;
    card(s, 1.05, y, 11.25, 0.72, { fill: C.paper });
    iconBubble(s, String(i + 1), 1.35, y + 0.12, color);
    s.addText(title, { x: 2.05, y: y + 0.18, w: 2.55, h: 0.2, fontFace: 'Aptos Display', fontSize: 13, bold: true, color, margin: 0 });
    s.addText(body, { x: 4.75, y: y + 0.18, w: 6.65, h: 0.22, fontFace: 'Noto Sans Thai', fontSize: 10.8, color: C.ink, margin: 0, fit: 'shrink' });
  });
  footer(s, no);
}

function summarySlide(no) {
  const s = pptx.addSlide();
  bg(s, true);
  s.addText('สรุปสำหรับการนำเสนอ', { x: 0.82, y: 0.85, w: 6.8, h: 0.5, fontFace: 'Noto Sans Thai', fontSize: 28, bold: true, color: C.paper, margin: 0 });
  const points = [
    ['รวมศูนย์ข้อมูล', 'ลดการกระจายของกระดาษ ไฟล์แยก และข้อความสนทนา'],
    ['ทำงานตามบทบาท', 'Staff ทำงานหน้างาน Supervisor/Admin ติดตามและดูแลระบบ'],
    ['ตรวจสอบย้อนหลังได้', 'ข้อมูล checklist, incident และ activity log ใช้ทบทวนเป็นหลักฐานได้'],
  ];
  points.forEach(([t, b], i) => {
    card(s, 0.92 + i * 4.1, 2.1, 3.55, 2.1, { fill: '20384D', line: '2F4B63', shadow: false });
    iconBubble(s, String(i + 1), 1.22 + i * 4.1, 2.45, [C.teal2, C.amber, C.green][i]);
    s.addText(t, { x: 1.22 + i * 4.1, y: 3.04, w: 2.88, h: 0.26, fontFace: 'Noto Sans Thai', fontSize: 15, bold: true, color: C.paper, margin: 0 });
    s.addText(b, { x: 1.22 + i * 4.1, y: 3.48, w: 2.85, h: 0.46, fontFace: 'Noto Sans Thai', fontSize: 9.7, color: C.darkMuted, margin: 0, fit: 'shrink' });
  });
  s.addText('แนวทางพัฒนาต่อ: notification พื้นฐาน, การพิมพ์/ส่งออกสรุปรายงานตามช่วงเวลา, mobile-friendly workflow และ test coverage เพิ่มเติม', {
    x: 1.05, y: 5.35, w: 11.15, h: 0.52,
    fontFace: 'Noto Sans Thai', fontSize: 14, color: 'E6EEF7', align: 'center', margin: 0, fit: 'shrink',
  });
  footer(s, no);
}

titleSlide();
backgroundSlide(2);
scopeSlide(3);
toolsSlide(4);
diagramSlide(5, 'ก้างปลา: สาเหตุและผลกระทบของระบบเดิม', '04 FISHBONE', 'ch3-01-cause-effect-final.png', 'Cause and Effect Analysis', [
  'แสดงปัญหาจากวิธีทำงานเดิม เครื่องมือ ข้อมูล คน และการติดตาม',
  'ใช้เป็น slide เปิดประเด็นก่อนเข้าสู่ระบบงานใหม่',
  'ถ้ารายละเอียดแน่น ให้เปิดภาพในเล่มประกอบ',
]);
diagramSlide(6, 'Flowchart: ภาพรวมระบบงานใหม่', '05 SYSTEM FLOW', 'ch3-02-system-flowchart-v2.png', 'System Flowchart', [
  'เริ่มจาก Staff เลือกห้องและช่วงงาน',
  'ระบบตรวจ checklist ที่พร้อมใช้งานและรอบการตรวจของวัน',
  'Supervisor/Admin ติดตามผ่านแดชบอร์ดและ incident queue',
]);
diagramSlide(7, 'Context Diagram: ขอบเขตของระบบ', '06 CONTEXT', 'ch3-03-context-diagram-v2.png', 'Context Diagram Level 0', [
  'External entities มีเพียง Staff, Supervisor และ Admin',
  'ไม่แสดง subprocess หรือ data store ภายใน',
  'เน้นข้อมูลเข้า-ออกระดับสูงของระบบกลาง',
]);
diagramSlide(8, 'DFD Level 1: กระบวนการหลัก', '07 DFD LEVEL 1', 'ch3-04-dfd-level1-v3.png', 'Data Flow Diagram Level 1', [
  'แบ่งระบบเป็น 5 processes ตาม codebase และ diagrams',
  'Data stores D1-D6 สอดคล้องกับ schema truth',
  'เหมาะสำหรับเล่าภาพรวมก่อนลง Level 2',
]);
dfdLevel2Slide(9);
diagramSlide(10, 'Data Model: ERD', '08 DATA MODEL', 'ch3-08-erd-final.png', 'Entity Relationship Diagram', [
  '8 entities ตรงกับ models และ migrations',
  'แยกกลุ่ม checklist และ incident ชัดเจน',
  'ERD ย่อบาง auth fields เพื่อให้อ่านง่าย ส่วน Data Dictionary แสดงครบ',
]);
dataDictionarySlide(11);
resultsSlide(12);
summarySlide(13);

pptx.writeFile({ fileName: path.join(outDir, 'A-lite-Daily-Ops-Command-Center-presentation-redesign.pptx') });
