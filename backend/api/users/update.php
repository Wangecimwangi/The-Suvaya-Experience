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
if (empty($data->email)) {
    sendError(400, 'Email is required');
}

try {
    // Check if user exists
    $checkQuery = "SELECT id, email FROM users WHERE email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $data->email);
    $checkStmt->execute();

    $user = $checkStmt->fetch();

    if (!$user) {
        sendError(404, 'User not found');
    }

    // Build update query dynamically based on provided fields
    $updateFields = [];
    $params = [':email' => $data->email];

    if (isset($data->name)) {
        $updateFields[] = "name = :name";
        $params[':name'] = $data->name;
    }

    if (isset($data->phone)) {
        $updateFields[] = "phone = :phone";
        $params[':phone'] = $data->phone;
    }

    // If no fields to update
    if (empty($updateFields)) {
        sendError(400, 'No fields to update');
    }

    // Update user
    $query = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE email = :email";
    $stmt = $db->prepare($query);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();

    // Fetch updated user data
    $getUserQuery = "SELECT id, name, email, phone, is_admin, created_at FROM users WHERE email = :email";
    $getUserStmt = $db->prepare($getUserQuery);
    $getUserStmt->bindParam(':email', $data->email);
    $getUserStmt->execute();
    $updatedUser = $getUserStmt->fetch();

    sendResponse(200, 'Profile updated successfully', [
        'user' => $updatedUser
    ]);

} catch (Exception $e) {
    sendError(500, 'Failed to update profile: ' . $e->getMessage());
}
?>
