<?php
require_once '../../config/database.php';
require_once '../../utils/cors.php';
require_once '../../utils/helpers.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendError(405, 'Method not allowed');
}

$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (empty($data->email) || empty($data->current_password) || empty($data->new_password)) {
    sendError(400, 'Email, current password, and new password are required');
}

// Validate new password strength
if (strlen($data->new_password) < 6) {
    sendError(400, 'New password must be at least 6 characters long');
}

try {
    // Get user and verify current password
    $query = "SELECT id, email, password FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();

    $user = $stmt->fetch();

    if (!$user) {
        sendError(404, 'User not found');
    }

    // Verify current password
    if (!password_verify($data->current_password, $user['password'])) {
        sendError(401, 'Current password is incorrect');
    }

    // Hash new password
    $hashedPassword = password_hash($data->new_password, PASSWORD_DEFAULT);

    // Update password
    $updateQuery = "UPDATE users SET password = :password WHERE email = :email";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':password', $hashedPassword);
    $updateStmt->bindParam(':email', $data->email);
    $updateStmt->execute();

    sendResponse(200, 'Password changed successfully', [
        'email' => $user['email']
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to change password: ' . $e->getMessage());
}
?>
