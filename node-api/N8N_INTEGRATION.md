# n8n Integration Guide

คู่มือการเชื่อมต่อ WebManga Log API กับ n8n automation platform

## 📋 Table of Contents
- [Setup n8n](#setup-n8n)
- [API Endpoints for n8n](#api-endpoints-for-n8n)
- [Workflow Examples](#workflow-examples)
- [Best Practices](#best-practices)

## 🚀 Setup n8n

### ติดตั้ง n8n

```bash
# ติดตั้งด้วย npm
npm install -g n8n

# หรือใช้ Docker
docker run -it --rm \
  --name n8n \
  -p 5678:5678 \
  n8nio/n8n
```

### เริ่มต้น n8n

```bash
n8n start
```

เปิดเบราว์เซอร์ไปที่ `http://localhost:5678`

## 📡 API Endpoints for n8n

### 1. Get Logs (HTTP Request Node)

**Configuration:**
- **Method:** GET
- **URL:** `http://localhost:3000/api/logs`
- **Query Parameters:**
  ```
  date: {{$now.format('YYYY-MM-DD')}}
  limit: 100
  offset: 0
  ```

**Response Data:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {...}
}
```

### 2. Get Statistics (HTTP Request Node)

**Configuration:**
- **Method:** GET
- **URL:** `http://localhost:3000/api/logs/stats`
- **Query Parameters:**
  ```
  date: {{$now.format('YYYY-MM-DD')}}
  ```

**Use Case:** Monitor traffic, detect anomalies

### 3. Webhook Endpoint (Webhook Node)

**Configuration:**
- **Method:** POST
- **URL:** `http://localhost:3000/api/webhook/n8n`
- **Body:**
  ```json
  {
    "action": "getLogs",
    "filters": {
      "date": "{{$now.format('YYYY-MM-DD')}}",
      "ip": "192.168.1.1"
    }
  }
  ```

## 🔄 Workflow Examples

### Example 1: Hourly Log Summary

**Purpose:** ส่ง summary ของ logs ทุกชั่วโมง

**Nodes:**
1. **Schedule Trigger** - ทุกชั่วโมง
2. **HTTP Request** - GET /api/logs/stats
3. **Function** - จัดรูปแบบข้อความ
4. **Slack/Email** - ส่งรายงาน

**Function Node Code:**
```javascript
const stats = $input.first().json.data;

return {
  json: {
    message: `📊 Hourly Log Summary
    
Total Requests: ${stats.total}
Unique IPs: ${stats.uniqueIPs}
Top URL: ${Object.keys(stats.topURLs)[0]}

Methods:
${Object.entries(stats.methods).map(([k,v]) => `  ${k}: ${v}`).join('\n')}`
  }
};
```

### Example 2: Suspicious Activity Alert

**Purpose:** แจ้งเตือนเมื่อมี activity ผิดปกติ

**Nodes:**
1. **Schedule Trigger** - ทุก 5 นาที
2. **HTTP Request** - GET /api/logs/stats
3. **IF Node** - ตรวจสอบ threshold
4. **Slack/Discord** - ส่ง alert

**IF Node Conditions:**
```
{{$json.data.uniqueIPs}} > 50
OR
{{$json.data.total}} > 1000
```

### Example 3: Daily Log Backup

**Purpose:** Backup logs ทุกวัน

**Nodes:**
1. **Schedule Trigger** - ทุกวันเวลา 00:00
2. **HTTP Request** - GET /api/logs/download?format=csv
3. **Google Drive/Dropbox** - Upload file
4. **Email** - ส่งการแจ้งเตือน

### Example 4: Real-time Error Monitoring

**Purpose:** Monitor errors แบบ real-time

**Nodes:**
1. **Schedule Trigger** - ทุกนาที
2. **HTTP Request** - GET /api/logs?url=/error
3. **IF Node** - มี errors ใหม่หรือไม่
4. **Multiple Alerts** - Slack, Email, SMS

### Example 5: User Activity Report

**Purpose:** รายงานการใช้งานของ users

**Nodes:**
1. **Schedule Trigger** - ทุกวันจันทร์
2. **HTTP Request** - GET /api/logs (สัปดาห์ที่แล้ว)
3. **Function** - วิเคราะห์ข้อมูล
4. **Email** - ส่งรายงาน

## 🎯 Use Cases

### 1. Security Monitoring
```
Monitor suspicious IPs
→ Alert on multiple failed logins
→ Block IP automatically
```

### 2. Performance Tracking
```
Track response times
→ Identify slow endpoints
→ Create performance reports
```

### 3. User Analytics
```
Track user behavior
→ Generate usage reports
→ Send to analytics platform
```

### 4. Compliance Reporting
```
Collect access logs
→ Generate compliance reports
→ Archive for audit
```

## 🔧 Advanced Configuration

### Authentication (Optional)

เพิ่ม API Key authentication:

**n8n HTTP Request Node:**
```
Headers:
  X-API-Key: your-secret-key
```

### Error Handling

**Function Node for Error Handling:**
```javascript
try {
  const response = $input.first().json;
  
  if (!response.success) {
    throw new Error(response.error);
  }
  
  return { json: response.data };
} catch (error) {
  // Log error
  console.error('API Error:', error);
  
  // Return empty result
  return { json: [] };
}
```

### Rate Limiting

ใช้ **Wait Node** เพื่อป้องกัน rate limiting:
```
Wait: 1 second between requests
```

## 📊 Monitoring Dashboard

สร้าง dashboard ด้วย n8n:

1. **Collect Stats** - ทุก 5 นาที
2. **Store in Database** - PostgreSQL/MySQL
3. **Visualize** - Grafana/Metabase
4. **Alert** - Threshold-based alerts

## 🐛 Troubleshooting

### n8n ไม่สามารถเชื่อมต่อ API

**ตรวจสอบ:**
1. API server รันอยู่หรือไม่ (`http://localhost:3000/health`)
2. Port ถูกต้องหรือไม่
3. Firewall settings
4. CORS configuration

**Solution:**
```bash
# Test API manually
curl http://localhost:3000/api/logs

# Check if API is running
curl http://localhost:3000/health
```

### Webhook ไม่ทำงาน

**ตรวจสอบ:**
1. Webhook URL ถูกต้อง
2. Request body format
3. API logs สำหรับ errors

### Data Format Issues

**ใช้ Function Node เพื่อ transform data:**
```javascript
// Transform API response for n8n
const items = $input.first().json.data;

return items.map(item => ({
  json: {
    timestamp: item.timestamp,
    ip: item.ip,
    url: item.url,
    user: item.username
  }
}));
```

## 📚 Resources

- [n8n Documentation](https://docs.n8n.io/)
- [n8n Community](https://community.n8n.io/)
- [Workflow Templates](./n8n-workflows/)

## 💡 Tips

1. **ใช้ Schedule Trigger** แทน Polling เพื่อประหยัด resources
2. **เพิ่ม Error Handling** ในทุก workflow
3. **ใช้ Function Node** สำหรับ data transformation
4. **Test workflows** ก่อน deploy
5. **Monitor workflow execution** ใน n8n dashboard

## 🎓 Next Steps

1. Import example workflows จาก `n8n-workflows/`
2. ปรับแต่ง workflows ตามความต้องการ
3. ตั้งค่า notifications (Slack, Email, etc.)
4. สร้าง custom workflows สำหรับ use cases ของคุณ

Happy Automating! 🚀
