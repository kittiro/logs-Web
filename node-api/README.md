# 🚀 WebManga Log API + LINE Bot + n8n Automation

Node.js API สำหรับดูและวิเคราะห์ logs พร้อม LINE Bot และระบบ automation ด้วย n8n

---

## ✨ Features

### 📊 Log API
- REST API สำหรับอ่านและวิเคราะห์ logs
- Filter ตาม date, IP, URL, method, username
- Download logs (JSON, CSV, TXT)
- Statistics และ analytics

### 🤖 LINE Bot
- ตอบข้อความอัตโนมัติ
- ดู stats และ logs ผ่าน LINE
- คำสั่ง: `/help`, `/stats`, `/logs`, `/dates`

### 🔄 n8n Automation
- **LINE Bot Auto Reply** - ตอบข้อความอัตโนมัติผ่าน n8n
- **Daily Log Report** - ส่งรายงานประจำวันทุกเช้า
- **Error Alert Monitor** - แจ้งเตือน error real-time

---

## 📚 Documentation

| Document | Description |
|----------|-------------|
| **[SETUP_STEP_BY_STEP.md](./SETUP_STEP_BY_STEP.md)** | 🎯 คู่มือทีละขั้นตอน (เริ่มที่นี่!) |
| **[QUICK_REFERENCE.md](./QUICK_REFERENCE.md)** | ⚡ คำสั่งด่วนและ reference |
| **[N8N_COMPLETE_GUIDE.md](./N8N_COMPLETE_GUIDE.md)** | 📖 คู่มือ n8n ครบถ้วน |
| **[QUICK_START.md](./QUICK_START.md)** | 🚀 เริ่มต้นใช้งาน API |
| **[n8n-workflows/README.md](./n8n-workflows/README.md)** | 🤖 คู่มือ workflows |

---

## 🚀 Quick Start

### 1. ติดตั้ง Dependencies

```bash
cd node-api
npm install
```

### 2. ตั้งค่า Environment Variables

สร้างไฟล์ `.env`:

```env
# Server
PORT=3000
LOG_DIR=../storage/logs

# LINE Messaging API
LINE_CHANNEL_SECRET=your_channel_secret
LINE_CHANNEL_ACCESS_TOKEN=your_access_token
LINE_USER_ID=your_user_id
```

### 3. รัน API

```bash
npm start
```

API จะรันที่: **http://localhost:3000**

### 4. รัน n8n (ถ้าต้องการใช้ automation)

```bash
./start-n8n.sh
```

n8n จะรันที่: **http://localhost:5678**

### 5. Import Workflows

1. เปิด n8n Dashboard: http://localhost:5678
2. Import workflows จาก `n8n-workflows/`
3. ตั้งค่า Environment Variables
4. Activate workflows

**อ่านคู่มือเต็ม:** [SETUP_STEP_BY_STEP.md](./SETUP_STEP_BY_STEP.md)

---

## 📋 API Endpoints

### Logs
```
GET  /api/logs              # ดู logs ทั้งหมด
GET  /api/logs/stats        # สถิติ logs
GET  /api/logs/dates        # วันที่ที่มี logs
GET  /api/logs/download     # ดาวน์โหลด logs
```

### LINE
```
POST /webhook/line          # LINE webhook
POST /webhook/line/push     # Push message
```

### n8n
```
POST /api/webhook/n8n       # n8n webhook
```

**ดูเพิ่มเติม:** [QUICK_REFERENCE.md](./QUICK_REFERENCE.md)

---

## 🤖 LINE Bot Commands

| Command | Description |
|---------|-------------|
| `/help` | แสดงคำสั่งทั้งหมด |
| `/stats` | สถิติ logs วันนี้ |
| `/logs` | logs ล่าสุด 5 รายการ |
| `/dates` | วันที่ที่มี logs |
| `/ip [IP]` | ค้นหาจาก IP |
| `/url [URL]` | ค้นหาจาก URL |

---

## 🔄 n8n Workflows

### 1. LINE Bot Auto Reply
**ไฟล์:** `n8n-workflows/1-line-bot-auto-reply.json`

ให้ LINE Bot ตอบข้อความอัตโนมัติผ่าน n8n

**Features:**
- ตอบคำสั่ง `/help`, `/stats`, `/logs`
- ดึงข้อมูลจาก API
- Format ข้อความสวยงาม

### 2. Daily Log Report
**ไฟล์:** `n8n-workflows/2-daily-log-report.json`

ส่งรายงาน logs ประจำวันทุกเช้า 9 โมง

**รายงานประกอบด้วย:**
- สถิติรวม (Total, Unique IPs)
- Top URLs และ Top Users
- กิจกรรมล่าสุด

### 3. Error Alert Monitor
**ไฟล์:** `n8n-workflows/3-error-alert-monitor.json`

ตรวจสอบและแจ้งเตือน error ทุก 5 นาที

**ตรวจสอบ:**
- HTTP Errors (4xx, 5xx)
- Suspicious Activity
- Failed Login Attempts

---

## 🧪 ทดสอบ

### ทดสอบ API

```bash
# Health check
curl http://localhost:3000/health

# ดู logs
curl http://localhost:3000/api/logs

# ดู stats
curl http://localhost:3000/api/logs/stats
```

### ทดสอบ LINE Bot

ส่งข้อความไปที่ LINE Bot:
```
/help
/stats
/logs
```

### ทดสอบ n8n Workflows

1. เปิด n8n Dashboard
2. เปิด Workflow
3. คลิก node → "Execute Node"

---

## 📊 Architecture

```
┌─────────────┐
│  LINE User  │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│  LINE Platform  │
└──────┬──────────┘
       │ Webhook
       ▼
┌─────────────────┐
│     ngrok       │ (for development)
└──────┬──────────┘
       │
       ▼
┌─────────────────┐      ┌──────────────┐
│      n8n        │◄────►│ Node.js API  │
│   Workflows     │      │   (Logs)     │
└──────┬──────────┘      └──────────────┘
       │
       ▼
┌─────────────────┐
│  LINE Platform  │
└──────┬──────────┘
       │
       ▼
┌─────────────┐
│  LINE User  │
└─────────────┘
```

---

## 🛠️ Scripts

```bash
# รัน API
npm start

# รัน n8n
./start-n8n.sh

# รันทุกอย่างพร้อมกัน (API + Laravel + n8n)
../start-with-n8n.sh

# ทดสอบ API
./test-server.sh
```

---

## 🔧 Troubleshooting

### Bot ไม่ตอบ
1. ตรวจสอบว่า API รันอยู่
2. ตรวจสอบว่า n8n รันอยู่
3. ตรวจสอบ Workflow active หรือไม่
4. ตรวจสอบ LINE Webhook URL

### ไม่ได้รับรายงาน
1. ตรวจสอบ `LINE_USER_ID` ถูกต้อง
2. ตรวจสอบ Workflow active หรือไม่
3. ทดสอบด้วย "Execute Node"

### Secure Cookie Error
```bash
# ใช้ localhost แทน 127.0.0.1
http://localhost:5678

# หรือตั้งค่า environment variable
export N8N_SECURE_COOKIE=false
n8n start
```

**ดูเพิ่มเติม:** [N8N_COMPLETE_GUIDE.md](./N8N_COMPLETE_GUIDE.md#troubleshooting)

---

## 📦 Dependencies

```json
{
  "@line/bot-sdk": "^9.9.0",
  "cors": "^2.8.5",
  "dotenv": "^16.3.1",
  "express": "^4.18.2"
}
```

---

## 🌐 Deployment

### Development (ngrok)
```bash
ngrok http 5678
```

### Production
- ใช้ Cloudflare Tunnel
- หรือ deploy บน VPS/Cloud
- ตั้งค่า HTTPS
- อัพเดท LINE Webhook URL

---

## 📚 Resources

- [n8n Documentation](https://docs.n8n.io/)
- [LINE Messaging API](https://developers.line.biz/en/docs/messaging-api/)
- [Express.js](https://expressjs.com/)
- [ngrok](https://ngrok.com/)

---

## 💡 Tips

1. **ใช้ localhost** แทน 127.0.0.1 เพื่อหลีกเลี่ยง cookie error
2. **Backup workflows** - Export เป็น JSON เก็บไว้
3. **Monitor executions** - ดู execution history ใน n8n
4. **Set proper intervals** - อย่าตั้งเวลาสั้นเกินไป
5. **Use environment variables** - อย่า hardcode credentials

---

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first.

---

## 📄 License

MIT

---

## 🆘 Need Help?

1. อ่าน [SETUP_STEP_BY_STEP.md](./SETUP_STEP_BY_STEP.md) - คู่มือทีละขั้นตอน
2. ดู [QUICK_REFERENCE.md](./QUICK_REFERENCE.md) - คำสั่งด่วน
3. อ่าน [N8N_COMPLETE_GUIDE.md](./N8N_COMPLETE_GUIDE.md) - คู่มือครบถ้วน
4. ดู [Troubleshooting](#troubleshooting) section

---

**Happy Automating! 🎉**
