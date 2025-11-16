# 💬 LINE Messaging API Integration Guide

## 📋 สิ่งที่ต้องเตรียม

### 1. สร้าง LINE Messaging API Channel

1. **ไปที่ LINE Developers Console**
   - เปิด: https://developers.line.biz/console/
   - Login ด้วย LINE account

2. **สร้าง Provider**
   - คลิก "Create a new provider"
   - ใส่ชื่อ Provider (เช่น "WebManga Bot")

3. **สร้าง Messaging API Channel**
   - คลิก "Create a Messaging API channel"
   - กรอกข้อมูล:
     - Channel name: "WebManga Log Bot"
     - Channel description: "Bot for monitoring web logs"
     - Category: เลือกตามที่เหมาะสม
     - Subcategory: เลือกตามที่เหมาะสม

4. **ดึงข้อมูลสำคัญ**
   - ไปที่ "Basic settings" tab
   - คัดลอก **Channel Secret**
   - ไปที่ "Messaging API" tab
   - คัดลอก **Channel Access Token** (ถ้ายังไม่มีให้กด Issue)

### 2. ข้อมูลที่ต้องใช้

คุณต้องมี 2 ค่านี้:
- ✅ **Channel Secret** - สำหรับ verify webhook
- ✅ **Channel Access Token** - สำหรับส่งข้อความ

## 🔧 Setup

### 1. ติดตั้ง LINE SDK

```bash
cd node-api
npm install @line/bot-sdk
```

### 2. เพิ่ม Configuration

แก้ไขไฟล์ `node-api/.env`:

```env
# LINE Messaging API
LINE_CHANNEL_SECRET=your_channel_secret_here
LINE_CHANNEL_ACCESS_TOKEN=your_channel_access_token_here
```

### 3. ตั้งค่า Webhook URL

ใน LINE Developers Console:
1. ไปที่ "Messaging API" tab
2. ตั้งค่า Webhook URL:
   - **Local (ทดสอบ)**: ใช้ ngrok (ดูด้านล่าง)
   - **Production**: `https://your-domain.com/webhook/line`
3. เปิด "Use webhook": ON
4. ปิด "Auto-reply messages": OFF (ถ้าไม่ต้องการ)

## 🚀 การใช้งาน

### ทดสอบ Local ด้วย ngrok

```bash
# ติดตั้ง ngrok
brew install ngrok  # macOS
# หรือ download จาก https://ngrok.com/

# รัน ngrok
ngrok http 3000

# คัดลอก HTTPS URL (เช่น https://abc123.ngrok.io)
# ไปตั้งค่าใน LINE Developers Console
# Webhook URL: https://abc123.ngrok.io/webhook/line
```

### รัน Application

```bash
# รัน Node.js API
cd node-api
npm start

# หรือรันทั้งหมด
./start-all.sh
```

### ทดสอบ LINE Bot

1. ไปที่ LINE Developers Console
2. ไปที่ "Messaging API" tab
3. Scan QR Code เพื่อเพิ่มเพื่อน
4. ส่งข้อความทดสอบ

## 💬 คำสั่งที่ Bot รองรับ

### คำสั่งพื้นฐาน

- **`/help`** - แสดงคำสั่งทั้งหมด
- **`/stats`** - แสดง log statistics วันนี้
- **`/logs`** - แสดง logs ล่าสุด 5 รายการ
- **`/logs [จำนวน]`** - แสดง logs ล่าสุด (เช่น `/logs 10`)
- **`/dates`** - แสดงวันที่ที่มี logs
- **`/ip [IP]`** - ค้นหา logs จาก IP (เช่น `/ip 192.168.1.1`)
- **`/url [URL]`** - ค้นหา logs จาก URL (เช่น `/url /dashboard`)

### ตัวอย่างการใช้งาน

```
User: /stats
Bot: 📊 Log Statistics (Today)
     Total: 1,250 requests
     Unique IPs: 45
     GET: 1,100 | POST: 150

User: /logs 3
Bot: 📝 Latest 3 Logs:
     1. [10:30:45] 192.168.1.1 → GET /dashboard
     2. [10:29:12] 192.168.1.2 → POST /login
     3. [10:28:05] 192.168.1.1 → GET /logs

User: /ip 192.168.1.1
Bot: 🔍 Logs from IP: 192.168.1.1
     Found 15 requests
     Latest: [10:30:45] GET /dashboard
```

## 🔔 Auto Notifications

Bot สามารถส่ง notifications อัตโนมัติ:

### 1. High Traffic Alert
```
⚠️ High Traffic Alert!

Unique IPs: 75 (threshold: 50)
Total Requests: 1,500
Time: 2025-09-25 10:30:45
```

### 2. Error Alert
```
🚨 Error Detected!

URL: /api/users
Count: 5 errors in last 5 minutes
Latest: 500 Internal Server Error
```

### 3. Daily Summary
```
📊 Daily Summary (2025-09-25)

Total Requests: 5,420
Unique IPs: 120
Top URL: /dashboard (1,200 requests)
Top User: admin (500 requests)
```

## 🔗 Integration กับ n8n

### Workflow: LINE Alert from n8n

```
Schedule Trigger (Every 5 minutes)
  ↓
HTTP Request → GET /api/logs/stats
  ↓
IF (uniqueIPs > 50)
  ↓
LINE Notify → Send alert to LINE
```

**n8n LINE Node Configuration:**
- Node: LINE
- Operation: Send Message
- Channel Access Token: `{{$env.LINE_CHANNEL_ACCESS_TOKEN}}`
- Message: Custom alert message

## 🛠️ Advanced Features

### 1. Rich Messages

Bot รองรับ LINE Rich Messages:
- Flex Messages
- Template Messages
- Quick Reply Buttons

### 2. Interactive Buttons

```
User: /stats
Bot: [แสดง statistics พร้อม buttons]
     [View Details] [Download CSV] [Set Alert]
```

### 3. Push Notifications

ส่งข้อความไปหา user โดยตรง (ไม่ต้องรอ user ส่งมาก่อน):
```javascript
// ใน n8n หรือ custom script
POST /webhook/line/push
{
  "userId": "U1234567890abcdef",
  "message": "Alert: High traffic detected!"
}
```

## 🐛 Troubleshooting

### Bot ไม่ตอบ

1. **ตรวจสอบ Webhook URL**
   ```bash
   curl -X POST https://your-webhook-url/webhook/line \
     -H "Content-Type: application/json" \
     -d '{"events":[]}'
   ```

2. **ตรวจสอบ Tokens**
   - Channel Secret ถูกต้องหรือไม่
   - Channel Access Token ยังใช้ได้หรือไม่

3. **ดู Logs**
   ```bash
   # ใน terminal ที่รัน Node.js API
   # จะเห็น webhook requests
   ```

### Webhook Verification Failed

- ตรวจสอบ `LINE_CHANNEL_SECRET` ใน `.env`
- ตรวจสอบว่า signature verification ทำงานถูกต้อง

### ngrok Timeout

- ngrok free tier มี timeout 2 ชั่วโมง
- ต้อง restart และอัพเดท webhook URL ใหม่
- หรือใช้ ngrok paid plan

## 📱 LINE Official Account Features

### ตั้งค่าเพิ่มเติม

1. **Profile**
   - ตั้งรูปโปรไฟล์
   - เขียน description
   - ตั้ง status message

2. **Rich Menu**
   - สร้างเมนูด้านล่างแชท
   - เพิ่มปุ่มคำสั่งต่างๆ

3. **Auto Reply**
   - ตั้งข้อความตอบกลับอัตโนมัติ
   - (แนะนำให้ปิดถ้าใช้ webhook)

## 💡 Tips

1. **ใช้ ngrok สำหรับ development**
2. **Deploy to Render/Railway สำหรับ production**
3. **ตั้งค่า Rich Menu** เพื่อให้ใช้งานง่าย
4. **ใช้ n8n** สำหรับ automation ที่ซับซ้อน
5. **Monitor webhook logs** เพื่อ debug

## 🔐 Security

- ✅ Webhook signature verification
- ✅ HTTPS only
- ✅ Environment variables สำหรับ secrets
- ✅ Rate limiting (ถ้าจำเป็น)

## 📚 Resources

- [LINE Messaging API Docs](https://developers.line.biz/en/docs/messaging-api/)
- [LINE Bot SDK Node.js](https://github.com/line/line-bot-sdk-nodejs)
- [Flex Message Simulator](https://developers.line.biz/flex-simulator/)

## 🎯 Next Steps

1. ✅ สร้าง LINE Channel
2. ✅ ดึง Channel Secret และ Access Token
3. ✅ ติดตั้ง dependencies
4. ✅ ตั้งค่า .env
5. ✅ Setup ngrok (สำหรับ local testing)
6. ✅ ตั้งค่า Webhook URL
7. ✅ ทดสอบส่งข้อความ
8. ✅ Deploy to production

Happy Chatting! 💬
