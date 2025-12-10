<?php
function sendResponse($status, $message, $data = null) {
    http_response_code($status);
    echo json_encode([
        'success' => true,
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function sendError($status, $message) {
    http_response_code($status);
    echo json_encode([
        'success' => false,
        'status' => $status,
        'error' => $message,
        'message' => $message
    ]);
    exit();
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
