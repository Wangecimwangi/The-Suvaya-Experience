<?php
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

// Check if file was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    sendError(400, 'No image file uploaded or upload error occurred');
}

$file = $_FILES['image'];
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowedTypes)) {
    sendError(400, 'Invalid file type. Only JPG, PNG, GIF, and WebP images are allowed');
}

// Validate file size
if ($file['size'] > $maxFileSize) {
    sendError(400, 'File size exceeds 5MB limit');
}

try {
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'event_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadDir = '../../uploads/events/';
    $uploadPath = $uploadDir . $filename;

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        // Return the relative path that will be stored in database
        $imagePath = '/uploads/events/' . $filename;

        sendResponse(200, 'Image uploaded successfully', [
            'image_path' => $imagePath,
            'filename' => $filename
        ]);
    } else {
        sendError(500, 'Failed to save uploaded file');
    }

} catch (Exception $e) {
    sendError(500, 'Upload failed: ' . $e->getMessage());
}
?>
