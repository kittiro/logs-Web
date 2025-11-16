# 🚀 Quick Start Guide - Node.js Log API

## วิธีรัน Server

### 1. รัน Server
```bash
cd node-api
npm start
```

หรือใช้สคริปต์:
```bash
cd node-api
./start-server.sh
```

### 2. ทดสอบ API (เปิด terminal ใหม่)
```bash
cd node-api
./test-server.sh
```

### 3. หยุด Server ที่รันอยู่
```bash
# หา process ID
lsof -ti:3000

# หยุด process
kill -9 <PID>
```

หรือใช้คำสั่งเดียว:
```bash
kill -9 $(lsof -ti:3000)
```

## 📍 API Endpoints

### ข้อมูลทั่วไป
- `GET /` - API overview และ endpoints ทั้งหมด
- `GET /health` - Health check

### Log APIs
- `GET /api/logs` - ดู logs (รองรับ filters และ pagination)
- `GET /api/logs/stats` - สถิติ logs
- `GET /api/logs/dates` - วันที่ที่มี logs
- `GET /api/logs/download` - ดาวน์โหลด logs (JSON, CSV, TXT)

### LINE Bot
- `POST /webhook/line` - LINE webhook (สำหรับ LINE Bot)
- `POST /webhook/line/push` - Push message ไปหาผู้ใช้

### n8n Integration
- `POST /api/webhook/n8n` - n8n webhook

## 🧪 ตัวอย่างการใช้งาน

### ดู logs ทั้งหมด
```bash
curl http://localhost:3000/api/logs
```

### ดู logs แบบกรอง
```bash
# กรองตาม IP
curl "http://localhost:3000/api/logs?ip=192.168.1.1"

# กรองตาม URL
curl "http://localhost:3000/api/logs?url=/login"

# กรองตาม method
curl "http://localhost:3000/api/logs?method=POST"

# กรองตามวันที่
curl "http://localhost:3000/api/logs?date=2025-09-25"
```

### ดูสถิติ
```bash
curl http://localhost:3000/api/logs/stats
```

### ดาวน์โหลด logs
```bash
# JSON
curl "http://localhost:3000/api/logs/download?format=json" -o logs.json

# CSV
curl "http://localhost:3000/api/logs/download?format=csv" -o logs.csv

# TXT
curl "http://localhost:3000/api/logs/download?format=txt" -o logs.txt
```

## 🤖 LINE Bot Commands

เมื่อ server รันแล้ว ส่งข้อความไปที่ LINE Bot:

- `/help` - แสดงคำสั่งทั้งหมด
- `/stats` - สถิติ logs วันนี้
- `/logs` - logs ล่าสุด 5 รายการ
- `/logs 10` - logs ล่าสุด 10 รายการ
- `/dates` - วันที่ที่มี logs
- `/ip 192.168.1.1` - ค้นหาจาก IP
- `/url /login` - ค้นหาจาก URL

## 🌐 Deploy to Production

### ใช้ ngrok (สำหรับทดสอบ)
```bash
ngrok http 3000
```

จากนั้นตั้งค่า LINE Webhook URL เป็น:
```
https://your-ngrok-url.ngrok.io/webhook/line
```

### ใช้ Cloudflare Tunnel
```bash
cloudflared tunnel --url http://localhost:3000
```

## 🔧 Troubleshooting

### Port 3000 ถูกใช้งานอยู่
```bash
# หยุด process ที่ใช้ port 3000
kill -9 $(lsof -ti:3000)
```

### ไม่พบ logs
ตรวจสอบว่า `LOG_DIR` ใน `.env` ชี้ไปที่ directory ที่ถูกต้อง:
```
LOG_DIR=../storage/logs
```

### LINE Bot ไม่ตอบ
1. ตรวจสอบว่า server รันอยู่
2. ตรวจสอบ LINE credentials ใน `.env`
3. ตรวจสอบ Webhook URL ใน LINE Developers Console
4. ดู logs ใน terminal เพื่อดู error

## 📝 Environment Variables

แก้ไขไฟล์ `.env`:

```env
# Server Port
PORT=3000

# Log Files Directory
LOG_DIR=../storage/logs

# CORS
CORS_ORIGIN=*

# LINE Messaging API
LINE_CHANNEL_SECRET=your_channel_secret
LINE_CHANNEL_ACCESS_TOKEN=your_access_token
```
