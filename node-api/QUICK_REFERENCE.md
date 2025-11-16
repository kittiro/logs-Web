# ⚡ Quick Reference - คำสั่งด่วน

## 🚀 รันระบบ

### รัน n8n อย่างเดียว
```bash
cd node-api
./start-n8n.sh
```

### รัน Node.js API อย่างเดียว
```bash
cd node-api
npm start
```

### รันทุกอย่างพร้อมกัน
```bash
./start-with-n8n.sh
```

### รัน ngrok
```bash
ngrok http 5678
```

---

## 🔗 URLs

| Service | URL |
|---------|-----|
| n8n Dashboard | http://localhost:5678 |
| Node.js API | http://localhost:3000 |
| API Health | http://localhost:3000/health |
| API Logs | http://localhost:3000/api/logs |
| API Stats | http://localhost:3000/api/logs/stats |

---

## 🤖 LINE Bot Commands

| Command | Description |
|---------|-------------|
| `/help` | แสดงคำสั่งทั้งหมด |
| `/stats` | สถิติ logs วันนี้ |
| `/logs` | logs ล่าสุด 5 รายการ |
| `/logs 10` | logs ล่าสุด 10 รายการ |
| `/dates` | วันที่ที่มี logs |
| `/ip 192.168.1.1` | ค้นหาจาก IP |
| `/url /login` | ค้นหาจาก URL |

---

## 📁 Workflows

| Workflow | File | Purpose |
|----------|------|---------|
| LINE Bot Auto Reply | `1-line-bot-auto-reply.json` | ตอบข้อความอัตโนมัติ |
| Daily Log Report | `2-daily-log-report.json` | รายงานประจำวัน |
| Error Alert Monitor | `3-error-alert-monitor.json` | แจ้งเตือน error |

---

## ⚙️ Environment Variables

### ใน `.env` (Node.js API)
```env
LINE_CHANNEL_SECRET=your_secret
LINE_CHANNEL_ACCESS_TOKEN=your_token
LINE_USER_ID=your_user_id
```

### ใน n8n Settings
```
LINE_CHANNEL_ACCESS_TOKEN=your_token
LINE_USER_ID=your_user_id
```

---

## 🧪 ทดสอบ

### ทดสอบ API
```bash
curl http://localhost:3000/
curl http://localhost:3000/health
curl http://localhost:3000/api/logs
curl http://localhost:3000/api/logs/stats
```

### ทดสอบ LINE Bot
```
ส่งข้อความไปที่ LINE Bot:
/help
/stats
/logs
```

### ทดสอบ n8n Workflow
1. เปิด Workflow
2. คลิก node ที่ต้องการ
3. คลิก "Execute Node"

---

## 🔧 Troubleshooting

### หยุด process ที่ใช้ port
```bash
# หา PID
lsof -ti:3000
lsof -ti:5678

# หยุด process
kill -9 $(lsof -ti:3000)
kill -9 $(lsof -ti:5678)
```

### Restart ทุกอย่าง
```bash
# หยุดทุก process (Ctrl+C)

# รันใหม่
./start-with-n8n.sh
```

### ดู Logs
```bash
# Node.js API logs
# ดูใน terminal ที่รัน npm start

# n8n logs
# ไปที่ n8n Dashboard → Executions

# LINE webhook logs
# LINE Developers Console → Messaging API → Webhook settings → View logs
```

---

## 📊 API Endpoints

### Logs
```bash
GET /api/logs                    # ดู logs ทั้งหมด
GET /api/logs?limit=10           # จำกัดจำนวน
GET /api/logs?ip=192.168.1.1     # กรองตาม IP
GET /api/logs?url=/login         # กรองตาม URL
GET /api/logs?method=POST        # กรองตาม method
GET /api/logs?date=2025-09-25    # กรองตามวันที่
```

### Stats
```bash
GET /api/logs/stats              # สถิติ logs
GET /api/logs/dates              # วันที่ที่มี logs
```

### Download
```bash
GET /api/logs/download?format=json   # ดาวน์โหลด JSON
GET /api/logs/download?format=csv    # ดาวน์โหลด CSV
GET /api/logs/download?format=txt    # ดาวน์โหลด TXT
```

### LINE
```bash
POST /webhook/line               # LINE webhook
POST /webhook/line/push          # Push message
```

---

## 🔐 LINE Webhook Setup

### 1. รัน ngrok
```bash
ngrok http 5678
```

### 2. คัดลอก URL
```
https://xxxx-xxxx-xxxx.ngrok.io
```

### 3. ตั้งค่าใน LINE Developers Console
```
Webhook URL: https://xxxx-xxxx-xxxx.ngrok.io/webhook/line-bot
Use webhook: ON
```

### 4. Verify
คลิก "Verify" ควรเห็น ✅ Success

---

## 📝 Import Workflows

### ใน n8n Dashboard
1. Workflows → Add Workflow → Import from File
2. เลือกไฟล์จาก `node-api/n8n-workflows/`
3. Import ทั้ง 3 ไฟล์
4. Activate แต่ละ workflow

---

## 🎯 Activate Workflows

### ใน n8n Dashboard
1. เปิด Workflow
2. คลิกสวิตช์ "Inactive" → "Active"
3. ทำซ้ำกับทุก workflow

---

## 📚 Documentation

| Document | Description |
|----------|-------------|
| `SETUP_STEP_BY_STEP.md` | คู่มือทีละขั้นตอน |
| `N8N_COMPLETE_GUIDE.md` | คู่มือครบถ้วน |
| `QUICK_START.md` | เริ่มต้นใช้งานเร็ว |
| `n8n-workflows/README.md` | คู่มือ workflows |

---

## 💡 Tips

- ใช้ `localhost` แทน `127.0.0.1` เพื่อหลีกเลี่ยง cookie error
- ดู Execution History ใน n8n เพื่อ debug
- Backup workflows เป็น JSON เก็บไว้
- ตั้ง interval ไม่ต่ำกว่า 5 นาที
- ใช้ Environment Variables แทน hardcode

---

## 🆘 Help

มีปัญหา? อ่านคู่มือเพิ่มเติม:
- [Setup Step by Step](./SETUP_STEP_BY_STEP.md)
- [Complete Guide](./N8N_COMPLETE_GUIDE.md)
- [Workflows README](./n8n-workflows/README.md)
