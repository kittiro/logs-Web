<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real log data
        $logData = $this->getLogAnalytics();
        
        return view('dashboard', [
            'logData' => $logData,
            'actionTypes' => ['view', 'download', 'error', 'login', 'logout'],
            'severities' => ['info', 'warning', 'error', 'critical']
        ]);
    }
    
    private function getLogAnalytics()
    {
        // Use all log files from the logs directory
        $logsPath = storage_path('logs');
        $logFiles = File::files($logsPath);
        
        // Initialize counters and data structures
        $loginActivity = $this->initializeTimeData();
        $purchaseSuccessCount = 0;
        $purchaseFailedCount = 0;
        $itemSales = [];
        $errors = [];
        $totalLoginEvents = 0;
        
        // Track the earliest and latest timestamps
        $earliestTimestamp = null;
        $latestTimestamp = null;
        
        // Process each log file
        foreach ($logFiles as $file) {
            // Skip large files to prevent memory issues
            if ($file->getSize() > 10 * 1024 * 1024) { // Skip files larger than 10MB
                continue;
            }
            
            $content = File::get($file->getPathname());
            $lines = explode("\n", $content);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                // Extract timestamp for time series data
                $timestamp = $this->extractTimestamp($line);
                
                // Update earliest and latest timestamps
                if ($timestamp) {
                    $timestampObj = Carbon::parse($timestamp);
                    
                    if (is_null($earliestTimestamp) || $timestampObj < $earliestTimestamp) {
                        $earliestTimestamp = $timestampObj;
                    }
                    
                    if (is_null($latestTimestamp) || $timestampObj > $latestTimestamp) {
                        $latestTimestamp = $timestampObj;
                    }
                    
                    $hourBucket = $timestampObj->format('H:i');
                    
                    // Track login activity for Purchase Status section
                    if (Str::contains($line, ['login', 'Login', 'logged in', 'user authentication', 'authenticated'])) {
                        $totalLoginEvents++;
                        
                        // For successful logins
                        if (Str::contains($line, ['successful', 'success', 'succeeded'])) {
                            if (isset($loginActivity['successByHour'][$hourBucket])) {
                                $loginActivity['successByHour'][$hourBucket]++;
                            } else {
                                // If the hour bucket doesn't exist, create it
                                $loginActivity['successByHour'][$hourBucket] = 1;
                            }
                        } 
                        // For failed logins
                        elseif (Str::contains($line, ['fail', 'Fail', 'invalid', 'error', 'Error'])) {
                            if (isset($loginActivity['failedByHour'][$hourBucket])) {
                                $loginActivity['failedByHour'][$hourBucket]++;
                            } else {
                                // If the hour bucket doesn't exist, create it
                                $loginActivity['failedByHour'][$hourBucket] = 1;
                            }
                        }
                        // Default to success if no failure indicators
                        else {
                            if (isset($loginActivity['successByHour'][$hourBucket])) {
                                $loginActivity['successByHour'][$hourBucket]++;
                            } else {
                                // If the hour bucket doesn't exist, create it
                                $loginActivity['successByHour'][$hourBucket] = 1;
                            }
                        }
                    }
                    
                    // Continue tracking purchases for other charts
                    if (Str::contains($line, ['purchase', 'Purchase', 'transaction', 'Transaction'])) {
                        if (Str::contains($line, ['success', 'Success', 'completed', 'Completed'])) {
                            $purchaseSuccessCount++;
                            if (isset($loginActivity['successByHour'][$hourBucket])) {
                                $loginActivity['successByHour'][$hourBucket]++;
                            }
                        } elseif (Str::contains($line, ['fail', 'Fail', 'error', 'Error'])) {
                            $purchaseFailedCount++;
                            if (isset($loginActivity['failedByHour'][$hourBucket])) {
                                $loginActivity['failedByHour'][$hourBucket]++;
                            }
                        }
                    }
                    
                    // Extract item IDs (looking for patterns like ID: 12345678)
                    if (preg_match('/(?:item|product|ID|Id|id)[:\s-]+(\d{5,})/', $line, $matches)) {
                        $itemId = $matches[1];
                        if (!isset($itemSales[$itemId])) {
                            $itemSales[$itemId] = 0;
                        }
                        $itemSales[$itemId]++;
                    }
                    
                    // Extract error information
                    if (Str::contains($line, ['error', 'Error', 'warning', 'Warning', 'exception', 'Exception'])) {
                        // Look for error codes
                        if (preg_match('/(?:status|code)[:\s]+(\d{3})/', $line, $matches)) {
                            $errorCode = "Status Code: " . $matches[1];
                            if (!isset($errors[$errorCode])) {
                                $errors[$errorCode] = 0;
                            }
                            $errors[$errorCode]++;
                        }
                        // Look for error IDs
                        elseif (preg_match('/(?:error|exception)(?:\s+id)?[:\s]+(\d{7,})/', $line, $matches)) {
                            $errorId = "Error ID: " . $matches[1];
                            if (!isset($errors[$errorId])) {
                                $errors[$errorId] = 0;
                            }
                            $errors[$errorId]++;
                        }
                        // Generic errors
                        else {
                            if (!isset($errors['Other'])) {
                                $errors['Other'] = 0;
                            }
                            $errors['Other']++;
                        }
                    }
                }
            }
        }
        
        // Prepare time range label from actual log timestamps
        $timeRangeLabel = $this->getTimeRangeFromLogs($earliestTimestamp, $latestTimestamp);
        
        // Prepare the data for charts
        $chartData = [
            'purchaseStatus' => $this->prepareLoginActivityData($loginActivity, $timeRangeLabel, $totalLoginEvents),
            'purchaseSuccessRate' => [
                'succeeded' => $purchaseSuccessCount,
                'failed' => $purchaseFailedCount,
                'totalEvents' => $purchaseSuccessCount + $purchaseFailedCount,
                'timeRange' => $timeRangeLabel
            ],
            'bestSellingItems' => $this->prepareBestSellingItemsData($itemSales, $timeRangeLabel),
            'topErrors' => $this->prepareTopErrorsData($errors, $timeRangeLabel)
        ];
        
        return $chartData;
    }
    
    private function initializeTimeData()
    {
        $hourlyData = [];
        
        // Create time buckets for the last 24 hours to capture more login data
        $now = Carbon::now();
        for ($i = 24; $i >= 0; $i--) {
            $time = $now->copy()->subHours($i)->format('H:i');
            $hourlyData[$time] = 0;
        }
        
        return [
            'successByHour' => $hourlyData,
            'failedByHour' => $hourlyData
        ];
    }
    
    private function extractTimestamp($logLine)
    {
        // Match the timestamp in Laravel log format or common log formats
        if (preg_match('/\[(\d{4}-\d{2}-\d{2}[T\s]\d{2}:\d{2}:\d{2})/', $logLine, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    private function getTimeRangeFromLogs($earliestTimestamp, $latestTimestamp)
    {
        if (is_null($earliestTimestamp) || is_null($latestTimestamp)) {
            return $this->getTimeRangeLabel(); // Fallback to default
        }
        
        $duration = $earliestTimestamp->diffInHours($latestTimestamp);
        if ($duration < 1) {
            $duration = $earliestTimestamp->diffInMinutes($latestTimestamp) . ' min';
        } else {
            $duration = $duration . ' hr';
        }
        
        return $earliestTimestamp->format('M d, H:i') . ' - ' . $latestTimestamp->format('H:i') . ' (' . $duration . ')';
    }
    
    private function getTimeRangeLabel()
    {
        $now = Carbon::now();
        $oneHourAgo = $now->copy()->subHour();
        
        return $oneHourAgo->format('M d, H:i') . ' - ' . $now->format('H:i') . ' (1 hr)';
    }
    
    private function prepareLoginActivityData($loginActivity, $timeRangeLabel, $totalEvents)
    {
        // Sort by time
        ksort($loginActivity['successByHour']);
        ksort($loginActivity['failedByHour']);
        
        $labels = array_keys($loginActivity['successByHour']);
        $successData = array_values($loginActivity['successByHour']);
        $failedData = array_values($loginActivity['failedByHour']);
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Successful Logins',
                    'data' => $successData,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                ],
                [
                    'label' => 'Failed Logins',
                    'data' => $failedData,
                    'backgroundColor' => 'rgba(255, 159, 64, 0.5)',
                    'borderColor' => 'rgba(255, 159, 64, 1)',
                ]
            ],
            'totalEvents' => $totalEvents,
            'timeRange' => $timeRangeLabel
        ];
    }
    
    private function prepareBestSellingItemsData($itemSales, $timeRangeLabel)
    {
        // Sort by sales count in descending order
        arsort($itemSales);
        
        // Take top 5 items
        $topItems = array_slice($itemSales, 0, 5, true);
        
        // If no data found, create sample data
        if (empty($topItems)) {
            $topItems = ['23434500' => 950, '09680878' => 850];
        }
        
        return [
            'labels' => array_keys($topItems),
            'data' => array_values($topItems),
            'totalEvents' => array_sum($itemSales),
            'timeRange' => $timeRangeLabel
        ];
    }
    
    private function prepareTopErrorsData($errors, $timeRangeLabel)
    {
        // Sort by error count in descending order
        arsort($errors);
        
        // Take top 4 errors
        $topErrors = array_slice($errors, 0, 4, true);
        
        // If no data found, create sample data
        if (empty($topErrors)) {
            $topErrors = [
                'Status Code: 500' => 145,
                'Error ID: 10652978' => 35,
                'Error ID: 12321561' => 40,
                'Other' => 133
            ];
        }
        
        return [
            'labels' => array_keys($topErrors),
            'data' => array_values($topErrors),
            'totalEvents' => array_sum($errors),
            'timeRange' => $timeRangeLabel
        ];
    }
}
