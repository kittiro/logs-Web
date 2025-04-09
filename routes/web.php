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

// ใช้ Auth Routes (แค่เรียกครั้งเดียว)
Auth::routes();

// Route สำหรับการเข้าสู่ระบบ (Login)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login'])->name('login.submit');

// Route สำหรับ Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route สำหรับหน้าจัดการ Log (จะเห็นเฉพาะผู้ที่ล็อกอินแล้ว)
Route::middleware('auth')->group(function () {
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::get('/logs/download/{filename}', [LogController::class, 'download'])->name('logs.download');
    Route::get('/logs/checksum/{filename}', [LogController::class, 'checksum'])->name('logs.checksum');
    Route::get('/logs/checksum-all', [LogController::class, 'checksumAll'])->name('logs.checksum.all');
    Route::get('/logs/history', [LogController::class, 'index'])->name('log-history');
    
    // Route สำหรับหน้า Dashboard หรือหน้าป้องกัน
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Nginx logs routes
    Route::get('/nginx-logs', [NginxLogController::class, 'show'])->name('nginx.logs');
    Route::get('/download-nginx-log', [NginxLogController::class, 'download'])->name('nginx.download');
    
    // File preview route
    Route::post('/file-preview', [FilePreviewController::class, 'preview'])->name('file.preview');
});
