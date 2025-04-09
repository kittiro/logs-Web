<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LogController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs');
        
        \Log::info('Log Path: ' . $logPath); // Debug: ดูพาธของโฟลเดอร์

        if (!File::exists($logPath)) {
            \Log::error('Log directory not found: ' . $logPath);
            return response()->json(['error' => 'Log directory not found'], 404);
        }

        $files = File::files($logPath);

        \Log::info('Files found: ' . json_encode(collect($files)->map->getPathname()->toArray())); // Debug: ดูรายการไฟล์

        $logFiles = collect($files)->map(function ($file) {
            $filePath = str_replace('\\', '/', $file->getPathname());
            \Log::info('File Path for URL: ' . $filePath); // Debug: ดูพาธที่ใช้ใน $file['url']
            return [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 2) . ' KB',
                'sha256' => hash_file('sha256', $file->getPathname()),
                'path' => $filePath,
                'url' => $filePath,
                'download_url' => route('logs.download', ['filename' => $file->getFilename()]),
                'checksum_url' => route('logs.checksum', ['filename' => $file->getFilename()]),
            ];
        });

        return view('logs.index', compact('logFiles'));
    }

    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath) || dirname($logPath) !== storage_path('logs')) {
            abort(404, 'File not found');
        }

        return response()->download($logPath);
    }

    public function checksum($filename)
    {
        $logPath = storage_path('logs/' . $filename);
    
        if (!File::exists($logPath) || dirname($logPath) !== storage_path('logs')) {
            return response()->json(['error' => 'File not found'], 404);
        }
    
        $checksum = hash_file('sha256', $logPath);
        
        return response()->json([
            'filename' => $filename,
            'checksum' => $checksum
        ]);
    }
    
    public function checksumAll()
    {
        $logPath = storage_path('logs');
        if (!File::exists($logPath)) {
            return response()->json(['error' => 'Log directory not found'], 404);
        }
        
        $files = File::files($logPath);
        $checksums = [];
        
        foreach ($files as $file) {
            $checksums[] = [
                'filename' => $file->getFilename(),
                'checksum' => hash_file('sha256', $file->getPathname())
            ];
        }
        
        $content = "";
        foreach ($checksums as $item) {
            $content .= $item['checksum'] . "  " . $item['filename'] . "\n";
        }
        
        $headers = [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="checksums.txt"',
        ];
        
        return response($content, 200, $headers);
    }
}