<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class FilePreviewController extends Controller
{
    public function preview(Request $request)
    {
        $filePath = $request->input('path');
        
        // Validate the file path to prevent directory traversal attacks
        if (!$this->isValidFilePath($filePath)) {
            return response()->json(['error' => 'Invalid file path'], 400);
        }
        
        // Check if file exists
        if (!File::exists($filePath)) {
            return response()->json(['error' => 'File not found'], 404);
        }
        
        // Get file content
        $content = File::get($filePath);
        
        // Get file extension
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        return response()->json([
            'content' => $content,
            'extension' => $extension,
            'filename' => basename($filePath)
        ]);
    }
    
    private function isValidFilePath($path)
    {
        // Basic validation to prevent directory traversal
        if (empty($path) || !is_string($path)) {
            return false;
        }
        
        // Make sure the path is within the project directory
        $realPath = realpath($path);
        $projectRoot = realpath(base_path());
        
        if ($realPath === false || strpos($realPath, $projectRoot) !== 0) {
            return false;
        }
        
        return true;
    }
}
