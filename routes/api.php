<?php
use Illuminate\Http\Request;

Route::get('/get-file', function (Request $request) {
    $filePath = $request->query('url');
    
    if (empty($filePath)) {
        \Log::error('File path is empty');
        return response()->json(['error' => 'File path is empty'], 400);
    }

    // แปลง forward slash เป็น backslash สำหรับ Windows
    $filePath = str_replace('/', DIRECTORY_SEPARATOR, $filePath);

    if (!str_starts_with($filePath, storage_path('logs'))) {
        \Log::error('Invalid file path: ' . $filePath);
        return response()->json(['error' => 'Invalid file path'], 403);
    }

    if (file_exists($filePath)) {
        \Log::info('Serving file: ' . $filePath);
        return response()->stream(function () use ($filePath) {
            $handle = fopen($filePath, 'r');
            while (!feof($handle)) {
                echo fread($handle, 1024);
                flush();
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/plain',
        ]);
    } else {
        \Log::warning('File not found: ' . $filePath);
        return response()->json(['error' => 'File not found'], 404);
    }
});

