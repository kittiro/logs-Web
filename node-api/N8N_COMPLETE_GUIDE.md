# 🤖 n8n Complete Guide - LINE Bot & Log Monitoring

คู่มือการตั้งค่า n8n ครบทั้ง 3 ระบบ:
1. ✅ LINE Bot ตอบอัตโนมัติ
2. 📊 รายงาน Logs ประจำวัน
3. 🚨 แจ้งเตือน Error แบบ Real-time

---

## 📋 สารบัญ

- [ติดตั้ง n8n](#ติดตั้ง-n8n)
- [ตั้งค่า Environment Variables](#ตั้งค่า-environment-variables)
- [Import Workflows](#import-workflows)
- [ตั้งค่าแต่ละ Workflow](#ตั้งค่าแต่ละ-workflow)
- [ทดสอบระบบ](#ทดสอบระบบ)
- [Troubleshooting](#troubleshooting)

---

## 🚀 ติดตั้ง n8n

### วิธีที่ 1: ใช้ Docker (แนะนำ)

```bash
docker run -it --rm \
  --name n8n \
  -p 5678:5678 \
  -v ~/.n8n:/home/node/.n8n \
  n8nio/n8n
```

### วิธีที่ 2: ใช้ npm

```bash
npm install -g n8n
n8n start
```

### วิธีที่ 3: ใช้ Script ที่เตรียมไว้

```bash
./start-with-n8n.sh
```

เปิดเบราว์เซอร์ไปที่: `http://localhost:5678`

---

## ⚙️ ตั้งค่า Environment Variables

### 1. เพิ่มใน `.env` ของ Node.js API

```env
# LINE Messaging API
LINE_CHANNEL_SECRET=your_channel_secret
LINE_CHANNEL_ACCESS_TOKEN=your_access_token

# LINE User ID (สำหรับรับการแจ้งเตือน)
LINE_USER_ID=your_user_id
```

### 2. หา LINE User ID

ส่งข้อความไปที่ LINE Bot แล้วดู logs:

```bash
cd node-api
npm start
```

ส่งข้อความอะไรก็ได้ไปที่ Bot แล้วดู console จะแสดง User ID

### 3. ตั้งค่า Environment Variables ใน n8n

ไปที่ n8n → Settings → Environment Variables:

```
LINE_CHANNEL_ACCESS_TOKEN=your_access_token
LINE_USER_ID=your_user_id
```

---

## 📥 Import Workflows

### 1. เข้า n8n Dashboard

เปิด `http://localhost:5678`

### 2. Import Workflows ทั้ง 3 ตัว

**Workflow 1: LINE Bot Auto Reply**
- ไปที่ Workflows → Import from File
- เลือกไฟล์: `node-api/n8n-workflows/1-line-bot-auto-reply.json`
- คลิก Import

**Workflow 2: Daily Log Report**
- Import: `node-api/n8n-workflows/2-daily-log-report.json`

**Workflow 3: Error Alert Monitor**
- Import: `node-api/n8n-workflows/3-error-alert-monitor.json`

---

## 🔧 ตั้งค่าแต่ละ Workflow

### 1️⃣ LINE Bot Auto Reply

**จุดประสงค์:** ให้ Bot ตอบข้อความอัตโนมัติผ่าน n8n

**ขั้นตอน:**

1. เปิด Workflow "LINE Bot - Auto Reply"
2. คลิกที่ node "Webhook - LINE"
3. คัดลอก Webhook URL (จะเป็น `http://localhost:5678/webhook/line-bot`)
4. ตั้งค่า LINE Webhook URL ใน LINE Developers Console:
   - ถ้าใช้ ngrok: `https://your-ngrok-url.ngrok.io/webhook/line-bot`
   - ถ้าใช้ cloudflare tunnel: `https://your-tunnel-url/webhook/line-bot`
5. คลิก "Activate" เพื่อเปิดใช้งาน Workflow

**คำสั่งที่รองรับ:**
- `/help` - แสดงคำสั่งทั้งหมด
- `/stats` - สถิติ logs
- `/logs` - logs ล่าสุด

**ทดสอบ:**
```bash
# ส่งข้อความไปที่ LINE Bot
/help
```

---

### 2️⃣ Daily Log Report

**จุดประสงค์:** ส่งรายงาน logs ประจำวันทุกเช้า 9 โมง

**ขั้นตอน:**

1. เปิด Workflow "Daily Log Report"
2. ตรวจสอบ node "Schedule - Daily 9 AM"
   - ตั้งเวลาที่ต้องการ (default: 9:00 AM)
3. แก้ไข node "Send LINE Report"
   - ตรวจสอบว่า `LINE_USER_ID` ถูกต้อง
4. คลิก "Activate" เพื่อเปิดใช้งาน

**ทดสอบทันที:**
- คลิกที่ node "Schedule - Daily 9 AM"
- คลิก "Execute Node" เพื่อทดสอบทันที

**รายงานจะประกอบด้วย:**
- 📈 สถิติรวม (Total requests, Unique IPs)
- 🔝 Top URLs
- 👥 Top Users
- ⏰ กิจกรรมล่าสุด

---

### 3️⃣ Error Alert Monitor

**จุดประสงค์:** ตรวจสอบ error และแจ้งเตือนทุก 5 นาที

**ขั้นตอน:**

1. เปิด Workflow "Error Alert Monitor"
2. ตรวจสอบ node "Every 5 Minutes"
   - ปรับเวลาได้ตามต้องการ (default: 5 นาที)
3. แก้ไข node "Send LINE Alert"
   - ตรวจสอบว่า `LINE_USER_ID` ถูกต้อง
4. คลิก "Activate" เพื่อเปิดใช้งาน

**ระบบจะตรวจสอบ:**
- ❌ HTTP Errors (4xx, 5xx)
- ⚠️ Suspicious Activity (IP ที่ request มากเกินไป)
- 🔐 Failed Login Attempts

**ทดสอบทันที:**
- คลิกที่ node "Every 5 Minutes"
- คลิก "Execute Node"

---

## 🧪 ทดสอบระบบ

### ทดสอบ LINE Bot

```bash
# 1. รัน Node.js API
cd node-api
npm start

# 2. รัน n8n (terminal ใหม่)
n8n start

# 3. รัน ngrok (terminal ใหม่)
ngrok http 5678

# 4. ตั้งค่า LINE Webhook URL
# https://your-ngrok-url.ngrok.io/webhook/line-bot

# 5. ส่งข้อความทดสอบ
/help
/stats
/logs
```

### ทดสอบ Daily Report

```bash
# ใน n8n Dashboard
# 1. เปิด Workflow "Daily Log Report"
# 2. คลิก node "Schedule - Daily 9 AM"
# 3. คลิก "Execute Node"
# 4. ตรวจสอบ LINE ว่าได้รับรายงานหรือไม่
```

### ทดสอบ Error Alert

```bash
# ใน n8n Dashboard
# 1. เปิด Workflow "Error Alert Monitor"
# 2. คลิก node "Every 5 Minutes"
# 3. คลิก "Execute Node"
# 4. ถ้ามี error จะได้รับการแจ้งเตือนใน LINE
```

---

## 🔄 Architecture Overview

```
┌─────────────┐
│  LINE User  │
└──────┬──────┘
       │ ส่งข้อความ
       ▼
┌─────────────────┐
│  LINE Platform  │
└──────┬──────────┘
       │ Webhook
       ▼
┌─────────────────┐      ┌──────────────┐
│      n8n        │◄────►│  Node.js API │
│   Workflows     │      │   (Logs)     │
└──────┬──────────┘      └──────────────┘
       │ Reply
       ▼
┌─────────────────┐
│  LINE Platform  │
└──────┬──────────┘
       │ ส่งกลับ
       ▼
┌─────────────┐
│  LINE User  │
└─────────────┘
```

---

## 🛠️ Troubleshooting

### ปัญหา: Bot ไม่ตอบ

**แก้ไข:**
1. ตรวจสอบว่า Workflow "LINE Bot Auto Reply" เปิดใช้งาน (Active)
2. ตรวจสอบ Webhook URL ใน LINE Developers Console
3. ตรวจสอบ `LINE_CHANNEL_ACCESS_TOKEN` ใน n8n
4. ดู Execution Log ใน n8n เพื่อดู error

### ปัญหา: ไม่ได้รับรายงานประจำวัน

**แก้ไข:**
1. ตรวจสอบว่า Workflow "Daily Log Report" เปิดใช้งาน
2. ตรวจสอบเวลาใน node "Schedule"
3. ตรวจสอบ `LINE_USER_ID` ถูกต้อง
4. ทดสอบด้วย "Execute Node" ก่อน

### ปัญหา: ไม่ได้รับการแจ้งเตือน Error

**แก้ไข:**
1. ตรวจสอบว่า Workflow "Error Alert Monitor" เปิดใช้งาน
2. ตรวจสอบว่ามี error ใน logs จริงหรือไม่
3. ลองปรับ threshold ใน node "Analyze for Errors"
4. ทดสอบด้วย "Execute Node"

### ปัญหา: n8n ไม่เชื่อมต่อกับ Node.js API

**แก้ไข:**
1. ตรวจสอบว่า Node.js API รันอยู่ที่ `http://localhost:3000`
2. ตรวจสอบ URL ใน HTTP Request nodes
3. ถ้ารันใน Docker ให้ใช้ `host.docker.internal:3000` แทน `localhost:3000`

### ปัญหา: Webhook URL ไม่ทำงาน

**แก้ไข:**
1. ตรวจสอบว่า n8n รันอยู่และเข้าถึงได้จาก internet
2. ใช้ ngrok หรือ cloudflare tunnel
3. ตรวจสอบว่า Webhook URL ใน LINE ถูกต้อง
4. ตรวจสอบว่า Workflow เปิดใช้งาน (Active)

---

## 📊 Monitoring & Logs

### ดู Execution History ใน n8n

1. ไปที่ Executions (เมนูซ้าย)
2. ดู execution ที่ผ่านมา
3. คลิกเพื่อดู details และ error

### ดู Logs ของ Node.js API

```bash
cd node-api
npm start

# จะแสดง logs ทุกครั้งที่มี request
```

### ดู LINE Webhook Logs

ไปที่ LINE Developers Console → Messaging API → Webhook settings → View logs

---

## 🎯 Best Practices

1. **ใช้ Environment Variables** - อย่า hardcode credentials
2. **ทดสอบก่อน Deploy** - ใช้ "Execute Node" ทดสอบก่อน
3. **Monitor Executions** - ตรวจสอบ execution history เป็นประจำ
4. **Backup Workflows** - Export workflows เป็น JSON เก็บไว้
5. **Set Proper Intervals** - อย่าตั้งเวลาสั้นเกินไป (ควรอย่างน้อย 5 นาที)

---

## 🚀 Next Steps

1. ✅ ติดตั้งและตั้งค่า n8n
2. ✅ Import workflows ทั้ง 3 ตัว
3. ✅ ตั้งค่า Environment Variables
4. ✅ ทดสอบแต่ละ workflow
5. ✅ Deploy to production (ใช้ ngrok หรือ cloudflare tunnel)
6. 📊 Monitor และปรับแต่งตามความต้องการ

---

## 📚 Resources

- [n8n Documentation](https://docs.n8n.io/)
- [LINE Messaging API](https://developers.line.biz/en/docs/messaging-api/)
- [Node.js API Documentation](./README.md)
- [Quick Start Guide](./QUICK_START.md)

---

## 💡 Tips

- ใช้ ngrok สำหรับทดสอบ: `ngrok http 5678`
- ใช้ cloudflare tunnel สำหรับ production
- ตั้งค่า error handling ใน workflows
- ใช้ n8n cloud ถ้าไม่อยากจัดการ server เอง

---

**Happy Automating! 🎉**
