<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NginxLogController extends Controller
{
    protected $logDirectory;
    protected $logPath;

    public function __construct()
    {
        $this->logDirectory = storage_path('logs/nginx');
        $this->logPath = $this->logDirectory . '/access.log';
        
        // Create directory and sample log file if they don't exist
        $this->ensureLogFileExists();
    }

    protected function ensureLogFileExists()
    {
        // Create directory if it doesn't exist
        if (!File::exists($this->logDirectory)) {
            File::makeDirectory($this->logDirectory, 0755, true);
        }

        // Create a sample log file if it doesn't exist
        if (!File::exists($this->logPath)) {
            $sampleLogs = [
                date('Y-m-d H:i:s') . " [info] Sample log entry 1 - Application started",
                date('Y-m-d H:i:s') . " [info] Sample log entry 2 - User authentication",
                date('Y-m-d H:i:s') . " [warning] Sample log entry 3 - Low disk space",
                date('Y-m-d H:i:s') . " [error] Sample log entry 4 - Database connection failed",
                date('Y-m-d H:i:s') . " [info] Sample log entry 5 - Request processed successfully"
            ];
            
            File::put($this->logPath, implode(PHP_EOL, $sampleLogs));
        }
    }

    public function show()
    {
        if (!File::exists($this->logPath)) {
            return view('nginx-logs')->with('error', 'Log file not found');
        }

        // Read the last 1000 lines of the log file
        $logs = array_slice(file($this->logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES), -1000);
        
        return view('nginx-logs', ['logs' => $logs]);
    }

    public function download(): StreamedResponse
    {
        if (!File::exists($this->logPath)) {
            abort(404, 'Log file not found');
        }

        return response()->download($this->logPath, 'nginx-access.log');
    }
}
