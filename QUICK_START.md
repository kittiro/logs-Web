# 🚀 Quick Start Guide

## ติดตั้งและรัน WebManga Demo + Node.js Log API

### 📋 Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+
- npm

### 🔧 Installation

```bash
# 1. Clone repository (ถ้ายังไม่ได้ clone)
git clone https://github.com/kittiro/logs-Web.git
cd logs-Web

# 2. ติดตั้ง Laravel dependencies
composer install

# 3. ติดตั้ง Node.js API dependencies
cd node-api
npm install
cd ..

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Setup database
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

### 🏃 Running

#### Option 1: รันทั้งหมดพร้อมกัน (แนะนำ)

```bash
./start-all.sh
```

จะรัน:
- Laravel: `http://localhost:8000`
- Node.js API: `http://localhost:3000`

#### Option 2: รันแยกกัน

**Terminal 1 - Laravel:**
```bash
php artisan serve --port=8000
```

**Terminal 2 - Node.js API:**
```bash
cd node-api
npm start
```

### 🧪 Testing

#### ทดสอบ Laravel App
```bash
# เปิดเบราว์เซอร์
open http://localhost:8000

# Login
Username: admin
Password: password
```

#### ทดสอบ Node.js API
```bash
cd node-api
./test-api.sh
```

หรือทดสอบด้วย curl:
```bash
# Health check
curl http://localhost:3000/health

# Get logs
curl http://localhost:3000/api/logs

# Get stats
curl http://localhost:3000/api/logs/stats
```

### 📡 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/health` | GET | Health check |
| `/api/logs` | GET | Get logs with filters |
| `/api/logs/dates` | GET | Get available dates |
| `/api/logs/stats` | GET | Get statistics |
| `/api/logs/download` | GET | Download logs |
| `/api/webhook/n8n` | POST | n8n webhook |

### 🔗 n8n Integration

1. **ติดตั้ง n8n:**
```bash
npm install -g n8n
```

2. **รัน n8n:**
```bash
n8n start
```

3. **เปิด n8n:** `http://localhost:5678`

4. **Import workflow:**
   - ไปที่ n8n dashboard
   - Import workflow จาก `node-api/n8n-workflows/example-log-monitor.json`

5. **ตั้งค่า HTTP Request Node:**
   - URL: `http://localhost:3000/api/logs`
   - Method: GET

### 📚 Documentation

- **Laravel App:** [README.md](README.md)
- **Node.js API:** [node-api/README.md](node-api/README.md)
- **n8n Integration:** [node-api/N8N_INTEGRATION.md](node-api/N8N_INTEGRATION.md)
- **Render Deployment:** [RENDER_DEPLOYMENT.md](RENDER_DEPLOYMENT.md)
- **Railway Deployment:** [RAILWAY_DEPLOYMENT.md](RAILWAY_DEPLOYMENT.md)

### 🐛 Troubleshooting

#### Port already in use
```bash
# Kill process on port 8000
lsof -ti:8000 | xargs kill -9

# Kill process on port 3000
lsof -ti:3000 | xargs kill -9
```

#### Node.js API can't find logs
```bash
# Check LOG_DIR in node-api/.env
LOG_DIR=../storage/logs

# Create logs directory if not exists
mkdir -p storage/logs
```

#### Database errors
```bash
# Reset database
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate:fresh --seed
```

### 🎯 Next Steps

1. ✅ ทดสอบ Laravel app
2. ✅ ทดสอบ Node.js API
3. ✅ ติดตั้ง n8n
4. ✅ Import example workflows
5. ✅ สร้าง custom workflows
6. ✅ Deploy to Render/Railway

### 💡 Tips

- ใช้ `npm run dev` ใน node-api สำหรับ development (auto-reload)
- ดู logs ใน `storage/logs/`
- ใช้ Postman หรือ Insomnia สำหรับทดสอบ API
- อ่าน n8n integration guide สำหรับ automation ideas

Happy coding! 🚀
