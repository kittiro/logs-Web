<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NginxLogController;
use App\Http\Controllers\FilePreviewController;
use App\Http\Controllers\DashboardController;

// ทำให้หน้าแรกเป็นหน้า Login
Route::get('/', function () {
    return redirect('/login');
})->middleware('guest');

// Test route without auth for debugging
Route::get('/test-logs', function() {
    return response(file_get_contents(public_path('logs.html')), 200)
        ->header('Content-Type', 'text/html');
})->name('test.logs');

// ใช้ Auth Routes (แค่เรียกครั้งเดียว)
Auth::routes();

// Route สำหรับการเข้าสู่ระบบ (Login)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.submit');

// Route สำหรับ Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route สำหรับหน้าจัดการ Log (ปิด auth ชั่วคราวเพื่อทดสอบ)
Route::get('/logs', [LogController::class, 'index'])->name('logs.index');

// Route สำหรับหน้า Dashboard หรือหน้าป้องกัน (ปิด auth ชั่วคราวเพื่อทดสอบ)
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

// File preview route (ปิด auth ชั่วคราวเพื่อทดสอบ)
Route::post('/file-preview', [FilePreviewController::class, 'preview'])->name('file.preview');

Route::middleware('auth')->group(function () {
    Route::get('/logs/download/{filename}', [LogController::class, 'download'])->name('logs.download');
    Route::get('/logs/checksum/{filename}', [LogController::class, 'checksum'])->name('logs.checksum');
    Route::get('/logs/checksum-all', [LogController::class, 'checksumAll'])->name('logs.checksum.all');
    Route::get('/logs/history', [LogController::class, 'index'])->name('log-history');

    // Nginx logs routes
    Route::get('/nginx-logs', [NginxLogController::class, 'show'])->name('nginx.logs');
    Route::get('/download-nginx-log', [NginxLogController::class, 'download'])->name('nginx.download');
    
    // Web Access Logs routes
    Route::get('/web-access-logs', [App\Http\Controllers\WebAccessLogController::class, 'index'])->name('web-access-logs.index');
    Route::get('/web-access-logs/download/{date?}', [App\Http\Controllers\WebAccessLogController::class, 'download'])->name('web-access-logs.download');
    Route::get('/web-access-logs/stats', [App\Http\Controllers\WebAccessLogController::class, 'stats'])->name('web-access-logs.stats');
});
