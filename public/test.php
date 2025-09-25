<?php
// Simple test page to check if logs can be displayed
require_once '../vendor/autoload.php';

$logPath = '../storage/logs';
$files = [];

if (is_dir($logPath)) {
    $directory = new DirectoryIterator($logPath);
    foreach ($directory as $fileInfo) {
        if ($fileInfo->isDot() || $fileInfo->isDir()) continue;
        
        $files[] = [
            'name' => $fileInfo->getFilename(),
            'size' => round($fileInfo->getSize() / 1024, 2) . ' KB',
            'sha256' => hash_file('sha256', $fileInfo->getPathname()),
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log Files Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Log Files Test</h1>
    <p>Found <?= count($files) ?> log files</p>
    
    <table>
        <tr>
            <th>File Name</th>
            <th>Size</th>
            <th>SHA256</th>
        </tr>
        <?php foreach ($files as $file): ?>
        <tr>
            <td><?= htmlspecialchars($file['name']) ?></td>
            <td><?= htmlspecialchars($file['size']) ?></td>
            <td><?= htmlspecialchars(substr($file['sha256'], 0, 32)) ?>...</td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>