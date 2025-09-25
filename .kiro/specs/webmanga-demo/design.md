# Design Document - WebManga Demo on Railway

## Overview

WebManga Demo เป็นแพลตฟอร์มอ่านมังงะออนไลน์ที่พัฒนาด้วย Laravel 11 และ deploy บน Railway platform โดยใช้ SQLite database เพื่อความง่ายในการ deploy และ maintain ระบบจะมีการจัดการไฟล์รูปภาพมังงะ, ระบบนำทาง, และ admin panel สำหรับจัดการเนื้อหา

## Architecture

### System Architecture
```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Railway CDN   │    │   Laravel App    │    │  SQLite Database│
│   (Static Files)│◄───┤   (Railway)      │◄───┤   (Local File)  │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌──────────────────┐
                       │  File Storage    │
                       │  (Railway Volume)│
                       └──────────────────┘
```

### Technology Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Blade Templates + Tailwind CSS + Alpine.js
- **Database**: SQLite (Railway-optimized)
- **File Storage**: Railway persistent volumes
- **Deployment**: Railway platform with Nixpacks
- **Authentication**: Laravel built-in auth

### Railway-Specific Considerations
- ใช้ SQLite เพื่อหลีกเลี่ยงการต้องตั้งค่า external database
- File uploads จะใช้ Railway's persistent storage
- Environment variables จัดการผ่าน Railway dashboard
- Automatic HTTPS และ domain management
- Built-in monitoring และ logging

## Components and Interfaces

### 1. Manga Management System

#### Models
```php
// Manga Model
class Manga extends Model
{
    protected $fillable = ['title', 'description', 'cover_image', 'author', 'status'];
    
    public function chapters()
    {
        return $this->hasMany(Chapter::class)->orderBy('chapter_number');
    }
}

// Chapter Model  
class Chapter extends Model
{
    protected $fillable = ['manga_id', 'title', 'chapter_number', 'pages_count'];
    
    public function manga()
    {
        return $this->belongsTo(Manga::class);
    }
    
    public function pages()
    {
        return $this->hasMany(Page::class)->orderBy('page_number');
    }
}

// Page Model
class Page extends Model
{
    protected $fillable = ['chapter_id', 'page_number', 'image_path'];
    
    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
```

#### Controllers
- `MangaController`: จัดการการแสดงรายการมังงะและรายละเอียด
- `ChapterController`: จัดการการอ่านบทและนำทาง
- `AdminController`: จัดการ CRUD operations สำหรับมังงะ
- `FileUploadController`: จัดการการอัพโหลดไฟล์รูปภาพ

### 2. Reader Interface

#### Reader Component
- **Page Navigation**: Previous/Next page buttons
- **Chapter Navigation**: Chapter selector dropdown
- **Responsive Design**: Mobile-first approach
- **Keyboard Shortcuts**: Arrow keys สำหรับนำทาง
- **Progress Tracking**: แสดงหน้าปัจจุบัน/ทั้งหมด

#### Reader Features
```javascript
// Alpine.js Reader Component
Alpine.data('mangaReader', () => ({
    currentPage: 1,
    totalPages: 0,
    chapterId: null,
    
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.loadPage();
        }
    },
    
    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.loadPage();
        }
    }
}));
```

### 3. Admin Panel

#### Admin Features
- **Manga Management**: เพิ่ม/แก้ไข/ลบมังงะ
- **Chapter Management**: จัดการบทและลำดับ
- **Bulk Upload**: อัพโหลดหลายไฟล์พร้อมกัน
- **Image Optimization**: ปรับขนาดรูปภาพอัตโนมัติ
- **Preview System**: ดูตัวอย่างก่อน publish

#### File Upload System
```php
class FileUploadService
{
    public function uploadMangaPages($files, $chapterId)
    {
        $uploadedPages = [];
        
        foreach ($files as $index => $file) {
            $path = $file->store("manga/chapters/{$chapterId}", 'public');
            
            $page = Page::create([
                'chapter_id' => $chapterId,
                'page_number' => $index + 1,
                'image_path' => $path
            ]);
            
            $uploadedPages[] = $page;
        }
        
        return $uploadedPages;
    }
}
```

### 4. Railway Integration

#### Deployment Configuration
```dockerfile
# Dockerfile.railway
FROM php:8.2-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    nginx \
    sqlite \
    sqlite-dev \
    nodejs \
    npm

# Laravel setup
COPY . /var/www/html
WORKDIR /var/www/html

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

# Railway-specific setup
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 8080
CMD ["./start.sh"]
```

#### Environment Configuration
```bash
# Railway Environment Variables
APP_NAME="WebManga Demo"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://webmanga-demo.up.railway.app

DB_CONNECTION=sqlite
DB_DATABASE=/tmp/database.sqlite

FILESYSTEM_DISK=public
```

## Data Models

### Database Schema

#### Manga Table
```sql
CREATE TABLE manga (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255),
    author VARCHAR(255),
    status ENUM('ongoing', 'completed', 'hiatus') DEFAULT 'ongoing',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Chapters Table
```sql
CREATE TABLE chapters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    manga_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    chapter_number DECIMAL(5,2) NOT NULL,
    pages_count INTEGER DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (manga_id) REFERENCES manga(id) ON DELETE CASCADE
);
```

#### Pages Table
```sql
CREATE TABLE pages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    chapter_id INTEGER NOT NULL,
    page_number INTEGER NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE
);
```

#### Users Table (สำหรับ Admin)
```sql
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Data Relationships
- Manga → hasMany → Chapters
- Chapter → belongsTo → Manga
- Chapter → hasMany → Pages  
- Page → belongsTo → Chapter

## Error Handling

### Railway-Specific Error Handling

#### Database Connection
```php
// config/database.php - Railway SQLite setup
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DATABASE_URL'),
    'database' => env('DB_DATABASE', '/tmp/database.sqlite'),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

#### File Storage Error Handling
```php
class MangaController extends Controller
{
    public function show($id)
    {
        try {
            $manga = Manga::with('chapters.pages')->findOrFail($id);
            return view('manga.show', compact('manga'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('home')
                ->with('error', 'ไม่พบมังงะที่ต้องการ');
        } catch (\Exception $e) {
            Log::error('Manga display error: ' . $e->getMessage());
            return redirect()->route('home')
                ->with('error', 'เกิดข้อผิดพลาดในการโหลดมังงะ');
        }
    }
}
```

#### Image Loading Fallbacks
```php
// Helper for image URLs with fallbacks
class ImageHelper
{
    public static function getMangaImageUrl($imagePath, $type = 'cover')
    {
        if (!$imagePath || !Storage::disk('public')->exists($imagePath)) {
            return asset("images/defaults/{$type}-placeholder.jpg");
        }
        
        return Storage::disk('public')->url($imagePath);
    }
}
```

### User Experience Error Handling
- **404 Pages**: Custom error pages สำหรับมังงะหรือบทที่ไม่พบ
- **Image Loading**: Placeholder images เมื่อโหลดรูปไม่ได้
- **Network Issues**: Retry mechanisms และ offline indicators
- **Upload Errors**: Clear error messages และ progress indicators

## Testing Strategy

### Unit Testing
```php
// Tests/Unit/MangaTest.php
class MangaTest extends TestCase
{
    public function test_manga_can_have_chapters()
    {
        $manga = Manga::factory()->create();
        $chapter = Chapter::factory()->create(['manga_id' => $manga->id]);
        
        $this->assertTrue($manga->chapters->contains($chapter));
    }
    
    public function test_chapter_pages_are_ordered()
    {
        $chapter = Chapter::factory()->create();
        Page::factory()->create(['chapter_id' => $chapter->id, 'page_number' => 2]);
        Page::factory()->create(['chapter_id' => $chapter->id, 'page_number' => 1]);
        
        $pages = $chapter->pages;
        $this->assertEquals(1, $pages->first()->page_number);
        $this->assertEquals(2, $pages->last()->page_number);
    }
}
```

### Feature Testing
```php
// Tests/Feature/MangaReaderTest.php
class MangaReaderTest extends TestCase
{
    public function test_user_can_read_manga_chapter()
    {
        $manga = Manga::factory()->create();
        $chapter = Chapter::factory()->create(['manga_id' => $manga->id]);
        
        $response = $this->get(route('manga.read', [$manga->id, $chapter->id]));
        
        $response->assertStatus(200);
        $response->assertSee($manga->title);
        $response->assertSee($chapter->title);
    }
}
```

### Railway Deployment Testing
```bash
# Railway CLI testing commands
railway run php artisan test
railway run php artisan migrate:fresh --seed
railway logs --tail
```

### Performance Testing
- **Image Loading**: Test large image files และ optimization
- **Database Queries**: N+1 query prevention
- **Caching**: Redis caching สำหรับ frequently accessed data
- **CDN Integration**: Static file delivery optimization

### Browser Testing
- **Responsive Design**: Mobile, tablet, desktop viewports
- **Cross-browser**: Chrome, Firefox, Safari compatibility  
- **Touch Navigation**: Swipe gestures สำหรับ mobile readers
- **Keyboard Shortcuts**: Arrow key navigation testing

## Railway-Specific Implementation Notes

### Persistent Storage
```php
// config/filesystems.php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

### Database Migrations
```php
// Migration สำหรับ Railway deployment
public function up()
{
    // ใช้ SQLite-compatible syntax
    Schema::create('manga', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->string('cover_image')->nullable();
        $table->string('author')->nullable();
        $table->enum('status', ['ongoing', 'completed', 'hiatus'])->default('ongoing');
        $table->timestamps();
    });
}
```

### Startup Script
```bash
#!/bin/bash
# start.sh - Railway startup script

# Create storage directories
mkdir -p storage/app/public/manga
mkdir -p storage/logs

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Run migrations
php artisan migrate --force

# Create symbolic link for storage
php artisan storage:link

# Start the application
php artisan serve --host=0.0.0.0 --port=$PORT
```