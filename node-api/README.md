# WebManga Log API - Node.js

Node.js API สำหรับดู web access logs แบบ offline พร้อมรองรับการเชื่อมต่อกับ n8n automation workflows

## 🚀 Features

- ✅ RESTful API สำหรับอ่าน logs
- ✅ Filter logs ตาม date, IP, URL, method, username
- ✅ Download logs ในรูปแบบ JSON, CSV, TXT
- ✅ Statistics และ analytics
- ✅ **n8n Integration** - รองรับ n8n workflows
- ✅ Webhook endpoint สำหรับ n8n
- ✅ CORS enabled สำหรับ cross-origin requests

## 📦 Installation

```bash
cd node-api
npm install
```

## ⚙️ Configuration

สร้างไฟล์ `.env` (หรือแก้ไขจาก `.env.example`):

```env
PORT=3000
LOG_DIR=../storage/logs
API_PREFIX=/api
CORS_ORIGIN=*
DEFAULT_LIMIT=100
MAX_LIMIT=1000
```

## 🏃 Running

```bash
# Production
npm start

# Development (with auto-reload)
npm run dev
```

Server จะรันที่ `http://localhost:3000`

## 📡 API Endpoints

### 1. Health Check
```
GET /health
```

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2025-09-25T10:30:45.000Z",
  "uptime": 123.45,
  "version": "1.0.0"
}
```

### 2. Get Logs
```
GET /api/logs?date=2025-09-25&ip=192.168.1.1&limit=50&offset=0
```

**Query Parameters:**
- `date` - วันที่ (YYYY-MM-DD)
- `ip` - Filter by IP address
- `url` - Filter by URL
- `method` - Filter by HTTP method (GET, POST, etc.)
- `username` - Filter by username
- `limit` - จำนวน records (default: 100)
- `offset` - เริ่มจาก record ที่ (default: 0)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "timestamp": "2025-09-25 10:30:45",
      "ip": "192.168.1.1",
      "method": "GET",
      "url": "/dashboard",
      "userAgent": "Mozilla/5.0...",
      "username": "admin",
      "userId": "1"
    }
  ],
  "pagination": {
    "total": 150,
    "limit": 50,
    "offset": 0,
    "hasMore": true
  }
}
```

### 3. Get Available Dates
```
GET /api/logs/dates
```

**Response:**
```json
{
  "success": true,
  "data": ["2025-09-25", "2025-09-24", "2025-09-23"],
  "count": 3
}
```

### 4. Download Logs
```
GET /api/logs/download?format=csv&date=2025-09-25
```

**Query Parameters:**
- `format` - รูปแบบไฟล์: `json`, `csv`, `txt`
- `date`, `ip`, `url`, `method`, `username` - Filters

### 5. Get Statistics
```
GET /api/logs/stats?date=2025-09-25
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 1250,
    "uniqueIPs": 45,
    "methods": {
      "GET": 1100,
      "POST": 150
    },
    "topURLs": {
      "/dashboard": 450,
      "/logs": 320
    },
    "users": {
      "admin": 500,
      "demo": 300
    }
  }
}
```

## 🔗 n8n Integration

### Webhook Endpoint
```
POST /api/webhook/n8n
```

**Request Body:**
```json
{
  "action": "getLogs",
  "filters": {
    "date": "2025-09-25",
    "ip": "192.168.1.1"
  }
}
```

**Actions:**
- `getLogs` - ดึง logs ตาม filters
- `getStats` - ดึง statistics

### n8n Workflow Examples

#### 1. ดึง Logs ทุกชั่วโมง
```
Schedule Trigger (Every hour)
  ↓
HTTP Request (GET http://localhost:3000/api/logs)
  ↓
Process Data / Send Alert
```

#### 2. Monitor Suspicious Activity
```
Schedule Trigger (Every 5 minutes)
  ↓
HTTP Request (GET /api/logs/stats)
  ↓
IF (uniqueIPs > threshold)
  ↓
Send Notification (Email/Slack/Discord)
```

#### 3. Daily Log Report
```
Schedule Trigger (Daily at 9 AM)
  ↓
HTTP Request (GET /api/logs/download?format=csv&date=yesterday)
  ↓
Email Attachment
```

### n8n HTTP Request Node Configuration

**URL:** `http://localhost:3000/api/logs`

**Method:** GET

**Query Parameters:**
```
date: {{$now.format('YYYY-MM-DD')}}
limit: 100
```

**Headers:**
```
Content-Type: application/json
```

## 🔧 Development

### Project Structure
```
node-api/
├── server.js              # Main server file
├── routes/
│   └── logs.js           # API routes
├── services/
│   └── logReader.js      # Log reading service
├── utils/
│   └── formatters.js     # Format converters
├── package.json
├── .env
└── README.md
```

## 🐛 Troubleshooting

### ไม่พบ log files
- ตรวจสอบ `LOG_DIR` ใน `.env`
- ตรวจสอบว่า path ถูกต้อง (relative to node-api folder)

### CORS errors
- ตั้งค่า `CORS_ORIGIN` ใน `.env`
- สำหรับ development ใช้ `*`
- สำหรับ production ระบุ domain ที่อนุญาต

### n8n ไม่สามารถเชื่อมต่อ
- ตรวจสอบว่า API server รันอยู่
- ตรวจสอบ port และ URL
- ลอง test ด้วย curl หรือ Postman ก่อน

## 📝 License

MIT
