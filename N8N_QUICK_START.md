# 🔗 n8n Quick Start Guide

## ✅ n8n Status

**n8n version 1.119.2 ติดตั้งแล้ว!**

## 🚀 วิธีรัน

### Option 1: รันทุกอย่างพร้อมกัน (Laravel + Node.js API + n8n)

```bash
./start-with-n8n.sh
```

จะเปิด:
- 🎨 Laravel: http://localhost:8000
- 📡 Node.js API: http://localhost:3000
- 🔗 n8n: http://localhost:5678

### Option 2: รัน n8n แยก

```bash
npx n8n
```

หรือ

```bash
n8n start
```

## 📋 Setup n8n Workflow

### 1. เปิด n8n Dashboard

เปิดเบราว์เซอร์ไปที่: http://localhost:5678

### 2. สร้าง Account (ครั้งแรก)

- กรอก Email และ Password
- ตั้งชื่อ workspace

### 3. Import Example Workflow

1. คลิก **"Import from File"**
2. เลือกไฟล์: `node-api/n8n-workflows/example-log-monitor.json`
3. คลิก **"Import"**

### 4. ตั้งค่า HTTP Request Node

1. เปิด workflow ที่ import มา
2. คลิกที่ **"Get Log Stats"** node
3. ตรวจสอบ URL: `http://localhost:3000/api/logs/stats`
4. คลิก **"Execute Node"** เพื่อทดสอบ

## 🎯 Example Workflows

### Workflow 1: Monitor Logs Every 5 Minutes

```
Schedule Trigger (Every 5 minutes)
  ↓
HTTP Request → GET http://localhost:3000/api/logs/stats
  ↓
IF (uniqueIPs > 50)
  ↓
Send Alert (Slack/Email/Discord)
```

**Setup:**
1. Add **Schedule Trigger** node
   - Interval: 5 minutes

2. Add **HTTP Request** node
   - Method: GET
   - URL: `http://localhost:3000/api/logs/stats`

3. Add **IF** node
   - Condition: `{{$json.data.uniqueIPs}} > 50`

4. Add **Slack/Email** node
   - Message: "⚠️ High traffic detected!"

### Workflow 2: Daily Log Backup

```
Schedule Trigger (Daily at 9 AM)
  ↓
HTTP Request → GET http://localhost:3000/api/logs/download?format=csv
  ↓
Google Drive/Dropbox Upload
  ↓
Email Notification
```

### Workflow 3: Real-time Error Monitoring

```
Schedule Trigger (Every minute)
  ↓
HTTP Request → GET http://localhost:3000/api/logs?url=/error
  ↓
IF (has new errors)
  ↓
Multiple Alerts (Slack + Email + SMS)
```

## 🔧 HTTP Request Node Configuration

### Get Logs
```
Method: GET
URL: http://localhost:3000/api/logs
Query Parameters:
  - date: {{$now.format('YYYY-MM-DD')}}
  - limit: 100
```

### Get Stats
```
Method: GET
URL: http://localhost:3000/api/logs/stats
Query Parameters:
  - date: {{$now.format('YYYY-MM-DD')}}
```

### Download Logs
```
Method: GET
URL: http://localhost:3000/api/logs/download
Query Parameters:
  - format: csv
  - date: {{$now.format('YYYY-MM-DD')}}
```

### Webhook (POST)
```
Method: POST
URL: http://localhost:3000/api/webhook/n8n
Body (JSON):
{
  "action": "getLogs",
  "filters": {
    "date": "{{$now.format('YYYY-MM-DD')}}",
    "ip": "192.168.1.1"
  }
}
```

## 📊 Available API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/health` | GET | Health check |
| `/api/logs` | GET | Get logs with filters |
| `/api/logs/dates` | GET | Get available dates |
| `/api/logs/stats` | GET | Get statistics |
| `/api/logs/download` | GET | Download logs |
| `/api/webhook/n8n` | POST | n8n webhook |

## 🎨 n8n Expressions

### Date/Time
```javascript
{{$now.format('YYYY-MM-DD')}}           // Today's date
{{$now.minus({days: 1}).format('YYYY-MM-DD')}}  // Yesterday
{{$now.format('HH:mm:ss')}}             // Current time
```

### Data Access
```javascript
{{$json.data.total}}                    // Access total
{{$json.data.uniqueIPs}}                // Access unique IPs
{{$json.data.methods.GET}}              // Access GET count
```

### Conditions
```javascript
{{$json.data.total > 1000}}             // More than 1000 requests
{{$json.data.uniqueIPs > 50}}           // More than 50 unique IPs
{{$json.success === true}}              // Check success
```

## 🔔 Notification Examples

### Slack Message
```
⚠️ Log Alert

Total Requests: {{$json.data.total}}
Unique IPs: {{$json.data.uniqueIPs}}
Time: {{$now.format('YYYY-MM-DD HH:mm:ss')}}

Top URL: {{Object.keys($json.data.topURLs)[0]}}
```

### Email Subject
```
[Alert] High Traffic Detected - {{$json.data.total}} requests
```

### Email Body
```html
<h2>Log Statistics Alert</h2>
<p><strong>Date:</strong> {{$now.format('YYYY-MM-DD')}}</p>
<p><strong>Total Requests:</strong> {{$json.data.total}}</p>
<p><strong>Unique IPs:</strong> {{$json.data.uniqueIPs}}</p>
<p><strong>GET Requests:</strong> {{$json.data.methods.GET}}</p>
<p><strong>POST Requests:</strong> {{$json.data.methods.POST}}</p>
```

## 🐛 Troubleshooting

### n8n ไม่เปิด
```bash
# ตรวจสอบว่าติดตั้งแล้ว
npx n8n --version

# ลองรันใหม่
npx n8n
```

### ไม่สามารถเชื่อมต่อ API
```bash
# ตรวจสอบว่า API รันอยู่
curl http://localhost:3000/health

# ตรวจสอบ port
lsof -i :3000
```

### Workflow ไม่ทำงาน
1. ตรวจสอบ URL ใน HTTP Request node
2. คลิก "Execute Node" เพื่อทดสอบ
3. ดู error message ใน n8n
4. ตรวจสอบ API logs

### CORS Errors
- API มี CORS enabled แล้ว
- ตรวจสอบ `CORS_ORIGIN` ใน `node-api/.env`

## 💡 Tips

1. **ใช้ Schedule Trigger** แทน Polling
2. **เพิ่ม Error Handling** ในทุก workflow
3. **Test workflows** ก่อน activate
4. **Monitor execution** ใน n8n dashboard
5. **Save workflows** เป็น JSON เพื่อ backup

## 📚 Resources

- [n8n Documentation](https://docs.n8n.io/)
- [n8n Community](https://community.n8n.io/)
- [API Documentation](node-api/README.md)
- [n8n Integration Guide](node-api/N8N_INTEGRATION.md)

## 🎓 Next Steps

1. ✅ เปิด n8n dashboard
2. ✅ Import example workflow
3. ✅ ทดสอบ HTTP Request nodes
4. ✅ สร้าง custom workflows
5. ✅ ตั้งค่า notifications (Slack, Email, etc.)
6. ✅ Monitor และ optimize workflows

Happy Automating! 🚀
