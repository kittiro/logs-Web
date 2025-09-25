# ระบบ Web Access Logging ตาม พ.ร.บ. คอมพิวเตอร์

## ภาพรวม

ระบบนี้ได้รับการพัฒนาเพื่อเก็บบันทึกการเข้าถึงเว็บไซต์ตามข้อกำหนดของ **พระราชบัญญัติว่าด้วยการกระทำความผิดเกี่ยวกับคอมพิวเตอร์ พ.ศ. 2550** และที่แก้ไขเพิ่มเติม

## คุณสมบัติหลัก

### 1. การเก็บ Log แบบครบถ้วน
- **IP Address**: ที่อยู่ IP ของผู้เข้าถึง (รองรับ Proxy และ Load Balancer)
- **User Information**: ข้อมูลผู้ใช้ (Guest หรือ Username)
- **Request Details**: Method, URL, Headers, Request Data
- **Response Information**: Status Code, Response Size, Response Time
- **Session Tracking**: Session ID สำหรับติดตาม
- **Server Information**: Server Name, Process ID
- **Timestamp**: เวลาที่แม่นยำพร้อม Timezone

### 2. การป้องกันข้อมูลสำคัญ
- **Password Redaction**: รหัสผ่านถูกซ่อนเป็น `[REDACTED]`
- **Token Protection**: API Keys และ Tokens ถูกป้องกัน
- **Sensitive Data Filtering**: ข้อมูลสำคัญอื่นๆ ถูกกรองออก

### 3. รูปแบบ Log ตาม Computer Act
```
[Timestamp] [IP:xxx.xxx.xxx.xxx] [User:username] [METHOD URL] [Status:xxx] [Size:xxx bytes] [Time:xxx ms] [UA:User-Agent] [Additional Data]
```

## ไฟล์และโครงสร้าง

### Middleware
- **`app/Http/Middleware/WebAccessLogger.php`**: Middleware หลักสำหรับเก็บ log
- ติดตั้งใน `web` middleware group ใน `app/Http/Kernel.php`

### Controller
- **`app/Http/Controllers/WebAccessLogController.php`**: จัดการการแสดงผลและดาวน์โหลด log

### Views
- **`resources/views/logs/web-access.blade.php`**: หน้าแสดง log พร้อมฟีเจอร์ค้นหาและสถิติ

### Commands
- **`app/Console/Commands/LogCleanup.php`**: คำสั่งสำหรับทำความสะอาด log เก่า

### Log Files
- **`storage/logs/web-access-YYYY-MM-DD.log`**: ไฟล์ log รายวัน
- **`storage/logs/laravel.log`**: Laravel log สำรอง

## การใช้งาน

### 1. เข้าถึงหน้า Web Access Logs
```
http://localhost/web-access-logs
```

### 2. ดาวน์โหลด Log Files
```
http://localhost/web-access-logs/download?date=2025-08-22
```

### 3. ดูสถิติการเข้าถึง
```
http://localhost/web-access-logs/stats?date=2025-08-22
```

### 4. ทำความสะอาด Log เก่า
```bash
# ดูว่าจะลบไฟล์อะไรบ้าง (ไม่ลบจริง)
php artisan logs:cleanup --dry-run

# ลบ log เก่ากว่า 90 วัน
php artisan logs:cleanup

# ลบ log เก่ากว่า 30 วัน
php artisan logs:cleanup --days=30
```

## ฟีเจอร์ในหน้าเว็บ

### 1. การกรองข้อมูล
- **เลือกวันที่**: ดู log ของวันที่ต้องการ
- **ค้นหา**: ค้นหาจาก IP, URL, User Agent
- **จำนวนรายการต่อหน้า**: 25, 50, 100 รายการ

### 2. การแสดงผล
- **ตารางข้อมูล**: แสดงข้อมูลสำคัญในรูปแบบตาราง
- **Status Badge**: แสดงสถานะ HTTP ด้วยสี
- **Response Time**: เวลาตอบสนองเป็น milliseconds
- **User Badge**: แยกแสดง Guest และ User ที่ล็อกอิน

### 3. รายละเอียดเพิ่มเติม
- **Log Details Modal**: ดูรายละเอียดครบถ้วนของแต่ละ entry
- **Statistics Modal**: สถิติการเข้าถึงรายวัน
- **Raw Log View**: ดู log ในรูปแบบดิบ

### 4. สถิติที่แสดง
- **Total Requests**: จำนวนคำขอทั้งหมด
- **Unique IPs**: จำนวน IP ที่ไม่ซ้ำ
- **Success Rate**: อัตราความสำเร็จ (HTTP 200)
- **Error Rate**: อัตราข้อผิดพลาด (HTTP 4xx/5xx)
- **Top IP Addresses**: IP ที่เข้าถึงมากที่สุด
- **Top Pages**: หน้าที่ถูกเข้าถึงมากที่สุด
- **Status Code Distribution**: การกระจายของ HTTP Status Code

## การปฏิบัติตาม พ.ร.บ. คอมพิวเตอร์

### 1. ข้อมูลที่เก็บตามกฎหมาย
- ✅ วันเวลาที่เข้าใช้ระบบ
- ✅ ระยะเวลาที่ใช้ระบบ
- ✅ หมายเลข IP Address
- ✅ ข้อมูลการเข้าถึงและการใช้งาน
- ✅ ข้อมูลผู้ใช้งาน (ถ้ามี)

### 2. ระยะเวลาการเก็บรักษา
- **ค่าเริ่มต้น**: 90 วัน (ปรับได้ตามนีดส์)
- **การทำความสะอาดอัตโนมัติ**: ใช้ Cron Job รัน command
- **การสำรองข้อมูล**: Log ถูกเก็บใน Laravel log ด้วย

### 3. ความปลอดภัย
- **การเข้าถึง**: ต้อง Authentication
- **การป้องกันข้อมูล**: ข้อมูลสำคัญถูกซ่อน
- **File Permissions**: ไฟล์ log มีสิทธิ์ที่เหมาะสม

## การติดตั้งและกำหนดค่า

### 1. Middleware Registration
Middleware ถูกเพิ่มใน `app/Http/Kernel.php`:
```php
'web' => [
    // ... other middleware
    \App\Http\Middleware\WebAccessLogger::class,
],
```

### 2. Routes Registration
Routes ถูกเพิ่มใน `routes/web.php`:
```php
Route::middleware('auth')->group(function () {
    Route::get('/web-access-logs', [WebAccessLogController::class, 'index']);
    Route::get('/web-access-logs/download', [WebAccessLogController::class, 'download']);
    Route::get('/web-access-logs/stats', [WebAccessLogController::class, 'stats']);
});
```

### 3. Navigation Menu
เมนูถูกเพิ่มใน `resources/views/layouts/app.blade.php`:
```html
<li class="nav-item">
    <a class="nav-link" href="{{ route('web-access-logs.index') }}">
        <i class="fas fa-globe me-2"></i> Web Access Logs
    </a>
</li>
```

## การบำรุงรักษา

### 1. Cron Job สำหรับทำความสะอาด
เพิ่มใน crontab:
```bash
# รันทุกวันเวลา 02:00 น.
0 2 * * * cd /path/to/project && php artisan logs:cleanup
```

### 2. การตรวจสอบพื้นที่ดิสก์
```bash
# ตรวจสอบขนาดไฟล์ log
du -sh storage/logs/

# ตรวจสอบไฟล์ log ทั้งหมด
ls -lah storage/logs/web-access-*.log
```

### 3. การสำรองข้อมูล
- Log files ควรถูกสำรองเป็นประจำ
- ใช้ rsync หรือเครื่องมือสำรองอื่นๆ
- เก็บสำรองนอกเซิร์ฟเวอร์หลัก

## ตัวอย่าง Log Entry

```
[2025-08-22T04:32:15+00:00] [IP:192.168.65.1] [User:admin] [POST http://localhost/login] [Status:302] [Size:0 bytes] [Time:89.12 ms] [UA:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Safari/605.1.15] [Referer:http://localhost/login] [Session:hXmqLbZOkBc124ozBECzr508PeTtugxRrlDbWvhx] [Server:ac4fadf8842e] [PID:1] [Headers:{"host":["localhost"],"content-type":["application/x-www-form-urlencoded"]}] [Data:{"username":"admin","password":"[REDACTED]","_token":"[REDACTED]"}]
```

## การแก้ไขปัญหา

### 1. Log ไม่ถูกสร้าง
- ตรวจสอบ middleware ใน Kernel.php
- ตรวจสอบสิทธิ์ไฟล์ storage/logs/
- ตรวจสอบ Laravel log สำหรับ error

### 2. หน้าเว็บไม่แสดง
- ตรวจสอบ routes registration
- ตรวจสอบ authentication
- Clear cache: `php artisan route:clear`

### 3. Performance Issues
- ใช้ log rotation
- ลบไฟล์เก่าเป็นประจำ
- พิจารณาใช้ database สำหรับ log ขนาดใหญ่

## สรุป

ระบบ Web Access Logging นี้ได้รับการออกแบบให้ปฏิบัติตามข้อกำหนดของ พ.ร.บ. คอมพิวเตอร์ อย่างครบถ้วน พร้อมทั้งมีฟีเจอร์ที่ใช้งานง่ายสำหรับการจัดการและตรวจสอบ log การเข้าถึงเว็บไซต์