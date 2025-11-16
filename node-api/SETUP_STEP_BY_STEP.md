# 🚀 Setup Step by Step - เชื่อม n8n กับ LINE Bot

คู่มือทีละขั้นตอนสำหรับเชื่อม n8n กับ LINE Bot ให้ทำงานได้

---

## 📋 สิ่งที่ต้องเตรียม

- ✅ LINE Developer Account
- ✅ LINE Messaging API Channel
- ✅ Node.js และ npm ติดตั้งแล้ว
- ✅ n8n ติดตั้งแล้ว (หรือจะติดตั้งในขั้นตอนนี้)

---

## 🎯 ขั้นตอนที่ 1: ติดตั้งและรัน n8n

### 1.1 ติดตั้ง n8n (ถ้ายังไม่มี)

```bash
npm install -g n8n
```

### 1.2 รัน n8n

```bash
cd node-api
./start-n8n.sh
```

หรือ

```bash
export N8N_SECURE_COOKIE=false
n8n start
```

### 1.3 เปิด n8n Dashboard

เปิดเบราว์เซอร์ไปที่: **http://localhost:5678**

### 1.4 สร้าง Account (ครั้งแรกเท่านั้น)

- กรอก Email และ Password
- คลิก "Get Started"

✅ **Checkpoint:** คุณควรเห็น n8n Dashboard แล้ว

---

## 🎯 ขั้นตอนที่ 2: รัน Node.js API

### 2.1 เปิด Terminal ใหม่

```bash
cd node-api
npm install
```

### 2.2 ตรวจสอบ .env

เปิดไฟล์ `node-api/.env` และตรวจสอบว่ามี:

```env
LINE_CHANNEL_SECRET=your_channel_secret
LINE_CHANNEL_ACCESS_TOKEN=your_access_token
```

### 2.3 รัน API

```bash
npm start
```

### 2.4 ทดสอบ API

เปิดเบราว์เซอร์: **http://localhost:3000**

ควรเห็น:
```json
{
  "name": "Log API Server",
  "version": "1.0.0",
  "status": "running",
  ...
}
```

✅ **Checkpoint:** API ทำงานแล้ว

---

## 🎯 ขั้นตอนที่ 3: Import Workflows ใน n8n

### 3.1 ไปที่ n8n Dashboard

เปิด: **http://localhost:5678**

### 3.2 Import Workflow 1: LINE Bot Auto Reply

1. คลิก **"Workflows"** (เมนูซ้าย)
2. คลิก **"Add Workflow"** → **"Import from File"**
3. เลือกไฟล์: `node-api/n8n-workflows/1-line-bot-auto-reply.json`
4. คลิก **"Import"**

### 3.3 Import Workflow 2: Daily Log Report

1. ทำซ้ำขั้นตอนเดิม
2. เลือกไฟล์: `node-api/n8n-workflows/2-daily-log-report.json`
3. คลิก **"Import"**

### 3.4 Import Workflow 3: Error Alert Monitor

1. ทำซ้ำขั้นตอนเดิม
2. เลือกไฟล์: `node-api/n8n-workflows/3-error-alert-monitor.json`
3. คลิก **"Import"**

✅ **Checkpoint:** คุณควรเห็น 3 workflows ใน n8n แล้ว

---

## 🎯 ขั้นตอนที่ 4: หา LINE User ID

### 4.1 ส่งข้อความไปที่ LINE Bot

เปิด LINE แล้วส่งข้อความอะไรก็ได้ไปที่ Bot ของคุณ

### 4.2 ดู Logs ใน Terminal

ใน terminal ที่รัน Node.js API จะแสดง:

```
[2025-11-16T...] POST /webhook/line
```

### 4.3 ดู User ID

ถ้าไม่เห็น User ID ให้เพิ่ม log ใน `node-api/routes/line.js`:

```javascript
router.post('/line', line.middleware(config), async (req, res) => {
  try {
    const events = req.body.events;
    
    // เพิ่มบรรทัดนี้
    console.log('LINE Event:', JSON.stringify(events, null, 2));
    
    await Promise.all(events.map(handleEvent));
    res.json({ success: true });
  } catch (error) {
    console.error('LINE webhook error:', error);
    res.status(500).json({ error: error.message });
  }
});
```

ส่งข้อความใหม่แล้วดู logs จะเห็น:

```json
{
  "source": {
    "userId": "Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
  }
}
```

คัดลอก `userId` นี้ไว้

✅ **Checkpoint:** คุณมี LINE User ID แล้ว

---

## 🎯 ขั้นตอนที่ 5: ตั้งค่า Environment Variables ใน n8n

### 5.1 ไปที่ Settings

ใน n8n Dashboard:
1. คลิก **"Settings"** (เมนูซ้าย)
2. คลิก **"Environment Variables"**

### 5.2 เพิ่ม Variables

คลิก **"Add Variable"** และเพิ่ม:

**Variable 1:**
- Name: `LINE_CHANNEL_ACCESS_TOKEN`
- Value: `your_line_channel_access_token`

**Variable 2:**
- Name: `LINE_USER_ID`
- Value: `Uxxxxxxxxxxxxxxxxxxxxxxxxxxxxx` (จากขั้นตอนที่ 4)

### 5.3 Save

คลิก **"Save"**

✅ **Checkpoint:** Environment Variables ตั้งค่าเรียบร้อย

---

## 🎯 ขั้นตอนที่ 6: ตั้งค่า Workflow 1 - LINE Bot Auto Reply

### 6.1 เปิด Workflow

1. ไปที่ **"Workflows"**
2. คลิกที่ **"LINE Bot - Auto Reply"**

### 6.2 ตรวจสอบ Webhook Node

1. คลิกที่ node **"Webhook - LINE"**
2. คัดลอก **Webhook URL** (จะเป็น `http://localhost:5678/webhook/line-bot`)
3. เก็บ URL นี้ไว้ใช้ในขั้นตอนถัดไป

### 6.3 ทดสอบ Workflow

1. คลิกที่ node **"Extract Message"**
2. คลิก **"Execute Node"** (ปุ่มด้านบน)
3. ถ้ามี error ไม่เป็นไร เพราะยังไม่มีข้อมูล

### 6.4 Activate Workflow

1. คลิกสวิตช์ **"Inactive"** ด้านบนขวา
2. เปลี่ยนเป็น **"Active"**

✅ **Checkpoint:** Workflow 1 พร้อมใช้งาน

---

## 🎯 ขั้นตอนที่ 7: เชื่อม LINE Webhook กับ n8n (ใช้ ngrok)

### 7.1 ติดตั้ง ngrok

ดาวน์โหลดจาก: https://ngrok.com/download

หรือใช้ Homebrew:
```bash
brew install ngrok
```

### 7.2 รัน ngrok

เปิด Terminal ใหม่:

```bash
ngrok http 5678
```

### 7.3 คัดลอก Public URL

จะเห็น:
```
Forwarding  https://xxxx-xxxx-xxxx.ngrok.io -> http://localhost:5678
```

คัดลอก URL: `https://xxxx-xxxx-xxxx.ngrok.io`

### 7.4 ตั้งค่า LINE Webhook

1. ไปที่ **LINE Developers Console**: https://developers.line.biz/console/
2. เลือก Channel ของคุณ
3. ไปที่ **"Messaging API"** tab
4. หา **"Webhook settings"**
5. คลิก **"Edit"**
6. ใส่ URL: `https://xxxx-xxxx-xxxx.ngrok.io/webhook/line-bot`
7. คลิก **"Update"**
8. เปิด **"Use webhook"** (toggle เป็นสีเขียว)
9. คลิก **"Verify"** เพื่อทดสอบ

ควรเห็น: ✅ **Success**

✅ **Checkpoint:** LINE Webhook เชื่อมกับ n8n แล้ว

---

## 🎯 ขั้นตอนที่ 8: ทดสอบ LINE Bot

### 8.1 ส่งข้อความทดสอบ

เปิด LINE แล้วส่งข้อความไปที่ Bot:

```
/help
```

### 8.2 ตรวจสอบการตอบกลับ

Bot ควรตอบกลับ:

```
📚 คำสั่งที่ใช้ได้:

/stats - สถิติ logs วันนี้
/logs - logs ล่าสุด 5 รายการ
/dates - วันที่ที่มี logs
/help - แสดงคำสั่งนี้
```

### 8.3 ทดสอบคำสั่งอื่นๆ

```
/stats
/logs
```

### 8.4 ดู Execution Log ใน n8n

1. ไปที่ **"Executions"** (เมนูซ้าย)
2. คลิกที่ execution ล่าสุด
3. ดู flow และ data ที่ผ่านแต่ละ node

✅ **Checkpoint:** LINE Bot ตอบกลับได้แล้ว! 🎉

---

## 🎯 ขั้นตอนที่ 9: ตั้งค่า Workflow 2 - Daily Report

### 9.1 เปิด Workflow

1. ไปที่ **"Workflows"**
2. คลิกที่ **"Daily Log Report"**

### 9.2 แก้ไข Schedule

1. คลิกที่ node **"Schedule - Daily 9 AM"**
2. ตั้งเวลาที่ต้องการ (default: 9:00 AM)
3. คลิก **"Save"**

### 9.3 ทดสอบทันที

1. คลิกที่ node **"Schedule - Daily 9 AM"**
2. คลิก **"Execute Node"**
3. รอสักครู่
4. ตรวจสอบ LINE ว่าได้รับรายงานหรือไม่

### 9.4 Activate Workflow

คลิกสวิตช์ **"Inactive"** → **"Active"**

✅ **Checkpoint:** Daily Report ทำงานแล้ว

---

## 🎯 ขั้นตอนที่ 10: ตั้งค่า Workflow 3 - Error Alert

### 10.1 เปิด Workflow

1. ไปที่ **"Workflows"**
2. คลิกที่ **"Error Alert Monitor"**

### 10.2 แก้ไข Interval (ถ้าต้องการ)

1. คลิกที่ node **"Every 5 Minutes"**
2. ปรับเวลาตามต้องการ
3. คลิก **"Save"**

### 10.3 ทดสอบ

1. คลิกที่ node **"Every 5 Minutes"**
2. คลิก **"Execute Node"**
3. ถ้ามี error ใน logs จะได้รับการแจ้งเตือนใน LINE

### 10.4 Activate Workflow

คลิกสวิตช์ **"Inactive"** → **"Active"**

✅ **Checkpoint:** Error Alert ทำงานแล้ว

---

## 🎉 เสร็จสิ้น!

ตอนนี้คุณมีระบบครบทั้ง 3 อย่างแล้ว:

1. ✅ **LINE Bot Auto Reply** - ตอบข้อความอัตโนมัติ
2. ✅ **Daily Log Report** - ส่งรายงานประจำวัน
3. ✅ **Error Alert Monitor** - แจ้งเตือน error real-time

---

## 📊 สรุป Architecture

```
┌─────────────┐
│  LINE User  │ ส่งข้อความ
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│  LINE Platform  │
└──────┬──────────┘
       │ Webhook
       ▼
┌─────────────────┐
│     ngrok       │ Tunnel
└──────┬──────────┘
       │
       ▼
┌─────────────────┐      ┌──────────────┐
│      n8n        │◄────►│ Node.js API  │
│   (localhost)   │      │ (localhost)  │
└──────┬──────────┘      └──────────────┘
       │
       ▼
┌─────────────────┐
│  LINE Platform  │ ตอบกลับ
└──────┬──────────┘
       │
       ▼
┌─────────────┐
│  LINE User  │ ได้รับข้อความ
└─────────────┘
```

---

## 🔧 Troubleshooting

### Bot ไม่ตอบ

**ตรวจสอบ:**
1. ✅ n8n รันอยู่ (`http://localhost:5678`)
2. ✅ Node.js API รันอยู่ (`http://localhost:3000`)
3. ✅ ngrok รันอยู่
4. ✅ Workflow "LINE Bot Auto Reply" เป็น Active
5. ✅ LINE Webhook URL ถูกต้อง
6. ✅ LINE Webhook เปิดใช้งาน (Use webhook = ON)

**ดู Logs:**
- n8n: Executions → คลิกที่ execution ล่าสุด
- Node.js API: ดู terminal ที่รัน `npm start`
- LINE: Developers Console → Webhook settings → View logs

### ไม่ได้รับรายงานประจำวัน

**ตรวจสอบ:**
1. ✅ Workflow "Daily Log Report" เป็น Active
2. ✅ `LINE_USER_ID` ถูกต้อง
3. ✅ เวลาใน Schedule node ถูกต้อง

**ทดสอบ:**
- Execute Node ที่ "Schedule - Daily 9 AM"

### ไม่ได้รับการแจ้งเตือน Error

**ตรวจสอบ:**
1. ✅ Workflow "Error Alert Monitor" เป็น Active
2. ✅ มี error ใน logs จริงหรือไม่
3. ✅ Threshold ใน "Analyze for Errors" เหมาะสมหรือไม่

**ทดสอบ:**
- Execute Node ที่ "Every 5 Minutes"

---

## 📚 Resources

- [n8n Documentation](https://docs.n8n.io/)
- [LINE Messaging API](https://developers.line.biz/en/docs/messaging-api/)
- [ngrok Documentation](https://ngrok.com/docs)
- [Complete Guide](./N8N_COMPLETE_GUIDE.md)
- [Workflows README](./n8n-workflows/README.md)

---

## 💡 Tips

1. **ใช้ ngrok สำหรับทดสอบ** - ฟรีและง่าย
2. **ดู Execution History** - เพื่อ debug ปัญหา
3. **Backup Workflows** - Export เป็น JSON เก็บไว้
4. **Monitor Logs** - ตรวจสอบ logs เป็นประจำ
5. **Test ทีละ Node** - ใช้ "Execute Node" ทดสอบ

---

**Happy Automating! 🎉**

มีปัญหาหรือคำถาม? อ่านคู่มือเพิ่มเติมที่ `N8N_COMPLETE_GUIDE.md`
