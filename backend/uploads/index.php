<?php
// Simple static file server for uploaded images
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$requestUri = $_SERVER['REQUEST_URI'];
$basePath = __DIR__;

// Extract the file path from the request
$filePath = parse_url($requestUri, PHP_URL_PATH);
$filePath = str_replace('/uploads/', '', $filePath);
$fullPath = $basePath . '/' . $filePath;

// Security check - prevent directory traversal
$realPath = realpath($fullPath);
$realBase = realpath($basePath);

if ($realPath === false || strpos($realPath, $realBase) !== 0) {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

// Check if file exists
if (!file_exists($fullPath) || !is_file($fullPath)) {
    http_response_code(404);
    echo 'File not found';
    exit;
}

// Get MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $fullPath);
finfo_close($finfo);

// Serve the file
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($fullPath));
readfile($fullPath);
?>
