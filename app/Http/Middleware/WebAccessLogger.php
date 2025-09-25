<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WebAccessLogger
{
    /**
     * Handle an incoming request and log access according to Computer Act requirements.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        // Process the request
        $response = $next($request);
        
        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
        
        // Collect comprehensive log data according to Computer Act requirements
        $logData = [
            'timestamp' => Carbon::now()->toISOString(),
            'client_ip' => $this->getClientIp($request),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'uri' => $request->getRequestUri(),
            'protocol' => $request->getScheme(),
            'host' => $request->getHost(),
            'port' => $request->getPort(),
            'status_code' => $response->getStatusCode(),
            'response_size' => strlen($response->getContent()),
            'response_time_ms' => $responseTime,
            'referer' => $request->header('referer', '-'),
            'session_id' => $request->session() ? $request->session()->getId() : '-',
            'user_id' => auth()->check() ? auth()->id() : '-',
            'username' => auth()->check() ? auth()->user()->username ?? auth()->user()->email : '-',
            'request_headers' => json_encode($request->headers->all()),
            'request_data' => $this->sanitizeRequestData($request),
            'server_name' => gethostname(),
            'process_id' => getmypid(),
        ];
        
        // Format log entry according to Computer Act standards
        $logEntry = $this->formatLogEntry($logData);
        
        // Write to dedicated access log file
        $this->writeToAccessLog($logEntry);
        
        return $response;
    }
    
    /**
     * Get the real client IP address
     */
    private function getClientIp(Request $request): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $request->ip();
    }
    
    /**
     * Sanitize request data for logging (remove sensitive information)
     */
    private function sanitizeRequestData(Request $request): string
    {
        $data = $request->all();
        
        // Remove sensitive fields
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'ssn',
            'social_security',
            '_token'
        ];
        
        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '[REDACTED]';
            }
        }
        
        return json_encode($data);
    }
    
    /**
     * Format log entry according to Computer Act requirements
     */
    private function formatLogEntry(array $data): string
    {
        // Format: [Timestamp] [IP] [User] [Method] [URL] [Status] [Size] [Response Time] [User Agent] [Additional Data]
        return sprintf(
            "[%s] [IP:%s] [User:%s] [%s %s] [Status:%d] [Size:%d bytes] [Time:%s ms] [UA:%s] [Referer:%s] [Session:%s] [Server:%s] [PID:%s] [Headers:%s] [Data:%s]",
            $data['timestamp'],
            $data['client_ip'],
            $data['username'],
            $data['method'],
            $data['url'],
            $data['status_code'],
            $data['response_size'],
            $data['response_time_ms'],
            $data['user_agent'],
            $data['referer'],
            $data['session_id'],
            $data['server_name'],
            $data['process_id'],
            $data['request_headers'],
            $data['request_data']
        );
    }
    
    /**
     * Write log entry to dedicated access log file
     */
    private function writeToAccessLog(string $logEntry): void
    {
        $logFile = storage_path('logs/web-access-' . date('Y-m-d') . '.log');
        
        // Ensure log directory exists
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Write to file with file locking
        file_put_contents($logFile, $logEntry . PHP_EOL, FILE_APPEND | LOCK_EX);
        
        // Also log to Laravel's default log for backup
        Log::channel('daily')->info('Web Access', [
            'entry' => $logEntry
        ]);
    }
}