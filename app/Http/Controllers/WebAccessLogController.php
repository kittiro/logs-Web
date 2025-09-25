<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class WebAccessLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 25);
        
        // Get available dates from log files
        $availableDates = $this->getAvailableDates();
        
        $logPath = storage_path("logs/web-access-{$date}.log");
        $logs = [];
        $totalEntries = 0;

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = array_filter(explode("\n", $content));
            $totalEntries = count($lines);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                // Parse log line (format: timestamp|ip|user|method|url|status|size|response_time|user_agent|referer|session)
                $parts = explode('|', $line);
                if (count($parts) >= 6) {
                    $log = [
                        'timestamp' => $parts[0] ?? '',
                        'ip' => $parts[1] ?? '',
                        'user' => $parts[2] ?? 'Guest',
                        'method' => $parts[3] ?? '',
                        'url' => $parts[4] ?? '',
                        'status' => intval($parts[5] ?? 0),
                        'size' => intval($parts[6] ?? 0),
                        'response_time' => intval($parts[7] ?? 0),
                        'user_agent' => $parts[8] ?? '',
                        'referer' => $parts[9] ?? '',
                        'session' => $parts[10] ?? '',
                        'raw' => $line
                    ];
                    
                    // Apply search filter
                    if (empty($search) || 
                        strpos($log['ip'], $search) !== false ||
                        strpos($log['url'], $search) !== false ||
                        strpos($log['user_agent'], $search) !== false) {
                        $logs[] = $log;
                    }
                }
            }
        }

        // Reverse logs to show newest first
        $logs = array_reverse($logs);
        
        // Paginate results
        $logs = array_slice($logs, 0, $perPage);

        return view('logs.web-access', compact('logs', 'date', 'search', 'perPage', 'availableDates', 'totalEntries'));
    }
    
    private function getAvailableDates()
    {
        $logDir = storage_path('logs');
        $dates = [];
        
        if (File::isDirectory($logDir)) {
            $files = File::files($logDir);
            foreach ($files as $file) {
                if (preg_match('/web-access-(\d{4}-\d{2}-\d{2})\.log$/', $file->getFilename(), $matches)) {
                    $dates[] = $matches[1];
                }
            }
        }
        
        // If no log files found, add today's date
        if (empty($dates)) {
            $dates[] = date('Y-m-d');
        }
        
        rsort($dates); // Sort newest first
        return $dates;
    }

    public function download($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $logPath = storage_path("logs/web-access-{$date}.log");
        
        if (!File::exists($logPath)) {
            abort(404, 'Log file not found for date: ' . $date);
        }

        $filename = "web-access-{$date}.log";
        
        return Response::download($logPath, $filename, [
            'Content-Type' => 'text/plain',
        ]);
    }

    public function stats()
    {
        $logPath = storage_path('logs/web-access.log');
        $stats = [
            'total_requests' => 0,
            'unique_ips' => 0,
            'status_codes' => [],
            'top_pages' => [],
            'hourly_traffic' => [],
            'daily_traffic' => []
        ];

        if (File::exists($logPath)) {
            $content = File::get($logPath);
            $lines = array_filter(explode("\n", $content));
            
            $ips = [];
            $pages = [];
            $hourly = [];
            $daily = [];
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                $parts = explode('|', $line);
                if (count($parts) >= 5) {
                    $timestamp = $parts[0];
                    $ip = $parts[1];
                    $url = $parts[3];
                    $status = $parts[4];
                    
                    $stats['total_requests']++;
                    $ips[$ip] = true;
                    
                    // Status codes
                    if (!isset($stats['status_codes'][$status])) {
                        $stats['status_codes'][$status] = 0;
                    }
                    $stats['status_codes'][$status]++;
                    
                    // Top pages
                    if (!isset($pages[$url])) {
                        $pages[$url] = 0;
                    }
                    $pages[$url]++;
                    
                    try {
                        $carbon = Carbon::parse($timestamp);
                        
                        // Hourly traffic
                        $hour = $carbon->format('H:00');
                        if (!isset($hourly[$hour])) {
                            $hourly[$hour] = 0;
                        }
                        $hourly[$hour]++;
                        
                        // Daily traffic
                        $day = $carbon->format('Y-m-d');
                        if (!isset($daily[$day])) {
                            $daily[$day] = 0;
                        }
                        $daily[$day]++;
                    } catch (\Exception $e) {
                        // Skip invalid timestamps
                    }
                }
            }
            
            $stats['unique_ips'] = count($ips);
            
            // Sort and limit
            arsort($pages);
            $stats['top_pages'] = array_slice($pages, 0, 10, true);
            
            ksort($hourly);
            $stats['hourly_traffic'] = $hourly;
            
            ksort($daily);
            $stats['daily_traffic'] = $daily;
        }

        return response()->json($stats);
    }
}