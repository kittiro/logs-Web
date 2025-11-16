# 🤖 n8n Workflows for LINE Bot & Log Monitoring

workflows ทั้งหมดสำหรับระบบ LINE Bot และการตรวจสอบ logs อัตโนมัติ

---

## 📦 Workflows ที่มี

### 1. LINE Bot - Auto Reply
**ไฟล์:** `1-line-bot-auto-reply.json`

**จุดประสงค์:** ให้ LINE Bot ตอบข้อความอัตโนมัติผ่าน n8n

**Features:**
- ✅ ตอบคำสั่ง `/help`, `/stats`, `/logs`
- ✅ ดึงข้อมูลจาก Node.js API
- ✅ Format ข้อความให้สวยงาม
- ✅ ตอบกลับผ่าน LINE Messaging API

**คำสั่งที่รองรับ:**
- `/help` - แสดงคำสั่งทั้งหมด
- `/stats` - สถิติ logs วันนี้
- `/logs` - logs ล่าสุด 5 รายการ

---

### 2. Daily Log Report
**ไฟล์:** `2-daily-log-report.json`

**จุดประสงค์:** ส่งรายงาน logs ประจำวันอัตโนมัติ

**Features:**
- 📊 รายงานสถิติรวม
- 🔝 Top URLs และ Top Users
- ⏰ กิจกรรมล่าสุด
- 📅 ส่งทุกวันเวลา 9:00 AM

**รายงานประกอบด้วย:**
- Total Requests
- Unique IPs
- HTTP Methods (GET, POST)
- Top 5 URLs
- Top 5 Users
- Recent Activity

---

### 3. Error Alert Monitor
**ไฟล์:** `3-error-alert-monitor.json`

**จุดประสงค์:** ตรวจสอบและแจ้งเตือน error แบบ real-time

**Features:**
- 🚨 ตรวจสอบ HTTP Errors (4xx, 5xx)
- ⚠️ ตรวจจับ Suspicious Activity
- 🔐 ตรวจสอบ Failed Login Attempts
- ⏱️ ตรวจสอบทุก 5 นาที

**การแจ้งเตือน:**
- Error count และ details
- Suspicious IPs (request มากเกินไป)
- Failed login attempts
- Timestamp และ summary

---

## 🚀 วิธีใช้งาน

### 1. ติดตั้ง n8n

```bash
# ใช้ npm
npm install -g n8n

# หรือใช้ Docker
docker run -it --rm --name n8n -p 5678:5678 n8nio/n8n
```

### 2. รัน n8n

```bash
n8n start
```

เปิดเบราว์เซอร์: `http://localhost:5678`

### 3. Import Workflows

1. ไปที่ n8n Dashboard
2. คลิก "Workflows" → "Import from File"
3. เลือกไฟล์ workflow ที่ต้องการ
4. คลิก "Import"

### 4. ตั้งค่า Environment Variables

ใน n8n → Settings → Environment Variables:

```
LINE_CHANNEL_ACCESS_TOKEN=your_access_token
LINE_USER_ID=your_user_id
```

### 5. Activate Workflows

1. เปิด workflow ที่ต้องการ
2. คลิก "Activate" (สวิตช์ด้านบน)
3. Workflow จะเริ่มทำงานอัตโนมัติ

---

## ⚙️ การตั้งค่า

### LINE Bot Auto Reply

1. เปิด workflow
2. คลิก node "Webhook - LINE"
3. คัดลอก Webhook URL
4. ตั้งค่าใน LINE Developers Console:
   - Webhook URL: `https://your-domain/webhook/line-bot`
   - Enable webhook
5. Activate workflow

### Daily Log Report

1. เปิด workflow
2. แก้ไข node "Schedule - Daily 9 AM"
   - ตั้งเวลาที่ต้องการ
3. แก้ไข node "Send LINE Report"
   - ตรวจสอบ `LINE_USER_ID`
4. Activate workflow

### Error Alert Monitor

1. เปิด workflow
2. แก้ไข node "Every 5 Minutes"
   - ปรับ interval ตามต้องการ
3. แก้ไข node "Analyze for Errors"
   - ปรับ threshold (default: 20 requests/IP)
4. Activate workflow

---

## 🧪 การทดสอบ

### ทดสอบ LINE Bot

```bash
# 1. รัน Node.js API
cd node-api
npm start

# 2. รัน n8n
n8n start

# 3. ส่งข้อความทดสอบไปที่ LINE Bot
/help
/stats
/logs
```

### ทดสอบ Daily Report

1. เปิด workflow "Daily Log Report"
2. คลิก node "Schedule - Daily 9 AM"
3. คลิก "Execute Node"
4. ตรวจสอบ LINE ว่าได้รับรายงาน

### ทดสอบ Error Alert

1. เปิด workflow "Error Alert Monitor"
2. คลิก node "Every 5 Minutes"
3. คลิก "Execute Node"
4. ถ้ามี error จะได้รับการแจ้งเตือน

---

## 📊 Architecture

```
┌──────────────┐
│  LINE User   │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ LINE Platform│
└──────┬───────┘
       │ Webhook
       ▼
┌──────────────┐      ┌──────────────┐
│     n8n      │◄────►│ Node.js API  │
│  Workflows   │      │   (Logs)     │
└──────┬───────┘      └──────────────┘
       │
       ▼
┌──────────────┐
│ LINE Platform│
└──────┬───────┘
       │
       ▼
┌──────────────┐
│  LINE User   │
└──────────────┘
```

---

## 🔧 Customization

### เพิ่มคำสั่งใหม่ใน LINE Bot

1. เปิด workflow "LINE Bot - Auto Reply"
2. แก้ไข node "Check Command"
   - เพิ่ม condition ใหม่
3. เพิ่ม HTTP Request node สำหรับดึงข้อมูล
4. เพิ่ม Code node สำหรับ format ข้อความ
5. เชื่อมต่อกับ "Reply to LINE"

### ปรับเวลาส่งรายงาน

1. เปิด workflow "Daily Log Report"
2. แก้ไข node "Schedule - Daily 9 AM"
3. เปลี่ยน `triggerAtHour` เป็นเวลาที่ต้องการ

### ปรับ Error Threshold

1. เปิด workflow "Error Alert Monitor"
2. แก้ไข node "Analyze for Errors"
3. เปลี่ยนค่า threshold:
   ```javascript
   const suspiciousIPs = Object.entries(ipCounts)
     .filter(([ip, count]) => count > 20) // เปลี่ยนตรงนี้
   ```

---

## 📚 Resources

- [n8n Documentation](https://docs.n8n.io/)
- [LINE Messaging API](https://developers.line.biz/en/docs/messaging-api/)
- [Complete Setup Guide](../N8N_COMPLETE_GUIDE.md)
- [Node.js API Documentation](../README.md)

---

## 💡 Tips

1. **ทดสอบก่อน Deploy** - ใช้ "Execute Node" ทดสอบแต่ละ node
2. **Monitor Executions** - ดู execution history เป็นประจำ
3. **Backup Workflows** - Export เป็น JSON เก็บไว้
4. **Use Environment Variables** - อย่า hardcode credentials
5. **Set Proper Intervals** - อย่าตั้งเวลาสั้นเกินไป

---

## 🐛 Troubleshooting

### Bot ไม่ตอบ
- ตรวจสอบ workflow active หรือไม่
- ตรวจสอบ webhook URL
- ดู execution log ใน n8n

### ไม่ได้รับรายงาน
- ตรวจสอบ `LINE_USER_ID` ถูกต้อง
- ตรวจสอบเวลาใน schedule node
- ทดสอบด้วย "Execute Node"

### ไม่ได้รับการแจ้งเตือน
- ตรวจสอบว่ามี error ใน logs จริง
- ปรับ threshold ให้เหมาะสม
- ทดสอบด้วย "Execute Node"

---

**Happy Automating! 🎉**
